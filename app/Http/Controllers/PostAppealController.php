<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostAppealController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show user's deleted posts
     */
    public function index()
    {
        $deletedPosts = Post::onlyTrashed()
            ->where('user_id', Auth::id())
            ->with('deletedBy')
            ->orderByDesc('deleted_at')
            ->get();

        return view('posts.deleted', compact('deletedPosts'));
    }

    /**
     * Submit an appeal for a deleted post
     */
    public function submitAppeal(Request $request, $postId)
    {
        $request->validate([
            'message' => 'required|string|max:1000'
        ]);

        $post = Post::onlyTrashed()
            ->where('id', $postId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // Check if appeal deadline has passed (15 days)
        if ($post->deleted_at->addDays(15)->isPast()) {
            return response()->json([
                'success' => false,
                'message' => 'The appeal deadline has passed.'
            ], 400);
        }

        // Check if already appealed
        if ($post->appeal_status !== 'none') {
            return response()->json([
                'success' => false,
                'message' => 'You have already submitted an appeal for this post.'
            ], 400);
        }

        $post->appeal_status = 'pending';
        $post->appeal_message = $request->message;
        $post->appeal_at = now();
        $post->save();

        return response()->json([
            'success' => true,
            'message' => 'Your appeal has been submitted and will be reviewed by an admin.'
        ]);
    }
}
