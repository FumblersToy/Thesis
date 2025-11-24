<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Models\Musician;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Cloudinary\Cloudinary;
use Exception;

class MusicianController extends Controller
{
    public function __construct()
    {
        // Remove auth middleware - user doesn't exist yet
    }

    public function showCreateForm()
    {
        // Check if user has verified email in session
        $pendingData = session('pending_registration');
        if (!$pendingData || !isset($pendingData['email_verified'])) {
            return redirect()->route('register')->withErrors(['email' => 'Please complete email verification first.']);
        }

        return view('create_musician');
    }

    public function createMusicianProfile(Request $request)
    {
        // Get pending registration data from session
        $pendingData = session('pending_registration');
        if (!$pendingData || !isset($pendingData['email_verified'])) {
            return redirect()->route('register')->withErrors(['email' => 'Session expired. Please register again.']);
        }

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'stage_name' => 'required|string|max:255',
            'genre' => 'required|in:RnB,House,Pop Punk,Electronic,Reggae,Jazz,Rock',
            'genre2' => 'nullable|in:RnB,House,Pop Punk,Electronic,Reggae,Jazz,Rock',
            'genre3' => 'nullable|in:RnB,House,Pop Punk,Electronic,Reggae,Jazz,Rock',
            'instrument' => 'nullable|string|max:255',
            'instrument2' => 'nullable|string|max:255',
            'instrument3' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'profile_picture' => 'nullable|image|max:2048',
        ]);

        Log::info('[PROFILE CREATION] Creating user account', ['email' => $pendingData['email']]);

        // NOW create the user account
        $user = User::create([
            'email' => $pendingData['email'],
            'password' => $pendingData['password'],
            'account_type' => 'musician',
            'email_verified_at' => now(),
        ]);

        Log::info('[PROFILE CREATION] User created', ['user_id' => $user->id]);

        $profilePictureUrl = '';
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

        Log::info('[PROFILE CREATION] Creating musician profile', ['user_id' => $user->id]);

        $musician = Musician::create([
            'user_id' => $user->id,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'stage_name' => $request->stage_name,
            'genre' => $request->genre,
            'genre2' => $request->genre2,
            'genre3' => $request->genre3,
            'instrument' => $request->instrument,
            'instrument2' => $request->instrument2,
            'instrument3' => $request->instrument3,
            'location' => $request->location,
            'bio' => $request->bio,
            'profile_picture' => $profilePictureUrl,
            'profile_picture_public_id' => $profilePicturePublicId,
        ]);

        Log::info('[PROFILE CREATION] Musician profile created', ['musician_id' => $musician->id]);

        // Clear session data
        session()->forget('pending_registration');

        // Log the user in
        Auth::login($user);

        Log::info('[PROFILE CREATION] User logged in, redirecting to feed');

        return redirect()->route('feed', ['id' => $musician->id])
            ->with('success', 'Welcome to Bandmate! Your profile has been created.');
    }
}
