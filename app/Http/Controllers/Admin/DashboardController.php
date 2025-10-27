<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Post;
use App\Models\Musician;
use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Cloudinary\Cloudinary;
use Exception;

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
        
        // Delete the image from Cloudinary if it exists
        if ($post->image_public_id) {
            try {
                $cloudinaryUrl = config('cloudinary.cloud_url');
                if ($cloudinaryUrl) {
                    $cloudinary = new Cloudinary($cloudinaryUrl);
                    $cloudinary->uploadApi()->destroy($post->image_public_id);
                }
            } catch (Exception $e) {
                Log::error('Cloudinary delete error: ' . $e->getMessage());
                // Continue with post deletion even if Cloudinary deletion fails
            }
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
        
        try {
            $cloudinaryUrl = config('cloudinary.cloud_url');
            $cloudinary = $cloudinaryUrl ? new Cloudinary($cloudinaryUrl) : null;

            // Delete all user's posts and their images from Cloudinary
            $posts = Post::where('user_id', $userId)->get();
            foreach ($posts as $post) {
                if ($post->image_public_id && $cloudinary) {
                    try {
                        $cloudinary->uploadApi()->destroy($post->image_public_id);
                    } catch (Exception $e) {
                        Log::error('Cloudinary delete error for post: ' . $e->getMessage());
                        // Continue deletion even if Cloudinary deletion fails
                    }
                }
            }

            // Delete profile pictures from Cloudinary if they exist
            if ($user->musician && $user->musician->profile_picture_public_id && $cloudinary) {
                try {
                    $cloudinary->uploadApi()->destroy($user->musician->profile_picture_public_id);
                } catch (Exception $e) {
                    Log::error('Cloudinary delete error for musician profile: ' . $e->getMessage());
                }
            }

            if ($user->business && $user->business->profile_picture_public_id && $cloudinary) {
                try {
                    $cloudinary->uploadApi()->destroy($user->business->profile_picture_public_id);
                } catch (Exception $e) {
                    Log::error('Cloudinary delete error for business profile: ' . $e->getMessage());
                }
            }
        } catch (Exception $e) {
            Log::error('Error deleting user assets from Cloudinary: ' . $e->getMessage());
        }
        
        // Delete user (cascade will handle related records)
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully'
        ]);
    }
}