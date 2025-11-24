<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>My Music - Gigsly</title>
    @vite(['resources/css/app.css'])
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
    </style>
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen">
        <!-- Navigation -->
        <nav class="bg-white/10 backdrop-blur-xl border-b border-white/20 sticky top-0 z-40">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-20">
                    <a href="{{ route('feed') }}">
                        <img src="{{ asset('assets/logo_black.png') }}" alt="Bandmate Logo" class="h-10 md:hidden cursor-pointer hover:opacity-90 transition-opacity">
                        <img src="{{ asset('assets/logo_both.png') }}" alt="Bandmate Logo" class="h-12 hidden md:block cursor-pointer hover:opacity-90 transition-opacity">
                    </a>

                    <div class="flex items-center gap-4">
                        <button id="profileBtn" class="flex items-center gap-3 bg-white/20 hover:bg-white/30 backdrop-blur-xl px-4 py-2 rounded-full transition-all duration-300">
                            <img class="w-10 h-10 rounded-full object-cover border-2 border-white" src="{{ Auth::user()->musician->profile_picture ?? 'https://via.placeholder.com/150' }}" alt="profile">
                            <span class="text-white font-medium hidden sm:inline">{{ Auth::user()->musician->artist_name ?? 'User' }}</span>
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <div id="profileDropdown" class="absolute right-4 top-24 w-64 bg-white/95 backdrop-blur-xl rounded-2xl shadow-2xl overflow-hidden hidden animate-scale-in z-50 border border-gray-200">
                            <div class="p-4 border-b border-gray-200">
                                <div class="flex items-center gap-3">
                                    <img class="w-16 h-16 rounded-full object-cover border-2 border-gray-200" src="{{ Auth::user()->musician->profile_picture ?? 'https://via.placeholder.com/150' }}" alt="profile">
                                    <div>
                                        <p class="text-gray-800 font-semibold text-lg">{{ Auth::user()->musician->artist_name ?? 'User' }}</p>
                                        <p class="text-gray-600">Musician</p>
                                    </div>
                                </div>
                            </div>

                            <div class="p-2">
                                <a href="{{ route('profile.show', Auth::id()) }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-100 transition-colors text-black hover:text-gray-900">
                                    <span class="text-lg">👤</span>
                                    View Profile
                                </a>

                                <button id="notificationsBtn" class="w-full flex items-center gap-3 p-3 rounded-xl hover:bg-gray-100 transition-colors text-gray-700 hover:text-gray-900 relative">
                                    <span class="text-lg">🔔</span>
                                    Notifications
                                    <span id="notificationBadge" class="hidden absolute top-2 left-6 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">0</span>
                                </button>

                                @if(Auth::user()->musician)
                                <a href="{{ route('music.index') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-100 transition-colors text-gray-700 hover:text-gray-900">
                                    <span class="text-lg">🎵</span>
                                    My Music
                                </a>
                                @endif

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
            </div>
        </nav>

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

        <!-- Main Content -->
        <div class="max-w-5xl mx-auto px-4 py-8">
            <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-lg p-8 mb-8">
                <h2 class="text-3xl font-bold mb-6 text-gray-800 flex items-center gap-3">
                    🎵 My Music Library
                </h2>

                <!-- Upload Form -->
                <div class="bg-gradient-to-r from-purple-50 to-blue-50 rounded-2xl p-6 mb-8 border border-purple-200">
                    <h3 class="text-xl font-semibold mb-4 text-gray-800">Upload New Track</h3>
                    <form id="uploadMusicForm" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        <div>
                            <label for="title" class="block text-gray-700 font-medium mb-2">Track Title</label>
                            <input type="text" id="title" name="title" required class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-purple-500 focus:ring-2 focus:ring-purple-200 transition-all" placeholder="Enter track title">
                        </div>

                        <div>
                            <label for="audio" class="block text-gray-700 font-medium mb-2">Audio File (MP3, WAV, OGG, M4A - Max 20MB)</label>
                            <input type="file" id="audio" name="audio" accept=".mp3,.wav,.ogg,.m4a" required class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-purple-500 transition-all">
                        </div>

                        <button type="submit" id="uploadBtn" class="w-full bg-gradient-to-r from-purple-600 to-blue-600 text-white px-6 py-3 rounded-xl font-semibold hover:from-purple-700 hover:to-blue-700 transition-all shadow-lg hover:shadow-xl">
                            Upload Track
                        </button>
                    </form>

                    <div id="uploadProgress" class="hidden mt-4">
                        <div class="bg-white rounded-full h-3 overflow-hidden">
                            <div id="progressBar" class="bg-gradient-to-r from-purple-600 to-blue-600 h-full transition-all duration-300" style="width: 0%"></div>
                        </div>
                        <p class="text-center text-sm text-gray-600 mt-2">Uploading...</p>
                    </div>
                </div>

                <!-- Music List -->
                <div id="musicList" class="space-y-4">
                    @forelse($musicTracks as $track)
                    <div class="bg-white rounded-2xl p-6 shadow-md hover:shadow-lg transition-all border border-gray-200" data-track-id="{{ $track->id }}">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-4 flex-1">
                                <div class="w-12 h-12 bg-gradient-to-r from-purple-600 to-blue-600 rounded-full flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M18 3a1 1 0 00-1.196-.98l-10 2A1 1 0 006 5v9.114A4.369 4.369 0 005 14c-1.657 0-3 .895-3 2s1.343 2 3 2 3-.895 3-2V7.82l8-1.6v5.894A4.37 4.37 0 0015 12c-1.657 0-3 .895-3 2s1.343 2 3 2 3-.895 3-2V3z"></path>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-semibold text-gray-800 text-lg">{{ $track->title }}</h4>
                                    <p class="text-sm text-gray-500">{{ $track->created_at->format('M d, Y') }} • {{ $track->duration ? gmdate('i:s', $track->duration) : '--:--' }}</p>
                                </div>
                            </div>

                            <div class="flex items-center gap-3">
                                <audio controls class="max-w-xs">
                                    <source src="{{ $track->audio_url }}" type="audio/mpeg">
                                    Your browser does not support the audio element.
                                </audio>
                                
                                <button onclick="deleteTrack({{ $track->id }})" class="p-2 text-red-500 hover:bg-red-50 rounded-full transition-colors">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-12 text-gray-500">
                        <svg class="w-24 h-24 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"></path>
                        </svg>
                        <p class="text-lg font-medium">No tracks uploaded yet</p>
                        <p class="text-sm">Upload your first track to get started!</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <script>
        // Profile dropdown toggle
        const profileBtn = document.getElementById('profileBtn');
        const profileDropdown = document.getElementById('profileDropdown');

        profileBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            profileDropdown.classList.toggle('hidden');
        });

        document.addEventListener('click', function(e) {
            if (!profileDropdown.contains(e.target) && !profileBtn.contains(e.target)) {
                profileDropdown.classList.add('hidden');
            }
        });

        // Notifications Modal functionality
        const notificationsBtn = document.getElementById('notificationsBtn');
        const notificationsModal = document.getElementById('notificationsModal');
        const closeNotificationsModal = document.getElementById('closeNotificationsModal');
        const notificationBadge = document.getElementById('notificationBadge');
        
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
                            <div class="${bgColor} p-4 rounded-xl hover:shadow-md transition-all mb-3 border border-gray-100 cursor-pointer" onclick="window.location.href='/feed#post-${notif.post_id}'; markNotificationAsReadRedirect(${notif.id})">
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
        
        // Mark notification as read and redirect to feed
        window.markNotificationAsReadRedirect = async function(notificationId) {
            try {
                await fetch(`/api/notifications/${notificationId}/read`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
            } catch (error) {
                console.error('Error marking notification as read:', error);
            }
        };
        
        // Load notification count on page load
        loadNotifications();

        // Upload form
        const uploadForm = document.getElementById('uploadMusicForm');
        const uploadProgress = document.getElementById('uploadProgress');
        const progressBar = document.getElementById('progressBar');
        const uploadBtn = document.getElementById('uploadBtn');

        uploadForm.addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(uploadForm);
            
            uploadBtn.disabled = true;
            uploadBtn.textContent = 'Uploading...';
            uploadProgress.classList.remove('hidden');

            try {
                const response = await fetch('{{ route('music.store') }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    }
                });

                const data = await response.json();

                if (data.success) {
                    progressBar.style.width = '100%';
                    
                    setTimeout(() => {
                        location.reload();
                    }, 500);
                } else {
                    alert('Upload failed: ' + (data.message || 'Unknown error'));
                    uploadBtn.disabled = false;
                    uploadBtn.textContent = 'Upload Track';
                    uploadProgress.classList.add('hidden');
                    progressBar.style.width = '0%';
                }
            } catch (error) {
                console.error('Upload error:', error);
                alert('Upload failed. Please try again.');
                uploadBtn.disabled = false;
                uploadBtn.textContent = 'Upload Track';
                uploadProgress.classList.add('hidden');
                progressBar.style.width = '0%';
            }
        });

        // Delete track
        window.deleteTrack = async function(trackId) {
            if (!confirm('Are you sure you want to delete this track?')) {
                return;
            }

            try {
                const response = await fetch(`/music/${trackId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    }
                });

                const data = await response.json();

                if (data.success) {
                    document.querySelector(`[data-track-id="${trackId}"]`).remove();
                    
                    // Check if there are no more tracks
                    const musicList = document.getElementById('musicList');
                    if (musicList.querySelectorAll('[data-track-id]').length === 0) {
                        location.reload();
                    }
                } else {
                    alert('Failed to delete track: ' + (data.message || 'Unknown error'));
                }
            } catch (error) {
                console.error('Delete error:', error);
                alert('Failed to delete track. Please try again.');
            }
        };
    </script>

    @vite(['resources/js/app.js'])
</body>
</html>
