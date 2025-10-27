<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Models\Business;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Cloudinary\Cloudinary;
use Exception;

class BusinessController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function showCreateForm()
    {
        return view('create_business');
    }

    public function createBusinessProfile(Request $request)
    {
        $request->validate([
            'business_name' => 'required|string|max:255',
            'contact_email' => 'required|email|max:255|unique:businesses,contact_email',
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'venue' => 'required|string|max:255',
            'profile_picture' => 'nullable|image|max:2048',
        ]);

        if (Business::where('user_id', Auth::id())->exists()) {
            return redirect()->back()->with('error', 'You already have a business profile.');
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

        $business = Business::create([
            'user_id' => Auth::id(),
            'business_name' => $request->business_name,
            'contact_email' => $request->contact_email,
            'phone_number' => $request->phone_number,
            'address' => $request->address,
            'venue' => $request->venue,
            'profile_picture' => $profilePictureUrl,
            'profile_picture_public_id' => $profilePicturePublicId,
        ]);

        return redirect()->route('feed')->with('success', 'Business profile created successfully!');
    }
}
