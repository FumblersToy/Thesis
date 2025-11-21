<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class ForgotPasswordController extends Controller
{
    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ], [
            'email.exists' => 'We can\'t find a user with that email address.'
        ]);

        // Generate token
        $token = Str::random(64);
        $email = $request->email;

        // Store token in database
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            [
                'email' => $email,
                'token' => Hash::make($token),
                'created_at' => now()
            ]
        );

        // Send email
        $resetUrl = route('password.reset', ['token' => $token, 'email' => $email]);
        
        Mail::send('emails.password-reset', [
            'resetUrl' => $resetUrl,
            'user' => User::where('email', $email)->first()
        ], function ($message) use ($email) {
            $message->to($email)
                    ->subject('Reset Your Password - Bandmate');
        });

        // Return JSON response for AJAX requests
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => 'We have emailed your password reset link!'
            ]);
        }

        return back()->with('status', 'We have emailed your password reset link!');
    }
}