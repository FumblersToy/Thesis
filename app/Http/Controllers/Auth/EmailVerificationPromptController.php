<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EmailVerificationPromptController extends Controller
{
    public function __invoke(Request $request)
    {
        // Check if there's pending registration
        if (!session()->has('pending_user')) {
            return redirect()->route('register')->with('error', 'No pending registration. Please register first.');
        }

        return view('auth.verify-email');
    }
}
