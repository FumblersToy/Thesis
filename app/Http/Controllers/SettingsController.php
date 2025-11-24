<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Musician;
use App\Models\Business;
use Cloudinary\Cloudinary;
use Exception;

class SettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function show()
    {
        $user = Auth::user();
        $musician = Musician::where('user_id', $user->id)->first();
        $business = Business::where('user_id', $user->id)->first();

        return view('main.settings', compact('user', 'musician', 'business'))
            ->withHeaders([
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0',
            ]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $musician = Musician::where('user_id', $user->id)->first();
        $business = Business::where('user_id', $user->id)->first();

        $validatedUser = $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        if (!empty($validatedUser['name'])) {
            $user->name = $validatedUser['name'];
        }
        if (!empty($validatedUser['email'])) {
            $user->email = $validatedUser['email'];
        }
        if (!empty($validatedUser['password'])) {
            $user->password = bcrypt($validatedUser['password']);
        }
        $user->save();

        if ($musician) {
            $validatedMusician = $request->validate([
                'musician.profile_picture' => 'nullable|image|max:3072',
                'musician.credential_document' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
                'musician.first_name' => 'nullable|string|max:255',
                'musician.last_name' => 'nullable|string|max:255',
                'musician.stage_name' => 'nullable|string|max:255',
                'musician.genre' => 'nullable|string|max:255',
                'musician.genre2' => 'nullable|string|max:255',
                'musician.genre3' => 'nullable|string|max:255',
                'musician.instrument' => 'nullable|string|max:255',
                'musician.instrument2' => 'nullable|string|max:255',
                'musician.instrument3' => 'nullable|string|max:255',
                'musician.bio' => 'nullable|string',
                'musician.latitude' => 'nullable|numeric|between:-90,90',
                'musician.longitude' => 'nullable|numeric|between:-180,180',
                'musician.location_name' => 'nullable|string|max:255',
            ]);

            if ($request->hasFile('musician.profile_picture')) {
                $file = $request->file('musician.profile_picture');
                if ($file->isValid()) {
                    // Delete old profile picture from Cloudinary if it exists
                    if ($musician->profile_picture_public_id) {
                        try {
                            $cloudinaryUrl = config('cloudinary.cloud_url');
                            if ($cloudinaryUrl) {
                                $cloudinary = new Cloudinary($cloudinaryUrl);
                                $cloudinary->uploadApi()->destroy($musician->profile_picture_public_id);
                            }
                        } catch (Exception $e) {
                            Log::error('Cloudinary delete error for old musician profile: ' . $e->getMessage());
                        }
                    }

                    // Upload new profile picture to Cloudinary
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

                            $musician->profile_picture = $uploadedFile['secure_url'] ?? '';
                            $musician->profile_picture_public_id = $uploadedFile['public_id'] ?? null;
                        }
                    } catch (Exception $e) {
                        Log::error('Cloudinary upload error for musician profile: ' . $e->getMessage());
                    }
                }
            }
            
            // Handle credential document upload
            if ($request->hasFile('musician.credential_document')) {
                $file = $request->file('musician.credential_document');
                if ($file->isValid()) {
                    // Delete old credential document from Cloudinary if it exists
                    if ($musician->credential_document_public_id) {
                        try {
                            $cloudinaryUrl = config('cloudinary.cloud_url');
                            if ($cloudinaryUrl) {
                                $cloudinary = new Cloudinary($cloudinaryUrl);
                                $cloudinary->uploadApi()->destroy($musician->credential_document_public_id);
                            }
                        } catch (Exception $e) {
                            Log::error('Cloudinary delete error for old musician credential: ' . $e->getMessage());
                        }
                    }

                    // Upload new credential document to Cloudinary
                    try {
                        $cloudinaryUrl = config('cloudinary.cloud_url');
                        if ($cloudinaryUrl) {
                            $cloudinary = new Cloudinary($cloudinaryUrl);
                            $uploadedFile = $cloudinary->uploadApi()->upload($file->getRealPath(), [
                                'folder' => 'musician_credentials',
                                'resource_type' => 'auto', // Allows PDF and images
                            ]);

                            $musician->credential_document = $uploadedFile['secure_url'] ?? '';
                            $musician->credential_document_public_id = $uploadedFile['public_id'] ?? null;
                        }
                    } catch (Exception $e) {
                        Log::error('Cloudinary upload error for musician credential: ' . $e->getMessage());
                    }
                }
            }
            
            $musician->first_name = $request->input('musician.first_name');
            $musician->last_name = $request->input('musician.last_name');
            $musician->stage_name = $request->input('musician.stage_name');
            $musician->genre = $request->input('musician.genre');
            $musician->genre2 = $request->input('musician.genre2');
            $musician->genre3 = $request->input('musician.genre3');
            $musician->instrument = $request->input('musician.instrument');
            $musician->instrument2 = $request->input('musician.instrument2');
            $musician->instrument3 = $request->input('musician.instrument3');
            $musician->bio = $request->input('musician.bio');
            $musician->latitude = $request->input('musician.latitude');
            $musician->longitude = $request->input('musician.longitude');
            $musician->location_name = $request->input('musician.location_name');
            $musician->save();
        }

        if ($business) {
            $validatedBusiness = $request->validate([
                'business.profile_picture' => 'nullable|image|max:3072',
                'business.business_permit' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
                'business.business_name' => 'nullable|string|max:255',
                'business.contact_email' => 'nullable|email|max:255',
                'business.phone_number' => 'nullable|string|max:20',
                'business.address' => 'nullable|string|max:255',
                'business.venue' => 'nullable|string|max:255',
                'business.latitude' => 'nullable|numeric|between:-90,90',
                'business.longitude' => 'nullable|numeric|between:-180,180',
                'business.location_name' => 'nullable|string|max:255',
                'business.address_latitude' => 'nullable|numeric|between:-90,90',
                'business.address_longitude' => 'nullable|numeric|between:-180,180',
            ]);

            if ($request->hasFile('business.profile_picture')) {
                $file = $request->file('business.profile_picture');
                if ($file->isValid()) {
                    // Delete old profile picture from Cloudinary if it exists
                    if ($business->profile_picture_public_id) {
                        try {
                            $cloudinaryUrl = config('cloudinary.cloud_url');
                            if ($cloudinaryUrl) {
                                $cloudinary = new Cloudinary($cloudinaryUrl);
                                $cloudinary->uploadApi()->destroy($business->profile_picture_public_id);
                            }
                        } catch (Exception $e) {
                            Log::error('Cloudinary delete error for old business profile: ' . $e->getMessage());
                        }
                    }

                    // Upload new profile picture to Cloudinary
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

                            $business->profile_picture = $uploadedFile['secure_url'] ?? '';
                            $business->profile_picture_public_id = $uploadedFile['public_id'] ?? null;
                        }
                    } catch (Exception $e) {
                        Log::error('Cloudinary upload error for business profile: ' . $e->getMessage());
                    }
                }
            }

            if ($request->hasFile('business.business_permit')) {
                $file = $request->file('business.business_permit');
                if ($file->isValid()) {
                    // Delete old permit from Cloudinary if it exists
                    if ($business->business_permit_public_id) {
                        try {
                            $cloudinaryUrl = config('cloudinary.cloud_url');
                            if ($cloudinaryUrl) {
                                $cloudinary = new Cloudinary($cloudinaryUrl);
                                $cloudinary->uploadApi()->destroy($business->business_permit_public_id);
                            }
                        } catch (Exception $e) {
                            Log::error('Cloudinary delete error for old business permit: ' . $e->getMessage());
                        }
                    }

                    // Upload new permit to Cloudinary
                    try {
                        $cloudinaryUrl = config('cloudinary.cloud_url');
                        if ($cloudinaryUrl) {
                            $cloudinary = new Cloudinary($cloudinaryUrl);
                            $uploadedFile = $cloudinary->uploadApi()->upload($file->getRealPath(), [
                                'folder' => 'business_permits',
                                'resource_type' => 'auto',
                            ]);

                            $business->business_permit = $uploadedFile['secure_url'] ?? '';
                            $business->business_permit_public_id = $uploadedFile['public_id'] ?? null;
                        }
                    } catch (Exception $e) {
                        Log::error('Cloudinary upload error for business permit: ' . $e->getMessage());
                    }
                }
            }

            $business->business_name = $request->input('business.business_name');
            $business->contact_email = $request->input('business.contact_email');
            $business->phone_number = $request->input('business.phone_number');
            $business->address = $request->input('business.address');
            $business->venue = $request->input('business.venue');
            $business->latitude = $request->input('business.latitude');
            $business->longitude = $request->input('business.longitude');
            $business->location_name = $request->input('business.location_name');
            $business->address_latitude = $request->input('business.address_latitude');
            $business->address_longitude = $request->input('business.address_longitude');
            $business->save();
        }

        return redirect()->route('settings.show')->with('status', 'Settings updated successfully.');
    }
}


