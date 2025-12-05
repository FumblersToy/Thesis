<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Follow;
use Illuminate\Support\Facades\Auth;

class FollowController extends Controller
{
    public function toggle(Request $request, $userId)
    {
        // Check if account is disabled
        if (Auth::user()->isDisabled()) {
            return response()->json([
                'success' => false,
                'message' => 'Your account is disabled. You cannot follow users.'
            ], 403);
        }

        $user = User::findOrFail($userId);
        $currentUser = Auth::user();

        // Prevent users from following themselves
        if ($currentUser->id === $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot follow yourself.'
            ], 400);
        }

        $follow = Follow::where('follower_id', $currentUser->id)
                       ->where('following_id', $user->id)
                       ->first();

        if ($follow) {
            // Unfollow
            $follow->delete();
            $following = false;
        } else {
            // Follow
            Follow::create([
                'follower_id' => $currentUser->id,
                'following_id' => $user->id,
            ]);
            $following = true;
        }

        // Get updated follower count
        $followerCount = $user->followers()->count();

        return response()->json([
            'success' => true,
            'following' => $following,
            'follower_count' => $followerCount,
            'message' => $following ? 'You are now following this user.' : 'You have unfollowed this user.'
        ]);
    }
}
