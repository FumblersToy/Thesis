<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Models\Business;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Cloudinary\Cloudinary;
use Exception;

class BusinessController extends Controller
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

        return view('create_business');
    }

    public function createBusinessProfile(Request $request)
    {
        // Get pending registration data from session
        $pendingData = session('pending_registration');
        if (!$pendingData || !isset($pendingData['email_verified'])) {
            return redirect()->route('register')->withErrors(['email' => 'Session expired. Please register again.']);
        }

        $request->validate([
            'business_name' => 'required|string|max:255',
            'contact_email' => 'required|email|max:255',
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'venue' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'profile_picture' => 'nullable|image|max:2048',
        ]);

        Log::info('[PROFILE CREATION] Creating user account', ['email' => $pendingData['email']]);

        // NOW create the user account
        $user = User::create([
            'email' => $pendingData['email'],
            'password' => $pendingData['password'],
            'account_type' => 'business',
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
                            'folder' => 'business_profiles',
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

        Log::info('[PROFILE CREATION] Creating business profile', ['user_id' => $user->id]);

        $business = Business::create([
            'user_id' => $user->id,
            'business_name' => $request->business_name,
            'contact_email' => $request->contact_email,
            'phone_number' => $request->phone_number,
            'address' => $request->address,
            'venue' => $request->venue,
            'location' => $request->location,
            'profile_picture' => $profilePictureUrl,
            'profile_picture_public_id' => $profilePicturePublicId,
        ]);

        Log::info('[PROFILE CREATION] Business profile created', ['business_id' => $business->id]);

        // Clear session data
        session()->forget('pending_registration');

        // Log the user in
        Auth::login($user);

        Log::info('[PROFILE CREATION] User logged in, redirecting to feed');

        return redirect()->route('feed')->with('success', 'Welcome to Bandmate! Your business profile has been created.');
    }
}
