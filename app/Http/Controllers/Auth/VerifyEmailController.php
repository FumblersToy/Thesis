<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerifyEmailController extends Controller
{
    public function __invoke(Request $request, $token)
    {
        $pendingUser = session('pending_user');

        // Check if session data exists
        if (!$pendingUser) {
            return redirect()->route('register')->with('error', 'Registration session expired. Please register again.');
        }

        // Check if token matches and not expired
        if ($pendingUser['token'] !== $token || now()->greaterThan($pendingUser['expires_at'])) {
            session()->forget('pending_user');
            return redirect()->route('register')->with('error', 'Verification link is invalid or expired. Please register again.');
        }

        // NOW create the user in database (only after verification)
        $user = User::create([
            'email' => $pendingUser['email'],
            'password' => $pendingUser['password'],
            'email_verified_at' => now(),
        ]);

        // Clear session data
        session()->forget('pending_user');

        // Log user in
        Auth::login($user);

        // Redirect to account type selection
        return redirect()->route('create')->with('verified', true);
    }
}
