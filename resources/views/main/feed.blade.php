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

        <!-- Mobile Sidebar -->
        <aside id="mobileMenu" class="fixed inset-y-0 left-0 z-40 w-80 glass-effect backdrop-blur-xl p-6 transform -translate-x-full lg:hidden transition-transform duration-300 gradient-bg">
            <!-- Static mobile sidebar content -->
            <h3 class="text-white font-semibold mb-4">Filters</h3>
            <div class="text-white">Instruments & venues (static)</div>
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
                }
            });

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

                modal.innerHTML = `
                    <div class="flex flex-col md:flex-row h-full max-h-[80vh]">
                        <div class="md:w-1/2 bg-black flex items-center justify-center">
                            <img src="${postData.imageUrl || '/images/sample-post-1.jpg'}" alt="post image" class="object-contain max-h-[80vh] w-full h-full">
                        </div>
                        <div class="md:w-1/2 p-6 flex flex-col">
                            <div class="flex items-center gap-4 mb-4">
                                ${avatarHtml}
                                <div>
                                    <div class="font-bold text-gray-800">${postData.userName || 'User'} ${userTypeEmoji}</div>
                                    <div class="text-gray-500 text-sm">${postData.userGenre || ''}</div>
                                </div>
                                <div class="ml-auto text-gray-400 text-sm">${new Date(postData.createdAt).toLocaleString()}</div>
                            </div>

                            <div class="flex-1 overflow-auto mb-4">
                                <p class="text-gray-700 leading-relaxed">${postData.description || ''}</p>

                                <div class="mt-6 border-t pt-4">
                                    <div class="flex items-center gap-4 text-sm text-gray-600">
                                        <button class="like-btn inline-flex items-center gap-2 px-3 py-2 bg-white/90 rounded-xl border" data-liked="${postData.is_liked}" aria-pressed="${postData.is_liked}">
                                            <span class="like-emoji">${postData.is_liked ? '❤️' : '🤍'}</span>
                                            <span class="like-count">${postData.like_count}</span>
                                        </button>
                                        <div class="flex items-center gap-2">
                                            <span>💬</span>
                                            <span class="comment-count">${postData.comment_count}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4 comments-section">
                                    <div class="comments-list space-y-3 max-h-40 overflow-auto"></div>
                                </div>
                            </div>

                            <div class="pt-4 border-t">
                                <div class="flex gap-2">
                                    <input type="text" class="comment-input flex-1 px-3 py-2 border rounded-xl" placeholder="Write a comment...">
                                    <button class="comment-submit-btn px-4 py-2 bg-blue-600 text-white rounded-xl">Send</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button class="close-modal absolute top-4 right-4 bg-black/50 hover:bg-black/70 text-white p-2 rounded-full">✕</button>
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

            async function toggleLike(likeBtn, postId) {
                if (!postId) return;
                // sample posts may have sample- prefix
                if (postId.startsWith && postId.startsWith('sample-')) {
                    alert('Like available only for real posts.');
                    return;
                }
                const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const isLiked = likeBtn.getAttribute('data-liked') === 'true';
                try {
                    const res = await fetch(`/posts/${postId}/like`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
                        body: JSON.stringify({})
                    });
                    const data = await res.json();
                    if (data && data.success) {
                        const countEl = likeBtn.querySelector('.like-count');
                        const emoji = likeBtn.querySelector('.like-emoji');
                        const newCount = data.like_count ?? (parseInt(countEl.textContent||'0') + (isLiked ? -1 : 1));
                        countEl.textContent = newCount;
                        likeBtn.setAttribute('data-liked', (!isLiked).toString());
                        emoji.textContent = (!isLiked) ? '❤️' : '🤍';
                    }
                } catch (err) {
                    console.error('Like error', err);
                }
            }

            async function addComment(postId, content, commentInput, modal) {
                if (!postId) return;
                if (postId.startsWith && postId.startsWith('sample-')) {
                    alert('Commenting available only for real posts.');
                    return;
                }
                const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                try {
                    const res = await fetch(`/posts/${postId}/comments`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
                        body: JSON.stringify({ content })
                    });
                    const data = await res.json();
                    if (data && data.success) {
                        addCommentToModal(data.comment, modal);
                        const commentCountEl = modal.querySelector('.comment-count');
                        if (commentCountEl) commentCountEl.textContent = (parseInt(commentCountEl.textContent||'0')+1).toString();
                        commentInput.value = '';
                    }
                } catch (err) { console.error('Comment error', err); }
            }

            async function loadComments(postId, modal) {
                const commentsList = modal.querySelector('.comments-list');
                if (!commentsList) return;
                commentsList.innerHTML = '<div class="text-sm text-gray-500">Loading comments...</div>';
                if (postId.startsWith && postId.startsWith('sample-')) {
                    commentsList.innerHTML = '<div class="text-sm text-gray-500">No comments for sample posts.</div>';
                    return;
                }
                try {
                    const res = await fetch(`/posts/${postId}/comments`, { headers: { 'Accept': 'application/json' } });
                    const data = await res.json();
                    commentsList.innerHTML = '';
                    if (Array.isArray(data.comments)) {
                        data.comments.forEach(c => addCommentToModal(c, modal));
                    }
                } catch (err) { commentsList.innerHTML = '<div class="text-sm text-gray-500">Failed to load comments.</div>'; }
            }

            function addCommentToModal(comment, modal) {
                const commentsList = modal.querySelector('.comments-list');
                if (!commentsList) return;
                const el = document.createElement('div');
                el.className = 'flex gap-3 p-3 bg-gray-50 rounded-lg';
                const avatar = comment.user_avatar ? `<img class="w-8 h-8 rounded-full object-cover" src="${comment.user_avatar}"/>` : `<div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center">${(comment.user_name||'U').charAt(0).toUpperCase()}</div>`;
                el.innerHTML = `
                    <div class="flex-shrink-0">${avatar}</div>
                    <div>
                        <div class="font-semibold text-sm">${comment.user_name || 'Unknown'}</div>
                        <div class="text-sm text-gray-600">${comment.content}</div>
                    </div>
                `;
                commentsList.appendChild(el);
            }
        });
    </script>

    @vite(['resources/js/app.js', 'resources/js/feed.js', 'resources/js/socket.js'])
</body> 
</html>