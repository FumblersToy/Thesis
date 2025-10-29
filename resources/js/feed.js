function getImageUrl(path) {
    if (!path) return '/images/sample-profile.jpg';
    
    // Fix double-processing: remove /storage/ if it's followed by http
    if (path.includes('/storage/http')) {
        path = path.replace('/storage/', '');
    }
    
    // If path already starts with http or /storage/, return as-is
    if (path.startsWith('http://') || path.startsWith('https://') || path.startsWith('/storage/')) {
        return path;
    }
    
    // Otherwise prepend /storage/
    return `/storage/${path}`;
}

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

    // Image modal functionality
    document.addEventListener('click', function(e) {
        if (e.target.closest('.post-image')) {
            e.preventDefault();
            console.log('Post image clicked!'); // Debug log
            const img = e.target.closest('.post-image');
            const postData = extractPostDataFromImage(img);
            
            console.log('Post data:', postData); // Debug log
            showImageModal(postData);
        }
    });

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
        const originalContent = buttonElement.innerHTML;
        
        try {
            // Show loading state
            buttonElement.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
            buttonElement.disabled = true;
            
            const response = await fetch(`/posts/${postId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (data.success) {
                showNotification('Post deleted successfully!', 'success');
                
                // Find and remove the post element
                const postElement = buttonElement.closest('.bg-white\\/80');
                if (postElement) {
                    postElement.style.transition = 'opacity 0.3s ease-out, transform 0.3s ease-out';
                    postElement.style.opacity = '0';
                    postElement.style.transform = 'scale(0.95)';
                    
                    setTimeout(() => {
                        postElement.remove();
                    }, 300);
                }
            } else {
                showNotification(data.message || 'Error deleting post', 'error');
                buttonElement.innerHTML = originalContent;
                buttonElement.disabled = false;
            }
        } catch (error) {
            console.error('Error:', error);
            showNotification('Network error. Please try again.', 'error');
            buttonElement.innerHTML = originalContent;
            buttonElement.disabled = false;
        }
    }

    // Extract post data from element
    function extractPostData(postElement) {
        const img = postElement.querySelector('.post-image');
        if (!img) return null;
        
        return {
            id: img.getAttribute('data-post-id'),
            imageUrl: img.getAttribute('data-image-url'),
            userName: img.getAttribute('data-user-name'),
            userGenre: img.getAttribute('data-user-genre'),
            userType: img.getAttribute('data-user-type'),
            userAvatar: img.getAttribute('data-user-avatar'),
            description: img.getAttribute('data-description'),
            createdAt: img.getAttribute('data-created-at')
        };
    }

    // Extract post data directly from image element
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
        console.log('showImageModal called with:', postData); // Debug log
        if (!postData) {
            console.log('No post data, returning'); // Debug log
            return;
        }
        
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
        
        console.log('Modal created and added to DOM');
        
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
            console.log('Setting up like button for post:', postData.id);
            likeBtn.addEventListener('click', (e) => {
                e.preventDefault();
                console.log('Like button clicked for post:', postData.id);
                toggleLike(likeBtn, postData.id);
            });
        } else {
            console.log('Like button not found in modal');
        }

        // Add comment functionality
        const commentInput = modal.querySelector('input[type="text"]');
        const commentSubmitBtn = modal.querySelector('.comment-submit-btn');
        if (commentInput && commentSubmitBtn) {
            console.log('Setting up comment functionality for post:', postData.id);
            commentSubmitBtn.addEventListener('click', (e) => {
                e.preventDefault();
                const content = commentInput.value.trim();
                console.log('Comment submit button clicked, content:', content);
                if (content) {
                    addComment(postData.id, content, commentInput, modal);
                }
            });
            
            commentInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    const content = commentInput.value.trim();
                    console.log('Enter pressed in comment input, content:', content);
                    if (content) {
                        addComment(postData.id, content, commentInput, modal);
                    }
                }
            });
        } else {
            console.log('Comment input or submit button not found in modal');
        }

        // Load comments
        console.log('Loading comments for post:', postData.id);
        loadComments(postData.id, modal);
    }

    // Toggle like function
    async function toggleLike(likeBtn, postId) {
        console.log('toggleLike called with postId:', postId);
        
        // Check if this is a sample post (not a real database post)
        if (postId.startsWith('sample-')) {
            console.log('This is a sample post, like functionality not available');
            showNotification('Like functionality is only available for real posts. Create a post to test this feature!', 'info');
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
        console.log('addComment called with postId:', postId, 'content:', content);
        
        // Check if this is a sample post (not a real database post)
        if (postId.startsWith('sample-')) {
            console.log('This is a sample post, comment functionality not available');
            showNotification('Comment functionality is only available for real posts. Create a post to test this feature!', 'info');
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
                console.log('Comment added successfully:', data.comment);
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
                console.log('Failed to add comment:', data);
            }
        } catch (error) {
            console.error('Error adding comment:', error);
        }
    }

    // Load comments function
    async function loadComments(postId, modal) {
        console.log('loadComments called with postId:', postId, 'modal:', modal);
        
        // Check if this is a sample post (not a real database post)
        if (postId.startsWith('sample-')) {
            console.log('This is a sample post, comments not available');
            return;
        }
        
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        try {
            console.log('Fetching comments from:', `/posts/${postId}/comments`);
            const response = await fetch(`/posts/${postId}/comments`, {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            });

            console.log('Comments response status:', response.status);
            const data = await response.json();

            console.log('Comments response:', data);
            if (data.success && data.comments.length > 0) {
                const commentsContainer = modal.querySelector('.space-y-3');
                console.log('Comments container for loading:', commentsContainer);
                if (commentsContainer) {
                    // Clear the "no comments" message
                    commentsContainer.innerHTML = '';
                    
                    // Add each comment
                    data.comments.forEach(comment => {
                        addCommentToModal(comment, modal);
                    });
                } else {
                    console.log('Comments container not found for loading!');
                }
            } else if (data.success && data.comments.length === 0) {
                console.log('No comments found for this post');
                // Keep the "no comments" message
            } else {
                console.log('Error loading comments:', data);
            }
        } catch (error) {
            console.error('Error loading comments:', error);
        }
    }

    // Add comment to modal function
    function addCommentToModal(comment, modal) {
        console.log('Adding comment to modal:', comment);
        const commentsContainer = modal.querySelector('.space-y-3');
        console.log('Comments container found:', commentsContainer);
        
        if (!commentsContainer) {
            console.log('Comments container not found!');
            return;
        }

        const commentElement = document.createElement('div');
        commentElement.className = 'flex gap-3 p-3 bg-gray-50 rounded-lg';
        const userName = comment.user_name || 'Unknown User';
        const userInitial = userName.charAt(0).toUpperCase();
        
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
        console.log('Comment added to container');
    }

    // Initialize the page
    console.log('Initializing filters and loading posts...');
    initializeFilters();
    loadPosts(1, false);
    
    console.log('Feed page initialization complete');
});

