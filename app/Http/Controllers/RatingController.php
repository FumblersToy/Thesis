<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rating;
use App\Models\User;
use App\Models\Musician;
use App\Models\Business;
use Illuminate\Support\Facades\Auth;

class RatingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request, $musicianId)
    {
        $user = Auth::user();
        
        // Check if account is disabled
        if ($user->isDisabled()) {
            return response()->json([
                'success' => false,
                'message' => 'Your account is disabled. You cannot rate musicians.'
            ], 403);
        }
        
        // Check if user is a business
        $business = Business::where('user_id', $user->id)->first();
        if (!$business) {
            return response()->json([
                'success' => false,
                'message' => 'Only businesses can rate musicians.'
            ], 403);
        }

        // Check if the target is a musician
        $musician = Musician::where('user_id', $musicianId)->first();
        if (!$musician) {
            return response()->json([
                'success' => false,
                'message' => 'Can only rate musicians.'
            ], 400);
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500'
        ]);

        // Create or update rating
        $rating = Rating::updateOrCreate(
            [
                'business_id' => $user->id,
                'musician_id' => $musicianId
            ],
            [
                'rating' => $request->rating,
                'comment' => $request->comment
            ]
        );

        // Calculate new average rating
        $avgRating = Rating::where('musician_id', $musicianId)->avg('rating');
        $ratingCount = Rating::where('musician_id', $musicianId)->count();

        return response()->json([
            'success' => true,
            'message' => 'Rating submitted successfully!',
            'rating' => $rating,
            'average_rating' => round($avgRating, 1),
            'rating_count' => $ratingCount
        ]);
    }

    public function getUserRating($musicianId)
    {
        $user = Auth::user();
        
        $rating = Rating::where('business_id', $user->id)
            ->where('musician_id', $musicianId)
            ->first();

        $avgRating = Rating::where('musician_id', $musicianId)->avg('rating');
        $ratingCount = Rating::where('musician_id', $musicianId)->count();

        return response()->json([
            'success' => true,
            'user_rating' => $rating ? $rating->rating : null,
            'user_comment' => $rating ? $rating->comment : null,
            'average_rating' => round($avgRating ?? 0, 1),
            'rating_count' => $ratingCount
        ]);
    }
}
