<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;

Route::get('/', function () {
    return response()->view('welcome')
        ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
        ->header('Pragma', 'no-cache')
        ->header('Expires', '0');
});


Route::get('/login', function () {
    return response()->view('login')
        ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
        ->header('Pragma', 'no-cache')
        ->header('Expires', '0');
})->middleware('guest')->name('login');

Route::Post('/login', 
[LoginController::class, 'login']);

// Password Reset Routes
Route::get('/forgot-password', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])->middleware('guest')->name('password.request');
Route::post('/forgot-password', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->middleware('guest')->name('password.email');
Route::get('/reset-password/{token}', [App\Http\Controllers\Auth\ResetPasswordController::class, 'showResetForm'])->middleware('guest')->name('password.reset');
Route::post('/reset-password', [App\Http\Controllers\Auth\ResetPasswordController::class, 'reset'])->middleware('guest')->name('password.update');

Route::get('/register', function () {
    return response()->view('register')
        ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
        ->header('Pragma', 'no-cache')
        ->header('Expires', '0');
})->middleware('guest')->name('register');

Route::post('/register', 
[RegisterController::class, 'register']);

Route::get('/create', function () {
    return response()->view('create')
        ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
        ->header('Pragma', 'no-cache')
        ->header('Expires', '0');
})->middleware('auth')->name('create');


Route::middleware('auth')->group(function () {
    Route::get('/create/musician', [App\Http\Controllers\Auth\MusicianController::class, 'showCreateForm'])->name('create.musician');
    Route::post('/musician', [App\Http\Controllers\Auth\MusicianController::class, 'createMusicianProfile'])->name('musician.store');
    Route::get('/create/business', [App\Http\Controllers\Auth\BusinessController::class, 'showCreateForm'])->name('create.business');
    Route::post('/business', [App\Http\Controllers\Auth\BusinessController::class, 'createBusinessProfile'])->name('business.store');
    // Accept POST to /create/business as well, in case of older forms
    Route::post('/create/business', [App\Http\Controllers\Auth\BusinessController::class, 'createBusinessProfile']);
    // Posts
    Route::post('/posts', [App\Http\Controllers\PostController::class, 'store'])->name('posts.store');
    Route::delete('/posts/{id}', [App\Http\Controllers\PostController::class, 'destroy'])->name('posts.destroy');
    
    // Likes
    Route::post('/posts/{id}/like', [App\Http\Controllers\LikeController::class, 'toggle'])->name('posts.like');
    
    // Comments
    Route::post('/posts/{id}/comments', [App\Http\Controllers\CommentController::class, 'store'])->name('posts.comments.store');
    Route::get('/posts/{id}/comments', [App\Http\Controllers\CommentController::class, 'index'])->name('posts.comments.index');
    
    // Follow functionality
    Route::post('/users/{id}/follow', [App\Http\Controllers\FollowController::class, 'toggle'])->name('users.follow');
    
    // Messages
    Route::get('/messages', function () {
        return response()->view('messages.index')
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    })->name('messages.index');

    // Settings
    Route::get('/settings', [App\Http\Controllers\SettingsController::class, 'show'])->name('settings.show');
    Route::post('/settings', [App\Http\Controllers\SettingsController::class, 'update'])->name('settings.update');
    
    // Map
    Route::get('/map', function () {
        return view('main.map');
    })->name('map');
});

// Debug route (remove after testing)
Route::get('/debug-admin', function() {
    $admin = \App\Models\Admin::first();
    
    return response()->json([
        'admin_exists' => $admin ? true : false,
        'email' => $admin ? $admin->email : null,
        'password_hash' => $admin ? substr($admin->password, 0, 20) . '...' : null,
        'can_login_hash' => $admin ? \Illuminate\Support\Facades\Hash::check('admin123', $admin->password) : false,
        'current_admin' => \Illuminate\Support\Facades\Auth::guard('admin')->user(),
        'admin_routes' => [
            'login' => route('admin.login'),
            'dashboard' => route('admin.dashboard')
        ]
    ]);
});

// Test route to check if admin dashboard works without auth
Route::get('/test-admin-dashboard', function() {
    return view('admin.dashboard', [
        'users' => collect([]),
        'stats' => [
            'total_users' => 0,
            'total_posts' => 0,
            'total_musicians' => 0,
            'total_businesses' => 0,
        ]
    ]);
});

// Admin Routes
Route::prefix('admin')->name('admin.')->group(function () {
    // Admin Authentication Routes
    Route::get('/login', [App\Http\Controllers\Admin\AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [App\Http\Controllers\Admin\AuthController::class, 'login']);
    Route::post('/logout', [App\Http\Controllers\Admin\AuthController::class, 'logout'])->name('logout');
    
    // Protected Admin Routes
    Route::middleware('admin')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/users/{user}/posts', [App\Http\Controllers\Admin\DashboardController::class, 'userPosts'])->name('user.posts');
        Route::delete('/posts/{post}', [App\Http\Controllers\Admin\DashboardController::class, 'deletePost'])->name('post.delete');
        Route::delete('/users/{user}', [App\Http\Controllers\Admin\DashboardController::class, 'deleteUser'])->name('user.delete');
    });
});

Route::get('/main/feed', function () {
    return response()->view('main.feed')
        ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
        ->header('Pragma', 'no-cache')
        ->header('Expires', '0');
})->middleware('auth')->name('feed');

Route::get('/profile/{id}', function ($id) {
    return response()->view('main.profile', ['id' => $id])
        ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
        ->header('Pragma', 'no-cache')
        ->header('Expires', '0');
})->middleware('auth')->name('profile.show');

// Gracefully handle POST to /profile/{id} by redirecting to GET
Route::post('/profile/{id}', function ($id) {
    return redirect()->route('profile.show', ['id' => $id]);
})->middleware('auth');

// Compatibility redirects for stale /user/{id} links
Route::get('/user/{id}', function ($id) {
    return redirect()->route('profile.show', ['id' => $id]);
})->middleware('auth');
Route::post('/user/{id}', function ($id) {
    return redirect()->route('profile.show', ['id' => $id]);
})->middleware('auth');

// Search route (full page)
Route::get('/search', function () {
    $query = request('query', '');

    $musicians = \App\Models\Musician::query()
        ->when($query !== '', function ($q) use ($query) {
            $q->where(function ($w) use ($query) {
                $w->where('stage_name', 'like', "%{$query}%")
                  ->orWhere('first_name', 'like', "%{$query}%")
                  ->orWhere('last_name', 'like', "%{$query}%")
                  ->orWhere('genre', 'like', "%{$query}%")
                  ->orWhere('instrument', 'like', "%{$query}%");
            });
        })
        ->get();

    $venues = \App\Models\Business::query()
        ->when($query !== '', function ($q) use ($query) {
            $q->where(function ($w) use ($query) {
                $w->where('business_name', 'like', "%{$query}%")
                  ->orWhere('venue', 'like', "%{$query}%")
                  ->orWhere('address', 'like', "%{$query}%");
            });
        })
        ->get();

    $posts = collect();

    return response()->view('main.search-results', [
        'query' => $query,
        'musicians' => $musicians,
        'venues' => $venues,
        'posts' => $posts,
    ])->header('Cache-Control', 'no-cache, no-store, must-revalidate')
      ->header('Pragma', 'no-cache')
      ->header('Expires', '0');
})->middleware('auth')->name('search');

// Live search API for dropdown
Route::get('/api/search', function () {
    $query = request('query', '');

    $musicians = \App\Models\Musician::query()
        ->when($query !== '', function ($q) use ($query) {
            $q->where(function ($w) use ($query) {
                $w->where('stage_name', 'like', "%{$query}%")
                  ->orWhere('first_name', 'like', "%{$query}%")
                  ->orWhere('last_name', 'like', "%{$query}%")
                  ->orWhere('genre', 'like', "%{$query}%")
                  ->orWhere('instrument', 'like', "%{$query}%");
            });
        })
        ->limit(5)
        ->get()
        ->map(function ($m) {
            return [
                'user_id' => $m->user_id,
                'stage_name' => $m->stage_name,
                'genre' => $m->genre,
                'profile_image' => $m->profile_picture ? \Illuminate\Support\Facades\Storage::url($m->profile_picture) : null,
            ];
        });

    $venues = \App\Models\Business::query()
        ->when($query !== '', function ($q) use ($query) {
            $q->where(function ($w) use ($query) {
                $w->where('business_name', 'like', "%{$query}%")
                  ->orWhere('venue', 'like', "%{$query}%")
                  ->orWhere('address', 'like', "%{$query}%");
            });
        })
        ->limit(5)
        ->get()
        ->map(function ($b) {
            return [
                'user_id' => $b->user_id,
                'business_name' => $b->business_name,
                'location' => $b->venue,
                'profile_image' => $b->profile_picture ? \Illuminate\Support\Facades\Storage::url($b->profile_picture) : null,
            ];
        });

    return response()->json([
        'musicians' => $musicians,
        'venues' => $venues,
        'posts' => [],
    ]);
})->middleware('auth');

// API: Messages
Route::prefix('api/messages')->middleware('auth')->group(function () {
    Route::get('/', [App\Http\Controllers\MessageController::class, 'index']);
    Route::get('/{userId}', [App\Http\Controllers\MessageController::class, 'show']);
    Route::post('/', [App\Http\Controllers\MessageController::class, 'store']);
    Route::post('/{userId}/read', [App\Http\Controllers\MessageController::class, 'markAsRead']);
    Route::get('/unread/count', [App\Http\Controllers\MessageController::class, 'unreadCount']);
    Route::get('/search/users', [App\Http\Controllers\MessageController::class, 'searchUsers']);
});

// API: posts list with filters
// API route for map users
Route::get('/api/map/users', function () {
    $users = collect();
    
    // Get musicians with locations
    $musicians = \App\Models\Musician::whereNotNull('latitude')
        ->whereNotNull('longitude')
        ->with('user')
        ->get();
    
    foreach ($musicians as $musician) {
        $users->push([
            'user_id' => $musician->user_id,
            'name' => $musician->stage_name ?: ($musician->first_name . ' ' . $musician->last_name),
            'type' => 'musician',
            'latitude' => (float) $musician->latitude,
            'longitude' => (float) $musician->longitude,
            'location_name' => $musician->location_name,
            'instrument' => $musician->instrument,
            'genre' => $musician->genre,
            'bio' => $musician->bio,
            'avatar' => $musician->profile_picture ? \Illuminate\Support\Facades\Storage::url($musician->profile_picture) : '/images/sample-profile.jpg'
        ]);
    }
    
    // Get businesses with locations
    $businesses = \App\Models\Business::whereNotNull('latitude')
        ->whereNotNull('longitude')
        ->with('user')
        ->get();
    
    foreach ($businesses as $business) {
        $users->push([
            'user_id' => $business->user_id,
            'name' => $business->business_name,
            'type' => 'business',
            'latitude' => (float) $business->latitude,
            'longitude' => (float) $business->longitude,
            'location_name' => $business->location_name,
            'venue' => $business->venue,
            'address' => $business->address,
            'bio' => null,
            'avatar' => $business->profile_picture ? \Illuminate\Support\Facades\Storage::url($business->profile_picture) : '/images/sample-profile.jpg'
        ]);
    }
    
    return response()->json([
        'success' => true,
        'users' => $users->values()->all()
    ]);
})->middleware('auth');

Route::get('/api/posts', function () {
    $perPage = max(1, (int) request('per_page', 12));
    $instruments = collect(explode(',', (string) request('instruments', '')))->filter()->values();
    $venues = collect(explode(',', (string) request('venues', '')))->filter()->values();
    $sortBy = request('sort_by', 'recent');
    $userLat = request('user_latitude');
    $userLng = request('user_longitude');
    $maxDistance = request('max_distance');

    $query = \App\Models\Post::query();

    if ($instruments->isNotEmpty()) {
        $query->whereHas('user.musician', function ($q) use ($instruments) {
            $q->whereIn('instrument', $instruments);
        });
    }

    if ($venues->isNotEmpty()) {
        $query->whereHas('user.business', function ($q) use ($venues) {
            $q->whereIn('venue', $venues);
        });
    }

    // Distance filtering and sorting
    if ($sortBy === 'distance' && $userLat && $userLng) {
        // Add distance calculation using Haversine formula
        $query->select('posts.*')
            ->selectRaw('
                COALESCE(
                    (6371 * acos(cos(radians(?)) * cos(radians(COALESCE(musicians.latitude, businesses.latitude))) 
                    * cos(radians(COALESCE(musicians.longitude, businesses.longitude)) - radians(?)) 
                    + sin(radians(?)) * sin(radians(COALESCE(musicians.latitude, businesses.latitude))))),
                    999999
                ) AS distance', [$userLat, $userLng, $userLat])
            ->leftJoin('users', 'posts.user_id', '=', 'users.id')
            ->leftJoin('musicians', 'users.id', '=', 'musicians.user_id')
            ->leftJoin('businesses', 'users.id', '=', 'businesses.user_id');

        // Filter by maximum distance if specified
        if ($maxDistance) {
            $query->havingRaw('distance <= ?', [$maxDistance]);
        }

        $query->orderBy('distance', 'asc');
    } else {
        // Default sorting by creation date
        $query->orderByDesc('created_at');
    }

    $paginator = $query->paginate($perPage);

    $posts = collect($paginator->items())->map(function ($post) {
        $user = \App\Models\User::find($post->user_id);
        $musician = $user ? \App\Models\Musician::where('user_id', $user->id)->first() : null;
        $business = $user && !$musician ? \App\Models\Business::where('user_id', $user->id)->first() : null;

        $userType = $musician ? 'musician' : ($business ? 'business' : 'member');
        $userName = $musician?->stage_name ?: ($business?->business_name ?: ($user->name ?? 'User'));
        $userGenre = $musician?->instrument ?: ($business?->venue ?: '');
        $userAvatarPath = $musician?->profile_picture ?: ($business?->profile_picture ?: null);
        $userAvatar = $userAvatarPath ? \Illuminate\Support\Facades\Storage::url($userAvatarPath) : null;

        // Get like and comment counts
        $likeCount = $post->likes()->count();
        $commentCount = $post->comments()->count();
        $isLiked = \Illuminate\Support\Facades\Auth::check() ? 
            $post->likes()->where('user_id', \Illuminate\Support\Facades\Auth::id())->exists() : false;

        return [
            'id' => $post->id,
            'description' => $post->description,
            'image_path' => $post->image_path ? \Illuminate\Support\Facades\Storage::url($post->image_path) : null,
            'created_at' => optional($post->created_at)->toDateTimeString(),
            'user_type' => $userType,
            'user_name' => $userName,
            'user_genre' => $userGenre,
            'user_avatar' => $userAvatar,
            'user_id' => $post->user_id,
            'is_owner' => $post->user_id === \Illuminate\Support\Facades\Auth::id(),
            'like_count' => $likeCount,
            'comment_count' => $commentCount,
            'is_liked' => $isLiked,
        ];
    });

    return response()->json([
        'success' => true,
        'posts' => $posts,
        'pagination' => [
            'current_page' => $paginator->currentPage(),
            'has_more' => $paginator->hasMorePages(),
        ],
    ]);
})->middleware('auth');

// Venue detail: redirect to user profile for business owner
Route::get('/venue/{id}', function ($id) {
    $business = \App\Models\Business::findOrFail($id);
    return redirect()->route('profile.show', ['id' => $business->user_id]);
})->middleware('auth')->name('venue.show');

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
// Handle GET /logout for users who click or navigate directly
Route::get('/logout', [LoginController::class, 'logout'])->middleware('auth');

// Debug route for CSRF testing
Route::get('/debug/csrf', function () {
    return response()->json([
        'csrf_token' => csrf_token(),
        'session_id' => session()->getId(),
        'session_data' => session()->all(),
        'session_started' => session()->isStarted()
    ]);
});