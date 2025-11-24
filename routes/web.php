<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\PostController;

Route::get('/', function () {
    return response()->view('welcome')
        ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
        ->header('Pragma', 'no-cache')
        ->header('Expires', '0');
});


Route::get('/login', function () {
    // If user is already authenticated, redirect to feed
    if (Auth::check()) {
        return redirect()->route('feed');
    }
    
    return response()->view('login')
        ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
        ->header('Pragma', 'no-cache')
        ->header('Expires', '0');
})->name('login');

Route::Post('/login', 
[LoginController::class, 'login']);

// Password Reset Routes
Route::get('/forgot-password', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])->middleware('guest')->name('password.request');
Route::post('/forgot-password', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->middleware('guest')->name('password.email');
Route::get('/reset-password/{token}', [App\Http\Controllers\Auth\ResetPasswordController::class, 'showResetForm'])->middleware('guest')->name('password.reset');
Route::post('/reset-password', [App\Http\Controllers\Auth\ResetPasswordController::class, 'reset'])->middleware('guest')->name('password.update');

// Debug routes - Email related only
Route::get('/debug/mail-config', function () {
    return response()->json([
        'MAIL_MAILER' => config('mail.default'),
        'MAIL_MAILER_ENV' => env('MAIL_MAILER'),
        'MAIL_HOST' => config('mail.mailers.smtp.host'),
        'MAIL_PORT' => config('mail.mailers.smtp.port'),
        'MAIL_USERNAME' => config('mail.mailers.smtp.username'),
        'MAIL_ENCRYPTION' => config('mail.mailers.smtp.encryption'),
        'MAIL_FROM_ADDRESS' => config('mail.from.address'),
        'MAIL_FROM_NAME' => config('mail.from.name'),
        'WARNING' => 'If MAIL_MAILER is "log", emails are saved to storage/logs instead of being sent'
    ]);
})->name('debug.mail');

Route::get('/debug/clear-config', function () {
    \Illuminate\Support\Facades\Artisan::call('config:clear');
    \Illuminate\Support\Facades\Artisan::call('cache:clear');
    
    return response()->json([
        'success' => true,
        'message' => 'Configuration cache cleared. Check /debug/mail-config again.',
        'config_clear_output' => \Illuminate\Support\Facades\Artisan::output()
    ]);
})->name('debug.clear-config');

Route::get('/debug/test-email/{email}', function ($email) {
    try {
        \Log::info('[DEBUG] Testing email to: ' . $email);
        
        \Illuminate\Support\Facades\Mail::raw('This is a test email from Bandmate. If you receive this, your email configuration is working!', function ($message) use ($email) {
            $message->to($email)
                    ->subject('Test Email from Bandmate');
        });
        
        \Log::info('[DEBUG] Email test completed successfully');
        
        return response()->json([
            'success' => true,
            'message' => 'Email sent! Check your inbox and spam folder.',
            'config' => [
                'driver' => config('mail.default'),
                'host' => config('mail.mailers.smtp.host'),
                'from' => config('mail.from.address')
            ]
        ]);
    } catch (\Exception $e) {
        \Log::error('[DEBUG] Email test failed', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'class' => get_class($e)
        ], 500);
    }
})->name('debug.test-email');

Route::get('/register', [RegisterController::class, 'create'])->middleware('guest')->name('register');
Route::post('/register', [RegisterController::class, 'store']);

// Email Verification Routes (code-based)
Route::get('/verify-code', [VerifyEmailController::class, 'showCodeForm'])
    ->middleware('guest')
    ->name('verification.code');

Route::post('/verify-code', [VerifyEmailController::class, 'verifyCode'])
    ->middleware('guest')
    ->name('verification.verify');

Route::post('/verify-code/resend', [VerifyEmailController::class, 'resendCode'])
    ->middleware('guest')
    ->name('verification.resend');

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
    
    // Posts - Using PostController
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    Route::delete('/posts/{id}', [PostController::class, 'destroy'])->name('posts.destroy');
    
    // Likes
    Route::post('/posts/{id}/like', [App\Http\Controllers\LikeController::class, 'toggle'])->name('posts.like');
    
    // Comments
    Route::post('/posts/{id}/comments', [App\Http\Controllers\CommentController::class, 'store'])->name('posts.comments.store');
    Route::get('/posts/{id}/comments', [App\Http\Controllers\CommentController::class, 'index'])->name('posts.comments.index');
    
    // Follow functionality
    Route::post('/users/{id}/follow', [App\Http\Controllers\FollowController::class, 'toggle'])->name('users.follow');
    
    // Rating functionality
    Route::post('/users/{musicianId}/rate', [App\Http\Controllers\RatingController::class, 'store'])->name('users.rate');
    Route::get('/users/{musicianId}/rating', [App\Http\Controllers\RatingController::class, 'getUserRating'])->name('users.getRating');
    
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

// Admin Routes
Route::prefix('admin')->name('admin.')->group(function () {
    // Admin Authentication Routes
    Route::get('/login', function() {
        $response = app(App\Http\Controllers\Admin\AuthController::class)->showLogin();
        return response($response)
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    })->name('login');
    Route::post('/login', [App\Http\Controllers\Admin\AuthController::class, 'login']);
    Route::post('/logout', [App\Http\Controllers\Admin\AuthController::class, 'logout'])->name('logout');
    
    // Protected Admin Routes
    Route::middleware('admin')->group(function () {
        Route::get('/dashboard', function() {
            $response = app(App\Http\Controllers\Admin\DashboardController::class)->index();
            return response($response)
                ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
                ->header('Pragma', 'no-cache')
                ->header('Expires', '0');
        })->name('dashboard');
        Route::get('/users/{user}/posts', [App\Http\Controllers\Admin\DashboardController::class, 'userPosts'])->name('user.posts');
        Route::delete('/posts/{post}', [App\Http\Controllers\Admin\DashboardController::class, 'deletePost'])->name('post.delete');
        Route::delete('/users/{user}', [App\Http\Controllers\Admin\DashboardController::class, 'deleteUser'])->name('user.delete');
        Route::post('/businesses/{business}/verify', [App\Http\Controllers\Admin\DashboardController::class, 'toggleVerification'])->name('business.verify');
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
                'profile_image' => $m->profile_picture ? getImageUrl($m->profile_picture) : null,
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

// Map feature removed: /api/map/users route deleted

// API: posts list with filters - NOW USING PostController
Route::get('/api/posts', [PostController::class, 'index'])->middleware('auth');

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

Route::get('/debug-build', function() {
    return response()->json([
        'manifest_exists' => file_exists(public_path('build/manifest.json')),
        'build_dir_exists' => is_dir(public_path('build')),
        'public_hot_exists' => file_exists(public_path('hot')),
        'storage_link_exists' => is_link(public_path('storage')),
        'storage_path' => storage_path('app/public'),
        'storage_writable' => is_writable(storage_path('app/public')),
        'app_env' => config('app.env'),
    ]);
});

Route::get('/debug-comment', function() {
    $user = Auth::user();
    
    $avatar = null;
    if ($user->musician) {
        $avatar = $user->musician->profile_picture;
    } elseif ($user->business) {
        $avatar = $user->business->profile_picture;
    }
    
    return response()->json([
        'raw_avatar' => $avatar,
        'processed_avatar' => $avatar ? getImageUrl($avatar) : null,
        'is_valid_url' => $avatar ? filter_var($avatar, FILTER_VALIDATE_URL) : null,
        'starts_with_http' => $avatar ? (strpos($avatar, 'http') === 0) : null,
    ]);
})->middleware('auth');