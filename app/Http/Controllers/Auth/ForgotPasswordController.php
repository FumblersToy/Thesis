<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
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
        \Log::info('[FORGOT PASSWORD] Request received', [
            'email' => $request->email,
            'is_ajax' => $request->ajax(),
            'wants_json' => $request->wantsJson(),
            'headers' => $request->headers->all()
        ]);

        try {
            $request->validate([
                'email' => 'required|email|exists:users,email'
            ], [
                'email.exists' => 'We can\'t find a user with that email address.'
            ]);

            \Log::info('[FORGOT PASSWORD] Validation passed');

            // Generate token
            $token = Str::random(64);
            $email = $request->email;

            \Log::info('[FORGOT PASSWORD] Generated token for email', ['email' => $email]);

            // Store token in database
            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $email],
                [
                    'email' => $email,
                    'token' => Hash::make($token),
                    'created_at' => now()
                ]
            );

            \Log::info('[FORGOT PASSWORD] Token stored in database');

            // Send email
            $resetUrl = route('password.reset', ['token' => $token, 'email' => $email]);
            
            \Log::info('[FORGOT PASSWORD] Attempting to send email', [
                'to' => $email,
                'reset_url' => $resetUrl
            ]);

            Mail::send('emails.password-reset', [
                'resetUrl' => $resetUrl,
                'user' => User::where('email', $email)->first()
            ], function ($message) use ($email) {
                $message->to($email)
                        ->subject('Reset Your Password - Bandmate');
            });

            \Log::info('[FORGOT PASSWORD] Email sent successfully');

            // Return JSON response for AJAX requests
            if ($request->wantsJson() || $request->ajax()) {
                \Log::info('[FORGOT PASSWORD] Returning JSON response');
                return response()->json([
                    'status' => 'success',
                    'message' => 'We have emailed your password reset link!'
                ]);
            }

            \Log::info('[FORGOT PASSWORD] Redirecting back with status');
            return back()->with('status', 'We have emailed your password reset link!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::warning('[FORGOT PASSWORD] Validation failed', [
                'errors' => $e->errors()
            ]);
            throw $e;
        } catch (\Exception $e) {
            \Log::error('[FORGOT PASSWORD] Exception occurred', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'An error occurred while processing your request: ' . $e->getMessage()
                ], 500);
            }
            
            throw $e;
        }
    }
}