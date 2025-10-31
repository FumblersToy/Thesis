document.addEventListener('DOMContentLoaded', function() {
    console.log('Feed page loaded');
    
    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
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
                     data-created-at="${createdAt}">` : '';

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

    // Delete post functionality
    document.addEventListener('click', function(e) {
        if (e.target.closest('.delete-post-btn')) {
            e.preventDefault();
            const button = e.target.closest('.delete-post-btn');
            const postId = button.getAttribute('data-post-id');
            
            showDeleteConfirmation(postId, button);
        }
    });

    function showImageModal(post) {
        console.log('🖼 Showing modal for post:', post);
        let modal = document.getElementById('imageModal');

        if (!modal) {
            modal = document.createElement('div');
            modal.id = 'imageModal';
            modal.className = 'fixed inset-0 bg-black/70 backdrop-blur-md flex items-center justify-center z-50 hidden';
            modal.innerHTML = `
                <div class="bg-white rounded-2xl shadow-xl w-[90%] max-w-3xl p-6 relative">
                    <button id="closeModalBtn" class="absolute top-3 right-3 text-gray-600 hover:text-black text-2xl">&times;</button>
                    <img id="modalImage" src="" class="w-full rounded-lg mb-4 object-cover max-h-[60vh]" alt="Post image">
                    <div class="flex justify-between text-gray-700 mb-2">
                        <span id="modalUserName"></span>
                        <span id="modalCreatedAt" class="text-sm text-gray-500"></span>
                    </div>
                    <p id="modalDescription" class="text-gray-800 mb-4"></p>
                    <div class="flex justify-between items-center text-gray-500 text-sm mb-3">
                        <button id="likeButton" class="hover:text-red-500 flex items-center gap-1">❤️ <span id="likeCount">0</span></button>
                        <button id="commentToggle" class="hover:text-blue-500 flex items-center gap-1">💬 <span id="commentCount">0</span></button>
                    </div>
                    <div id="commentSection" class="hidden">
                        <div id="commentsList" class="space-y-2 mb-3 max-h-40 overflow-y-auto"></div>
                        <input id="commentInput" type="text" placeholder="Add a comment..." class="w-full border rounded-xl px-3 py-2 text-sm">
                    </div>
                </div>`;
            document.body.appendChild(modal);
        }

        // Fill modal data
        document.getElementById('modalImage').src = post.imageUrl;
        document.getElementById('modalUserName').textContent = post.userName;
        document.getElementById('modalCreatedAt').textContent = post.createdAt;
        document.getElementById('modalDescription').textContent = post.description;
        document.getElementById('likeCount').textContent = post.likeCount;
        document.getElementById('commentCount').textContent = post.commentCount;

        modal.classList.remove('hidden');

        // Load comments
        loadComments(post.id);

        // Like button toggle
        document.getElementById('likeButton').onclick = () => toggleLike(post.id);

        // Comment section toggle
        document.getElementById('commentToggle').onclick = () => {
            document.getElementById('commentSection').classList.toggle('hidden');
        };

        // Add comment input
        const commentInput = document.getElementById('commentInput');
        commentInput.onkeydown = (e) => {
            if (e.key === 'Enter' && commentInput.value.trim() !== '') {
                addComment(post.id, commentInput.value.trim());
                commentInput.value = '';
            }
        };
    }

    document.addEventListener('click', e => {
        if (e.target.id === 'closeModalBtn' || e.target.id === 'imageModal') {
            document.getElementById('imageModal')?.classList.add('hidden');
        }
    });

    // ================================================
    // COMMENT + LIKE LOGIC
    // ================================================

    async function loadComments(postId) {
        console.log(`💬 Loading comments for post ${postId}`);
        const commentsList = document.getElementById('commentsList');
        commentsList.innerHTML = `<p class="text-gray-400 text-sm">Loading...</p>`;
        try {
            const response = await fetch(`/posts/${postId}/comments`);
            const comments = await response.json();
            commentsList.innerHTML = '';
            comments.forEach(addCommentToModal);
            console.log(`✅ Loaded ${comments.length} comments`);
        } catch (error) {
            console.error('⚠️ Error loading comments:', error);
            commentsList.innerHTML = `<p class="text-red-500 text-sm">Failed to load comments</p>`;
        }
    }

    function addCommentToModal(comment) {
        const commentsList = document.getElementById('commentsList');
        const div = document.createElement('div');
        div.className = 'bg-gray-100 p-2 rounded-lg';
        div.innerHTML = `<strong>${comment.user_name}</strong>: ${comment.text}`;
        commentsList.appendChild(div);
    }

    async function addComment(postId, commentText) {
        console.log(`➕ Adding comment to post ${postId}: "${commentText}"`);
        try {
            const response = await fetch(`/comments`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ post_id: postId, text: commentText })
            });

            if (!response.ok) throw new Error('Failed to add comment');
            const newComment = await response.json();
            addCommentToModal(newComment);
            console.log('✅ Comment added');
        } catch (error) {
            console.error('⚠️ Error adding comment:', error);
        }
    }

    async function toggleLike(postId) {
        console.log(`❤️ Toggling like for post ${postId}`);
        try {
            const response = await fetch(`/posts/${postId}/like`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });
            const result = await response.json();
            document.getElementById('likeCount').textContent = result.likeCount;
            console.log('✅ Like updated:', result);
        } catch (error) {
            console.error('⚠️ Error toggling like:', error);
        }
    }

    // ================================================
    // IMAGE CLICK HANDLER FOR MODAL OPEN
    // ================================================

    document.querySelectorAll('.post-image').forEach(img => {
        img.addEventListener('click', () => {
            const postData = {
                id: img.dataset.postId,
                imageUrl: img.dataset.imageUrl,
                userName: img.dataset.userName,
                description: img.dataset.description,
                createdAt: img.dataset.createdAt,
                likeCount: img.dataset.likeCount,
                commentCount: img.dataset.commentCount
            };
            showImageModal(postData);
        });
    });

    console.log('✅ Modal + comment system loaded');

});