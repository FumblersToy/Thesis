<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class RegisterController extends Controller
{
    public function register(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
        ]);

        // Generate verification token
        $token = bin2hex(random_bytes(32));

        // Store data in session temporarily (NOT in database)
        $request->session()->put('pending_user', [
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'token' => $token,
            'expires_at' => now()->addHours(24),
        ]);

        // Send verification email
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addHours(24),
            ['token' => $token]
        );

        Mail::raw("Welcome to Bandmate!\n\nPlease click the link below to verify your email and complete your registration:\n\n{$verificationUrl}\n\nThis link will expire in 24 hours.\n\nIf you didn't create this account, you can ignore this email.", function ($message) use ($request) {
            $message->to($request->email)
                    ->subject('Verify Your Bandmate Account');
        });

        return redirect()->route('verification.notice')->with('email', $request->email);
    }
}
