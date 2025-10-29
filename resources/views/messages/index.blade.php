<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages - BandMate</title>
    @vite(['resources/css/app.css', 'resources/css/feed.css', 'resources/css/socket.css', 'resources/js/app.js', 'resources/js/socket.js', 'resources/js/messages.js'])
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen relative overflow-x-hidden gradient-bg">
    <div class="floating-elements fixed inset-0 pointer-events-none"></div>
    
    

    <!-- Header with Logo and Navigation -->
    <div class="flex min-h-screen relative z-10">
        <!-- Main Content -->
        <section class="flex-1 p-6 lg:p-8 flex flex-col">
            <!-- Header with Logo and User Profile -->
            <div class="flex justify-between items-center mb-8 animate-fade-in">
                <!-- Logo and Back Button -->
                <div class="flex items-center gap-4">
                    <a href="{{ route('feed') }}" class="flex items-center gap-3 bg-white/80 backdrop-blur-xl p-3 rounded-2xl hover:bg-white/90 shadow-lg transition-all duration-300 border border-gray-200">
                        <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        <span class="text-gray-800 font-semibold">Back to Feed</span>
                    </a>
                    
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-r from-purple-400 to-pink-400 rounded-full animate-pulse-slow flex items-center justify-center">
                            <span class="text-white font-bold text-lg">💬</span>
                        </div>
                        <h1 class="text-2xl font-bold text-white">Messages</h1>
                    </div>
                </div>
                
                <!-- User Profile Section -->
                <!-- Replace the User Profile Section in your messages index.blade.php -->

@php
    $user = Auth::user();
    $musician = $user ? \App\Models\Musician::where('user_id', $user->id)->first() : null;
    $business = $user ? \App\Models\Business::where('user_id', $user->id)->first() : null;
    $displayName = $musician?->stage_name
        ?: ($business?->business_name ?: ($user->name ?? 'User'));
    $roleLabel = $musician?->instrument ?: ($business?->venue ?: 'Member');
    
    // Fix: Use getImageUrl() helper function like in settings
    $profileImage = null;
    if ($musician && $musician->profile_picture) {
        $profileImage = getImageUrl($musician->profile_picture);
    } elseif ($business && $business->profile_picture) {
        $profileImage = getImageUrl($business->profile_picture);
    } else {
        $profileImage = '/images/sample-profile.jpg';
    }
@endphp

<div class="relative">
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
                <img class="w-16 h-16 rounded-full object-cover border-2 border-gray-200" 
                     src="{{ $profileImage }}" 
                     alt="profile">
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
            
            <a href="{{ route('map') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-100 transition-colors text-gray-700 hover:text-gray-900">
                <span class="text-lg">🗺️</span>
                Map
            </a>

            <a href="#" class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-100 transition-colors text-gray-700 hover:text-gray-900">
                <span class="text-lg">🎵</span>
                My Music
            </a>
            
            <a href="{{ route('messages.index') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-100 transition-colors text-gray-700 hover:text-gray-900 bg-blue-50">
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

            <!-- Messages Container -->
            <div class="flex-1 glass-effect backdrop-blur-xl rounded-3xl shadow-2xl border border-white/20 overflow-hidden">
                <div class="flex h-full">
                    <!-- Conversations Sidebar -->
                    <div class="w-1/3 bg-white/10 backdrop-blur-xl border-r border-white/20 flex flex-col">
                        <!-- Sidebar Header -->
                        <div class="p-6 border-b border-white/20">
                            <div class="flex items-center justify-between mb-4">
                                <h2 class="text-xl font-bold text-white">Conversations</h2>
                                <button id="newMessageBtn" class="p-3 bg-gradient-to-r from-purple-500 to-pink-500 text-white rounded-2xl hover:from-purple-600 hover:to-pink-600 transition-all duration-300 shadow-lg hover-glow">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                </button>
                            </div>
                            
                            <!-- Search -->
                            <div class="relative">
                                <input type="text" id="searchUsers" placeholder="Search users..." 
                                       class="w-full px-4 py-3 border border-white/20 rounded-2xl focus:outline-none focus:ring-2 focus:ring-white/30 focus:border-white/30 bg-white/10 backdrop-blur-xl placeholder-white/70 text-white">
                                <div id="searchResults" class="absolute top-full left-0 right-0 bg-white/95 backdrop-blur-xl border border-white/20 rounded-2xl shadow-lg mt-1 hidden z-10">
                                    <!-- Search results will be populated here -->
                                </div>
                            </div>
                        </div>

                        <!-- Conversations List -->
                        <div id="conversationsList" class="flex-1 overflow-y-auto p-2">
                            <!-- Conversations will be populated here -->
                        </div>
                    </div>

                    <!-- Chat Area -->
                    <div class="flex-1 flex flex-col bg-white/5 backdrop-blur-xl">
                        <!-- Chat Header -->
                        <div id="chatHeader" class="p-6 border-b border-white/20 bg-white/10 backdrop-blur-xl hidden">
                            <div class="flex items-center gap-4">
                                <div id="chatUserAvatar" class="w-12 h-12 rounded-full bg-gradient-to-r from-purple-400 to-pink-400 flex items-center justify-center text-white font-bold">
                                    <!-- Avatar will be populated here -->
                                </div>
                                <div>
                                    <h3 id="chatUserName" class="text-lg font-semibold text-white">User Name</h3>
                                    <p id="chatUserStatus" class="text-sm text-white/70">Online</p>
                                </div>
                            </div>
                        </div>

                        <!-- Messages Area -->
                        <div id="messagesArea" class="flex-1 overflow-y-auto p-6 hidden">
                            <div id="messagesContainer" class="space-y-4">
                                <!-- Messages will be populated here -->
                            </div>
                        </div>

                        <!-- Message Input -->
                        <div id="messageInput" class="p-6 border-t border-white/20 bg-white/10 backdrop-blur-xl hidden">
                            <div class="flex gap-4">
                                <input type="text" id="messageText" placeholder="Type a message..." 
                                       class="flex-1 px-4 py-3 border border-white/20 rounded-2xl focus:outline-none focus:ring-2 focus:ring-white/30 focus:border-white/30 bg-white/10 backdrop-blur-xl placeholder-white/70 text-white">
                                <button id="sendMessageBtn" class="px-6 py-3 bg-gradient-to-r from-purple-500 to-pink-500 text-white rounded-2xl hover:from-purple-600 hover:to-pink-600 transition-all duration-300 shadow-lg hover-glow font-semibold">
                                    Send
                                </button>
                            </div>
                        </div>

                        <!-- Empty State -->
                        <div id="emptyState" class="flex-1 flex items-center justify-center p-6">
                            <div class="text-center text-white/70">
                                <div class="w-24 h-24 mx-auto mb-6 bg-gradient-to-r from-purple-400 to-pink-400 rounded-full flex items-center justify-center">
                                    <span class="text-4xl">💬</span>
                                </div>
                                <h3 class="text-2xl font-bold mb-2 text-white">Start a Conversation</h3>
                                <p class="text-lg mb-6">Select a conversation from the sidebar or search for a user to begin messaging.</p>
                                <button id="startNewChatBtn" class="px-8 py-4 bg-gradient-to-r from-purple-500 to-pink-500 text-white rounded-2xl hover:from-purple-600 hover:to-pink-600 transition-all duration-300 shadow-lg hover-glow font-semibold text-lg">
                                    Start New Chat
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Socket.IO Initialization Script -->
    <script>
    // Set current user ID for JS
    window.currentUserId = {{ Auth::id() ?? 'null' }};
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

            // Profile dropdown functionality
            const profileButton = document.getElementById('profileButton');
            const profileDropdown = document.getElementById('profileDropdown');
            profileButton.addEventListener('click', function(e) {
                e.stopPropagation();
                profileDropdown.classList.toggle('hidden');
            });
            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!profileButton.contains(e.target) && !profileDropdown.contains(e.target)) {
                    profileDropdown.classList.add('hidden');
                }
            });

            // Start new chat button
            const startNewChatBtn = document.getElementById('startNewChatBtn');
            const newMessageBtn = document.getElementById('newMessageBtn');
            function showUserSearch() {
                const searchInput = document.getElementById('searchUsers');
                searchInput.focus();
                searchInput.click();
            }
            startNewChatBtn.addEventListener('click', showUserSearch);
            newMessageBtn.addEventListener('click', showUserSearch);

            // Auto-open chat if ?user=ID is present in URL
            const urlParams = new URLSearchParams(window.location.search);
            const targetUserId = urlParams.get('user');
            if (targetUserId) {
                // Wait for conversations to load, then open chat
                const tryOpenChat = () => {
                    if (typeof window.openChat === 'function') {
                        window.openChat(parseInt(targetUserId));
                    } else {
                        setTimeout(tryOpenChat, 200);
                    }
                };
                tryOpenChat();
            }
        });
    </script>
</body>
</html>