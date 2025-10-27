<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Models\Musician;
use Illuminate\Support\Facades\Auth;

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
            // match exact option values from the form
            'genre' => 'required|in:RnB,House,Pop Punk,Electronic,Reggae,Jazz,Rock',
            'instrument' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
            'profile_picture' => 'nullable|image|max:2048', // Max 2MB
        ]);

        // Check if musician profile already exists for this user
        if (Musician::where('user_id', Auth::id())->exists()) {
            return redirect()->back()->with('error', 'You already have a musician profile.');
        }

        // Handle profile picture upload if provided
        $profilePicturePath = null;
        if ($request->hasFile('profile_picture')) {
            $profilePicturePath = $request->file('profile_picture')->store('profile_pictures', 'public');
        }

        // Create the musician profile
        $musician = new Musician();
        $musician->user_id = Auth::id(); // Using Auth facade
        $musician->first_name = $request->first_name;
        $musician->last_name = $request->last_name;
        $musician->stage_name = $request->stage_name;
        $musician->genre = $request->genre;
        $musician->instrument = $request->instrument;
        $musician->bio = $request->bio;
        $musician->profile_picture = $profilePicturePath;
        $musician->save();

        // Redirect to a desired location, e.g., musician profile page
        return redirect()->route('feed', ['id' => $musician->id])
            ->with('success', 'Musician profile created successfully!');
    }
}