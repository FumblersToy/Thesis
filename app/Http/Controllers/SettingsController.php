<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Musician;
use App\Models\Business;

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
                'musician.first_name' => 'nullable|string|max:255',
                'musician.last_name' => 'nullable|string|max:255',
                'musician.stage_name' => 'nullable|string|max:255',
                'musician.genre' => 'nullable|string|max:255',
                'musician.instrument' => 'nullable|string|max:255',
                'musician.bio' => 'nullable|string',
                'musician.latitude' => 'nullable|numeric|between:-90,90',
                'musician.longitude' => 'nullable|numeric|between:-180,180',
                'musician.location_name' => 'nullable|string|max:255',
            ]);

            if ($request->hasFile('musician.profile_picture')) {
                $path = $request->file('musician.profile_picture')->store('profiles', 'public');
                $musician->profile_picture = $path;
            }
            $musician->first_name = $request->input('musician.first_name');
            $musician->last_name = $request->input('musician.last_name');
            $musician->stage_name = $request->input('musician.stage_name');
            $musician->genre = $request->input('musician.genre');
            $musician->instrument = $request->input('musician.instrument');
            $musician->bio = $request->input('musician.bio');
            $musician->latitude = $request->input('musician.latitude');
            $musician->longitude = $request->input('musician.longitude');
            $musician->location_name = $request->input('musician.location_name');
            $musician->save();
        }

        if ($business) {
            $validatedBusiness = $request->validate([
                'business.profile_picture' => 'nullable|image|max:3072',
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
                $path = $request->file('business.profile_picture')->store('profiles', 'public');
                $business->profile_picture = $path;
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


