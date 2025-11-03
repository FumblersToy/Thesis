                    </div>
                    
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
                                     src="{{ $currentProfileImage ?? '/images/sample-profile.jpg' }}"
                                     alt="profile">
                                
                                <div class="hidden sm:block text-left">
                                    <p class="text-gray-800 font-semibold">
                                        {{ $currentDisplayName ?? ($user->name ?? 'User') }}
                                    </p>
                                    <p class="text-gray-600 text-sm">{{ $currentRoleLabel ?? 'Member' }}</p>
                                </div>

                                <svg class="w-5 h-5 text-gray-500 group-hover:text-gray-700 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>

                            <div id="profileDropdown" class="absolute right-0 top-full mt-2 w-64 bg-white/95 backdrop-blur-xl rounded-2xl shadow-2xl overflow-hidden hidden animate-scale-in z-50 border border-gray-200">
                                <div class="p-4 border-b border-gray-200">
                                    <div class="flex items-center gap-3">
                                        <img class="w-16 h-16 rounded-full object-cover border-2 border-gray-200" src="{{ $currentProfileImage ?? '/images/sample-profile.jpg' }}" alt="profile">
                                        <div>
                                            <p class="text-gray-800 font-semibold text-lg">{{ $currentDisplayName ?? ($user->name ?? 'User') }}</p>
                                            <p class="text-gray-600">{{ $currentRoleLabel ?? 'Member' }}</p>
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
        </script>
    </body>
    </html>