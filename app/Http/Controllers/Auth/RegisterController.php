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

        // Generate 6-digit verification code
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Store user data in session temporarily
        $request->session()->put('pending_registration', [
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'code' => $code,
            'expires_at' => now()->addMinutes(30),
        ]);

        Log::info('[REGISTRATION] Session stored', ['elapsed_ms' => round((microtime(true) - $startTime) * 1000)]);

        // Queue the email to be sent after the response
        SendVerificationEmail::dispatch($request->email, $code)
            ->afterResponse();

        $totalTime = round((microtime(true) - $startTime) * 1000);
        Log::info('[REGISTRATION] Completed', ['total_ms' => $totalTime]);

        return redirect()->route('verification.code')->with('email', $request->email);
    }
}
