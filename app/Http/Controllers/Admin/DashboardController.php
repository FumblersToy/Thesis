<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Post;
use App\Models\Musician;
use App\Models\Business;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index()
    {
        $users = User::with(['musician', 'business'])
            ->withCount(['posts', 'likes', 'comments', 'followers', 'following'])
            ->paginate(20);

        $stats = [
            'total_users' => User::count(),
            'total_posts' => Post::count(),
            'total_musicians' => Musician::count(),
            'total_businesses' => Business::count(),
        ];

        return view('admin.dashboard', compact('users', 'stats'));
    }

    public function deletePost(Request $request, $postId)
    {
        $post = Post::findOrFail($postId);
        
        // Delete the image file if it exists
        if ($post->image_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($post->image_path)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($post->image_path);
        }
        
        $post->delete();

        return response()->json([
            'success' => true,
            'message' => 'Post deleted successfully'
        ]);
    }

    public function userPosts($userId)
    {
        $user = User::with(['musician', 'business'])->findOrFail($userId);
        $posts = Post::where('user_id', $userId)
            ->withCount(['likes', 'comments'])
            ->orderByDesc('created_at')
            ->paginate(12);

        return view('admin.user-posts', compact('user', 'posts'));
    }

    public function deleteUser(Request $request, $userId)
    {
        $user = User::findOrFail($userId);
        
        // Delete all user's posts and their images
        $posts = Post::where('user_id', $userId)->get();
        foreach ($posts as $post) {
            if ($post->image_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($post->image_path)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($post->image_path);
            }
        }
        
        // Delete user (cascade will handle related records)
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully'
        ]);
    }
}