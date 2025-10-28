<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\Post;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
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

        $imageUrl = null;
        $imagePublicId = null;

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            if ($file->isValid()) {
                try {
                    // Upload to Cloudinary using Facade
                    $uploadedFile = Cloudinary::upload($file->getRealPath(), [
                        'folder' => 'musician_posts',
                        'transformation' => [
                            'width' => 1200,
                            'height' => 1200,
                            'crop' => 'limit',
                            'quality' => 'auto'
                        ],
                    ]);

                    $imageUrl = $uploadedFile->getSecurePath();
                    $imagePublicId = $uploadedFile->getPublicId();
                    
                    Log::info('Cloudinary upload successful', [
                        'url' => $imageUrl,
                        'public_id' => $imagePublicId
                    ]);
                } catch (Exception $e) {
                    Log::error('Cloudinary upload error: ' . $e->getMessage());
                    Log::error('Stack trace: ' . $e->getTraceAsString());
                    
                    // Fallback to local storage on error
                    try {
                        $storedPath = $file->store('posts', 'public');
                        $imageUrl = $storedPath; // Store path, getImageUrl() will convert it
                        Log::info('Using local storage fallback: ' . $imageUrl);
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
                'image_url' => $imageUrl,
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
                        'user_avatar' => $userAvatarPath ? getImageUrl($userAvatarPath) : null,
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
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create post. Please try again.'
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Failed to create post. Please try again.');
        }
    }

    public function index(Request $request)
    {
        try {
            $perPage = max(1, (int) $request->input('per_page', 12));
            $instruments = collect(explode(',', (string) $request->input('instruments', '')))->filter()->values();
            $venues = collect(explode(',', (string) $request->input('venues', '')))->filter()->values();
            $sortBy = $request->input('sort_by', 'recent');
            $userLat = $request->input('user_latitude');
            $userLng = $request->input('user_longitude');
            $maxDistance = $request->input('max_distance');

            $query = Post::query();

            // Filter by instruments
            if ($instruments->isNotEmpty()) {
                $query->whereHas('user.musician', function ($q) use ($instruments) {
                    $q->whereIn('instrument', $instruments);
                });
            }

            // Filter by venues
            if ($venues->isNotEmpty()) {
                $query->whereHas('user.business', function ($q) use ($venues) {
                    $q->whereIn('venue', $venues);
                });
            }

            // Distance filtering and sorting
            if ($sortBy === 'distance' && $userLat && $userLng) {
                $query->select('posts.*')
                    ->selectRaw('
                        COALESCE(
                            (6371 * acos(cos(radians(?)) * cos(radians(COALESCE(musicians.latitude, businesses.latitude))) 
                            * cos(radians(COALESCE(musicians.longitude, businesses.longitude)) - radians(?)) 
                            + sin(radians(?)) * sin(radians(COALESCE(musicians.latitude, businesses.latitude))))),
                            999999
                        ) AS distance', [$userLat, $userLng, $userLat])
                    ->leftJoin('users', 'posts.user_id', '=', 'users.id')
                    ->leftJoin('musicians', 'users.id', '=', 'musicians.user_id')
                    ->leftJoin('businesses', 'users.id', '=', 'businesses.user_id');

                if ($maxDistance) {
                    $query->havingRaw('distance <= ?', [$maxDistance]);
                }

                $query->orderBy('distance', 'asc');
            } else {
                $query->orderByDesc('created_at');
            }

            $paginator = $query->paginate($perPage);

            $posts = collect($paginator->items())->map(function ($post) {
                $user = \App\Models\User::find($post->user_id);
                $musician = $user ? \App\Models\Musician::where('user_id', $user->id)->first() : null;
                $business = $user && !$musician ? \App\Models\Business::where('user_id', $user->id)->first() : null;

                $userType = $musician ? 'musician' : ($business ? 'business' : 'member');
                $userName = $musician?->stage_name ?: ($business?->business_name ?: ($user->name ?? 'User'));
                $userGenre = $musician?->instrument ?: ($business?->venue ?: '');
                $userAvatarPath = $musician?->profile_picture ?: ($business?->profile_picture ?: null);

                $likeCount = \App\Models\Like::where('post_id', $post->id)->count();
                $commentCount = \App\Models\Comment::where('post_id', $post->id)->count();
                $isLiked = Auth::check() ? 
                    \App\Models\Like::where('post_id', $post->id)->where('user_id', Auth::id())->exists() : false;

                return [
                    'id' => $post->id,
                    'description' => $post->description,
                    'image_path' => $post->image_path ? getImageUrl($post->image_path) : null,
                    'created_at' => optional($post->created_at)->toDateTimeString(),
                    'user_type' => $userType,
                    'user_name' => $userName,
                    'user_genre' => $userGenre,
                    'user_avatar' => $userAvatarPath ? getImageUrl($userAvatarPath) : null,
                    'user_id' => $post->user_id,
                    'is_owner' => $post->user_id === Auth::id(),
                    'like_count' => $likeCount,
                    'comment_count' => $commentCount,
                    'is_liked' => $isLiked,
                ];
            });

            return response()->json([
                'success' => true,
                'posts' => $posts,
                'pagination' => [
                    'current_page' => $paginator->currentPage(),
                    'has_more' => $paginator->hasMorePages(),
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching posts: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch posts'
            ], 500);
        }
    }

    public function destroy(Request $request, $id)
    {
        $post = Post::findOrFail($id);

        if ($post->user_id !== Auth::id()) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to delete this post'
                ], 403);
            }
            return redirect()->back()->with('error', 'Unauthorized to delete this post');
        }

        // Delete image from Cloudinary if public_id exists
        if ($post->image_public_id) {
            try {
                Cloudinary::destroy($post->image_public_id);
                Log::info('Cloudinary image deleted', ['public_id' => $post->image_public_id]);
            } catch (Exception $e) {
                Log::error('Cloudinary delete error: ' . $e->getMessage());
            }
        }

        $post->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Post deleted successfully!'
            ]);
        }

        return redirect()->route('feed')->with('success', 'Post deleted successfully!');
    }
}