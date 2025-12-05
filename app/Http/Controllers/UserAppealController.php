<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class UserAppealController extends Controller
{
    public function submitAppeal(Request $request)
    {
        $user = Auth::user();
        
        // Check if user has a scheduled deletion
        if (!$user->deletion_scheduled_at) {
            return response()->json([
                'success' => false,
                'message' => 'Your account is not scheduled for deletion.'
            ], 400);
        }
        
        // Check if deletion date has passed
        if ($user->deletion_scheduled_at->isPast()) {
            return response()->json([
                'success' => false,
                'message' => 'The appeal period has expired.'
            ], 400);
        }
        
        // Check if already submitted an appeal
        if ($user->appeal_status !== 'none') {
            return response()->json([
                'success' => false,
                'message' => 'You have already submitted an appeal.'
            ], 400);
        }
        
        $request->validate([
            'message' => 'required|string|max:1000'
        ]);
        
        $user->update([
            'appeal_status' => 'pending',
            'appeal_message' => $request->message,
            'appeal_at' => now()
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Your appeal has been submitted successfully. The admin team will review it soon.'
        ]);
    }
    
    public function showAppealPage()
    {
        $user = Auth::user();
        
        // Check if user has a scheduled deletion
        if (!$user->deletion_scheduled_at) {
            return redirect()->route('settings')->with('error', 'Your account is not scheduled for deletion.');
        }
        
        return view('main.appeal', compact('user'));
    }
}
