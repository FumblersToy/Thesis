<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Like;
use App\Models\Post;

class LikeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function toggle(Request $request, $postId)
    {
        try {
            $post = Post::findOrFail($postId);
            $userId = Auth::id();

            // Check if user already liked this post
            $existingLike = Like::where('user_id', $userId)
                ->where('post_id', $postId)
                ->first();

            if ($existingLike) {
                // Unlike the post
                $existingLike->delete();
                $liked = false;
            } else {
                // Like the post
                Like::create([
                    'user_id' => $userId,
                    'post_id' => $postId,
                ]);
                $liked = true;
            }

            // Get updated like count
            $likeCount = $post->likes()->count();

            if ($request->wantsJson() || $request->header('Accept') === 'application/json') {
                return response()->json([
                    'success' => true,
                    'liked' => $liked,
                    'like_count' => $likeCount,
                ]);
            }

            return redirect()->back();
        } catch (\Exception $e) {
            if ($request->wantsJson() || $request->header('Accept') === 'application/json') {
                return response()->json([
                    'success' => false,
                    'message' => 'Error toggling like',
                ], 500);
            }
            throw $e;
        }
    }
}
