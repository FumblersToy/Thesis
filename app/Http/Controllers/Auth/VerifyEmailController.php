<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class VerifyEmailController extends Controller
{
    public function __invoke(Request $request, $token)
    {
        Log::info('[VERIFICATION] Token verification started', ['token' => $token]);

        // Get pending registration from session
        $pendingData = session('pending_registration');

        if (!$pendingData) {
            Log::error('[VERIFICATION] No pending registration found');
            return redirect()->route('register')->withErrors(['email' => 'No pending registration found. Please register again.']);
        }

        // Verify token matches
        if ($pendingData['token'] !== $token) {
            Log::error('[VERIFICATION] Invalid token', ['expected' => $pendingData['token'], 'received' => $token]);
            return redirect()->route('register')->withErrors(['email' => 'Invalid verification token. Please register again.']);
        }

        // Check if token has expired
        if (now()->greaterThan($pendingData['expires_at'])) {
            Log::error('[VERIFICATION] Token expired');
            session()->forget('pending_registration');
            return redirect()->route('register')->withErrors(['email' => 'Verification link has expired. Please register again.']);
        }

        Log::info('[VERIFICATION] Creating user', ['email' => $pendingData['email']]);

        // Create the user now that email is verified
        $user = User::create([
            'email' => $pendingData['email'],
            'password' => $pendingData['password'],
            'account_type' => 'pending',
            'email_verified_at' => now(),
        ]);

        Log::info('[VERIFICATION] User created successfully', ['user_id' => $user->id]);

        // Clear the pending registration
        session()->forget('pending_registration');

        // Log the user in
        Auth::login($user);

        Log::info('[VERIFICATION] User logged in, redirecting to create');

        // Redirect to profile creation
        return redirect()->route('create')->with('verified', true);
    }
}
