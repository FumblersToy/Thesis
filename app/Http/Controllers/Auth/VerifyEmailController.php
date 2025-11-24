<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class VerifyEmailController extends Controller
{
    public function showCodeForm()
    {
        // Check if there's pending registration data
        if (!session()->has('pending_registration')) {
            return redirect()->route('register')->withErrors(['email' => 'No pending registration found. Please register again.']);
        }

        return view('auth.verify-code');
    }

    public function verifyCode(Request $request)
    {
        $request->validate([
            'code' => ['required', 'string', 'size:6'],
        ]);

        Log::info('[VERIFICATION] Code verification started', ['code' => $request->code]);

        // Get pending registration from session
        $pendingData = session('pending_registration');

        if (!$pendingData) {
            Log::error('[VERIFICATION] No pending registration found');
            return back()->withErrors(['code' => 'No pending registration found. Please register again.']);
        }

        // Check if code has expired
        if (now()->greaterThan($pendingData['expires_at'])) {
            Log::error('[VERIFICATION] Code expired');
            session()->forget('pending_registration');
            return redirect()->route('register')->withErrors(['email' => 'Verification code has expired. Please register again.']);
        }

        // Verify code matches
        if ($pendingData['code'] !== $request->code) {
            Log::error('[VERIFICATION] Invalid code', ['expected' => $pendingData['code'], 'received' => $request->code]);
            return back()->withErrors(['code' => 'Invalid verification code. Please try again.']);
        }

        Log::info('[VERIFICATION] Code verified successfully', ['email' => $pendingData['email']]);

        // DON'T create user yet - keep data in session for profile creation
        // Mark as verified in session
        $pendingData['email_verified'] = true;
        session()->put('pending_registration', $pendingData);

        Log::info('[VERIFICATION] Email verified, redirecting to profile selection');

        // Redirect to profile creation (account type selection)
        return redirect()->route('create')->with('verified', true);
    }

    public function resendCode(Request $request)
    {
        $pendingData = session('pending_registration');

        if (!$pendingData) {
            return back()->withErrors(['code' => 'No pending registration found. Please register again.']);
        }

        // Generate new code
        $newCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        // Update session with new code and expiration
        $pendingData['code'] = $newCode;
        $pendingData['expires_at'] = now()->addMinutes(30);
        session()->put('pending_registration', $pendingData);

        // Send new code
        \App\Jobs\SendVerificationEmail::dispatch($pendingData['email'], $newCode)
            ->afterResponse();

        return back()->with('status', 'A new verification code has been sent to your email.');
    }
}
