<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modern Musician Feed</title>
    @vite(['resources/css/app.css', 'resources/css/feed.css', 'resources/css/socket.css'])
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
</head>
<body class="min-h-screen relative overflow-x-hidden gradient-bg">
    <div class="floating-elements fixed inset-0 pointer-events-none"></div>
    
    
    
    <div class="flex min-h-screen relative z-10">
        <aside id="sidebar" class="w-80 p-6 glass-effect backdrop-blur-xl shadow-2xl hidden lg:block animate-slide-up gradient-bg">
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

        <aside id="mobileMenu" class="fixed inset-y-0 left-0 z-40 w-80 glass-effect backdrop-blur-xl p-6 transform -translate-x-full lg:hidden transition-transform duration-300 gradient-bg overflow-y-auto">
            <div class="mb-6">
                <h2 class="font-bold text-2xl mb-6 text-white flex items-center gap-3">
                    <div class="w-8 h-8 bg-gradient-to-r from-purple-400 to-pink-400 rounded-full animate-pulse-slow"></div>
                    Filters
                </h2>
                
                <div class="space-y-8">
                    <div class="animate-fade-in">
                        <h3 class="font-semibold text-white/90 mb-4 text-lg">🎵 Instruments</h3>
                        <div class="space-y-3" id="mobileInstruments">
                            <!-- Will be populated by JavaScript -->
                        </div>
                    </div>

                    <div class="animate-fade-in" style="animation-delay: 0.1s">
                        <h3 class="font-semibold text-white/90 mb-4 text-lg">🏢 Venues</h3>
                        <div class="space-y-3" id="mobileVenues">
                            <!-- Will be populated by JavaScript -->
                        </div>
                    </div>

                    <div class="animate-fade-in" style="animation-delay: 0.2s">
                        <h3 class="font-semibold text-white/90 mb-4 text-lg">📍 Distance</h3>
                        <div class="space-y-3">
                            <button type="button" id="mobileGetCurrentLocation" class="w-full px-4 py-2 bg-white/20 text-white rounded-xl hover:bg-white/30 transition-colors text-sm">
                                📍 Use My Location
                            </button>
                            <div id="mobileLocationStatus" class="text-white/70 text-sm hidden"></div>
                            <div class="space-y-2">
                                <label class="block text-white/80 text-sm">Sort by:</label>
                                <select id="mobileSortBy" class="w-full px-3 py-2 bg-white/20 text-white rounded-xl border border-white/30 focus:outline-none focus:ring-2 focus:ring-white/30">
                                    <option value="recent" class="text-black">Most Recent</option>
                                    <option value="distance" class="text-black">Nearest First</option>
                                </select>
                            </div>
                            <div id="mobileDistanceFilter" class="space-y-2 hidden">
                                <label class="block text-white/80 text-sm">Within:</label>
                                <select id="mobileMaxDistance" class="w-full px-3 py-2 bg-white/20 text-white rounded-xl border border-white/30 focus:outline-none focus:ring-2 focus:ring-white/30">
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

                    <button type="button" id="mobileApplyFilters"
                        class="w-full px-6 py-4 bg-gradient-to-r from-purple-500 to-pink-500 text-white rounded-2xl hover:from-purple-600 hover:to-pink-600 transition-all duration-300 font-semibold text-lg shadow-lg hover-glow animate-scale-in border-2">
                        Apply Filters ✨
                    </button>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <section class="flex-1 p-6 lg:p-8 flex flex-col">
            <!-- Header with Search Bar and User Profile -->
            <div class="flex justify-between items-center mb-8 animate-fade-in">
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

                            <a href="#" class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-100 transition-colors text-gray-700 hover:text-gray-900">
                                <span class="text-lg">🎵</span>
                                My Music
                            </a>
                            
                            <a href="{{ route('messages.index') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-100 transition-colors text-gray-700 hover:text-gray-900">
                                <span class="text-lg">💬</span>
                                Messages
                            </a>
                            
                            <a href="{{ route('map') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-100 transition-colors text-gray-700 hover:text-gray-900">
                                <span class="text-lg">🗺️</span>
                                Map
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
                    <label for="image" class="block text-gray-700 font-medium mb-3 text-lg">📷 Upload Image (Optional)</label>
                    <div class="custom-file-input">
                        <input type="file" name="image" id="image" accept="image/*">
                        <label for="image" class="custom-file-label cursor-pointer">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                            <span id="fileText">Choose an image or drag it here</span>
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
    // Pass user data to JavaScript (available before feed.js loads)
    @auth
        window.userData = {
            id: {{ Auth::id() }},
            name: '{{ addslashes($displayName) }}',
            type: '{{ $musician ? "musician" : ($business ? "business" : "member") }}',
            email: '{{ addslashes(Auth::user()->email) }}'
        };
    @endauth

    document.addEventListener('click', function(e) {
    console.log('INLINE: Click detected anywhere');
    if (e.target.closest('.post-image')) {
        e.preventDefault();
        alert('POST IMAGE CLICKED! This works inline!');
    }
});

    function getImageUrl(path) {
    if (!path) return '/images/sample-profile.jpg';
    
    // Fix double-processing: remove ALL instances of /storage/ before http/https
    path = path.replace('/storage/https://', 'https://');
    path = path.replace('/storage/http://', 'http://');
    
    // If path already starts with http or /storage/, return as-is
    if (path.startsWith('http://') || path.startsWith('https://')) {
        return path;
    }
    
    if (path.startsWith('/storage/')) {
        return path;
    }
    
    // Otherwise prepend /storage/
    return `/storage/${path}`;
}

document.addEventListener('DOMContentLoaded', function() {
    console.log('Feed page loaded');
    
    // Initialize Socket.IO with user data (if available)
    if (window.userData && window.socketManager) {
        window.socketManager.init(window.userData);
        console.log('Socket.IO initialized with user data:', window.userData);
    }
    
    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    // ... rest of your existing code
    
    // Elements
    const createPostForm = document.getElementById('createPostForm');
    const postsGrid = document.getElementById('postsGrid');
    const loadMoreBtn = document.getElementById('loadMore');
    
    console.log('Elements found:', {
        createPostForm: !!createPostForm,
        postsGrid: !!postsGrid,
        loadMoreBtn: !!loadMoreBtn
    });
    const fileInput = document.getElementById('image');
    const fileName = document.getElementById('fileName');
    const fileText = document.getElementById('fileText');
    const mobileMenuButton = document.getElementById('mobileMenuButton');
    const mobileMenu = document.getElementById('mobileMenu');
    const profileButton = document.getElementById('profileButton');
    const profileDropdown = document.getElementById('profileDropdown');
    const applyFiltersBtn = document.getElementById('applyFilters');
    const getCurrentLocationBtn = document.getElementById('getCurrentLocation');
    const locationStatus = document.getElementById('locationStatus');
    const sortBySelect = document.getElementById('sortBy');
    const distanceFilter = document.getElementById('distanceFilter');
    
    let currentPage = 1;
    let loading = false;
    let userLocation = null;

    // Mobile menu functionality
    if (mobileMenuButton && mobileMenu) {
        mobileMenuButton.addEventListener('click', function() {
            mobileMenu.classList.toggle('-translate-x-full');
        });
    }

    // Profile dropdown functionality
    if (profileButton && profileDropdown) {
        console.log('Profile elements found:', profileButton, profileDropdown);
        
        profileButton.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('Profile button clicked');
            console.log('Dropdown classes before:', profileDropdown.classList.toString());
            
            const isHidden = profileDropdown.classList.contains('hidden') || 
                           window.getComputedStyle(profileDropdown).display === 'none';
            
            if (isHidden) {
                profileDropdown.classList.remove('hidden');
                profileDropdown.style.display = 'block';
                console.log('Showing dropdown');
            } else {
                profileDropdown.classList.add('hidden');
                profileDropdown.style.display = 'none';
                console.log('Hiding dropdown');
            }
            
            console.log('Dropdown classes after:', profileDropdown.classList.toString());
            console.log('Dropdown visibility:', window.getComputedStyle(profileDropdown).display);
        });
        
        profileDropdown.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    } else {
        console.log('Profile elements not found:', {
            profileButton: !!profileButton,
            profileDropdown: !!profileDropdown
        });
    }

    // Location and distance functionality
    if (getCurrentLocationBtn) {
        getCurrentLocationBtn.addEventListener('click', function() {
            getCurrentLocation();
        });
    }

    if (sortBySelect) {
        sortBySelect.addEventListener('change', function() {
            if (this.value === 'distance') {
                distanceFilter.classList.remove('hidden');
                if (!userLocation) {
                    getCurrentLocation();
                }
            } else {
                distanceFilter.classList.add('hidden');
            }
        });
    }

    // Close dropdowns and menus when clicking outside
    document.addEventListener('click', function(e) {
        if (profileDropdown && profileButton && !profileButton.contains(e.target) && !profileDropdown.contains(e.target)) {
            profileDropdown.classList.add('hidden');
            profileDropdown.style.display = 'none';
        }
        
        if (mobileMenu && mobileMenuButton && !mobileMenu.contains(e.target) && !mobileMenuButton.contains(e.target)) {
            mobileMenu.classList.add('-translate-x-full');
        }
    });

    // File upload handling
    if (fileInput) {
        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                fileName.textContent = file.name;
                fileName.classList.remove('hidden');
                fileText.textContent = 'File selected';
            } else {
                fileName.classList.add('hidden');
                fileText.textContent = 'Choose an image or drag it here';
            }
        });

        // Drag and drop functionality
        const customFileInput = document.querySelector('.custom-file-input');
        
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            customFileInput.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            customFileInput.addEventListener(eventName, highlight, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            customFileInput.addEventListener(eventName, unhighlight, false);
        });

        function highlight(e) {
            customFileInput.classList.add('border-purple-400', 'bg-purple-50');
        }

        function unhighlight(e) {
            customFileInput.classList.remove('border-purple-400', 'bg-purple-50');
        }

        customFileInput.addEventListener('drop', handleDrop, false);

        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            
            if (files.length > 0) {
                fileInput.files = files;
                const file = files[0];
                fileName.textContent = file.name;
                fileName.classList.remove('hidden');
                fileText.textContent = 'File selected';
            }
        }
    }

    // Create post form submission
    if (createPostForm) {
        createPostForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            
            try {
                submitBtn.disabled = true;
                submitBtn.textContent = 'Posting...';
                
                const formData = new FormData(this);
                
                const response = await fetch('/posts', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    showNotification('Post created successfully!', 'success');
                    this.reset();
                    fileName.classList.add('hidden');
                    fileText.textContent = 'Choose an image or drag it here';
                    
                    // Add new post to the grid
                    prependPostToGrid(data.post);
                } else {
                    showNotification(data.message || 'Error creating post', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                showNotification('Network error. Please try again.', 'error');
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            }
        });
    }

    // Apply filters with loading state
    if (applyFiltersBtn) {
        applyFiltersBtn.addEventListener('click', function() {
            const button = this;
            const originalText = button.innerHTML;
            button.innerHTML = '🔄 Applying...';
            
            currentPage = 1;
            loadPosts(1, false);
            
            setTimeout(() => {
                button.innerHTML = '✅ Applied!';
                setTimeout(() => {
                    button.innerHTML = originalText;
                }, 1000);
            }, 1000);
        });
    }

    // Load more posts with loading state
    if (loadMoreBtn) {
        loadMoreBtn.addEventListener('click', function() {
            const button = this;
            const originalText = button.innerHTML;
            button.innerHTML = '🔄 Loading...';
            
            loadPosts(currentPage + 1, true);
            
            setTimeout(() => {
                button.innerHTML = originalText;
            }, 1500);
        });
    }

    // Load posts
    async function loadPosts(page = 1, append = false) {
        if (loading) return;
        
        loading = true;
        
        // Show loading state if not appending
        if (!append && postsGrid) {
            postsGrid.innerHTML = '<div class="col-span-full text-center py-12"><div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-purple-500"></div><p class="mt-4 text-gray-600">Loading posts...</p></div>';
        }
        
        try {
            const filters = getActiveFilters();
            const params = new URLSearchParams({
                page: page,
                per_page: 12,
                ...filters
            });

            const response = await fetch(`/api/posts?${params}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            console.log('Posts loaded:', data);

            if (data.success) {
                if (append) {
                    appendPostsToGrid(data.posts);
                } else {
                    renderPostsGrid(data.posts);
                }
                
                // Show message if no posts
                if (data.posts.length === 0) {
                    postsGrid.innerHTML = '<div class="col-span-full text-center py-12"><p class="text-gray-600 text-lg">No posts yet. Be the first to post!</p></div>';
                }
                
                // Update load more button
                if (data.pagination && data.pagination.has_more) {
                    if (loadMoreBtn) {
                        loadMoreBtn.style.display = 'block';
                    }
                    currentPage = data.pagination.current_page;
                } else {
                    if (loadMoreBtn) {
                        loadMoreBtn.style.display = 'none';
                    }
                }
            } else {
                console.error('API returned unsuccessful:', data);
                showNotification('Error loading posts', 'error');
            }
        } catch (error) {
            console.error('Error loading posts:', error);
            if (postsGrid) {
                postsGrid.innerHTML = '<div class="col-span-full text-center py-12"><p class="text-red-600 text-lg">Error loading posts. Please refresh the page.</p></div>';
            }
            showNotification('Error loading posts', 'error');
        } finally {
            loading = false;
        }
    }

    // Get current location
    function getCurrentLocation() {
        if (!navigator.geolocation) {
            showLocationStatus('Geolocation is not supported by this browser.', 'error');
            return;
        }

        showLocationStatus('Getting your location...', 'loading');
        getCurrentLocationBtn.disabled = true;
        getCurrentLocationBtn.textContent = '🔄 Getting Location...';

        navigator.geolocation.getCurrentPosition(
            function(position) {
                userLocation = {
                    latitude: position.coords.latitude,
                    longitude: position.coords.longitude
                };
                showLocationStatus(`Location found: ${userLocation.latitude.toFixed(4)}, ${userLocation.longitude.toFixed(4)}`, 'success');
                getCurrentLocationBtn.textContent = '✅ Location Set';
                getCurrentLocationBtn.disabled = false;
                
                // Auto-apply filters if distance sorting is selected
                if (sortBySelect.value === 'distance') {
                    applyFiltersBtn.click();
                }
            },
            function(error) {
                let message = 'Unable to get your location.';
                switch(error.code) {
                    case error.PERMISSION_DENIED:
                        message = 'Location access denied by user.';
                        break;
                    case error.POSITION_UNAVAILABLE:
                        message = 'Location information is unavailable.';
                        break;
                    case error.TIMEOUT:
                        message = 'Location request timed out.';
                        break;
                }
                showLocationStatus(message, 'error');
                getCurrentLocationBtn.textContent = '📍 Use My Location';
                getCurrentLocationBtn.disabled = false;
            }
        );
    }

    // Show location status
    function showLocationStatus(message, type) {
        locationStatus.textContent = message;
        locationStatus.classList.remove('hidden', 'text-green-400', 'text-red-400', 'text-yellow-400');
        
        switch(type) {
            case 'success':
                locationStatus.classList.add('text-green-400');
                break;
            case 'error':
                locationStatus.classList.add('text-red-400');
                break;
            case 'loading':
                locationStatus.classList.add('text-yellow-400');
                break;
        }
        locationStatus.classList.remove('hidden');
    }

    // Get active filters
    function getActiveFilters() {
        const filters = {};
        
        const checkedInstruments = Array.from(
            document.querySelectorAll('#instruments input[type="checkbox"]:checked')
        ).map(cb => cb.value);
        
        const checkedVenues = Array.from(
            document.querySelectorAll('#venues input[type="checkbox"]:checked')
        ).map(cb => cb.value);
        
        if (checkedInstruments.length > 0) {
            filters.instruments = checkedInstruments.join(',');
        }
        
        if (checkedVenues.length > 0) {
            filters.venues = checkedVenues.join(',');
        }

        // Add distance and sorting filters
        if (sortBySelect) {
            filters.sort_by = sortBySelect.value;
        }

        if (userLocation && sortBySelect.value === 'distance') {
            filters.user_latitude = userLocation.latitude;
            filters.user_longitude = userLocation.longitude;
            
            const maxDistance = document.getElementById('maxDistance').value;
            if (maxDistance) {
                filters.max_distance = maxDistance;
            }
        }
        
        return filters;
    }

    // Render posts grid
    function renderPostsGrid(posts) {
        if (!postsGrid) {
            console.error('Posts grid element not found');
            return;
        }
        postsGrid.innerHTML = '';
        if (posts && posts.length > 0) {
            appendPostsToGrid(posts);
        } else {
            postsGrid.innerHTML = '<div class="col-span-full text-center py-12"><p class="text-gray-600 text-lg">No posts yet. Be the first to post!</p></div>';
        }
    }

    // Append posts to grid
    function appendPostsToGrid(posts) {
        if (!postsGrid) {
            console.error('Posts grid element not found');
            return;
        }
        posts.forEach(post => {
            console.log('Creating post element for:', post.id, post);
            const postElement = createPostElement(post);
            postsGrid.appendChild(postElement);
        });
    }

    // Prepend post to grid (for new posts)
    function prependPostToGrid(post) {
        const postElement = createPostElement(post);
        postElement.style.opacity = '0';
        postsGrid.insertBefore(postElement, postsGrid.firstChild);
        
        // Animate in
        setTimeout(() => {
            postElement.style.transition = 'opacity 0.5s ease-in-out';
            postElement.style.opacity = '1';
        }, 100);
    }

    // Create post element
    function createPostElement(post) {
        const hasImage = post.image_path && post.image_path.trim() !== '';
        const userName = post.user_name || 'User';
        const userGenre = post.user_genre || '';
        const userType = post.user_type || 'member';
        const userAvatar = post.user_avatar || null;
        const createdAt = post.created_at || new Date().toISOString();

        const postDiv = document.createElement('div');
        postDiv.className = 'bg-white/80 backdrop-blur-xl rounded-3xl shadow-lg overflow-hidden hover:shadow-xl transition-all duration-300 animate-scale-in border border-gray-200';
        
        const userTypeEmoji = userType === 'musician' ? '🎵' : 
                             userType === 'business' ? '🏢' : '👤';
        
        const avatarElement = userAvatar ? 
            `<img class="w-12 h-12 rounded-full object-cover border-2 border-gray-200" src="${userAvatar}" alt="avatar" onerror="this.parentElement.innerHTML='<div class=\\'w-12 h-12 bg-gradient-to-r from-purple-400 to-pink-400 rounded-full flex items-center justify-center text-white font-bold\\'>${userName.charAt(0).toUpperCase()}</div>'">` :
            `<div class="w-12 h-12 bg-gradient-to-r from-purple-400 to-pink-400 rounded-full flex items-center justify-center text-white font-bold">${userName.charAt(0).toUpperCase()}</div>`;

        // Format date for display
        const formattedDate = new Date(createdAt).toLocaleDateString('en-US', {
            month: 'short',
            day: 'numeric',
            year: 'numeric'
        });

        const imageSection = hasImage ? `
        <img class="post-image w-full h-80 object-cover cursor-pointer hover:opacity-90 transition-opacity" 
             src="${post.image_path}" 
             alt="Post image" 
             loading="lazy"
             onerror="this.src='/images/sample-post-1.jpg'"
             data-post-id="${post.id}"
             data-image-url="${post.image_path}"
             data-user-name="${userName}"
             data-user-genre="${userGenre}"
             data-user-type="${userType}"
             data-user-avatar="${userAvatar || ''}"
             data-description="${post.description || ''}"
             data-created-at="${createdAt}"
             data-like-count="${post.like_count || 0}"
             data-comment-count="${post.comment_count || 0}"
             data-is-liked="${post.is_liked ? 'true' : 'false'}">` : '';

        postDiv.innerHTML = `
            <div class="relative">
                ${imageSection}
                <div class="absolute top-4 right-4 bg-black/50 backdrop-blur-sm text-white px-3 py-1 rounded-full text-sm">
                    ${userTypeEmoji} ${userType}
                </div>
                ${post.is_owner ? `
                    <button class="delete-post-btn absolute top-4 left-4 bg-red-500/80 hover:bg-red-600 text-white p-2 rounded-full transition-colors duration-200" 
                            data-post-id="${post.id}" 
                            title="Delete post">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                ` : ''}
            </div>
            <div class="p-6">
                <div class="flex items-center gap-4 mb-4">
                    ${avatarElement}
                    <div>
                        <h3 class="font-bold text-gray-800 text-lg">${userName}</h3>
                        <p class="text-gray-600">${userGenre}</p>
                    </div>
                </div>
                <p class="text-gray-700 mb-4 leading-relaxed">${post.description || 'No description'}</p>
                <div class="flex justify-between items-center text-gray-500 text-sm">
                    <span>${formattedDate}</span>
                    <div class="flex gap-4">
                        <button class="hover:text-red-500 transition-colors flex items-center gap-1">
                            ❤️ <span>0</span>
                        </button>
                        <button class="hover:text-blue-500 transition-colors flex items-center gap-1">
                            💬 <span>0</span>
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        return postDiv;
    }

    // Show notification
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 p-4 rounded-2xl shadow-lg backdrop-blur-xl transform translate-x-full transition-transform duration-300 ${
            type === 'success' ? 'bg-green-500/90 text-white' : 
            type === 'error' ? 'bg-red-500/90 text-white' : 
            'bg-blue-500/90 text-white'
        }`;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        // Slide in
        setTimeout(() => {
            notification.classList.remove('translate-x-full');
        }, 100);
        
        // Remove after 3 seconds
        setTimeout(() => {
            notification.classList.add('translate-x-full');
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 300);
        }, 3000);
    }

    // Initialize filter options
    async function initializeFilters() {
        const instruments = ['Guitar', 'Drums', 'Piano', 'Bass', 'Vocals', 'Violin', 'Saxophone'];
        const venues = ['Studio', 'Club', 'Theater', 'Cafe', 'Restaurant', 'Bar', 'Event Venue', 'Music Hall'];
        
        populateFilterSection('instruments', instruments);
        populateFilterSection('venues', venues);
        populateFilterSection('mobileInstruments', instruments);
        populateFilterSection('mobileVenues', venues);
    }

    function populateFilterSection(sectionId, options) {
        const container = document.getElementById(sectionId);
        if (!container) return;
        
        container.innerHTML = '';
        options.forEach(option => {
            const div = document.createElement('div');
            div.innerHTML = `
                <label class="flex items-center gap-3 text-white/80 cursor-pointer hover:text-white transition-colors">
                    <input type="checkbox" value="${option}" class="rounded border-white/30 bg-white/10 text-purple-500 focus:ring-purple-400">
                    <span>${option}</span>
                </label>
            `;
            container.appendChild(div);
        });
    }

    // Add parallax effect to floating elements
    document.addEventListener('mousemove', function(e) {
        const floating = document.querySelector('.floating-elements');
        if (floating) {
            const x = e.clientX / window.innerWidth;
            const y = e.clientY / window.innerHeight;
            floating.style.transform = `translate(${x * 20}px, ${y * 20}px)`;
        }
    });

    // Tailwind config
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    primary: '#6366f1',
                    'primary-dark': '#4f46e5',
                    'glass': 'rgba(255, 255, 255, 0.1)',
                    'glass-dark': 'rgba(0, 0, 0, 0.1)',
                    'bg-main': '#f2f4f7',
                },
                backdropBlur: {
                    xs: '2px',
                },
                animation: {
                    'float': 'float 3s ease-in-out infinite',
                    'pulse-slow': 'pulse 3s ease-in-out infinite',
                    'slide-up': 'slideUp 0.3s ease-out',
                    'fade-in': 'fadeIn 0.5s ease-out',
                    'scale-in': 'scaleIn 0.3s ease-out',
                }
            }
        }
    }


    // Filtering and sorting
    const filterDropdown = document.getElementById('filterDropdown');
    if (filterDropdown) {
        filterDropdown.addEventListener('change', function() {
            const filterValue = this.value;
            loadPosts(filterValue);
        });
    }

    async function loadPosts(filter = 'latest') {
        try {
            const response = await fetch(`/posts?filter=${filter}`);
            if (!response.ok) throw new Error('Failed to load posts');

            const html = await response.text();
            document.getElementById('postContainer').innerHTML = html;
        } catch (error) {
            console.error('Error loading posts:', error);
        }
    }
});

</script>
@vite(['resources/js/app.js', 'resources/js/socket.js'])
</body> 
</html>