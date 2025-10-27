<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Post;
use Cloudinary\Cloudinary;
use Throwable;

class PostController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request)
    {
        $request->validate([
            'description' => 'nullable|string|max:1000',
            'image' => 'nullable|image|max:4096',
        ]);

        $imageUrl = ''; // safe default
        $imagePublicId = null;

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            if ($file->isValid()) {
                $cloudinary = new Cloudinary(env('CLOUDINARY_URL'));
                $uploadedFile = $cloudinary->uploadApi()->upload($file->getRealPath(), [
                    'folder' => 'posts',
                    'transformation' => [
                        'width' => 800,
                        'height' => 800,
                        'crop' => 'limit',
                    ],
                ]);

                $imageUrl = $uploadedFile['secure_url'] ?? '';
                $imagePublicId = $uploadedFile['public_id'] ?? null;
            }
        }

        $post = Post::create([
            'user_id' => Auth::id(),
            'description' => $request->description,
            'image_path' => $imageUrl,
            'image_public_id' => $imagePublicId,
        ]);

        return redirect()->route('feed')->with('success', 'Post created successfully!');
    }

    public function destroy($id)
    {
        $post = Post::findOrFail($id);

        if ($post->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Unauthorized to delete this post');
        }

        if ($post->image_public_id) {
            $cloudinary = new Cloudinary(env('CLOUDINARY_URL'));
            $cloudinary->uploadApi()->destroy($post->image_public_id);
        }

        $post->delete();

        return redirect()->route('feed')->with('success', 'Post deleted successfully!');
    }
}
