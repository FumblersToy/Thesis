try {
function initFeed() {
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

    // Initialize the page early so posts are fetched before any later runtime errors
    try {
        console.log('Initializing filters and loading posts (early)...');
        initializeFilters();
        loadPosts(1, false);
    } catch (e) {
        console.warn('Early initialization failed:', e);
    }

    // Mobile menu functionality — clone desktop filters into mobile menu on first open
    let mobileFiltersInitialized = false;
    if (mobileMenuButton && mobileMenu) {
        mobileMenuButton.addEventListener('click', function() {
            mobileMenu.classList.toggle('-translate-x-full');

            // If opening (menu moved into view), initialize mobile filters
            const isOpen = !mobileMenu.classList.contains('-translate-x-full');
            // Hide the mobile menu button itself when the menu is open so the close button can appear above it
            if (isOpen) {
                mobileMenuButton.classList.add('hidden');
            } else {
                mobileMenuButton.classList.remove('hidden');
            }
            if (isOpen && !mobileFiltersInitialized) {
                try {
                    const desktopFilters = document.getElementById('filters');
                    if (desktopFilters) {
                        const cloneWrapper = document.createElement('div');
                        cloneWrapper.id = 'mobileFilters';
                        cloneWrapper.className = 'space-y-6 overflow-y-auto';
                        // clone desktop filters HTML
                        cloneWrapper.innerHTML = desktopFilters.innerHTML;

                        // Fix duplicated IDs inside the clone (applyFilters)
                        const mobileApply = cloneWrapper.querySelector('#applyFilters');
                        if (mobileApply) {
                            // prevent duplicate id conflict
                            mobileApply.id = 'applyFiltersMobile';
                            mobileApply.addEventListener('click', function(e) {
                                e.preventDefault();
                                if (applyFiltersBtn) applyFiltersBtn.click();
                                // close mobile menu after applying
                                mobileMenu.classList.add('-translate-x-full');
                            });
                        } else {
                            // Add a fallback apply button
                            const btn = document.createElement('button');
                            btn.type = 'button';
                            btn.id = 'applyFiltersMobile';
                            btn.className = 'w-full px-6 py-4 bg-gradient-to-r from-purple-500 to-pink-500 text-white rounded-2xl font-semibold';
                            btn.textContent = 'Apply Filters ✨';
                            btn.addEventListener('click', function() {
                                if (applyFiltersBtn) applyFiltersBtn.click();
                                mobileMenu.classList.add('-translate-x-full');
                            });
                            cloneWrapper.appendChild(btn);
                        }

                        // Replace mobile menu content
                        mobileMenu.innerHTML = '';
                        const title = document.createElement('h3');
                        title.className = 'text-white font-semibold mb-4';
                        title.textContent = 'Filters';
                        mobileMenu.appendChild(title);
                        mobileMenu.appendChild(cloneWrapper);
                        mobileFiltersInitialized = true;
                    }
                } catch (err) {
                    console.warn('Failed to initialize mobile filters:', err);
                }
            }
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
            // Ensure the mobile menu button is visible again after closing
            try { mobileMenuButton.classList.remove('hidden'); } catch (err) {}
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
   // Load posts (with random option)
        async function loadPosts(page = 1, append = false, useRandom = false) {
            if (loading) return;
            
            loading = true;
            
            // Show loading state if not appending
            if (!append && postsGrid) {
                postsGrid.innerHTML = '<div class="col-span-full text-center py-12"><div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-purple-500"></div><p class="mt-4 text-gray-600">Loading posts...</p></div>';
            }
            
            try {
                const filters = getActiveFilters();
                
                // Use random endpoint ONLY on first load with no filters
                let url;
                if (useRandom && page === 1 && Object.keys(filters).length === 0) {
                    url = `/api/posts/random?count=12`;
                } else {
                    const params = new URLSearchParams({ page: page, per_page: 12, ...filters });
                    url = Object.keys(filters).length === 0 
                        ? `/api/posts?page=${page}&per_page=12`
                        : `/api/posts?${params.toString()}`;
                }

                console.log('Fetching posts from URL:', url);
                let response = await fetch(url, {
                    credentials: 'same-origin',
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
            document.querySelectorAll('#instruments input[type="checkbox"]:checked, #mobileFilters #instruments input[type="checkbox"]:checked')
        ).map(cb => cb.value);
        
        const checkedVenues = Array.from(
            document.querySelectorAll('#venues input[type="checkbox"]:checked, #mobileFilters #venues input[type="checkbox"]:checked')
        ).map(cb => cb.value);
        
        if (checkedInstruments.length > 0) {
            filters.instruments = checkedInstruments.join(',');
        }
        
        if (checkedVenues.length > 0) {
            filters.venues = checkedVenues.join(',');
        }

        const sortVal = (sortBySelect && sortBySelect.value) || (document.querySelector('#mobileFilters #sortBy')?.value);
        if (sortVal) {
            filters.sort_by = sortVal;
        }

        const usingDistance = sortVal === 'distance' || (sortBySelect && sortBySelect.value === 'distance');
        if (userLocation && usingDistance) {
            filters.user_latitude = userLocation.latitude;
            filters.user_longitude = userLocation.longitude;

            const maxDistanceEl = document.getElementById('maxDistance') || document.querySelector('#mobileFilters #maxDistance');
            const maxDistance = maxDistanceEl ? maxDistanceEl.value : '';
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
        const hasMedia = post.image_path && post.image_path.trim() !== '';
        const mediaType = post.media_type || 'image';
        const isVideo = mediaType === 'video';
        const userName = post.user_name || 'User';
    const userGenre = post.user_genre || '';
    const userLocation = post.user_location || post.user_city || '';
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

    // Ensure like/comment counts and liked state are present so modal can display them
    const likeCountAttr = post.like_count || post.likes_count || 0;
    const commentCountAttr = post.comment_count || post.comments_count || 0;
    const isLikedAttr = post.is_liked ? 'true' : 'false';

    const mediaSection = hasMedia ? (isVideo ? `
        <video class="post-image w-full h-80 object-cover cursor-pointer hover:opacity-90 transition-opacity" 
               controls
               loading="lazy"
               data-post-id="${post.id}"
               data-image-url="${post.image_path}"
               data-media-type="video"
               data-user-name="${userName}"
               data-user-genre="${userGenre}"
               data-user-location="${userLocation}"
               data-user-type="${userType}"
               data-user-avatar="${userAvatar || ''}"
               data-description="${post.description || ''}"
               data-created-at="${createdAt}"
               data-like-count="${likeCountAttr}"
               data-comment-count="${commentCountAttr}"
               data-is-liked="${isLikedAttr}">
            <source src="${post.image_path}" type="video/mp4">
            Your browser does not support the video tag.
        </video>` : `
        <img class="post-image w-full h-80 object-cover cursor-pointer hover:opacity-90 transition-opacity" 
             src="${post.image_path}" 
             alt="Post image" 
             loading="lazy"
             onerror="this.src='/images/sample-post-1.jpg'"
             data-post-id="${post.id}"
             data-image-url="${post.image_path}"
             data-media-type="image"
             data-user-name="${userName}"
             data-user-genre="${userGenre}"
             data-user-location="${userLocation}"
             data-user-type="${userType}"
             data-user-avatar="${userAvatar || ''}"
             data-description="${post.description || ''}"
             data-created-at="${createdAt}"
             data-like-count="${likeCountAttr}"
             data-comment-count="${commentCountAttr}"
             data-is-liked="${isLikedAttr}">`) : '';

        postDiv.innerHTML = `
            <div class="relative">
                ${mediaSection}
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
                        <p class="text-gray-600">${[userGenre, userLocation].filter(Boolean).join(' · ')}</p>
                    </div>
                </div>
                <p class="text-gray-700 mb-4 leading-relaxed">${post.description || 'No description'}</p>
                <div class="flex justify-between items-center text-gray-500 text-sm">
                    <span>${formattedDate}</span>
                    <div class="flex gap-4">
                        <!-- Like/comment icons removed from preview; available in modal only -->
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

    // Tailwind config - guard in case `tailwind` global is not available in the runtime
    try {
        if (typeof window !== 'undefined' && typeof window.tailwind !== 'undefined') {
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
        } else {
            // tailwind not present — skip runtime config
            console.debug('tailwind not present; skipping runtime tailwind.config assignment');
        }
    } catch (e) {
        console.warn('Skipping tailwind.config assignment due to error:', e);
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

    // Image modal functionality
    // Modal functionality has been moved inline into the Blade template
    // (resources/views/main/feed.blade.php) to match profile.blade.php and
    // avoid duplicate implementations. Do not add global modal handlers here.

    // Custom confirmation modal
    function showDeleteConfirmation(postId, buttonElement) {
        // Create modal overlay
        const overlay = document.createElement('div');
        overlay.className = 'fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4';
        overlay.style.opacity = '0';
        overlay.style.transition = 'opacity 0.3s ease-out';
        
        // Create modal content
        const modal = document.createElement('div');
        modal.className = 'bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 transform scale-95 transition-transform duration-300';
        
        modal.innerHTML = `
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                    <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 19.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Delete Post</h3>
                <p class="text-sm text-gray-500 mb-6">Are you sure you want to delete this post? This action cannot be undone.</p>
                <div class="flex gap-3 justify-center">
                    <button class="cancel-delete px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                        Cancel
                    </button>
                    <button class="confirm-delete px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors">
                        Delete
                    </button>
                </div>
            </div>
        `;
        
        overlay.appendChild(modal);
        document.body.appendChild(overlay);
        
        // Animate in
        setTimeout(() => {
            overlay.style.opacity = '1';
            modal.style.transform = 'scale(1)';
        }, 10);
        
        // Handle button clicks
        const cancelBtn = modal.querySelector('.cancel-delete');
        const confirmBtn = modal.querySelector('.confirm-delete');
        
        const closeModal = () => {
            overlay.style.opacity = '0';
            modal.style.transform = 'scale(0.95)';
            setTimeout(() => {
                document.body.removeChild(overlay);
            }, 300);
        };
        
        cancelBtn.addEventListener('click', closeModal);
        confirmBtn.addEventListener('click', () => {
            closeModal();
            deletePost(postId, buttonElement);
        });
        
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
    }

    // Delete post function
    async function deletePost(postId, buttonElement) {
        const originalContent = buttonElement && buttonElement.innerHTML;
        const csrfTokenLocal = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || csrfToken;

        try {
            // Show loading state
            if (buttonElement) {
                buttonElement.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
                buttonElement.disabled = true;
            }

            const response = await fetch(`/posts/${postId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfTokenLocal,
                    'Accept': 'application/json'
                }
            });

            let data = null;
            try {
                data = await response.json();
            } catch (e) {
                // non-JSON response
                data = { success: response.ok, message: response.ok ? 'Deleted' : 'Error' };
            }

            if (response.ok && data && data.success) {
                showNotification('Post deleted successfully!', 'success');

                // Prefer to locate the post by data attribute so we remove the correct element
                let target = document.querySelector(`[data-post-id="${postId}"]`);
                let postElement = null;

                if (target) {
                    // remove the nearest logical container (article for profile, div for feed)
                    postElement = target.closest('article, div');
                }

                // fallback: try to use the delete button's ancestor
                if (!postElement && buttonElement) {
                    postElement = buttonElement.closest('article, div');
                }

                if (postElement) {
                    postElement.style.transition = 'opacity 0.3s ease-out, transform 0.3s ease-out';
                    postElement.style.opacity = '0';
                    postElement.style.transform = 'scale(0.95)';
                    setTimeout(() => postElement.remove(), 300);
                }
            } else {
                showNotification((data && data.message) || 'Error deleting post', 'error');
                if (buttonElement) {
                    buttonElement.innerHTML = originalContent;
                    buttonElement.disabled = false;
                }
            }
        } catch (error) {
            console.error('Error:', error);
            showNotification('Network error. Please try again.', 'error');
            if (buttonElement) {
                buttonElement.innerHTML = originalContent;
                buttonElement.disabled = false;
            }
        }
    }

    // Initialize the page
    console.log('Initializing filters and loading posts...');
    initializeFilters();
    loadPosts(1, false, true);

    console.log('Feed page initialization complete');
}

// Run initFeed immediately if DOM is already loaded, otherwise wait for DOMContentLoaded
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initFeed);
} else {
    initFeed();
}
} catch (e) {
    console.error('Top-level error in feed.js:', e);
}