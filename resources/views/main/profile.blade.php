<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Bandmate</title>
    @vite(['resources/css/app.css', 'resources/css/feed.css', 'resources/css/socket.css', 'resources/js/app.js', 'resources/js/socket.js'])
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Leaflet removed: maps are no longer used. -->
</head>
<body class="min-h-screen relative overflow-x-hidden gradient-bg">
    <div class="floating-elements fixed inset-0 pointer-events-none"></div>
    
    
    
    @php
        // Get current user info first
        $user = Auth::user();
        
        $profileUserId = $id ?? request()->route('id');
        $profileUser = \App\Models\User::find($profileUserId);
        $musician = $profileUser ? \App\Models\Musician::where('user_id', $profileUser->id)->first() : null;
        $business = $profileUser ? \App\Models\Business::where('user_id', $profileUser->id)->first() : null;

        $displayName = $musician?->stage_name
            ?: ($business?->business_name ?: ($profileUser->name ?? 'User'));
        $profileImage = null;
        if ($musician && $musician->profile_picture) {
            $profileImage = getImageUrl($musician->profile_picture);
        } elseif ($business && $business->profile_picture) {
            $profileImage = getImageUrl($business->profile_picture);
        } else {
            $profileImage = '/assets/default1.jpg';
        }
        $bioText = $musician?->bio ?: '';
        $roleLabel = $musician?->instrument ?: ($business?->venue ?: '');

        $posts = \App\Models\Post::where('user_id', $profileUserId)
            ->orderByDesc('created_at')
            ->get();

        // Check if current user is following this profile user
        $isFollowing = false;
        $followerCount = 0;
        $followingCount = 0;
        if ($user && $profileUser) {
            $isFollowing = $user->isFollowing($profileUser);
            $followerCount = $profileUser->followers()->count();
            $followingCount = $profileUser->following()->count();
        }
        $currentUserMusician = $user ? \App\Models\Musician::where('user_id', $user->id)->first() : null;
        $currentUserBusiness = $user ? \App\Models\Business::where('user_id', $user->id)->first() : null;
        $currentDisplayName = $currentUserMusician?->stage_name
            ?: ($currentUserBusiness?->business_name ?: ($user->name ?? 'User'));
        $currentRoleLabel = $currentUserMusician?->instrument ?: ($currentUserBusiness?->venue ?: 'Member');
        $currentProfileImage = null;
        if ($currentUserMusician && $currentUserMusician->profile_picture) {
            $currentProfileImage = getImageUrl($currentUserMusician->profile_picture);
        } elseif ($currentUserBusiness && $currentUserBusiness->profile_picture) {
            $currentProfileImage = getImageUrl($currentUserBusiness->profile_picture);
        } else {
            $currentProfileImage = '/images/sample-profile.jpg';
        }
    @endphp

    <div class="flex min-h-screen relative z-10">
        <!-- Main Content -->
        <section class="flex-1 p-6 lg:p-8 flex flex-col">
            <!-- Header with Logo, Search Bar and User Profile -->
            <div class="flex justify-between items-center mb-8 animate-fade-in">
                <!-- Logo -->
                <div class="flex-shrink-0">
                    <a href="{{ route('feed') }}" class="flex items-center bg-white/10 backdrop-blur-xl rounded-2xl px-4 py-2 hover:bg-white/20 transition-all duration-300 shadow-lg">
                        <img src="{{ asset('assets/logo_both.png') }}" class="h-10" alt="Bandmate logo">
                    </a>
                </div>
                
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
                                class="block w-full pl-10 pr-3 py-3 border border-white/20 rounded-2xl leading-5 bg-white/10 backdrop-blur-xl placeholder-white/70 focus:outline-none focus:ring-2 focus:ring-white/30 focus:border-white/30 text-white sm:text-sm" 
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
                <div class="relative ml-6">
                    <button id="profileButton" class="flex items-center gap-3 bg-white/80 backdrop-blur-xl p-4 rounded-2xl hover:bg-white/90 shadow-lg transition-all duration-300 group border border-gray-200">
                        <img class="w-12 h-12 rounded-full object-cover border-2 border-gray-200"
                             src="{{ $currentProfileImage }}"
                             alt="profile">
                        
                        <div class="hidden sm:block text-left">
                            <p class="text-gray-800 font-semibold">
                                {{ $currentDisplayName }}
                            </p>
                            <p class="text-gray-600 text-sm">{{ $currentRoleLabel }}</p>
                        </div>

                        <svg class="w-5 h-5 text-gray-500 group-hover:text-gray-700 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>

                    <div id="profileDropdown" class="absolute right-0 top-full mt-2 w-64 bg-white/95 backdrop-blur-xl rounded-2xl shadow-2xl overflow-hidden hidden animate-scale-in z-50 border border-gray-200">
                        <div class="p-4 border-b border-gray-200">
                            <div class="flex items-center gap-3">
                                <img class="w-16 h-16 rounded-full object-cover border-2 border-gray-200" src="{{ $currentProfileImage }}" alt="profile">
                                <div>
                                    <p class="text-gray-800 font-semibold text-lg">{{ $currentDisplayName }}</p>
                                    <p class="text-gray-600">{{ $currentRoleLabel }}</p>
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

            <!-- Profile Header -->
            <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-lg mb-8 animate-scale-in border border-gray-200">
                <div class="p-8">
                    <!-- Profile Section -->
                    <div class="flex flex-col md:flex-row gap-8 md:gap-12">
                        
                        <!-- Profile Image -->
                        <div class="flex justify-center md:justify-start">
                            <img class="rounded-full h-32 w-32 md:h-40 md:w-40 object-cover border-4 border-white shadow-lg"
                                 src="{{ $profileImage }}"
                                 alt="profile">
                        </div>
                        
                        <!-- Profile Info -->
                        <div class="flex-1 text-center md:text-left">
                            
                            <!-- Username/Business Name + Button Row -->
                            <div class="flex flex-col md:flex-row md:items-center gap-4 md:gap-6 mb-6">
                                <h1 class="text-2xl md:text-3xl font-bold text-gray-800">
                                    {{ $displayName }}
                                </h1>
                                
                                <div class="flex gap-3 justify-center md:justify-start">
                                    @if($user && $profileUser && $user->id !== $profileUser->id)
                                    <button id="followButton" class="{{ $isFollowing ? 'bg-gray-500 hover:bg-gray-600' : 'bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600' }} px-6 py-2 rounded-xl text-white text-sm font-medium transition-all duration-300 shadow-lg hover:shadow-xl border-2 border-white/20" data-user-id="{{ $profileUserId }}" data-following="{{ $isFollowing ? 'true' : 'false' }}">
                                        <span class="follow-text {{ $isFollowing ? 'hidden' : '' }}">Follow</span>
                                        <span class="following-text {{ $isFollowing ? '' : 'hidden' }}">Following</span>
                                    </button>
                                    @endif
                                    @if($user && $user->id != $profileUserId)
                                        <a href="{{ route('messages.index') }}?user={{ $profileUserId }}" 
                                           class="bg-white/80 hover:bg-white border border-gray-200 px-6 py-2 rounded-xl text-gray-800 text-sm font-medium transition-all duration-300 shadow-lg hover:shadow-xl inline-block text-center">
                                            Message
                                        </a>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Stats Row -->
                            <div class="flex justify-center md:justify-start gap-8 md:gap-10 mb-6">
                                <div class="text-center md:text-left">
                                    <span class="font-bold text-gray-800 text-lg">{{ $posts->count() }}</span>
                                    <span class="text-gray-600 ml-1">posts</span>
                                </div>
                                <div class="text-center md:text-left">
                                    <span class="font-bold text-gray-800 text-lg" data-follower-count>{{ $followerCount }}</span>
                                    <span class="text-gray-600 ml-1">followers</span>
                                </div>
                                <div class="text-center md:text-left">
                                    <span class="font-bold text-gray-800 text-lg">{{ $followingCount }}</span>
                                    <span class="text-gray-600 ml-1">following</span>
                                </div>
                            </div>
                            
                            @php
                                $hasLocation = false;
                                $latitude = null;
                                $longitude = null;
                                $locationName = null;
                                
                                if ($profileUser->musician && $profileUser->musician->latitude && $profileUser->musician->longitude) {
                                    $hasLocation = true;
                                    $latitude = $profileUser->musician->latitude;
                                    $longitude = $profileUser->musician->longitude;
                                    $locationName = $profileUser->musician->location_name;
                                } elseif ($profileUser->business && $profileUser->business->latitude && $profileUser->business->longitude) {
                                    $hasLocation = true;
                                    $latitude = $profileUser->business->latitude;
                                    $longitude = $profileUser->business->longitude;
                                    $locationName = $profileUser->business->location_name;
                                }
                            @endphp
                            
                            @if ($hasLocation)
                            <!-- Location Section -->
                            <div class="mb-6">
                                <div class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-blue-50 to-purple-50 border border-blue-200 rounded-xl">
                                    <div class="flex items-center justify-center w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full">
                                        <svg class="w-4 h-4 text-white" fill="red" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-sm font-medium text-gray-700">Location</span>
                                        @if ($locationName)
                                            <span class="text-xs text-gray-600">{{ $locationName }}</span>
                                        @else
                                            <span class="text-xs text-gray-500">Coordinates: {{ number_format($latitude, 4) }}, {{ number_format($longitude, 4) }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endif
                            
                            <!-- Bio/Info Section -->
                            <div class="text-left space-y-2">
                                <p class="text-gray-700 mt-2 max-w-md">
                                    {{ $bioText ?: 'No bio available' }}
                                </p>

                                @if($roleLabel)
                                <div class="flex items-center gap-2 text-sm text-gray-700 mt-2">
                                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM21 16c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/>
                                    </svg>
                                    <span>{{ $roleLabel }}</span>
                                </div>
                                @endif

                            </div>
                        </div>
                    </div>
                    
                    <!-- Posts Grid Navigation -->
                    <div class="mt-8 border-t border-gray-200">
                        <div class="flex justify-center">
                            <button class="flex items-center gap-2 px-4 py-3 border-t-2 border-gray-800 text-gray-800 text-sm font-medium tracking-wider">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.25 5.5a.75.75 0 00-.75.75v8.5c0 .414.336.75.75.75h8.5a.75.75 0 00.75-.75v-3.5a.75.75 0 00-1.5 0v3h-7v-7h3a.75.75 0 000-1.5h-3.5z" clip-rule="evenodd"/>
                                </svg>
                                POSTS
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Posts Grid -->
            <div id="postsGrid" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8">
                @forelse($posts as $post)
                    @php
                        $exists = $post->image_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($post->image_path);
                        $imageUrl = $post->image_path ? getImageUrl($post->image_path) : '/images/sample-post-1.jpg';
                        $isOwner = $post->user_id === auth()->id();
                    @endphp
                    <article class="bg-white/80 backdrop-blur-xl rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition-all duration-300 animate-scale-in border border-gray-200">
                        <div class="relative">
                            <img 
                                src="{{ $imageUrl }}"
                                alt="post"
                                class="post-image w-full h-80 object-cover cursor-pointer hover:opacity-90 transition-opacity"
                                onerror="this.onerror=null;this.src='/images/sample-post-1.jpg';"
                                data-post-id="{{ $post->id }}"
                                data-image-url="{{ $imageUrl }}"
                                data-user-name="{{ $displayName }}"
                                data-user-genre="{{ $roleLabel }}"
                                data-user-type="{{ $musician ? 'musician' : ($business ? 'business' : 'member') }}"
                                data-user-avatar="{{ $profileImage }}"
                                data-description="{{ $post->description }}"
                                data-created-at="{{ $post->created_at->toDateTimeString() }}"
                                data-like-count="{{ $post->likes()->count() }}"
                                data-comment-count="{{ $post->comments()->count() }}"
                                data-is-liked="{{ $post->likes()->where('user_id', auth()->id())->exists() ? 'true' : 'false' }}"
                            >
                            @if($isOwner)
                                <button class="delete-post-btn absolute top-4 left-4 bg-red-500/80 hover:bg-red-600 text-white p-2 rounded-full transition-colors duration-200" 
                                        data-post-id="{{ $post->id }}" 
                                        title="Delete post">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            @endif
                        </div>
                        <div class="p-6">
                            <div class="flex items-center gap-4 mb-4">
                                <img class="w-12 h-12 rounded-full object-cover border-2 border-gray-200" 
                                     src="{{ $profileImage }}" 
                                     alt="profile">
                                <div>
                                    <h3 class="font-bold text-gray-800 text-lg">{{ $displayName }}</h3>
                                    <p class="text-gray-600">{{ $roleLabel }}</p>
                                </div>
                            </div>
                            @if($post->description)
                                <p class="text-gray-700 mb-4 leading-relaxed">{{ $post->description }}</p>
                            @endif
                            <div class="flex justify-between items-center text-gray-500 text-sm">
                                <span>{{ $post->created_at->format('M j, Y') }}</span>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="col-span-full text-center py-12">
                        <div class="bg-white/80 backdrop-blur-xl rounded-2xl shadow-lg p-8 border border-gray-200">
                            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <h3 class="text-lg font-semibold text-gray-700 mb-2">No posts yet</h3>
                            <p class="text-gray-500">This user hasn't shared any posts yet.</p>
                        </div>
                    </div>
                @endforelse
            </div>
        </section>
    </div>

    <script>
        function getImageUrl(path) {
    if (!path) return '/images/sample-profile.jpg';
    
    // If path already starts with http or /, return as is
    if (path.startsWith('http') || path.startsWith('/storage/')) {
        return path;
    }
    
    // Otherwise prepend /storage/
    return `/storage/${path}`;
}

        document.addEventListener('DOMContentLoaded', function() {
            // Profile dropdown functionality
            const profileButton = document.getElementById('profileButton');
            const profileDropdown = document.getElementById('profileDropdown');
            
            if (profileButton && profileDropdown) {
                profileButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    const isHidden = profileDropdown.classList.contains('hidden') || 
                                   window.getComputedStyle(profileDropdown).display === 'none';
                    
                    if (isHidden) {
                        profileDropdown.classList.remove('hidden');
                        profileDropdown.style.display = 'block';
                    } else {
                        profileDropdown.classList.add('hidden');
                        profileDropdown.style.display = 'none';
                    }
                });
                
                profileDropdown.addEventListener('click', function(e) {
                    e.stopPropagation();
                });
            }

            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (profileDropdown && profileButton && !profileButton.contains(e.target) && !profileDropdown.contains(e.target)) {
                    profileDropdown.classList.add('hidden');
                    profileDropdown.style.display = 'none';
                }
            });

            // Follow functionality
            const followButton = document.getElementById('followButton');
            if (followButton) {
                followButton.addEventListener('click', function() {
                    const userId = this.getAttribute('data-user-id');
                    const isFollowing = this.getAttribute('data-following') === 'true';
                    
                    toggleFollow(userId, isFollowing, this);
                });
            }

            // Image modal functionality - WITH MORE DEBUGGING
            document.addEventListener('click', function(e) {
                console.log('🔍 Click detected on:', e.target);
                console.log('🔍 Target classes:', e.target.className);
                console.log('🔍 Closest .post-image:', e.target.closest('.post-image'));
                
                if (e.target.closest('.post-image')) {
                    e.preventDefault();
                    console.log('✅ Post image clicked!'); // Debug log
                    const img = e.target.closest('.post-image');
                    const postData = extractPostDataFromImage(img);
                    
                    console.log('📦 Post data:', postData); // Debug log
                    showImageModal(postData);
                } else {
                    console.log('❌ Not a post image');
                }
            });

            // Search functionality
            const searchInput = document.getElementById('searchInput');
            const searchResults = document.getElementById('searchResults');
            
            if (searchInput && searchResults) {
                let searchTimeout;
                
                searchInput.addEventListener('input', function() {
                    clearTimeout(searchTimeout);
                    const query = this.value.trim();
                    
                    if (query.length > 2) {
                        searchTimeout = setTimeout(() => {
                            performSearch(query);
                        }, 300);
                    } else {
                        searchResults.classList.add('hidden');
                    }
                });
                
                // Hide search results when clicking outside
                document.addEventListener('click', function(e) {
                    if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
                        searchResults.classList.add('hidden');
                    }
                });
            }
            
            function performSearch(query) {
                if (!query) {
                    searchResults.classList.add('hidden');
                    return;
                }
                
                // Show loading state
                const searchLoading = document.getElementById('searchLoading');
                const searchResultsContent = document.getElementById('searchResultsContent');
                const noResults = document.getElementById('noResults');
                
                searchResults.classList.remove('hidden');
                searchLoading.classList.remove('hidden');
                searchResultsContent.classList.add('hidden');
                noResults.classList.add('hidden');
                
                fetch(`/api/search?query=${encodeURIComponent(query)}`, {
                    headers: { 'Accept': 'application/json' }
                })
                .then(r => r.json())
                .then(data => {
                    searchLoading.classList.add('hidden');
                    const musicians = Array.isArray(data.musicians) ? data.musicians : [];
                    const venues = Array.isArray(data.venues) ? data.venues : [];
                    
                    if (musicians.length === 0 && venues.length === 0) {
                        noResults.classList.remove('hidden');
                        searchResultsContent.classList.add('hidden');
                        return;
                    }
                    
                    const musicianItems = musicians.map(m => `
                        <a href="/profile/${m.user_id}" class="flex items-center gap-3 p-3 hover:bg-gray-100 rounded-xl transition-colors">
                            <div class="w-10 h-10 rounded-full overflow-hidden bg-gradient-to-r from-purple-400 to-pink-400 flex items-center justify-center text-white font-bold">
                                ${m.profile_image ? `<img src="${m.profile_image}" class="w-full h-full object-cover" alt="${m.stage_name}">` : (m.stage_name ? m.stage_name.charAt(0).toUpperCase() : 'M')}
                            </div>
                            <div class="min-w-0">
                                <div class="font-semibold text-gray-800 truncate">${m.stage_name}</div>
                                <div class="text-xs text-gray-500 truncate">${m.genre || ''}</div>
                            </div>
                            <span class="ml-auto text-xs bg-purple-100 text-purple-700 px-2 py-1 rounded-full">Musician</span>
                        </a>
                    `).join('');
                    
                    const venueItems = venues.map(v => `
                        <a href="/profile/${v.user_id}" class="flex items-center gap-3 p-3 hover:bg-gray-100 rounded-xl transition-colors">
                            <div class="w-10 h-10 rounded-full overflow-hidden bg-gradient-to-r from-purple-400 to-pink-400 flex items-center justify-center text-white font-bold">
                                ${v.profile_image ? `<img src="${v.profile_image}" class="w-full h-full object-cover" alt="${v.business_name}">` : (v.business_name ? v.business_name.charAt(0).toUpperCase() : 'B')}
                            </div>
                            <div class="min-w-0">
                                <div class="font-semibold text-gray-800 truncate">${v.business_name}</div>
                                <div class="text-xs text-gray-500 truncate">${v.location || ''}</div>
                            </div>
                            <span class="ml-auto text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded-full">Business</span>
                        </a>
                    `).join('');
                    
                    searchResultsContent.innerHTML = `
                        ${musicians.length ? `<div class=\"px-3 pt-3 pb-1 text-xs font-semibold text-gray-500\">Musicians</div>` : ''}
                        ${musicianItems}
                        ${venues.length ? `<div class=\"px-3 pt-3 pb-1 text-xs font-semibold text-gray-500\">Venues</div>` : ''}
                        ${venueItems}
                    `;
                    searchResultsContent.classList.remove('hidden');
                })
                .catch(() => {
                    searchLoading.classList.add('hidden');
                    noResults.classList.remove('hidden');
                });
            }
            
            // Toggle follow function
            async function toggleFollow(userId, isFollowing, button) {
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const followText = button.querySelector('.follow-text');
                const followingText = button.querySelector('.following-text');
                
                try {
                    const response = await fetch(`/users/${userId}/follow`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            follow: !isFollowing
                        })
                    });

                    const data = await response.json();

                    if (data.success) {
                        if (data.following) {
                            // Now following
                            button.setAttribute('data-following', 'true');
                            button.classList.remove('from-purple-500', 'to-pink-500', 'hover:from-purple-600', 'hover:to-pink-600');
                            button.classList.add('bg-gray-500', 'hover:bg-gray-600');
                            followText.classList.add('hidden');
                            followingText.classList.remove('hidden');
                        } else {
                            // No longer following
                            button.setAttribute('data-following', 'false');
                            button.classList.add('from-purple-500', 'to-pink-500', 'hover:from-purple-600', 'hover:to-pink-600');
                            button.classList.remove('bg-gray-500', 'hover:bg-gray-600');
                            followText.classList.remove('hidden');
                            followingText.classList.add('hidden');
                        }
                        
                        // Update follower count if available
                        const followerCount = document.querySelector('[data-follower-count]');
                        if (followerCount && data.follower_count !== undefined) {
                            followerCount.textContent = data.follower_count;
                        }
                    } else {
                        console.error('Follow action failed:', data.message);
                        alert(data.message || 'Failed to update follow status');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('Network error. Please try again.');
                }
            }

            // Extract post data from image element
            function extractPostDataFromImage(img) {
                if (!img) return null;
                
                return {
                    id: img.getAttribute('data-post-id'),
                    imageUrl: img.getAttribute('data-image-url'),
                    userName: img.getAttribute('data-user-name'),
                    userGenre: img.getAttribute('data-user-genre'),
                    userType: img.getAttribute('data-user-type'),
                    userAvatar: img.getAttribute('data-user-avatar'),
                    description: img.getAttribute('data-description'),
                    createdAt: img.getAttribute('data-created-at'),
                    like_count: parseInt(img.getAttribute('data-like-count')) || 0,
                    comment_count: parseInt(img.getAttribute('data-comment-count')) || 0,
                    is_liked: img.getAttribute('data-is-liked') === 'true'
                };
            }

            // Show image modal
            function showImageModal(postData) {
                if (!postData) return;
                
                // Create modal overlay
                const overlay = document.createElement('div');
                overlay.className = 'fixed inset-0 bg-black/90 backdrop-blur-sm z-50 flex items-center justify-center';
                overlay.style.opacity = '0';
                overlay.style.transition = 'opacity 0.3s ease-out';
                
                // Create modal content
                const modal = document.createElement('div');
                modal.className = 'bg-white rounded-2xl shadow-2xl max-w-6xl w-full mx-4 max-h-[90vh] overflow-hidden transform scale-95 transition-transform duration-300';
                
                const userTypeEmoji = postData.userType === 'musician' ? '🎵' : 
                                     postData.userType === 'business' ? '🏢' : '👤';
                
                const avatarElement = postData.userAvatar ? 
                    `<img class="w-16 h-16 rounded-full object-cover border-2 border-gray-200" src="${postData.userAvatar}" alt="avatar">` :
                    `<div class="w-16 h-16 bg-gradient-to-r from-purple-400 to-pink-400 rounded-full flex items-center justify-center text-white font-bold text-xl">${postData.userName.charAt(0).toUpperCase()}</div>`;
                
                modal.innerHTML = `
                    <div class="flex h-full max-h-[90vh]">
                        <!-- Image Section -->
                        <div class="flex-1 bg-black flex items-center justify-center">
                            <img src="${postData.imageUrl}" 
                                 alt="Post image" 
                                 class="max-w-full max-h-full object-contain">
                        </div>
                        
                        <!-- Details Section -->
                        <div class="w-96 bg-white flex flex-col">
                            <!-- Header -->
                            <div class="p-6 border-b border-gray-200">
                                <div class="flex items-center gap-4 mb-4">
                                    ${avatarElement}
                                    <div>
                                        <h3 class="font-bold text-gray-800 text-xl">${postData.userName}</h3>
                                        <p class="text-gray-600">${postData.userGenre}</p>
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
                                           class="flex-1 px-4 py-2 border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
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
                
                // Prevent body scroll
                document.body.style.overflow = 'hidden';
                
                // Animate in
                setTimeout(() => {
                    overlay.style.opacity = '1';
                    modal.style.transform = 'scale(1)';
                }, 10);
                
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
                
                // Close on overlay click (but not on modal content)
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
                const commentInput = modal.querySelector('input[type="text"]');
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
                        
                        // Update comment count
                        const commentCount = modal.querySelector('.comment-count');
                        if (commentCount) {
                            commentCount.textContent = parseInt(commentCount.textContent) + 1;
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
                    avatarHtml = `<img src="${comment.user_avatar}" alt="${userName}" class="w-8 h-8 rounded-full object-cover">`;                } else {
                    avatarHtml = `<div class="w-8 h-8 bg-gradient-to-r f    rom-purple-400 to-pink-400 rounded-full flex items-center justify-center text-white font-bold text-sm">${userInitial}</div>`;
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

    <!-- Socket.IO Initialization Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Socket.IO with user data
            @auth
                const userData = {
                    id: {{ Auth::id() }},
                    name: '{{ $currentDisplayName }}',
                    type: '{{ $currentUserMusician ? "musician" : ($currentUserBusiness ? "business" : "member") }}',
                    email: '{{ Auth::user()->email }}'
                };
                
                // Initialize socket connection
                if (window.socketManager) {
                    window.socketManager.init(userData);
                }
            @endauth
        });
        </script>
    </body>
</html>