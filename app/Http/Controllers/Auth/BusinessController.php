<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Models\Business;
use Illuminate\Support\Facades\Auth;

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

        $profilePicturePath = null;
        if ($request->hasFile('profile_picture')) {
            $profilePicturePath = $request->file('profile_picture')->store('business_profiles', 'public');
        }

        $business = new Business();
        $business->user_id = Auth::id();
        $business->business_name = $request->business_name;
        $business->contact_email = $request->contact_email;
        $business->phone_number = $request->phone_number;
        $business->address = $request->address;
        $business->venue = $request->venue;
        $business->profile_picture = $profilePicturePath;
        $business->save();

        return redirect()->route('feed')->with('success', 'Business profile created successfully!');
    }
}
