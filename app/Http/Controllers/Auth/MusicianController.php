<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Models\Musician;
use Illuminate\Support\Facades\Auth;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class MusicianController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function showCreateForm()
    {
        return view('create_musician');
    }

    public function createMusicianProfile(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'stage_name' => 'required|string|max:255',
            'genre' => 'required|in:RnB,House,Pop Punk,Electronic,Reggae,Jazz,Rock',
            'instrument' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
            'profile_picture' => 'nullable|image|max:2048', // Max 2MB
        ]);

        // Check if musician profile already exists for this user
        if (Musician::where('user_id', Auth::id())->exists()) {
            return redirect()->back()->with('error', 'You already have a musician profile.');
        }

        // Handle profile picture upload to Cloudinary if provided
        $profilePictureUrl = null;
        if ($request->hasFile('profile_picture')) {
            $uploadedFile = Cloudinary::upload($request->file('profile_picture')->getRealPath(), [
                'folder' => 'profile_pictures',
                'transformation' => [
                    'width' => 500,
                    'height' => 500,
                    'crop' => 'fill',
                    'gravity' => 'face'
                ]
            ]);
            $profilePictureUrl = $uploadedFile->getSecurePath();
        }

        // Create the musician profile
        $musician = new Musician();
        $musician->user_id = Auth::id();
        $musician->first_name = $request->first_name;
        $musician->last_name = $request->last_name;
        $musician->stage_name = $request->stage_name;
        $musician->genre = $request->genre;
        $musician->instrument = $request->instrument;
        $musician->bio = $request->bio;
        $musician->profile_picture = $profilePictureUrl; // Now stores full Cloudinary URL
        $musician->save();

        return redirect()->route('feed', ['id' => $musician->id])
            ->with('success', 'Musician profile created successfully!');
    }
}