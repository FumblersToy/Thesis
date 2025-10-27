<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Carbon\Carbon;

class ResetPasswordController extends Controller
{
    public function showResetForm(Request $request, $token = null)
    {
        $email = $request->query('email');
        
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $email
        ]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $email = $request->email;
        $token = $request->token;

        // Check if token exists and is valid (not older than 1 hour)
        $passwordReset = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->where('created_at', '>', Carbon::now()->subHour())
            ->first();

        if (!$passwordReset || !Hash::check($token, $passwordReset->token)) {
            return back()->withErrors([
                'email' => 'This password reset token is invalid or has expired.'
            ]);
        }

        // Update user password
        $user = User::where('email', $email)->first();
        if ($user) {
            $user->password = Hash::make($request->password);
            $user->save();

            // Delete the token
            DB::table('password_reset_tokens')
                ->where('email', $email)
                ->delete();

            return redirect()->route('login')->with('status', 'Your password has been reset successfully!');
        }

        return back()->withErrors([
            'email' => 'User not found.'
        ]);
    }
}