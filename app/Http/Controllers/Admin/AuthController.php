<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function __construct()
    {
        // Remove guest middleware to avoid conflicts
    }

    public function showLogin()
    {
        // If already authenticated as admin, redirect to dashboard
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }
        
        return view('admin.login');
    }

    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            $credentials = $request->only('email', 'password');
            
            // Check if admin exists
            $admin = Admin::where('email', $credentials['email'])->first();
            if (!$admin) {
                return back()->withErrors([
                    'email' => 'Admin account not found.',
                ])->onlyInput('email');
            }

            // Verify password manually first
            if (!Hash::check($credentials['password'], $admin->password)) {
                return back()->withErrors([
                    'email' => 'Invalid credentials.',
                ])->onlyInput('email');
            }

            // Manual login to avoid conflicts
            Auth::guard('admin')->login($admin, $request->filled('remember'));
            $request->session()->regenerate();
            
            return redirect()->route('admin.dashboard');
            
        } catch (\Exception $e) {
            \Log::error('Admin login error: ' . $e->getMessage());
            return back()->withErrors([
                'email' => 'Login failed. Please try again.',
            ])->onlyInput('email');
        }
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login');
    }
}