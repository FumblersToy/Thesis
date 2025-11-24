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
        Log::info('[REGISTRATION] Started', ['email' => $request->email]);

        $request->validate([
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'terms' => ['accepted'],
        ]);

        Log::info('[REGISTRATION] Validation passed', ['elapsed_ms' => round((microtime(true) - $startTime) * 1000)]);

        // Generate verification token
        $token = Str::random(64);

        // Store user data in session temporarily
        $request->session()->put('pending_registration', [
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'token' => $token,
            'expires_at' => now()->addHours(24),
        ]);

        Log::info('[REGISTRATION] Session stored', ['elapsed_ms' => round((microtime(true) - $startTime) * 1000)]);

        // Send verification email asynchronously
        $verificationUrl = route('verification.verify', ['token' => $token]);
        $email = $request->email;
        
        // Dispatch email sending to background (non-blocking)
        dispatch(function() use ($email, $verificationUrl) {
            try {
                Log::info('[EMAIL] Attempting to send verification email', ['to' => $email]);
                
                Mail::raw(
                    "Welcome to Bandmate!\n\n" .
                    "Please click the link below to verify your email address:\n\n" .
                    $verificationUrl . "\n\n" .
                    "This link will expire in 24 hours.\n\n" .
                    "If you didn't create an account, please ignore this email.\n\n" .
                    "Best regards,\nThe Bandmate Team",
                    function ($message) use ($email) {
                        $message->to($email)
                                ->subject('Verify Your Bandmate Account');
                    }
                );
                
                Log::info('[EMAIL] Verification email sent successfully', ['to' => $email]);
                
            } catch (\Exception $e) {
                Log::error('[EMAIL] Failed to send verification email', [
                    'to' => $email,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        })->afterResponse();

        $totalTime = round((microtime(true) - $startTime) * 1000);
        Log::info('[REGISTRATION] Completed (email dispatched to background)', ['total_ms' => $totalTime]);

        return redirect()->route('verification.notice')->with('email', $request->email);
    }
}
