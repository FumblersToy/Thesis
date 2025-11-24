<?php

namespace App\Http\Controllers;

use App\Models\Music;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Cloudinary\Cloudinary;

class MusicController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        
        // Only musicians can access this page
        if (!$user->musician) {
            abort(403, 'Only musicians can access this page');
        }

        $musicTracks = Music::where('musician_id', $user->musician->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('main.music', compact('musicTracks'));
    }

    public function show($userId)
    {
        $profileUser = \App\Models\User::findOrFail($userId);
        $musician = $profileUser->musician;
        
        // Only musicians have music pages
        if (!$musician) {
            abort(404, 'This user is not a musician');
        }

        $musicTracks = Music::where('musician_id', $musician->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Check if viewing own music page
        $isOwner = Auth::id() === $profileUser->id;

        return view('main.music-view', compact('musicTracks', 'musician', 'profileUser', 'isOwner'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->musician) {
            return response()->json(['success' => false, 'message' => 'Only musicians can upload music'], 403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'audio' => 'required|file|mimes:mp3,wav,ogg,m4a|max:20480', // 20MB max
        ]);

        try {
            $audioFile = $request->file('audio');
            
            // Upload to Cloudinary
            $cloudinary = new Cloudinary([
                'cloud' => [
                    'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                    'api_key' => env('CLOUDINARY_API_KEY'),
                    'api_secret' => env('CLOUDINARY_API_SECRET'),
                ],
            ]);

            $uploadResult = $cloudinary->uploadApi()->upload($audioFile->getRealPath(), [
                'resource_type' => 'video', // Audio files use 'video' resource type in Cloudinary
                'folder' => 'music',
            ]);

            $music = Music::create([
                'musician_id' => $user->musician->id,
                'title' => $request->title,
                'audio_url' => $uploadResult['secure_url'],
                'audio_public_id' => $uploadResult['public_id'],
                'duration' => $uploadResult['duration'] ?? null,
            ]);

            return response()->json([
                'success' => true,
                'music' => $music,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload audio: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        $user = Auth::user();
        
        if (!$user->musician) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $music = Music::where('id', $id)
            ->where('musician_id', $user->musician->id)
            ->first();

        if (!$music) {
            return response()->json(['success' => false, 'message' => 'Music not found'], 404);
        }

        try {
            // Delete from Cloudinary
            $cloudinary = new Cloudinary([
                'cloud' => [
                    'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                    'api_key' => env('CLOUDINARY_API_KEY'),
                    'api_secret' => env('CLOUDINARY_API_SECRET'),
                ],
            ]);

            $cloudinary->uploadApi()->destroy($music->audio_public_id, ['resource_type' => 'video']);

            $music->delete();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete audio: ' . $e->getMessage(),
            ], 500);
        }
    }
}
