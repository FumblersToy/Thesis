<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostAppealController extends Controller
{
    public function index()
    {
        $deletedPosts = Post::onlyTrashed()
            ->where('user_id', auth()->id())
            ->orderByDesc('deleted_at')
            ->get();

        return view('posts.deleted', compact('deletedPosts'));
    }

    public function submitAppeal(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string|max:500'
        ]);

        $post = Post::onlyTrashed()
            ->where('id', $id)
            ->where('user_id', auth()->id())
            ->where('appeal_status', 'none')
            ->firstOrFail();

        $post->appeal_status = 'pending';
        $post->appeal_message = $request->message;
        $post->appeal_at = now();
        $post->save();

        return response()->json([
            'success' => true,
            'message' => 'Your appeal has been submitted successfully.'
        ]);
    }
}
