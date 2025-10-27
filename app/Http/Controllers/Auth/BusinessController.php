<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Models\Business;
use Illuminate\Support\Facades\Auth;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

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

        $profilePictureUrl = '';
        if ($request->hasFile('profile_picture')) {
            $uploadedFile = Cloudinary::upload($request->file('profile_picture')->getRealPath(), [
                'folder' => 'business_profiles',
                'transformation' => [
                    'width' => 500,
                    'height' => 500,
                    'crop' => 'fill',
                    'gravity' => 'face'
                ]
            ]);
            if ($uploadedFile && method_exists($uploadedFile, 'getSecurePath')) {
                $profilePictureUrl = $uploadedFile->getSecurePath();
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
        ]);

        return redirect()->route('feed')->with('success', 'Business profile created successfully!');
    }
}
