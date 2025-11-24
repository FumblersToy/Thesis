<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules;

class RegisterController extends Controller
{
    public function create()
    {
        return view('register');
    }

    public function store(Request $request)
    {
        $startTime = microtime(true);
        Log::info('[REGISTRATION] Registration started', ['email' => $request->email]);

        $request->validate([
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'terms' => ['accepted'],
        ]);

        Log::info('[REGISTRATION] Validation passed', ['time' => microtime(true) - $startTime]);

        // Generate verification token
        $token = Str::random(64);

        // Store user data in session temporarily
        $request->session()->put('pending_registration', [
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'token' => $token,
            'expires_at' => now()->addHours(24),
        ]);

        Log::info('[REGISTRATION] Session data stored', ['time' => microtime(true) - $startTime]);

        // Send verification email
        $verificationUrl = route('verification.verify', ['token' => $token]);
        
        Log::info('[REGISTRATION] Attempting to send verification email', [
            'to' => $request->email,
            'url' => $verificationUrl,
            'time' => microtime(true) - $startTime
        ]);

        try {
            $emailStartTime = microtime(true);
            
            Mail::raw(
                "Welcome to Bandmate!\n\n" .
                "Please click the link below to verify your email address:\n\n" .
                $verificationUrl . "\n\n" .
                "This link will expire in 24 hours.\n\n" .
                "If you didn't create an account, please ignore this email.\n\n" .
                "Best regards,\nThe Bandmate Team",
                function ($message) use ($request) {
                    $message->to($request->email)
                            ->subject('Verify Your Bandmate Account');
                }
            );
            
            $emailDuration = microtime(true) - $emailStartTime;
            Log::info('[REGISTRATION] Email sent successfully', [
                'email_duration' => $emailDuration,
                'total_time' => microtime(true) - $startTime
            ]);
            
        } catch (\Exception $e) {
            Log::error('[REGISTRATION] Email failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'time' => microtime(true) - $startTime
            ]);
            
            // Continue anyway - don't block registration
        }

        $totalTime = microtime(true) - $startTime;
        Log::info('[REGISTRATION] Registration completed', ['total_time' => $totalTime]);

        return redirect()->route('verification.notice')->with('email', $request->email);
    }
}
