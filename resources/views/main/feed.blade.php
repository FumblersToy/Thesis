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
    
    
    
    <div class="flex min-h-screen relative z-10">
        <aside id="sidebar" class="w-80 p-6 glass-effect backdrop-blur-xl shadow-2xl hidden lg:block animate-slide-up gradient-bg overflow-y-auto">
            <div class="sticky top-6">
                <h2 class="font-bold text-2xl mb-6 text-white flex items-center gap-3">
                    <div class="w-8 h-8 bg-gradient-to-r from-purple-400 to-pink-400 rounded-full animate-pulse-slow"></div>
                    Filters
                </h2>
                
                <form id="filters" class="space-y-8">
                    <div class="animate-fade-in">
                        <h3 class="font-semibold text-white/90 mb-4 text-lg">🎵 Instruments</h3>
                        <div class="space-y-3" id="instruments">
                            <label class="flex items-center gap-3 text-white">
                                <input type="checkbox" class="form-checkbox"> Guitar
                            </label>
                            <label class="flex items-center gap-3 text-white">
                                <input type="checkbox" class="form-checkbox"> Drums
                            </label>
                            <label class="flex items-center gap-3 text-white">
                                <input type="checkbox" class="form-checkbox"> Piano
                            </label>
                        </div>
                    </div>

                    <div class="animate-fade-in" style="animation-delay: 0.1s">
                        <h3 class="font-semibold text-white/90 mb-4 text-lg">🏢 Venues</h3>
                        <div class="space-y-3" id="venues">
                            <label class="flex items-center gap-3 text-white">
                                <input type="checkbox" class="form-checkbox"> Studio
                            </label>
                            <label class="flex items-center gap-3 text-white">
                                <input type="checkbox" class="form-checkbox"> Club
                            </label>
                            <label class="flex items-center gap-3 text-white">
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
                                <label class="block text-white/80 text-sm">Sort by:</label>
                                <select id="sortBy" class="w-full px-3 py-2 bg-white/20 text-white rounded-xl border border-white/30 focus:outline-none focus:ring-2 focus:ring-white/30">
                                    <option value="recent" class="text-black">Most Recent</option>
                                    <option value="distance" class="text-black">Nearest First</option>
                                </select>
                            </div>
                            <div id="distanceFilter" class="space-y-2 hidden">
                                <label class="block text-white/80 text-sm">Within:</label>
                                <select id="maxDistance" class="w-full px-3 py-2 bg-white/20 text-white rounded-xl border border-white/30 focus:outline-none focus:ring-2 focus:ring-white/30">
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
        <section class="flex-1 p-6 lg:p-8 flex flex-col">
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
                        <img class="w-12 h-12 rounded-full object-cover border-2 border-gray-200"
                             src="{{ $profileImage }}"
                             alt="profile">
                        
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
                                    <p class="text-gray-500 text-sm">{{ $user->email ?? '' }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="p-2">
                            <a href="{{ route('profile.show', $user->id) }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-100 transition-colors text-black hover:text-gray-900">
                                <span class="text-lg">👤</span>
                                View Profile
                            </a>

                            <a href="{{ route('settings.show') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-100 transition-colors text-gray-700 hover:text-gray-900">
                                <span class="text-lg">⚙️</span>
                                Settings
                            </a>
                            
                            <a href="{{ route('messages.index') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-100 transition-colors text-gray-700 hover:text-gray-900">
                                <span class="text-lg">💬</span>
                                Messages
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

            <!-- Create Post (static form) -->
            <div class="bg-white/80 backdrop-blur-xl p-8 rounded-3xl shadow-lg mb-8 animate-scale-in hover:shadow-xl transition-all duration-300 border border-gray-200">
                <h2 class="text-2xl font-bold mb-6 text-gray-800 flex items-center gap-3">
                    Create a Post
                </h2>
                <form id="createPostForm" class="space-y-6" enctype="multipart/form-data" action="{{ route('posts.store') }}" method="POST">
                    @csrf
                    <label for="image" class="block text-gray-700 font-medium mb-3 text-lg">📷 Upload Image or Video (Optional)</label>
                    <div class="custom-file-input">
                        <input type="file" name="image" id="image" accept="image/*,video/*">
                        <label for="image" class="custom-file-label cursor-pointer">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                            <span id="fileText">Choose an image/video or drag it here</span>
                        </label>
                        
                        <span id="fileName" class="text-sm text-gray-600 hidden"></span>
                    </div>
                    <label class="block text-gray-700 font-medium mb-3 text-lg" for="description">Description</label>
                    <textarea name="description" id="description" class="w-full border-2 border-gray-400 rounded p-3 resize-none overflow-hidden" placeholder="Write your description here..." rows="4"></textarea>
                    <button class="px-6 py-2 button-bg rounded text-white font-bold" type="submit">Post</button>
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
                                        data-is-liked="${(post.is_liked? 'true' : 'false')}">
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
                                        data-user-name="${(post.user_name||'') }"
                                        data-user-genre="${(post.user_genre||'') }"
                                        data-user-location="${(post.user_location||post.user_city||'') }"
                                        data-user-type="${(post.user_type||'member') }"
                                        data-user-avatar="${(post.user_avatar||'') }"
                                        data-description="${(post.description||'') }"
                                        data-created-at="${(post.created_at||'') }"
                                        data-like-count="${likeCount}"
                                        data-comment-count="${commentCount}"
                                        data-is-liked="${(post.is_liked? 'true' : 'false')}"/>
                                `}
                                <div class="absolute top-4 right-4 bg-black/50 backdrop-blur-sm text-white px-3 py-1 rounded-full text-sm">
                                    ${userTypeEmoji} ${userType}
                                </div>
                            </div>
                        `;
                    }

                    inner += `
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center gap-4">
                                    ${avatarHtml}
                                    <div>
                                        <h3 class="font-bold text-gray-800 text-lg">${userName}</h3>
                                        <p class="text-gray-600">${userMeta}</p>
                                    </div>
                                </div>
                                ${isOwner ? `
                                    <button class="delete-post-btn text-red-500 hover:text-red-700 transition-colors" data-post-id="${post.id}">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                ` : ''}
                            </div>
                            <p class="text-gray-700 mb-4 leading-relaxed ${isVideo ? 'cursor-pointer hover:text-gray-900 post-description' : ''}" 
                               ${isVideo ? `data-post-id="${post.id}"
                               data-image-url="${post.image_path}"
                               data-media-type="video"
                               data-user-name="${(post.user_name||'')}"
                               data-user-genre="${(post.user_genre||'')}"
                               data-user-location="${(post.user_location||post.user_city||'')}"
                               data-user-type="${(post.user_type||'member')}"
                               data-user-avatar="${(post.user_avatar||'')}"
                               data-description="${(post.description||'')}"
                               data-created-at="${(post.created_at||'')}"
                               data-like-count="${likeCount}"
                               data-comment-count="${commentCount}"
                               data-is-liked="${(post.is_liked? 'true' : 'false')}"` : ''}>
                                ${(post.description||'')}
                            </p>
                            <div class="flex justify-between items-center text-gray-500 text-sm">
                                <span>${formattedDate}</span>
                                <div class="flex gap-4">
                                    <!-- Preview icons intentionally omitted -->
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
    <!-- Inline modal implementation for feed page (kept here to match profile.blade.php behavior) -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Delegate clicks on images to open modal (mirrors profile modal behavior)
            document.addEventListener('click', function(e) {
                const imgEl = e.target.closest('.post-image');
                if (imgEl) {
                    e.preventDefault();
                    const postData = extractPostDataFromImage(imgEl);
                    showImageModal(postData);
                    return;
                }
                
                // Handle clicks on video post descriptions
                const descEl = e.target.closest('.post-description');
                if (descEl && descEl.getAttribute('data-media-type') === 'video') {
                    e.preventDefault();
                    const postData = extractPostDataFromImage(descEl);
                    showImageModal(postData);
                }
            });

            function extractPostDataFromImage(img) {
                if (!img) return null;

                return {
                    id: img.getAttribute('data-post-id'),
                    imageUrl: img.getAttribute('data-image-url'),
                    mediaType: img.getAttribute('data-media-type') || 'image',
                    userName: img.getAttribute('data-user-name'),
                    userGenre: img.getAttribute('data-user-genre'),
                    userType: img.getAttribute('data-user-type'),
                    userAvatar: img.getAttribute('data-user-avatar'),
                    userLocation: img.getAttribute('data-user-location'),
                    description: img.getAttribute('data-description'),
                    createdAt: img.getAttribute('data-created-at'),
                    like_count: parseInt(img.getAttribute('data-like-count')) || 0,
                    comment_count: parseInt(img.getAttribute('data-comment-count')) || 0,
                    is_liked: img.getAttribute('data-is-liked') === 'true'
                };
            }

            function showImageModal(postData) {
                if (!postData) return;

                // Create overlay
                const overlay = document.createElement('div');
                overlay.className = 'fixed inset-0 bg-black/90 backdrop-blur-sm z-50 flex items-center justify-center p-4';
                overlay.style.opacity = '0';
                overlay.style.transition = 'opacity 0.2s ease-out';

                // Modal container
                const modal = document.createElement('div');
                modal.className = 'bg-white rounded-2xl shadow-2xl max-w-4xl w-full mx-4 max-h-[90vh] overflow-hidden transform scale-95 transition-transform duration-200';

                const userTypeEmoji = postData.userType === 'musician' ? '🎵' : (postData.userType === 'business' ? '🏢' : '👤');

                const avatarHtml = postData.userAvatar ?
                    `<img class="w-12 h-12 rounded-full object-cover border-2 border-gray-200" src="${postData.userAvatar}" alt="avatar">` :
                    `<div class="w-12 h-12 bg-gradient-to-r from-purple-400 to-pink-400 rounded-full flex items-center justify-center text-white font-bold">${(postData.userName||'U').charAt(0).toUpperCase()}</div>`;

                const isVideo = postData.mediaType === 'video';
                const mediaHtml = isVideo ? `
                    <video controls class="max-w-full max-h-full" style="width: 100%; height: 100%; object-fit: contain;">
                        <source src="${postData.imageUrl}" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                ` : `
                    <img src="${postData.imageUrl}" 
                         alt="Post image" 
                         class="max-w-full max-h-full object-contain">
                `;

                modal.innerHTML = `
                    <div class="flex h-full max-h-[90vh]">
                        <!-- Media Section -->
                        <div class="flex-1 bg-black flex items-center justify-center">
                            ${mediaHtml}
                        </div>
                        
                        <!-- Details Section -->
                        <div class="w-96 bg-white flex flex-col">
                            <!-- Header -->
                            <div class="p-6 border-b border-gray-200">
                                <div class="flex items-center gap-4 mb-4">
                                    ${avatarHtml}
                                    <div>
                                        <h3 class="font-bold text-gray-800 text-xl">${postData.userName}</h3>
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

                overlay.appendChild(modal);
                document.body.appendChild(overlay);
                document.body.style.overflow = 'hidden';

                setTimeout(() => { overlay.style.opacity = '1'; modal.style.transform = 'scale(1)'; }, 10);

                // wire up buttons
                const closeBtn = modal.querySelector('.close-modal');
                const likeBtn = modal.querySelector('.like-btn');
                const commentInput = modal.querySelector('.comment-input');
                const commentSubmitBtn = modal.querySelector('.comment-submit-btn');

                const removeModal = () => {
                    overlay.style.opacity = '0';
                    modal.style.transform = 'scale(0.95)';
                    document.body.style.overflow = '';
                    setTimeout(() => overlay.remove(), 220);
                    document.removeEventListener('keydown', handleEsc);
                };

                closeBtn.addEventListener('click', removeModal);
                overlay.addEventListener('click', (ev) => { if (ev.target === overlay) removeModal(); });

                function handleEsc(ev) { if (ev.key === 'Escape') removeModal(); }
                document.addEventListener('keydown', handleEsc);

                if (likeBtn) {
                    likeBtn.addEventListener('click', () => toggleLike(likeBtn, postData.id));
                }

                if (commentSubmitBtn) {
                    commentSubmitBtn.addEventListener('click', async () => {
                        const value = commentInput.value.trim();
                        if (!value) return;
                        await addComment(postData.id, value, commentInput, modal);
                    });
                }

                // load existing comments
                loadComments(postData.id, modal);
            }

            // Match profile modal behavior for likes/comments
            async function toggleLike(likeBtn, postId) {
                // Check if this is a sample post (not a real database post)
                if (postId.startsWith('sample-')) {
                    alert('Like functionality is only available for real posts. Create a post to test this feature!');
                    return;
                }
                
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const isLiked = likeBtn.getAttribute('data-liked') === 'true';
                
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
                        // Update like button state
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
                        
                        // Also update the original post data for consistency
                        const originalPostImage = document.querySelector(`[data-post-id="${postId}"]`);
                        if (originalPostImage) {
                            originalPostImage.setAttribute('data-like-count', data.like_count);
                            originalPostImage.setAttribute('data-is-liked', data.liked);
                        }
                    }
                } catch (error) {
                    console.error('Error toggling like:', error);
                }
            }

            // Add comment function
            async function addComment(postId, content, commentInput, modal) {
                // Check if this is a sample post (not a real database post)
                if (postId.startsWith('sample-')) {
                    alert('Comment functionality is only available for real posts. Create a post to test this feature!');
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
                        // Clear input
                        commentInput.value = '';

                        // Add comment to the list
                        addCommentToModal(data.comment, modal);

                        // Update comment count in modal
                        const commentCount = modal.querySelector('.comment-count');
                        if (commentCount) {
                            const newCount = parseInt(commentCount.textContent || '0') + 1;
                            commentCount.textContent = newCount;
                        }

                        // Also update the original post element's data attribute so counts stay consistent
                        const originalPostImage = document.querySelector(`[data-post-id="${postId}"]`);
                        if (originalPostImage) {
                            const origCount = parseInt(originalPostImage.getAttribute('data-comment-count') || '0') + 1;
                            originalPostImage.setAttribute('data-comment-count', origCount);
                        }
                    } else {
                        console.error('Failed to add comment:', data);
                    }
                } catch (error) {
                    console.error('Error adding comment:', error);
                }
            }

            // Load comments function
            async function loadComments(postId, modal) {
                // Check if this is a sample post (not a real database post)
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
                            // Clear the "no comments" message
                            commentsContainer.innerHTML = '';
                            
                            // Add each comment
                            data.comments.forEach(comment => {
                                addCommentToModal(comment, modal);
                            });
                        }
                    } else if (data.success && data.comments.length === 0) {
                        // No comments found — leave the placeholder
                    } else {
                        console.log('Error loading comments:', data);
                    }
                } catch (error) {
                    console.error('Error loading comments:', error);
                }
            }

            // Add comment to modal function
            function addCommentToModal(comment, modal) {
                const commentsContainer = modal.querySelector('.space-y-3');
                
                if (!commentsContainer) {
                    return;
                }

                const commentElement = document.createElement('div');
                commentElement.className = 'flex gap-3 p-3 bg-gray-50 rounded-lg';
                const userName = comment.user_name || 'Unknown User';
                const userInitial = userName.charAt(0).toUpperCase();
                
                // Check if user has an avatar
                let avatarHtml = '';
                if (comment.user_avatar) {
                    avatarHtml = `<img src="${comment.user_avatar}" alt="${userName}" class="w-8 h-8 rounded-full object-cover">`;
                } else {
                    avatarHtml = `<div class="w-8 h-8 bg-gradient-to-r from-purple-400 to-pink-400 rounded-full flex items-center justify-center text-white font-bold text-sm">${userInitial}</div>`;
                }
                
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
        });
    </script>

    @vite(['resources/js/app.js', 'resources/js/feed.js', 'resources/js/socket.js'])
</body> 
</html>