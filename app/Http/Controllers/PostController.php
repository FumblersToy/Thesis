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

            $imageUrl = '';
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
                if ($uploadedFile && method_exists($uploadedFile, 'getSecurePath')) {
                    $imageUrl = $uploadedFile->getSecurePath();
                    $imagePublicId = $uploadedFile->getPublicId();
                }
            }

            $post = Post::create([
                'user_id' => Auth::id(),
                'description' => $data['description'] ?? null,
                'image_path' => $imageUrl,
                'image_public_id' => $imagePublicId,
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

            if ($post->user_id !== Auth::id()) {
                return request()->wantsJson() || request()->header('Accept') === 'application/json'
                    ? response()->json(['success' => false, 'message' => 'Unauthorized'], 403)
                    : redirect()->back()->with('error', 'Unauthorized');
            }

            if ($post->image_public_id) {
                Cloudinary::destroy($post->image_public_id);
            }

            $post->delete();

            return request()->wantsJson() || request()->header('Accept') === 'application/json'
                ? response()->json(['success' => true, 'message' => 'Post deleted'])
                : redirect()->route('feed')->with('success', 'Post deleted');
        } catch (Throwable $e) {
            throw $e;
        }
    }
}
