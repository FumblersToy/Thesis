<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
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
        try {
            $request->validate([
                'description' => 'nullable|string|max:1000',
                'image' => 'nullable|image|max:4096',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        }

        // Ensure at least one field is provided
        $hasDescription = !empty($request->input('description'));
        $hasImage = $request->hasFile('image');
        
        if (!$hasDescription && !$hasImage) {
            $errorMessage = 'Please provide at least a description or an image.';
            
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 422);
            }
            
            return redirect()->back()->with('error', $errorMessage);
        }

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
                        // Fallback to local storage if Cloudinary not configured
                        $storedPath = $file->store('posts', 'public');
                        $imageUrl = Storage::url($storedPath);
                        Log::warning('Cloudinary URL not configured, using local storage');
                    }
                } catch (Exception $e) {
                    Log::error('Cloudinary upload error: ' . $e->getMessage());
                    // Fallback to local storage on error
                    try {
                        $storedPath = $file->store('posts', 'public');
                        $imageUrl = Storage::url($storedPath);
                    } catch (Exception $fallbackError) {
                        Log::error('Local storage fallback error: ' . $fallbackError->getMessage());
                    }
                }
            }
        }

        try {
            $post = Post::create([
                'user_id' => Auth::id(),
                'description' => $request->description,
                'image_path' => $imageUrl,
                'image_public_id' => $imagePublicId,
            ]);

            Log::info('Post created successfully', [
                'post_id' => $post->id,
                'user_id' => Auth::id(),
                'has_image' => !empty($imageUrl),
            ]);

            // Handle AJAX requests
            if ($request->wantsJson() || $request->ajax()) {
                // Get user info for the post
                $user = Auth::user();
                $musician = \App\Models\Musician::where('user_id', $user->id)->first();
                $business = $musician ? null : \App\Models\Business::where('user_id', $user->id)->first();
                
                $userType = $musician ? 'musician' : ($business ? 'business' : 'member');
                $userName = $musician?->stage_name ?: ($business?->business_name ?: ($user->name ?? 'User'));
                $userGenre = $musician?->instrument ?: ($business?->venue ?: '');
                $userAvatarPath = $musician?->profile_picture ?: ($business?->profile_picture ?: null);
                $userAvatar = $userAvatarPath ? getImageUrl($userAvatarPath) : null;
                
                return response()->json([
                    'success' => true,
                    'message' => 'Post created successfully!',
                    'post' => [
                        'id' => $post->id,
                        'description' => $post->description,
                        'image_path' => $post->image_path ? getImageUrl($post->image_path) : null,
                        'created_at' => $post->created_at->toDateTimeString(),
                        'user_type' => $userType,
                        'user_name' => $userName,
                        'user_genre' => $userGenre,
                        'user_avatar' => $userAvatar,
                        'user_id' => $post->user_id,
                        'is_owner' => true,
                        'like_count' => 0,
                        'comment_count' => 0,
                        'is_liked' => false,
                    ]
                ]);
            }

            return redirect()->route('feed')->with('success', 'Post created successfully!');
        } catch (\Exception $e) {
            Log::error('Error creating post: ' . $e->getMessage());
            
            // Handle AJAX requests
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create post. Please try again.'
                ], 422);
            }
            
            return redirect()->back()->with('error', 'Failed to create post. Please try again.');
        }
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
