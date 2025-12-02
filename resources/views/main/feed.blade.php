<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modern Musician Feed</title>
    @vite(['resources/css/app.css', 'resources/css/feed.css', 'resources/css/socket.css'])
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Leaflet removed: maps are no longer used in the app. Address inputs and "Use My Location" remain. -->
</head>
<body class="min-h-screen relative overflow-x-hidden gradient-bg">
    <div class="floating-elements fixed inset-0 pointer-events-none"></div>
    
    <!-- Toast Notification Container -->
    <div id="toastContainer" class="fixed top-20 right-4 z-[60] space-y-2" style="max-width: 350px;"></div>
    
    <div class="flex min-h-screen relative z-10">
        <aside id="sidebar" class="w-80 glass-effect backdrop-blur-xl shadow-2xl hidden lg:flex lg:flex-col animate-slide-up gradient-bg fixed left-0 top-0 bottom-0">
            <div class="p-6 flex-shrink-0">
                <h2 class="font-bold text-2xl mb-6 text-gray-100 flex items-center gap-3">
                    <div class="w-8 h-8 bg-gradient-to-r from-purple-300 to-purple-200 rounded-full animate-pulse-slow"></div>
                    Filters
                </h2>
            </div>
            <div class="flex-1 overflow-y-auto px-6 pb-6">
                
                <form id="filters" class="space-y-8">
                    <div class="animate-fade-in">
                        <h3 class="font-semibold text-gray-200 mb-4 text-lg">🎵 Instruments</h3>
                        <div class="space-y-3" id="instruments">
                            <label class="flex items-center gap-3 text-gray-300">
                                <input type="checkbox" class="form-checkbox"> Guitar
                            </label>
                            <label class="flex items-center gap-3 text-gray-300">
                                <input type="checkbox" class="form-checkbox"> Drums
                            </label>
                            <label class="flex items-center gap-3 text-gray-300">
                                <input type="checkbox" class="form-checkbox"> Piano
                            </label>
                        </div>
                    </div>

                    <div class="animate-fade-in" style="animation-delay: 0.1s">
                        <h3 class="font-semibold text-gray-200 mb-4 text-lg">🏢 Venues</h3>
                        <div class="space-y-3" id="venues">
                            <label class="flex items-center gap-3 text-gray-300">
                                <input type="checkbox" class="form-checkbox"> Studio
                            </label>
                            <label class="flex items-center gap-3 text-gray-300">
                                <input type="checkbox" class="form-checkbox"> Club
                            </label>
                            <label class="flex items-center gap-3 text-gray-300">
                                <input type="checkbox" class="form-checkbox"> Theater
                            </label>
                        </div>
                    </div>

                    <div class="animate-fade-in" style="animation-delay: 0.2s">
                        <h3 class="font-semibold text-white/90 mb-4 text-lg">📍 Distance</h3>
                        <div class="space-y-3">
                            <button type="button" id="getCurrentLocation" class="w-full px-4 py-2 bg-white/20 text-white rounded-xl hover:bg-white/30 transition-colors text-sm">
                                📍 Use My Location
                            </button>
                            <div id="locationStatus" class="text-white/70 text-sm hidden"></div>
                            <div class="space-y-2">
                                <label class="block text-gray-300 text-sm">Sort by:</label>
                                <select id="sortBy" class="w-full px-3 py-2 bg-white/10 text-gray-200 rounded-xl border border-gray-500 focus:outline-none focus:ring-2 focus:ring-purple-300">
                                    <option value="recent" class="text-black">Most Recent</option>
                                    <option value="distance" class="text-black">Nearest First</option>
                                </select>
                            </div>
                            <div id="distanceFilter" class="space-y-2 hidden">
                                <label class="block text-gray-300 text-sm">Within:</label>
                                <select id="maxDistance" class="w-full px-3 py-2 bg-white/10 text-gray-200 rounded-xl border border-gray-500 focus:outline-none focus:ring-2 focus:ring-purple-300">
                                    <option value="" class="text-black">Any Distance</option>
                                    <option value="5" class="text-black">5 km</option>
                                    <option value="10" class="text-black">10 km</option>
                                    <option value="25" class="text-black">25 km</option>
                                    <option value="50" class="text-black">50 km</option>
                                    <option value="100" class="text-black">100 km</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <button type="button" id="applyFilters"
                        class="w-full px-6 py-4 bg-gradient-to-r from-purple-500 to-pink-500 text-white rounded-2xl hover:from-purple-600 hover:to-pink-600 transition-all duration-300 font-semibold text-lg shadow-lg hover-glow animate-scale-in border-2">
                        Apply Filters ✨
                    </button>
                </form>
            </div>
        </aside>

        <!-- Mobile Menu Button -->
        <button id="mobileMenuButton" class="lg:hidden fixed top-6 left-6 z-50 glass-effect backdrop-blur-xl p-3 rounded-2xl text-white hover-glow gradient-bg">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>

        <!-- Mobile Sidebar -->
    <aside id="mobileMenu" class="fixed inset-y-0 left-0 w-80 glass-effect backdrop-blur-xl p-6 transform -translate-x-full lg:hidden transition-transform duration-300 gradient-bg overflow-y-auto h-full" style="z-index:9998;">
            <!-- Close button that appears with the mobile menu and overlaps the mobileMenuButton -->
            <button id="mobileMenuClose" aria-label="Close menu" class="absolute top-6 left-6 p-3 rounded-2xl text-white bg-black/30 hover:bg-black/50 transition-colors" style="z-index:9999;">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>

            <!-- Static mobile sidebar content -->
            <h3 class="text-white font-semibold mb-4">Filters</h3>
            <div class="text-white">Instruments & venues (static)</div>
        </aside>

        <!-- Main Content -->
        <section class="flex-1 p-6 lg:p-8 lg:ml-80 flex flex-col">
            <!-- Header with Search Bar and User Profile -->
            <div class="flex justify-between items-center mb-8 mt-12 lg:mt-0 animate-fade-in">
                <!-- Search Bar (centered) -->
                <div class="flex-1 flex justify-center">
                    <div class="w-full max-w-md relative">
                        <form action="{{ route('search') }}" method="GET" id="searchForm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white/70" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input 
                                type="text" 
                                name="query" 
                                id="searchInput"
                                class="block w-full px-3 py-3 border border-white/20 rounded-2xl leading-5 bg-white/10 backdrop-blur-xl placeholder-white/70 focus:outline-none focus:ring-2 focus:ring-white/30 focus:border-white/30 text-white sm:text-sm" 
                                placeholder="Search musicians, bands, venues..."
                                value="{{ request('query') }}"
                                autocomplete="off">
                        </form>
                        
                        <!-- Search Results Dropdown -->
                        <div id="searchResults" class="absolute top-full left-0 right-0 bg-white/95 backdrop-blur-xl border border-white/20 rounded-2xl shadow-lg mt-1 max-h-96 overflow-y-auto z-50 hidden">
                            <div id="searchResultsContent">
                                <!-- Results will be populated here -->
                            </div>
                            <div id="searchLoading" class="p-4 text-center text-gray-500 hidden">
                                <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-blue-500 mx-auto"></div>
                                <span class="ml-2">Searching...</span>
                            </div>
                            <div id="noResults" class="p-4 text-center text-gray-500 hidden">
                                No results found
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- User Profile Section (dynamic) -->
                @php
                    $user = Auth::user();
                    $musician = $user ? \App\Models\Musician::where('user_id', $user->id)->first() : null;
                    $business = $user ? \App\Models\Business::where('user_id', $user->id)->first() : null;
                    $displayName = $musician?->stage_name
                        ?: ($business?->business_name ?: ($user->name ?? 'User'));
                    $roleLabel = $musician?->instrument ?: ($business?->venue ?: 'Member');
                    $profileImage = null;
                    if ($musician && $musician->profile_picture) {
                        $profileImage = getImageUrl($musician->profile_picture);
                    } elseif ($business && $business->profile_picture) {
                        $profileImage = getImageUrl($business->profile_picture);
                    } else {
                        $profileImage = '/images/sample-profile.jpg';
                    }
                @endphp
                <div class="relative ml-6">
                    <button id="profileButton" class="flex items-center gap-3 bg-white/80 backdrop-blur-xl p-4 rounded-2xl hover:bg-white/90 shadow-lg transition-all duration-300 group border border-gray-200">
                        <div class="relative">
                            <img class="w-12 h-12 rounded-full object-cover border-2 border-gray-200"
                                 src="{{ $profileImage }}"
                                 alt="profile">
                            <span id="profileNotificationBadge" class="hidden absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center animate-pulse">0</span>
                        </div>
                        
                        <div class="hidden sm:block text-left">
                            <p class="text-gray-800 font-semibold">
                                {{ $displayName }}
                            </p>
                            <p class="text-gray-600 text-sm">{{ $roleLabel }}</p>
                        </div>

                        <svg class="w-5 h-5 text-gray-500 group-hover:text-gray-700 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>

                    <div id="profileDropdown" class="absolute right-0 top-full mt-2 w-64 bg-white/95 backdrop-blur-xl rounded-2xl shadow-2xl overflow-hidden hidden animate-scale-in z-50 border border-gray-200">
                        <div class="p-4 border-b border-gray-200">
                            <div class="flex items-center gap-3">
                                <img class="w-16 h-16 rounded-full object-cover border-2 border-gray-200" src="{{ $profileImage }}" alt="profile">
                                <div>
                                    <p class="text-gray-800 font-semibold text-lg">{{ $displayName }}</p>
                                    <p class="text-gray-600">{{ $roleLabel }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="p-2">
                            <a href="{{ route('profile.show', $user->id) }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-100 transition-colors text-black hover:text-gray-900">
                                <span class="text-lg">👤</span>
                                View Profile
                            </a>

                            <button id="notificationsBtn" class="w-full flex items-center gap-3 p-3 rounded-xl hover:bg-gray-100 transition-colors text-gray-700 hover:text-gray-900 relative">
                                <span class="text-lg">🔔</span>
                                Notifications
                                <span id="notificationBadge" class="hidden absolute top-2 left-6 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">0</span>
                            </button>

                            @if($user->musician)
                            <a href="{{ route('music.index') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-100 transition-colors text-gray-700 hover:text-gray-900">
                                <span class="text-lg">🎵</span>
                                My Music
                            </a>
                            @endif

                            <a href="{{ route('settings.show') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-100 transition-colors text-gray-700 hover:text-gray-900">
                                <span class="text-lg">⚙️</span>
                                Settings
                            </a>
                            
                            <a href="{{ route('messages.index') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-100 transition-colors text-gray-700 hover:text-gray-900 relative">
                                <span class="text-lg">💬</span>
                                Messages
                                <span id="messagesBadge" class="hidden absolute top-2 left-6 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">0</span>
                            </a>

                            <div class="border-t border-gray-200 my-2"></div>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-3 p-3 rounded-xl hover:bg-red-50 transition-colors text-red-600 hover:text-red-700">
                                    <span class="text-lg">🚪</span>
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notifications Modal -->
            <div id="notificationsModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
                <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[80vh] overflow-hidden">
                    <div class="p-6 border-b border-gray-200 flex items-center justify-between">
                        <h2 class="text-2xl font-bold text-gray-800">Notifications</h2>
                        <button id="closeNotificationsModal" class="text-gray-500 hover:text-gray-700 p-2 rounded-full hover:bg-gray-100">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    <div id="notificationsContent" class="p-6 overflow-y-auto max-h-[calc(80vh-100px)]">
                        <div class="text-center py-8 text-gray-500">
                            <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                            </svg>
                            <p class="text-lg font-medium">No notifications yet</p>
                            <p class="text-sm">When someone likes or comments on your posts, you'll see it here</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Create Post (static form) -->
            <div class="bg-white/80 backdrop-blur-xl p-8 rounded-3xl shadow-lg mb-8 animate-scale-in hover:shadow-xl transition-all duration-300 border border-gray-200">
                <h2 class="text-2xl font-bold mb-6 text-gray-800 flex items-center gap-3">
                    Create a Post
                </h2>
                <form id="createPostForm" class="space-y-6" enctype="multipart/form-data">
                    @csrf
                    <div>
                        <label for="image" class="block text-gray-700 font-medium mb-2 text-lg">📷 Upload Images/Videos (Up to 3)</label>
                        <p class="text-sm text-gray-500 mb-3">Maximum file size: <span class="font-semibold text-gray-700">50 MB</span> per file</p>
                        <div class="custom-file-input">
                            <input type="file" name="images[]" id="image" accept="image/*,video/*" multiple>
                            <label for="image" class="custom-file-label cursor-pointer">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                </svg>
                                <span id="fileText">Choose up to 3 images or drag them here</span>
                            </label>
                            
                            <div id="filesList" class="mt-3 space-y-2 hidden"></div>
                        </div>
                        
                        <button type="button" id="clearFilesBtn" class="hidden mt-3 px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors text-sm font-medium">
                            🗑️ Clear All Files
                        </button>
                    </div>
                    <label class="block text-gray-700 font-medium mb-3 text-lg" for="description">Description</label>
                    <textarea name="description" id="description" class="w-full border-2 border-gray-400 rounded p-3 resize-none overflow-hidden" placeholder="Write your description here..." rows="4"></textarea>
                    
                    <!-- Upload Progress Bar -->
                    <div id="uploadProgress" class="hidden space-y-2">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-700 font-medium">Uploading...</span>
                            <span id="progressPercentage" class="text-gray-600">0%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                            <div id="progressBar" class="bg-gradient-to-r from-purple-500 to-pink-500 h-3 rounded-full transition-all duration-300" style="width: 0%"></div>
                        </div>
                    </div>
                    
                    <div class="flex gap-3">
                        <button id="submitPostBtn" class="px-6 py-2 button-bg rounded text-white font-bold hover:opacity-90 transition-opacity" type="submit">Post</button>
                        <button id="cancelPostBtn" class="hidden px-6 py-2 bg-gray-500 rounded text-white font-bold hover:bg-gray-600 transition-colors" type="button">Cancel Upload</button>
                    </div>
                </form>
            </div>

            <!-- Posts Grid (loaded dynamically) -->
            <div id="postsGrid" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8">
                <!-- Posts will be loaded here via JavaScript -->
            </div>

            <div class="text-center mt-12">
                <button id="loadMore" class="px-8 py-4 bg-white/80 backdrop-blur-xl text-gray-800 rounded-2xl font-semibold hover:bg-white/90 shadow-lg transition-all duration-300 border border-gray-200">
                    Load More 🎵
                </button>
            </div>
        </section>
    </div>

    <!-- Socket.IO Initialization Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Socket.IO with user data
            @auth
                const userData = {
                    id: {{ Auth::id() }},
                    name: '{{ $displayName }}',
                    type: '{{ $musician ? "musician" : ($business ? "business" : "member") }}',
                    email: '{{ Auth::user()->email }}'
                };
                
                // Initialize socket connection
                if (window.socketManager) {
                    window.socketManager.init(userData);
                }
            @endauth
        });
    </script>
    <script>
        // Mobile menu close button wiring
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenu = document.getElementById('mobileMenu');
            const mobileMenuClose = document.getElementById('mobileMenuClose');
            const mobileMenuButton = document.getElementById('mobileMenuButton');

            if (mobileMenuClose && mobileMenu) {
                mobileMenuClose.addEventListener('click', function(e) {
                    e.stopPropagation();
                    // slide the menu out
                    if (!mobileMenu.classList.contains('-translate-x-full')) {
                        mobileMenu.classList.add('-translate-x-full');
                    }
                    // restore the mobile menu button visibility
                    try { if (mobileMenuButton) mobileMenuButton.classList.remove('hidden'); } catch (err) {}
                });
            }
        });
    </script>
    <!-- Fallback inline fetch: ensure posts render even if feed.js throws earlier -->
    <script>
        document.addEventListener('DOMContentLoaded', async function() {
            try {
                const postsGrid = document.getElementById('postsGrid');
                if (!postsGrid) return;

                console.log('Fallback fetch: requesting posts for initial render');
                const resp = await fetch('/api/posts?page=1&per_page=12', { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' });
                if (!resp.ok) {
                    console.warn('Fallback fetch failed with', resp.status);
                    return;
                }
                const data = await resp.json();
                if (!data.success || !data.posts) return;

                postsGrid.innerHTML = '';
                data.posts.forEach(post => {
                    const div = document.createElement('div');
                    div.className = 'bg-white/80 backdrop-blur-xl rounded-3xl shadow-lg overflow-hidden';

                    // localize fields with fallbacks to match feed.js createPostElement
                    const userId = post.user_id || post.id; // Fallback to post.id if user_id missing
                    const userName = post.user_name || 'User';
                    const userGenre = post.user_genre || '';
                    const userLocation = post.user_location || post.user_city || '';
                    const userType = post.user_type || 'member';
                    const userAvatar = post.user_avatar || '';
                    const createdAt = post.created_at || '';
                    const likeCount = post.like_count || post.likes_count || 0;
                    const commentCount = post.comment_count || post.comments_count || 0;
                    const isOwner = post.is_owner || false; // Add is_owner check
                    const mediaType = post.media_type || 'image';
                    const isVideo = mediaType === 'video';
                    const isVerified = post.is_verified || false; // Add verified status

                    const userTypeEmoji = userType === 'musician' ? '🎵' : (userType === 'business' ? '🏢' : '👤');
                    const userMeta = [userGenre, userLocation].filter(Boolean).join(' · ');

                    // Build avatar HTML (fall back to initial if no avatar)
                    const avatarHtml = userAvatar ?
                        `<img class="w-12 h-12 rounded-full object-cover border-2 border-gray-200" src="${userAvatar}" alt="avatar" onerror="this.src='/images/sample-profile.jpg'"/>` :
                        `<div class="w-12 h-12 bg-gradient-to-r from-purple-400 to-pink-400 rounded-full flex items-center justify-center text-white font-bold">${userName.charAt(0).toUpperCase()}</div>`;

                    // Format date similar to feed.js
                    let formattedDate = '';
                    try {
                        if (createdAt) {
                            formattedDate = new Date(createdAt).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
                        }
                    } catch (e) {
                        formattedDate = createdAt;
                    }

                    // include data attributes so existing modal/listeners pick this up
                    let inner = '';
                    if (post.image_path) {
                        inner += `
                            <div class="relative">
                                ${isVideo ? `
                                    <video 
                                        controls
                                        class="post-image w-full h-80 object-cover cursor-pointer" 
                                        data-post-id="${post.id}"
                                        data-image-url="${post.image_path}"
                                        data-media-type="video"
                                        data-user-name="${(post.user_name||'') }"
                                        data-user-genre="${(post.user_genre||'') }"
                                        data-user-location="${(post.user_location||post.user_city||'') }"
                                        data-user-type="${(post.user_type||'member') }"
                                        data-user-avatar="${(post.user_avatar||'') }"
                                        data-description="${(post.description||'') }"
                                        data-created-at="${(post.created_at||'') }"
                                        data-like-count="${likeCount}"
                                        data-comment-count="${commentCount}"
                                        data-is-liked="${(post.is_liked? 'true' : 'false')}"
                                        data-is-verified="${(isVerified ? 'true' : 'false')}">
                                        <source src="${post.image_path}" type="video/mp4">
                                        Your browser does not support the video tag.
                                    </video>
                                ` : `
                                    <img 
                                        src="${post.image_path}" 
                                        alt="Post image" 
                                        loading="lazy" 
                                        class="post-image w-full h-80 object-cover cursor-pointer" 
                                        onerror="this.src='/images/sample-post-1.jpg'"
                                        data-post-id="${post.id}"
                                        data-image-url="${post.image_path}"
                                        data-media-type="image"
                                        data-user-id="${userId}"
                                        data-user-name="${(post.user_name||'') }"
                                        data-user-genre="${(post.user_genre||'') }"
                                        data-user-location="${(post.user_location||post.user_city||'') }"
                                        data-user-type="${(post.user_type||'member') }"
                                        data-user-avatar="${(post.user_avatar||'') }"
                                        data-description="${(post.description||'') }"
                                        data-created-at="${(post.created_at||'') }"
                                        data-like-count="${likeCount}"
                                        data-comment-count="${commentCount}"
                                        data-is-liked="${(post.is_liked? 'true' : 'false')}"
                                        data-is-verified="${(isVerified ? 'true' : 'false')}"/>
                                `}
                            </div>
                        `;
                    }

                    inner += `
                        <div class="p-6 cursor-pointer hover:bg-gray-50 transition-colors post-content-clickable" 
                             data-post-id="${post.id}"
                             data-image-url="${post.image_path}"
                             data-image-url-2="${post.image_path_2 || ''}"
                             data-image-url-3="${post.image_path_3 || ''}"
                             data-media-type="${post.media_type || 'image'}"
                             data-user-name="${(post.user_name||'')}"
                             data-user-genre="${(post.user_genre||'')}"
                             data-user-location="${(post.user_location||post.user_city||'')}"
                             data-user-type="${(post.user_type||'member')}"
                             data-user-avatar="${(post.user_avatar||'')}"
                             data-description="${(post.description||'')}"
                             data-created-at="${(post.created_at||'')}"
                             data-like-count="${likeCount}"
                             data-comment-count="${commentCount}"
                             data-is-liked="${(post.is_liked? 'true' : 'false')}"
                             data-is-verified="${(isVerified ? 'true' : 'false')}">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center gap-4 no-modal-trigger">
                                    ${avatarHtml}
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <a href="/profile/${userId}" class="font-bold text-gray-800 text-lg hover:text-purple-600 transition-colors no-modal-trigger">${userName}</a>
                                            ${isVerified ? `<svg class="w-5 h-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>` : ''}
                                        </div>
                                        <p class="text-gray-600">${userMeta}</p>
                                    </div>
                                </div>
                                ${isOwner ? `
                                    <button class="delete-post-btn no-modal-trigger text-red-500 hover:text-red-700 transition-colors" data-post-id="${post.id}">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                ` : ''}
                            </div>
                            <div>
                                <p class="text-gray-700 mb-4 leading-relaxed">${(post.description||'')}</p>
                                <div class="flex justify-between items-center text-gray-500 text-sm">
                                    <span>${formattedDate}</span>
                                    <div class="flex gap-4">
                                        <!-- Preview icons intentionally omitted -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;

                    div.innerHTML = inner;
                    postsGrid.appendChild(div);
                });
            } catch (e) {
                console.warn('Fallback inline fetch error:', e);
            }

            // Defensive: ensure the profile button is visible after refresh
            try {
                const profileButton = document.getElementById('profileButton');
                const profileDropdown = document.getElementById('profileDropdown');
                if (profileButton) {
                    profileButton.style.display = '';
                }
                if (profileDropdown) {
                    // ensure dropdown is hidden by default
                    if (!profileDropdown.classList.contains('hidden')) {
                        profileDropdown.classList.add('hidden');
                    }
                    profileDropdown.style.display = 'none';
                }
            } catch (err) {
                console.warn('Profile defensive restore failed:', err);
            }
        });
    </script>

    <!-- Modal functionality for feed page -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // File upload handling with size display and preview
            const fileInput = document.getElementById('image');
            const fileText = document.getElementById('fileText');
            const filesList = document.getElementById('filesList');
            const clearFilesBtn = document.getElementById('clearFilesBtn');
            const createPostForm = document.getElementById('createPostForm');
            const uploadProgress = document.getElementById('uploadProgress');
            const progressBar = document.getElementById('progressBar');
            const progressPercentage = document.getElementById('progressPercentage');
            const submitPostBtn = document.getElementById('submitPostBtn');
            const cancelPostBtn = document.getElementById('cancelPostBtn');
            const maxFileSize = 50 * 1024 * 1024; // 50 MB in bytes
            let uploadAbortController = null;
            
            // Clear files button
            if (clearFilesBtn && fileInput) {
                clearFilesBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    fileInput.value = '';
                    fileText.textContent = 'Choose up to 3 images or drag them here';
                    filesList.classList.add('hidden');
                    filesList.innerHTML = '';
                    clearFilesBtn.classList.add('hidden');
                });
            }
            
            if (fileInput) {
                fileInput.addEventListener('change', function(e) {
                    const files = Array.from(e.target.files);
                    
                    if (files.length === 0) {
                        fileText.textContent = 'Choose up to 3 images or drag them here';
                        filesList.classList.add('hidden');
                        filesList.innerHTML = '';
                        clearFilesBtn.classList.add('hidden');
                        return;
                    }
                    
                    // Show clear button
                    clearFilesBtn.classList.remove('hidden');
                    
                    // Limit to 3 files
                    const limitedFiles = files.slice(0, 3);
                    
                    // Update text
                    fileText.textContent = `${limitedFiles.length} file${limitedFiles.length > 1 ? 's' : ''} selected`;
                    
                    // Display file list with sizes and previews
                    filesList.innerHTML = '';
                    filesList.classList.remove('hidden');
                    
                    let hasOversizedFile = false;
                    
                    limitedFiles.forEach((file, index) => {
                        const fileSize = file.size;
                        const fileSizeMB = (fileSize / (1024 * 1024)).toFixed(2);
                        const isOversized = fileSize > maxFileSize;
                        
                        if (isOversized) hasOversizedFile = true;
                        
                        const fileItem = document.createElement('div');
                        fileItem.className = `flex items-center gap-3 p-3 rounded-lg ${isOversized ? 'bg-red-50 border border-red-200' : 'bg-gray-50 border border-gray-200'}`;
                        
                        const fileType = file.type.startsWith('video/') ? '🎥' : '📷';
                        const sizeClass = isOversized ? 'text-red-600 font-semibold' : 'text-gray-600';
                        const warningIcon = isOversized ? '<span class="text-red-500 ml-2">⚠️ Too large!</span>' : '';
                        
                        // Create preview thumbnail
                        const previewContainer = document.createElement('div');
                        previewContainer.className = 'flex-shrink-0 w-16 h-16 bg-gray-200 rounded-lg overflow-hidden relative';
                        
                        if (file.type.startsWith('image/')) {
                            // Image preview
                            const img = document.createElement('img');
                            img.className = 'w-full h-full object-cover';
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                img.src = e.target.result;
                            };
                            reader.readAsDataURL(file);
                            previewContainer.appendChild(img);
                        } else if (file.type.startsWith('video/')) {
                            // Video preview with play icon
                            const video = document.createElement('video');
                            video.className = 'w-full h-full object-cover';
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                video.src = e.target.result;
                                video.currentTime = 1; // Show first frame
                            };
                            reader.readAsDataURL(file);
                            
                            // Add play icon overlay
                            const playIcon = document.createElement('div');
                            playIcon.className = 'absolute inset-0 flex items-center justify-center bg-black/30';
                            playIcon.innerHTML = '<svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20"><path d="M6.3 2.841A1.5 1.5 0 004 4.11V15.89a1.5 1.5 0 002.3 1.269l9.344-5.89a1.5 1.5 0 000-2.538L6.3 2.84z"></path></svg>';
                            
                            previewContainer.appendChild(video);
                            previewContainer.appendChild(playIcon);
                        }
                        
                        // Create info section
                        const infoDiv = document.createElement('div');
                        infoDiv.className = 'flex-1 min-w-0';
                        infoDiv.innerHTML = `
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-xl">${fileType}</span>
                                <span class="text-sm text-gray-700 truncate font-medium">${file.name}</span>
                            </div>
                            <div class="flex items-center gap-1">
                                <span class="text-xs ${sizeClass}">${fileSizeMB} MB</span>
                                ${warningIcon}
                            </div>
                        `;
                        
                        fileItem.appendChild(previewContainer);
                        fileItem.appendChild(infoDiv);
                        filesList.appendChild(fileItem);
                    });
                    
                    // Show warning if any file is too large
                    if (hasOversizedFile) {
                        const warningDiv = document.createElement('div');
                        warningDiv.className = 'p-3 bg-red-100 border border-red-300 rounded-lg text-red-700 text-sm mt-2';
                        warningDiv.innerHTML = `
                            <strong>⚠️ File size error:</strong> Some files exceed the 50 MB limit. Please choose smaller files.
                        `;
                        filesList.appendChild(warningDiv);
                    }
                });
            }
            
            // Handle form submission with progress
            if (createPostForm) {
                createPostForm.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    // Prevent multiple submissions
                    if (uploadAbortController) {
                        console.log('Already uploading, ignoring submit');
                        return;
                    }
                    
                    // Create new AbortController with cancellation flag
                    uploadAbortController = {
                        controller: new AbortController(),
                        cancelled: false
                    };
                    const signal = uploadAbortController.controller.signal;
                    
                    // DON'T create FormData yet - wait until after delay
                    // This prevents the browser from starting the upload early
                    
                    // Show progress bar and cancel button
                    uploadProgress.classList.remove('hidden');
                    submitPostBtn.disabled = true;
                    submitPostBtn.classList.add('opacity-50', 'cursor-not-allowed');
                    cancelPostBtn.classList.remove('hidden');
                    
                    try {
                        // Simulate progress for 3 seconds BEFORE sending request
                        const startTime = Date.now();
                        const delayDuration = 3000; // 3 second delay
                        
                        const progressInterval = setInterval(() => {
                            // Check if uploadAbortController still exists before accessing properties
                            if (!uploadAbortController || uploadAbortController.cancelled || signal.aborted) {
                                clearInterval(progressInterval);
                                return;
                            }
                            
                            const elapsed = Date.now() - startTime;
                            const percent = Math.min((elapsed / delayDuration) * 95, 95);
                            progressBar.style.width = percent + '%';
                            progressPercentage.textContent = Math.round(percent) + '%';
                        }, 50);
                        
                        // Wait for delay period with cancellation support
                        await new Promise((resolve, reject) => {
                            const timeoutId = setTimeout(resolve, delayDuration);
                            
                            // Listen for abort signal
                            signal.addEventListener('abort', () => {
                                clearTimeout(timeoutId);
                                clearInterval(progressInterval);
                                reject(new DOMException('Upload cancelled', 'AbortError'));
                            });
                        });
                        
                        clearInterval(progressInterval);
                    
                        // Check if cancelled during delay using BOTH flag and signal
                        if (!uploadAbortController || uploadAbortController.cancelled || signal.aborted) {
                            console.log('Upload cancelled before sending request');
                            if (uploadProgress) uploadProgress.classList.add('hidden');
                            if (progressBar) progressBar.style.width = '0%';
                            if (progressPercentage) progressPercentage.textContent = '0%';
                            if (submitPostBtn) {
                                submitPostBtn.disabled = false;
                                submitPostBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                            }
                            if (cancelPostBtn) cancelPostBtn.classList.add('hidden');
                            uploadAbortController = null;
                            return;
                        }
                        
                        // NOW create FormData only after delay completed successfully
                        const formData = new FormData(this);
                        // Now send the actual request
                        progressBar.style.width = '100%';
                        progressPercentage.textContent = '100%';
                        
                        console.log('SENDING REQUEST TO SERVER');
                        const response = await fetch('{{ route("posts.store") }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json'
                            },
                            body: formData,
                            signal: signal
                        });
                        
                        const data = await response.json();
                        
                        if (data.success) {
                            setTimeout(() => {
                                createPostForm.reset();
                                fileInput.value = '';
                                fileText.textContent = 'Choose up to 3 images or drag them here';
                                filesList.classList.add('hidden');
                                filesList.innerHTML = '';
                                clearFilesBtn.classList.add('hidden');
                                
                                // Reset upload UI inline
                                if (uploadProgress) uploadProgress.classList.add('hidden');
                                if (progressBar) progressBar.style.width = '0%';
                                if (progressPercentage) progressPercentage.textContent = '0%';
                                if (submitPostBtn) {
                                    submitPostBtn.disabled = false;
                                    submitPostBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                                }
                                if (cancelPostBtn) cancelPostBtn.classList.add('hidden');
                                
                                uploadAbortController = null;
                                
                                if (window.showNotificationToast) {
                                    window.showNotificationToast('Post created successfully! 🎉', 'info');
                                }
                                
                                window.location.reload();
                            }, 500);
                        } else {
                            throw new Error(data.message || 'Upload failed');
                        }
                    } catch (error) {
                        if (error.name === 'AbortError') {
                            console.log('Fetch aborted');
                            // Reset UI inline
                            if (uploadProgress) uploadProgress.classList.add('hidden');
                            if (progressBar) progressBar.style.width = '0%';
                            if (progressPercentage) progressPercentage.textContent = '0%';
                            if (submitPostBtn) {
                                submitPostBtn.disabled = false;
                                submitPostBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                            }
                            if (cancelPostBtn) cancelPostBtn.classList.add('hidden');
                            uploadAbortController = null;
                            return;
                        }
                        console.error('Upload error:', error);
                        alert('Upload failed: ' + error.message);
                        // Reset UI inline
                        if (uploadProgress) uploadProgress.classList.add('hidden');
                        if (progressBar) progressBar.style.width = '0%';
                        if (progressPercentage) progressPercentage.textContent = '0%';
                        if (submitPostBtn) {
                            submitPostBtn.disabled = false;
                            submitPostBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                        }
                        if (cancelPostBtn) cancelPostBtn.classList.add('hidden');
                        uploadAbortController = null;
                    }
                });
            }
            
            // Cancel upload button
            if (cancelPostBtn) {
                cancelPostBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    if (!uploadAbortController) {
                        console.log('No upload to cancel');
                        return;
                    }
                    
                    console.log('Cancel button clicked - aborting upload');
                    
                    // Set cancellation flag FIRST
                    uploadAbortController.cancelled = true;
                    
                    // Then abort the controller
                    uploadAbortController.controller.abort();
                    
                    // Reset UI without using resetUploadUI to avoid race condition
                    if (uploadProgress) uploadProgress.classList.add('hidden');
                    if (progressBar) progressBar.style.width = '0%';
                    if (progressPercentage) progressPercentage.textContent = '0%';
                    if (submitPostBtn) {
                        submitPostBtn.disabled = false;
                        submitPostBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                    }
                    if (cancelPostBtn) cancelPostBtn.classList.add('hidden');
                    
                    // Set to null AFTER resetting UI
                    uploadAbortController = null;
                    
                    // Show toast only once here
                    if (window.showNotificationToast) {
                        window.showNotificationToast('Upload cancelled', 'info');
                    }
                });
            }
            
            // Reset upload UI helper function
            function resetUploadUI() {
                try {
                    if (uploadProgress) uploadProgress.classList.add('hidden');
                    if (progressBar) progressBar.style.width = '0%';
                    if (progressPercentage) progressPercentage.textContent = '0%';
                    if (submitPostBtn) {
                        submitPostBtn.disabled = false;
                        submitPostBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                    }
                    if (cancelPostBtn) cancelPostBtn.classList.add('hidden');
                    // Don't set uploadAbortController to null here - let the caller do it
                } catch (error) {
                    console.error('Error in resetUploadUI:', error);
                }
            }
            
            // Delegate clicks on images and post content to open modal
            document.addEventListener('click', function(e) {
                // Don't open modal if clicking delete button
                if (e.target.closest('.delete-post-btn')) {
                    return;
                }
                
                // Check if clicked on post image
                if (e.target.closest('.post-image')) {
                    e.preventDefault();
                    const img = e.target.closest('.post-image');
                    const postData = extractPostDataFromElement(img);
                    showImageModal(postData);
                    return;
                }
                
                // Check if clicked on post content area (entire section below media)
                const contentEl = e.target.closest('.post-content-clickable');
                if (contentEl) {
                    e.preventDefault();
                    const postData = extractPostDataFromElement(contentEl);
                    showImageModal(postData);
                }
            });

            // Extract post data from element
            function extractPostDataFromElement(element) {
                if (!element) return null;
                
                console.log('Extracting from element:', element);
                console.log('data-image-url:', element.getAttribute('data-image-url'));
                console.log('data-image-url-2:', element.getAttribute('data-image-url-2'));
                console.log('data-image-url-3:', element.getAttribute('data-image-url-3'));
                
                return {
                    id: element.getAttribute('data-post-id'),
                    imageUrl: element.getAttribute('data-image-url'),
                    imageUrl2: element.getAttribute('data-image-url-2'),
                    imageUrl3: element.getAttribute('data-image-url-3'),
                    mediaType: element.getAttribute('data-media-type') || 'image',
                    userName: element.getAttribute('data-user-name'),
                    userId: element.getAttribute('data-user-id'),
                    userGenre: element.getAttribute('data-user-genre'),
                    userType: element.getAttribute('data-user-type'),
                    userAvatar: element.getAttribute('data-user-avatar'),
                    userLocation: element.getAttribute('data-user-location'),
                    description: element.getAttribute('data-description'),
                    createdAt: element.getAttribute('data-created-at'),
                    like_count: parseInt(element.getAttribute('data-like-count')) || 0,
                    comment_count: parseInt(element.getAttribute('data-comment-count')) || 0,
                    is_liked: element.getAttribute('data-is-liked') === 'true',
                    is_verified: element.getAttribute('data-is-verified') === 'true'
                };
            }

            // Show image modal with carousel support
            function showImageModal(postData) {
                if (!postData) return;
                
                console.log('=== MODAL DEBUG START ===');
                console.log('Full Post Data:', postData);
                console.log('imageUrl:', postData.imageUrl);
                console.log('imageUrl2:', postData.imageUrl2);
                console.log('imageUrl3:', postData.imageUrl3);
                
                // Collect all images/videos - filter out empty strings and null values
                const images = [postData.imageUrl, postData.imageUrl2, postData.imageUrl3]
                    .filter(url => url && url !== '' && url !== 'null' && url !== 'undefined');
                
                console.log('Filtered images array:', images);
                console.log('Images array length:', images.length);
                console.log('Will show navigation buttons:', images.length > 1);
                console.log('=== MODAL DEBUG END ===');
                
                let currentImageIndex = 0;
                
                // Create modal overlay
                const overlay = document.createElement('div');
                overlay.className = 'fixed inset-0 bg-black/90 backdrop-blur-sm z-50 flex items-center justify-center';
                overlay.style.opacity = '0';
                overlay.style.transition = 'opacity 0.3s ease-out';
                
                // Create modal content
                const modal = document.createElement('div');
                modal.className = 'bg-white rounded-2xl shadow-2xl max-w-6xl w-full mx-4 max-h-[90vh] overflow-y-auto lg:overflow-hidden transform scale-95 transition-transform duration-300';
                
                const userTypeEmoji = postData.userType === 'musician' ? '🎵' : 
                                     postData.userType === 'business' ? '🏢' : '👤';
                
                const avatarElement = postData.userAvatar ? 
                    `<img class="w-16 h-16 rounded-full object-cover border-2 border-gray-200" src="${postData.userAvatar}" alt="avatar">` :
                    `<div class="w-16 h-16 bg-gradient-to-r from-purple-400 to-pink-400 rounded-full flex items-center justify-center text-white font-bold text-xl">${postData.userName ? postData.userName.charAt(0).toUpperCase() : 'U'}</div>`;
                
                function renderModal() {
                    // Check if current media is a video by URL pattern
                    const currentUrl = images[currentImageIndex];
                    const isVideo = currentUrl && (
                        currentUrl.includes('.mp4') || 
                        currentUrl.includes('.mov') || 
                        currentUrl.includes('.avi') || 
                        currentUrl.includes('.wmv') ||
                        currentUrl.includes('/video/upload/') ||  // Cloudinary video URL
                        postData.mediaType === 'video'
                    );
                    
                    console.log('Current URL:', currentUrl);
                    console.log('Is Video:', isVideo);
                    
                    const mediaHtml = isVideo ? `
                        <video controls class="max-w-full max-h-full" style="width: 100%; height: 100%; object-fit: contain;">
                            <source src="${currentUrl}" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                    ` : `
                        <img src="${currentUrl}" 
                             alt="Post image" 
                             class="max-w-full max-h-full object-contain">
                    `;

                    // Add navigation buttons if multiple images
                    const navigationHtml = images.length > 1 ? `
                        <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex items-center gap-4 bg-black/50 px-4 py-2 rounded-full">
                            <button id="prevImage" class="text-white hover:text-gray-300 disabled:opacity-50 disabled:cursor-not-allowed" ${currentImageIndex === 0 ? 'disabled' : ''}>
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                </svg>
                            </button>
                            <span class="text-white font-medium">${currentImageIndex + 1} / ${images.length}</span>
                            <button id="nextImage" class="text-white hover:text-gray-300 disabled:opacity-50 disabled:cursor-not-allowed" ${currentImageIndex === images.length - 1 ? 'disabled' : ''}>
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </button>
                        </div>
                    ` : '';
                    
                    modal.innerHTML = `
                        <div class="flex flex-col lg:flex-row lg:h-full lg:max-h-[90vh]">
                            <!-- Media Section with Navigation -->
                            <div class="flex-1 bg-black flex items-center justify-center relative min-h-[40vh] lg:min-h-0">
                                ${mediaHtml}
                                ${navigationHtml}
                            </div>
                            
                            <!-- Details Section -->
                            <div class="w-full lg:w-96 bg-white flex flex-col lg:max-h-full">
                                <!-- Header -->
                                <div class="p-6 border-b border-gray-200">
                                    <div class="flex items-center gap-4 mb-4">
                                        ${avatarElement}
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <a href="/profile/${postData.userId}" class="font-bold text-gray-800 text-xl hover:text-purple-600 transition-colors">${postData.userName}</a>
                                                ${postData.is_verified ? `<svg class="w-5 h-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>` : ''}
                                            </div>
                                            <p class="text-gray-600">${[postData.userGenre, postData.userLocation].filter(Boolean).join(' · ')}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2 text-sm text-gray-500">
                                        <span>${userTypeEmoji} ${postData.userType}</span>
                                        <span>•</span>
                                        <span>${new Date(postData.createdAt).toLocaleDateString()}</span>
                                    </div>
                                </div>
                                
                                <!-- Description -->
                                <div class="flex-1 p-6 overflow-y-auto">
                                    ${postData.description ? `
                                        <div class="mb-6">
                                            <p class="text-gray-700 leading-relaxed">${postData.description}</p>
                                        </div>
                                    ` : ''}
                                    
                                    <!-- Comments Section -->
                                    <div class="space-y-4">
                                        <h4 class="font-semibold text-gray-800">Comments</h4>
                                        <div class="space-y-3">
                                            <div class="text-center py-8 text-gray-500">
                                                <svg class="w-12 h-12 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                                </svg>
                                                <p>No comments yet</p>
                                                <p class="text-sm">Be the first to comment!</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Actions -->
                                <div class="p-6 border-t border-gray-200">
                                    <div class="flex items-center gap-6 mb-4">
                                        <button class="like-btn flex items-center gap-2 transition-colors" 
                                                data-post-id="${postData.id}"
                                                data-liked="${postData.is_liked || false}">
                                            <svg class="w-6 h-6 ${postData.is_liked ? 'fill-red-500 text-red-500' : 'fill-none text-gray-600 hover:text-red-500'}" 
                                                 stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                            </svg>
                                            <span class="font-medium like-count">${postData.like_count || 0}</span>
                                        </button>
                                        <button class="comment-btn flex items-center gap-2 text-gray-600 hover:text-blue-500 transition-colors">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                            </svg>
                                            <span class="font-medium comment-count">${postData.comment_count || 0}</span>
                                        </button>
                                        <button class="share-btn flex items-center gap-2 text-gray-600 hover:text-green-500 transition-colors">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
                                            </svg>
                                            <span class="font-medium">Share</span>
                                        </button>
                                    </div>
                                    
                                    <!-- Comment Input -->
                                    <div class="flex gap-3">
                                        <input type="text" 
                                               placeholder="Add a comment..." 
                                               class="comment-input flex-1 px-4 py-2 border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <button class="comment-submit-btn px-6 py-2 bg-blue-600 text-white rounded-full hover:bg-blue-700 transition-colors">
                                            Post
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Close Button -->
                        <button class="close-modal absolute top-4 right-4 bg-black/50 hover:bg-black/70 text-white p-2 rounded-full transition-colors z-10">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    `;

                    // Re-attach carousel event listeners after rendering
                    if (images.length > 1) {
                        const prevBtn = modal.querySelector('#prevImage');
                        const nextBtn = modal.querySelector('#nextImage');

                        if (prevBtn) {
                            prevBtn.addEventListener('click', (e) => {
                                e.preventDefault();
                                if (currentImageIndex > 0) {
                                    currentImageIndex--;
                                    renderModal();
                                }
                            });
                        }

                        if (nextBtn) {
                            nextBtn.addEventListener('click', (e) => {
                                e.preventDefault();
                                if (currentImageIndex < images.length - 1) {
                                    currentImageIndex++;
                                    renderModal();
                                }
                            });
                        }
                    }
                }
                
                renderModal();
                overlay.appendChild(modal);
                document.body.appendChild(overlay);
                
                // Prevent body scroll
                document.body.style.overflow = 'hidden';
                
                // Animate in
                setTimeout(() => {
                    overlay.style.opacity = '1';
                    modal.style.transform = 'scale(1)';
                }, 10);

                // Add carousel navigation event listeners
                if (images.length > 1) {
                    const prevBtn = modal.querySelector('#prevImage');
                    const nextBtn = modal.querySelector('#nextImage');

                    if (prevBtn) {
                        prevBtn.addEventListener('click', () => {
                            if (currentImageIndex > 0) {
                                currentImageIndex--;
                                renderModal();
                            }
                        });
                    }

                    if (nextBtn) {
                        nextBtn.addEventListener('click', () => {
                            if (currentImageIndex < images.length - 1) {
                                currentImageIndex++;
                                renderModal();
                            }
                        });
                    }
                }
                
                // Handle close button
                const closeBtn = modal.querySelector('.close-modal');
                const closeModal = () => {
                    overlay.style.opacity = '0';
                    modal.style.transform = 'scale(0.95)';
                    document.body.style.overflow = '';
                    setTimeout(() => {
                        if (document.body.contains(overlay)) {
                            document.body.removeChild(overlay);
                        }
                    }, 300);
                };
                
                closeBtn.addEventListener('click', closeModal);
                
                // Close on overlay click
                overlay.addEventListener('click', (e) => {
                    if (e.target === overlay) {
                        closeModal();
                    }
                });
                
                // Close on Escape key
                const handleEscape = (e) => {
                    if (e.key === 'Escape') {
                        closeModal();
                        document.removeEventListener('keydown', handleEscape);
                    }
                };
                document.addEventListener('keydown', handleEscape);

                // Add like functionality
                const likeBtn = modal.querySelector('.like-btn');
                if (likeBtn) {
                    likeBtn.addEventListener('click', (e) => {
                        e.preventDefault();
                        toggleLike(likeBtn, postData.id);
                    });
                }

                // Add comment functionality
                const commentInput = modal.querySelector('.comment-input');
                const commentSubmitBtn = modal.querySelector('.comment-submit-btn');
                if (commentInput && commentSubmitBtn) {
                    commentSubmitBtn.addEventListener('click', (e) => {
                        e.preventDefault();
                        const content = commentInput.value.trim();
                        if (content) {
                            addComment(postData.id, content, commentInput, modal);
                        }
                    });
                    
                    commentInput.addEventListener('keypress', (e) => {
                        if (e.key === 'Enter') {
                            const content = commentInput.value.trim();
                            if (content) {
                                addComment(postData.id, content, commentInput, modal);
                            }
                        }
                    });
                }

                // Load comments
                loadComments(postData.id, modal);
            }

            // Toggle like function
            async function toggleLike(likeBtn, postId) {
                if (postId.startsWith('sample-')) {
                    alert('Like functionality is only available for real posts.');
                    return;
                }
                
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                
                try {
                    const response = await fetch(`/posts/${postId}/like`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        }
                    });

                    const data = await response.json();

                    if (data.success) {
                        const svg = likeBtn.querySelector('svg');
                        const likeCount = likeBtn.querySelector('.like-count');
                        
                        if (data.liked) {
                            svg.setAttribute('class', 'w-6 h-6 fill-red-500 text-red-500');
                            likeBtn.setAttribute('data-liked', 'true');
                        } else {
                            svg.setAttribute('class', 'w-6 h-6 fill-none text-gray-600 hover:text-red-500');
                            likeBtn.setAttribute('data-liked', 'false');
                        }
                        
                        likeCount.textContent = data.like_count;
                        
                        // Update the post element's data attributes
                        const postElement = document.querySelector(`[data-post-id="${postId}"]`);
                        if (postElement) {
                            postElement.setAttribute('data-like-count', data.like_count);
                            postElement.setAttribute('data-is-liked', data.liked ? 'true' : 'false');
                        }
                        
                        // Emit socket event for real-time notification
                        if (window.socketManager && data.post_owner_id) {
                            window.socketManager.emitPostLike(postId, data.like_count, data.liked, data.post_owner_id);
                        }
                    }
                } catch (error) {
                    console.error('Error toggling like:', error);
                }
            }

            // Add comment function
            async function addComment(postId, content, commentInput, modal) {
                if (postId.startsWith('sample-')) {
                    alert('Comment functionality is only available for real posts.');
                    return;
                }
                
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                
                try {
                    const response = await fetch(`/posts/${postId}/comments`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ content: content })
                    });

                    const data = await response.json();

                    if (data.success) {
                        commentInput.value = '';
                        addCommentToModal(data.comment, modal);
                        
                        const commentCount = modal.querySelector('.comment-count');
                        if (commentCount) {
                            const newCount = parseInt(commentCount.textContent) + 1;
                            commentCount.textContent = newCount;
                            
                            // Update the post element's data attribute
                            const postElement = document.querySelector(`[data-post-id="${postId}"]`);
                            if (postElement) {
                                postElement.setAttribute('data-comment-count', newCount);
                            }
                        }
                        
                        // Emit socket event for real-time notification
                        if (window.socketManager && data.post_owner_id) {
                            window.socketManager.emitNewComment({
                                postId: postId,
                                content: data.comment.content,
                                postOwnerId: data.post_owner_id
                            });
                        }
                    }
                } catch (error) {
                    console.error('Error adding comment:', error);
                }
            }

            // Load comments function
            async function loadComments(postId, modal) {
                if (postId.startsWith('sample-')) {
                    return;
                }
                
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                
                try {
                    const response = await fetch(`/posts/${postId}/comments`, {
                        method: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        }
                    });

                    const data = await response.json();

                    if (data.success && data.comments.length > 0) {
                        const commentsContainer = modal.querySelector('.space-y-3');
                        if (commentsContainer) {
                            commentsContainer.innerHTML = '';
                            data.comments.forEach(comment => {
                                addCommentToModal(comment, modal);
                            });
                        }
                    }
                } catch (error) {
                    console.error('Error loading comments:', error);
                }
            }

            // Add comment to modal function
            // Add comment to modal function
            function addCommentToModal(comment, modal) {
                const commentsContainer = modal.querySelector('.space-y-3');
                if (!commentsContainer) return;

                // Remove "No comments yet" message if it exists
                const noCommentsMsg = commentsContainer.querySelector('.text-center.py-8');
                if (noCommentsMsg) {
                    noCommentsMsg.remove();
                }

                const commentElement = document.createElement('div');
                commentElement.className = 'flex gap-3 p-3 bg-gray-50 rounded-lg';
                const userName = comment.user_name || 'Unknown User';
                const userInitial = userName.charAt(0).toUpperCase();
                
                let avatarHtml = comment.user_avatar ? 
                    `<img src="${comment.user_avatar}" alt="${userName}" class="w-8 h-8 rounded-full object-cover">` :
                    `<div class="w-8 h-8 bg-gradient-to-r from-purple-400 to-pink-400 rounded-full flex items-center justify-center text-white font-bold text-sm">${userInitial}</div>`;
                
                commentElement.innerHTML = `
                    <div class="w-8 h-8 flex-shrink-0">
                        ${avatarHtml}
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="font-semibold text-sm text-gray-800">${userName}</span>
                            <span class="text-xs text-gray-500">${new Date(comment.created_at).toLocaleDateString()}</span>
                        </div>
                        <p class="text-sm text-gray-700">${comment.content}</p>
                    </div>
                `;
                
                commentsContainer.appendChild(commentElement);
            }

            // Notifications functionality
            const notificationsBtn = document.getElementById('notificationsBtn');
            const notificationsModal = document.getElementById('notificationsModal');
            const closeNotificationsModal = document.getElementById('closeNotificationsModal');
            const notificationBadge = document.getElementById('notificationBadge');
            const profileDropdown = document.getElementById('profileDropdown');
            
            if (notificationsBtn && notificationsModal) {
                notificationsBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    notificationsModal.classList.remove('hidden');
                    notificationsModal.classList.add('flex');
                    profileDropdown.classList.add('hidden');
                    loadNotifications();
                });
                
                closeNotificationsModal.addEventListener('click', function() {
                    notificationsModal.classList.add('hidden');
                    notificationsModal.classList.remove('flex');
                });
                
                notificationsModal.addEventListener('click', function(e) {
                    if (e.target === notificationsModal) {
                        notificationsModal.classList.add('hidden');
                        notificationsModal.classList.remove('flex');
                    }
                });
            }
            
            // Load notifications
            async function loadNotifications() {
                const content = document.getElementById('notificationsContent');
                content.innerHTML = '<div class="text-center py-8"><div class="animate-spin rounded-full h-12 w-12 border-b-2 border-purple-500 mx-auto"></div></div>';
                
                try {
                    const response = await fetch('/api/notifications', {
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    });
                    
                    const data = await response.json();
                    
                    if (data.notifications && data.notifications.length > 0) {
                        const notificationsHtml = data.notifications.map(notif => {
                            const timeAgo = getTimeAgo(notif.created_at);
                            const icon = notif.type === 'like' ? '❤️' : '💬';
                            const bgColor = notif.read ? 'bg-white' : 'bg-blue-50';
                            
                            return `
                                <div class="${bgColor} p-4 rounded-xl hover:shadow-md transition-all mb-3 border border-gray-100 cursor-pointer" onclick="openNotificationPost(${notif.post_id}, ${notif.id})">
                                    <div class="flex items-start gap-3">
                                        <span class="text-2xl">${icon}</span>
                                        <div class="flex-1">
                                            <p class="text-gray-800 font-medium">${notif.message}</p>
                                            <p class="text-gray-500 text-sm mt-1">${timeAgo}</p>
                                        </div>
                                    </div>
                                </div>
                            `;
                        }).join('');
                        
                        content.innerHTML = notificationsHtml;
                        
                        // Update all badges
                        updateNotificationBadges(data.unread_count || 0);
                    } else {
                        content.innerHTML = `
                            <div class="text-center py-8 text-gray-500">
                                <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                </svg>
                                <p class="text-lg font-medium">No notifications yet</p>
                                <p class="text-sm">When someone likes or comments on your posts, you'll see it here</p>
                            </div>
                        `;
                        updateNotificationBadges(0);
                    }
                } catch (error) {
                    console.error('Error loading notifications:', error);
                    content.innerHTML = `
                        <div class="text-center py-8 text-red-500">
                            <p>Failed to load notifications</p>
                            <button onclick="loadNotifications()" class="mt-4 px-4 py-2 bg-purple-500 text-white rounded-full hover:bg-purple-600">
                                Retry
                            </button>
                        </div>
                    `;
                }
            }
            
            function updateNotificationBadges(count) {
                const notificationBadge = document.getElementById('notificationBadge');
                const profileNotificationBadge = document.getElementById('profileNotificationBadge');
                
                if (count > 0) {
                    if (notificationBadge) {
                        notificationBadge.textContent = count;
                        notificationBadge.classList.remove('hidden');
                    }
                    if (profileNotificationBadge) {
                        profileNotificationBadge.textContent = count;
                        profileNotificationBadge.classList.remove('hidden');
                    }
                } else {
                    if (notificationBadge) notificationBadge.classList.add('hidden');
                    if (profileNotificationBadge) profileNotificationBadge.classList.add('hidden');
                }
            }
            
            function getTimeAgo(dateString) {
                const date = new Date(dateString);
                const now = new Date();
                const seconds = Math.floor((now - date) / 1000);
                
                if (seconds < 60) return 'Just now';
                if (seconds < 3600) return `${Math.floor(seconds / 60)}m ago`;
                if (seconds < 86400) return `${Math.floor(seconds / 3600)}h ago`;
                if (seconds < 604800) return `${Math.floor(seconds / 86400)}d ago`;
                return date.toLocaleDateString();
            }
            
            // Open post modal from notification
            window.openNotificationPost = async function(postId, notificationId) {
                try {
                    // Mark notification as read
                    await fetch(`/api/notifications/${notificationId}/read`, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    });
                    
                    // Close notifications modal
                    notificationsModal.classList.add('hidden');
                    notificationsModal.classList.remove('flex');
                    
                    // Fetch post data and open in modal
                    const postElement = document.querySelector(`[data-post-id="${postId}"]`);
                    if (postElement) {
                        const postData = extractPostDataFromElement(postElement);
                        showImageModal(postData);
                    } else {
                        // If post not in DOM, fetch from API
                        const response = await fetch(`/api/posts?post_id=${postId}`, {
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        });
                        const data = await response.json();
                        if (data.success && data.posts && data.posts.length > 0) {
                            const post = data.posts[0];
                            showImageModal({
                                id: post.id,
                                imageUrl: post.image_path,
                                imageUrl2: post.image_path_2,
                                imageUrl3: post.image_path_3,
                                mediaType: post.image_path?.includes('/video/upload/') ? 'video' : 'image',
                                userName: post.user_name,
                                userId: post.user_id,
                                userGenre: post.user_genre || '',
                                userType: post.user_type || 'musician',
                                userAvatar: post.user_avatar,
                                userLocation: post.user_location || '',
                                description: post.description,
                                createdAt: post.created_at,
                                like_count: post.like_count || 0,
                                comment_count: post.comment_count || 0,
                                is_liked: post.is_liked || false
                            });
                        }
                    }
                    
                    // Update badge count
                    setTimeout(() => loadNotifications(), 100);
                } catch (error) {
                    console.error('Error opening notification post:', error);
                }
            }
            
            // Load notification count on page load
            loadNotifications();
            
            // Set up periodic refresh as fallback (every 30 seconds)
            setInterval(() => {
                loadNotifications();
            }, 30000);
        });
        
        // Global function for socket.js to update notification count
        window.updateNotificationCount = async function() {
            try {
                const response = await fetch('/api/notifications', {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                const data = await response.json();
                
                if (data.unread_count !== undefined) {
                    updateNotificationBadges(data.unread_count);
                }
            } catch (error) {
                console.error('Error updating notification count:', error);
            }
        };
        
        // Global function to show toast notifications
        window.showNotificationToast = function(message, type = 'info') {
            const container = document.getElementById('toastContainer');
            if (!container) return;
            
            const toast = document.createElement('div');
            toast.className = `transform transition-all duration-300 translate-x-full`;
            
            const bgColor = type === 'like' ? 'bg-gradient-to-r from-red-500 to-pink-500' : 
                           type === 'comment' ? 'bg-gradient-to-r from-blue-500 to-purple-500' : 
                           'bg-gradient-to-r from-gray-700 to-gray-800';
            
            const icon = type === 'like' ? '❤️' : type === 'comment' ? '💬' : '🔔';
            
            toast.innerHTML = `
                <div class="${bgColor} text-white px-4 py-3 rounded-xl shadow-2xl flex items-center gap-3 min-w-[300px]">
                    <span class="text-2xl">${icon}</span>
                    <p class="flex-1 text-sm font-medium">${message}</p>
                    <button onclick="this.closest('div').parentElement.remove()" class="text-white/80 hover:text-white">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            `;
            
            container.appendChild(toast);
            
            // Slide in with safety check
            setTimeout(() => {
                if (toast && toast.classList) {
                    toast.classList.remove('translate-x-full');
                }
            }, 10);
            
            // Auto remove after 5 seconds with safety checks
            setTimeout(() => {
                if (toast && toast.classList) {
                    toast.classList.add('translate-x-full');
                }
                setTimeout(() => {
                    if (toast && container && container.contains(toast)) {
                        container.removeChild(toast);
                    }
                }, 300);
            }, 5000);
        };
    </script>

    @vite(['resources/js/app.js', 'resources/js/feed.js', 'resources/js/socket.js'])
</body> 
</html>