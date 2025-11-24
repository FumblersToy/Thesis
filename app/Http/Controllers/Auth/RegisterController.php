<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules;
use App\Jobs\SendVerificationEmail;

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

        // Queue the email to be sent after the response
        $verificationUrl = route('verification.verify', ['token' => $token]);
        SendVerificationEmail::dispatch($request->email, $verificationUrl)
            ->afterResponse();

        $totalTime = round((microtime(true) - $startTime) * 1000);
        Log::info('[REGISTRATION] Completed', ['total_ms' => $totalTime]);

        return redirect()->route('verification.notice')->with('email', $request->email);
    }
}
