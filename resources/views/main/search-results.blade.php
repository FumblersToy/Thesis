    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Search Results - Bandmate</title>
        @vite(['resources/css/app.css', 'resources/css/feed.css', 'resources/js/app.js'])
        <meta name="csrf-token" content="{{ csrf_token() }}">
    </head>
    <body class="min-h-screen relative overflow-x-hidden gradient-bg">
        <div class="floating-elements fixed inset-0 pointer-events-none"></div>
        
        @php
            // Get current user info for profile dropdown
            $user = Auth::user();
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
                                <img src="{{ asset('assets/logo_black.png') }}" class="h-10 lg:hidden" alt="Bandmate logo">
                                <img src="{{ asset('assets/logo_both.png') }}" class="h-10 hidden lg:block" alt="Bandmate logo">
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
                                    value="{{ $query }}"
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

                                <button id="notificationsBtn" class="w-full flex items-center gap-3 p-3 rounded-xl hover:bg-gray-100 transition-colors text-gray-700 hover:text-gray-900 relative">
                                    <span class="text-lg">🔔</span>
                                    Notifications
                                    <span id="notificationBadge" class="hidden absolute top-2 left-6 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">0</span>
                                </button>

                                <a href="#" class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-100 transition-colors text-gray-700 hover:text-gray-900">
                                    <span class="text-lg">⚙️</span>
                                    Settings
                                </a>
                                
                                <a href="{{route('messages.index')}}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-100 transition-colors text-gray-700 hover:text-gray-900">
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

                <!-- Search Results Content -->
                <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-lg p-8 animate-scale-in border border-gray-200">
                    <h1 class="text-3xl font-bold mb-6 text-gray-800">
                Search Results for "{{ $query }}"
            </h1>
        
            @if($musicians->count() > 0)
                <section class="mb-8">
                            <h2 class="text-2xl font-semibold mb-6 text-gray-800 flex items-center gap-2">
                                <span class="text-2xl">🎵</span>
                                Musicians
                            </h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($musicians as $musician)
                                    <a href="{{ route('profile.show', $musician->user_id) }}" class="block group">
                                        <div class="bg-white/80 backdrop-blur-xl rounded-2xl shadow-lg p-6 hover:shadow-xl transition-all duration-300 border border-gray-200 group-hover:scale-105">
                                            <div class="flex items-center gap-4 mb-4">
                                                @if($musician->profile_picture)
                                                    <img src="{{ getImageUrl($musician->profile_picture) }}" 
                                                        alt="{{ $musician->stage_name }}" 
                                                        class="w-12 h-12 rounded-full object-cover border-2 border-gray-200">
                                                @else
                                                    <div class="w-12 h-12 bg-gradient-to-r from-purple-400 to-pink-400 rounded-full flex items-center justify-center text-white font-bold">
                                                        {{ substr($musician->stage_name, 0, 1) }}
                                                    </div>
                                                @endif
                                                <div>
                                                    <h3 class="font-bold text-gray-800 text-lg">{{ $musician->stage_name }}</h3>
                                    <p class="text-gray-600">{{ $musician->instrument ?? $musician->genre }}</p>
                                                </div>
                                            </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif
        
            @if($venues->count() > 0)
                <section class="mb-8">
                            <h2 class="text-2xl font-semibold mb-6 text-gray-800 flex items-center gap-2">
                                <span class="text-2xl">🏢</span>
                                Venues
                            </h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($venues as $venue)
                                    <a href="{{ route('profile.show', $venue->user_id) }}" class="block group">
                                        <div class="bg-white/80 backdrop-blur-xl rounded-2xl shadow-lg p-6 hover:shadow-xl transition-all duration-300 border border-gray-200 group-hover:scale-105">
                                            <div class="flex items-center gap-4 mb-4">
                                                @if($venue->profile_picture)
                                                    <img src="{{ getImageUrl($venue->profile_picture) }}"" 
                                                        alt="{{ $venue->business_name }}" 
                                                        class="w-12 h-12 rounded-full object-cover border-2 border-gray-200">
                                                @else
                                                    <div class="w-12 h-12 bg-gradient-to-r from-blue-400 to-green-400 rounded-full flex items-center justify-center text-white font-bold">
                                                        {{ substr($venue->business_name, 0, 1) }}
                                                    </div>
                                                @endif
                                                <div>
                                                    <h3 class="font-bold text-gray-800 text-lg">{{ $venue->business_name }}</h3>
                                    <p class="text-gray-600">{{ $venue->venue }}</p>
                                                </div>
                                            </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif
        
                    @if($posts->count() > 0)
                        <section class="mb-8">
                            <h2 class="text-2xl font-semibold mb-6 text-gray-800 flex items-center gap-2">
                                <span class="text-2xl">📝</span>
                                Posts
                            </h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                @foreach($posts as $post)
                                    <a href="#" class="block group">
                                        <div class="bg-white/80 backdrop-blur-xl rounded-2xl shadow-lg p-6 hover:shadow-xl transition-all duration-300 border border-gray-200 group-hover:scale-105">
                                            <p class="text-gray-700 mb-4">{{ Str::limit($post->description, 100) }}</p>
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 bg-gradient-to-r from-gray-400 to-gray-600 rounded-full flex items-center justify-center text-white font-bold text-sm">
                                                    {{ substr($post->musician?->stage_name ?? $post->business?->business_name ?? 'U', 0, 1) }}
                                                </div>
                                                <div>
                                                    <p class="text-sm font-medium text-gray-800">
                                                        {{ $post->musician?->stage_name ?? $post->business?->business_name ?? 'Unknown' }}
                                                    </p>
                                                    <p class="text-xs text-gray-500">{{ $post->created_at->format('M j, Y') }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </section>
                    @endif
                
                    @if($musicians->count() == 0 && $venues->count() == 0 && $posts->count() == 0 && !empty($query))
                        <div class="text-center py-12">
                            <div class="bg-white/80 backdrop-blur-xl rounded-2xl shadow-lg p-8 border border-gray-200">
                                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                <h3 class="text-lg font-semibold text-gray-700 mb-2">No results found</h3>
                                <p class="text-gray-500">No results found for "{{ $query }}"</p>
                            </div>
                        </div>
                    @endif
                </div>
            </section>
        </div>

        <script>
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
                    
                    // Simulate search (you can replace this with actual API call)
                    setTimeout(() => {
                        searchLoading.classList.add('hidden');
                        
                        // For now, show a placeholder result
                        searchResultsContent.innerHTML = `
                            <div class="p-4 text-center text-gray-500">
                                <p>Search functionality coming soon!</p>
                                <p class="text-sm">Search for: "${query}"</p>
                            </div>
                        `;
                        searchResultsContent.classList.remove('hidden');
                    }, 500);
                }
            });

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
                                <div class="${bgColor} p-4 rounded-xl hover:shadow-md transition-all mb-3 border border-gray-100">
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
                        
                        // Update badge
                        if (data.unread_count > 0) {
                            notificationBadge.textContent = data.unread_count;
                            notificationBadge.classList.remove('hidden');
                        } else {
                            notificationBadge.classList.add('hidden');
                        }
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
            
            // Load notification count on page load
            loadNotifications();
        </script>
    </body>
    </html>