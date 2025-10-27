<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Post;
use Cloudinary\Cloudinary;
use Exception;

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
                try {
                    $cloudinaryUrl = config('cloudinary.cloud_url');
                    if ($cloudinaryUrl) {
                        $cloudinary = new Cloudinary($cloudinaryUrl);
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
                    } else {
                        Log::warning('Cloudinary URL not configured');
                    }
                } catch (Exception $e) {
                    Log::error('Cloudinary upload error: ' . $e->getMessage());
                }
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

        // Delete image from Cloudinary if public_id exists
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

        return redirect()->route('feed')->with('success', 'Post deleted successfully!');
    }
}
