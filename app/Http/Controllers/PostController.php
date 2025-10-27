<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Post;
use Throwable;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class PostController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'description' => 'nullable|string|max:1000',
                'image' => 'nullable|image|max:4096',
            ]);

            $imageUrl = null;
            $imagePublicId = null;

            if ($request->hasFile('image')) {
                $uploadedFile = Cloudinary::upload($request->file('image')->getRealPath(), [
                    'folder' => 'posts',
                    'transformation' => [
                        'width' => 800,
                        'height' => 800,
                        'crop' => 'limit'
                    ]
                ]);
                $imageUrl = $uploadedFile->getSecurePath();
                $imagePublicId = $uploadedFile->getPublicId();
            }

            $post = Post::create([
                'user_id' => Auth::id(),
                'description' => $data['description'] ?? null,
                'image_path' => $imageUrl,
                'image_public_id' => $imagePublicId, // store for deletion
            ]);

            if ($request->wantsJson() || $request->header('Accept') === 'application/json') {
                return response()->json([
                    'success' => true,
                    'post' => [
                        'id' => $post->id,
                        'description' => $post->description,
                        'image_path' => $imageUrl,
                        'created_at' => $post->created_at->toDateTimeString(),
                        'user_name' => Auth::user()->name,
                        'user_genre' => '',
                        'user_type' => 'member',
                        'user_avatar' => null,
                        'user_id' => $post->user_id,
                        'is_owner' => true,
                    ],
                ], 201);
            }

            return redirect()->route('feed')->with('success', 'Post created!');
        } catch (Throwable $e) {
            if ($request->wantsJson() || $request->header('Accept') === 'application/json') {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                    'exception' => (new \ReflectionClass($e))->getShortName(),
                ], 500);
            }
            throw $e;
        }
    }

    public function destroy($id)
    {
        try {
            $post = Post::findOrFail($id);
            
            // Check ownership
            if ($post->user_id !== Auth::id()) {
                if (request()->wantsJson() || request()->header('Accept') === 'application/json') {
                    return response()->json([
                        'success' => false,
                        'message' => 'Unauthorized to delete this post',
                    ], 403);
                }
                return redirect()->back()->with('error', 'Unauthorized to delete this post');
            }

            // Delete Cloudinary image if exists
            if ($post->image_public_id) {
                Cloudinary::destroy($post->image_public_id);
            }

            $post->delete();

            if (request()->wantsJson() || request()->header('Accept') === 'application/json') {
                return response()->json([
                    'success' => true,
                    'message' => 'Post deleted successfully',
                ]);
            }

            return redirect()->route('feed')->with('success', 'Post deleted successfully');
        } catch (Throwable $e) {
            if (request()->wantsJson() || request()->header('Accept') === 'application/json') {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                    'exception' => (new \ReflectionClass($e))->getShortName(),
                ], 500);
            }
            throw $e;
        }
    }
}
