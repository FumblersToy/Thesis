<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Post;
use Throwable;

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

            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('posts', 'public');
            }

            $post = Post::create([
                'user_id' => Auth::id(),
                'description' => $data['description'] ?? null,
                'image_path' => $imagePath,
            ]);

            if ($request->wantsJson() || $request->header('Accept') === 'application/json') {
                return response()->json([
                    'success' => true,
                    'post' => [
                        'id' => $post->id,
                        'description' => $post->description,
                        'image_path' => $imagePath ? Storage::url($imagePath) : null,
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
                $status = 500;
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                    'exception' => (new \ReflectionClass($e))->getShortName(),
                ], $status);
            }
            throw $e;
        }
    }

    public function destroy($id)
    {
        try {
            $post = Post::findOrFail($id);
            
            // Check if the authenticated user owns this post
            if ($post->user_id !== Auth::id()) {
                if (request()->wantsJson() || request()->header('Accept') === 'application/json') {
                    return response()->json([
                        'success' => false,
                        'message' => 'Unauthorized to delete this post',
                    ], 403);
                }
                return redirect()->back()->with('error', 'Unauthorized to delete this post');
            }

            // Delete the image file if it exists
            if ($post->image_path && Storage::disk('public')->exists($post->image_path)) {
                Storage::disk('public')->delete($post->image_path);
            }

            // Delete the post
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
                $status = 500;
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                    'exception' => (new \ReflectionClass($e))->getShortName(),
                ], $status);
            }
            throw $e;
        }
    }
}
