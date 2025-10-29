<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Comment;
use App\Models\Post;

class CommentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request, $postId)
    {
        try {
            $request->validate([
                'content' => 'required|string|max:1000',
            ]);

            $post = Post::findOrFail($postId);

            $comment = Comment::create([
                'user_id' => Auth::id(),
                'post_id' => $postId,
                'content' => $request->content,
            ]);

            // Load the user relationship with musician and business profiles
            $comment->load(['user.musician', 'user.business']);

            if ($request->wantsJson() || $request->header('Accept') === 'application/json') {
                // Get the appropriate display name and avatar
                $displayName = $comment->user->name ?? $comment->user->email;
                $avatar = null;
                
                if ($comment->user->musician) {
                    $displayName = $comment->user->musician->stage_name;
                    $avatar = $comment->user->musician->profile_picture;
                } elseif ($comment->user->business) {
                    $displayName = $comment->user->business->business_name;
                    $avatar = $comment->user->business->profile_picture ?? null;
                }

                return response()->json([
                    'success' => true,
                    'comment' => [
                        'id' => $comment->id,
                        'content' => $comment->content,
                        'created_at' => $comment->created_at->toDateTimeString(),
                        'user_name' => $displayName,
                        'user_avatar' => $avatar,
                    ],
                ], 201);
            }

            return redirect()->back();
        } catch (\Exception $e) {
            if ($request->wantsJson() || $request->header('Accept') === 'application/json') {
                return response()->json([
                    'success' => false,
                    'message' => 'Error creating comment',
                ], 500);
            }
            throw $e;
        }
    }

    public function store(Request $request, $postId)
{
    try {
        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $post = Post::findOrFail($postId);

        $comment = Comment::create([
            'user_id' => Auth::id(),
            'post_id' => $postId,
            'content' => $request->content,
        ]);

        // Load the user relationship with musician and business profiles
        $comment->load(['user.musician', 'user.business']);

        if ($request->wantsJson() || $request->header('Accept') === 'application/json') {
            // Get the appropriate display name and avatar
            $displayName = $comment->user->name ?? $comment->user->email;
            $avatar = null;
            
            if ($comment->user->musician) {
                $displayName = $comment->user->musician->stage_name;
                $avatar = $comment->user->musician->profile_picture;
            } elseif ($comment->user->business) {
                $displayName = $comment->user->business->business_name;
                $avatar = $comment->user->business->profile_picture ?? null;
            }

            return response()->json([
                'success' => true,
                'comment' => [
                    'id' => $comment->id,
                    'content' => $comment->content,
                    'created_at' => $comment->created_at->toDateTimeString(),
                    'user_name' => $displayName,
                    'user_avatar' => $avatar ? getImageUrl($avatar) : null, // ADD getImageUrl() here
                ],
            ], 201);
        }

        return redirect()->back();
    } catch (\Exception $e) {
        if ($request->wantsJson() || $request->header('Accept') === 'application/json') {
            return response()->json([
                'success' => false,
                'message' => 'Error creating comment',
            ], 500);
        }
        throw $e;
    }
}

public function index(Request $request, $postId)
{
    try {
        $post = Post::findOrFail($postId);
        $comments = $post->comments()->with(['user.musician', 'user.business'])->get();

        $commentsData = $comments->map(function ($comment) {
            // Get the appropriate display name and avatar
            $displayName = $comment->user->name ?? $comment->user->email;
            $avatar = null;
            
            if ($comment->user->musician) {
                $displayName = $comment->user->musician->stage_name;
                $avatar = $comment->user->musician->profile_picture;
            } elseif ($comment->user->business) {
                $displayName = $comment->user->business->business_name;
                $avatar = $comment->user->business->profile_picture ?? null;
            }

            return [
                'id' => $comment->id,
                'content' => $comment->content,
                'created_at' => $comment->created_at->toDateTimeString(),
                'user_name' => $displayName,
                'user_avatar' => $avatar ? getImageUrl($avatar) : null, // ADD getImageUrl() here
            ];
        });

        if ($request->wantsJson() || $request->header('Accept') === 'application/json') {
            return response()->json([
                'success' => true,
                'comments' => $commentsData,
            ]);
        }

        return redirect()->back();
    } catch (\Exception $e) {
        if ($request->wantsJson() || $request->header('Accept') === 'application/json') {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching comments',
            ], 500);
        }
        throw $e;
    }
}
}
