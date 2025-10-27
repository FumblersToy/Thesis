<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Models\Musician;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Cloudinary\Cloudinary;
use Exception;

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
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'stage_name' => 'required|string|max:255',
            'genre' => 'required|in:RnB,House,Pop Punk,Electronic,Reggae,Jazz,Rock',
            'instrument' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
            'profile_picture' => 'nullable|image|max:2048',
        ]);

        if (Musician::where('user_id', Auth::id())->exists()) {
            return redirect()->back()->with('error', 'You already have a musician profile.');
        }

        $profilePictureUrl = ''; // default empty
        $profilePicturePublicId = null;

        if ($request->hasFile('profile_picture')) {
            $file = $request->file('profile_picture');
            if ($file->isValid()) {
                try {
                    $cloudinaryUrl = config('cloudinary.cloud_url');
                    if ($cloudinaryUrl) {
                        $cloudinary = new Cloudinary($cloudinaryUrl);
                        $uploadedFile = $cloudinary->uploadApi()->upload($file->getRealPath(), [
                            'folder' => 'profile_pictures',
                            'transformation' => [
                                'width' => 500,
                                'height' => 500,
                                'crop' => 'fill',
                                'gravity' => 'face',
                            ],
                        ]);

                        $profilePictureUrl = $uploadedFile['secure_url'] ?? '';
                        $profilePicturePublicId = $uploadedFile['public_id'] ?? null;
                    } else {
                        Log::warning('Cloudinary URL not configured');
                    }
                } catch (Exception $e) {
                    Log::error('Cloudinary upload error: ' . $e->getMessage());
                }
            }
        }

        $musician = Musician::create([
            'user_id' => Auth::id(),
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'stage_name' => $request->stage_name,
            'genre' => $request->genre,
            'instrument' => $request->instrument,
            'bio' => $request->bio,
            'profile_picture' => $profilePictureUrl,
            'profile_picture_public_id' => $profilePicturePublicId,
        ]);

        return redirect()->route('feed', ['id' => $musician->id])
            ->with('success', 'Musician profile created successfully!');
    }
}
