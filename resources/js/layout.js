document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const mobileSearchInput = document.getElementById('mobileSearchInput');
    const searchResults = document.getElementById('searchResults');
    const searchResultsContent = document.getElementById('searchResultsContent');
    const searchLoading = document.getElementById('searchLoading');
    const noResults = document.getElementById('noResults');
    
    let searchTimeout;
    let currentQuery = '';

    // Function to perform live search
    function performSearch(query) {
        if (query.length < 2) {
            hideSearchResults();
            return;
        }

        if (query === currentQuery) return;
        currentQuery = query;

        showLoading();

        // Clear previous timeout
        clearTimeout(searchTimeout);

        // Debounce search requests
        searchTimeout = setTimeout(() => {
            fetch(`/api/search?query=${encodeURIComponent(query)}`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') || ''
                }
            })
            .then(response => response.json())
            .then(data => {
                hideLoading();
                displaySearchResults(data);
            })
            .catch(error => {
                console.error('Search error:', error);
                hideLoading();
                showNoResults();
            });
        }, 300); // 300ms delay
    }

    // Function to display search results
    function displaySearchResults(results) {
        if (!results || (results.musicians?.length === 0 && results.venues?.length === 0 && results.posts?.length === 0)) {
            showNoResults();
            return;
        }

        let html = '';

        // Musicians - FIXED: Link to /profile/{user_id}
        if (results.musicians && results.musicians.length > 0) {
            html += '<div class="px-2 py-1 text-xs font-semibold text-gray-500 uppercase tracking-wide bg-gray-50">Musicians</div>';
            results.musicians.forEach(musician => {
                html += `
                    <a href="/profile/${musician.user_id}" class="flex items-center px-4 py-3 hover:bg-gray-100 border-b border-gray-100">
                        <div class="flex-shrink-0">
                            ${musician.profile_image 
                                ? `<img class="w-10 h-10 rounded-full object-cover" src="${musician.profile_image}" alt="${musician.stage_name}">` 
                                : `<div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-semibold">${(musician.stage_name || 'M').charAt(0).toUpperCase()}</div>`
                            }
                        </div>
                        <div class="ml-3 flex-1">
                            <div class="text-sm font-medium text-gray-900">${musician.stage_name || 'Musician'}</div>
                            <div class="text-sm text-gray-500">${musician.genre || 'Musician'}</div>
                        </div>
                        <div class="text-xs text-gray-400">👤</div>
                    </a>
                `;
            });
        }

        // Venues - FIXED: Link to /profile/{user_id}
        if (results.venues && results.venues.length > 0) {
            html += '<div class="px-2 py-1 text-xs font-semibold text-gray-500 uppercase tracking-wide bg-gray-50">Venues</div>';
            results.venues.forEach(venue => {
                html += `
                    <a href="/profile/${venue.user_id}" class="flex items-center px-4 py-3 hover:bg-gray-100 border-b border-gray-100">
                        <div class="flex-shrink-0">
                            ${venue.profile_image 
                                ? `<img class="w-10 h-10 rounded-full object-cover" src="${venue.profile_image}" alt="${venue.business_name}">` 
                                : `<div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center text-white font-semibold">${(venue.business_name || 'V').charAt(0).toUpperCase()}</div>`
                            }
                        </div>
                        <div class="ml-3 flex-1">
                            <div class="text-sm font-medium text-gray-900">${venue.business_name || 'Venue'}</div>
                            <div class="text-sm text-gray-500">${venue.location || 'Venue'}</div>
                        </div>
                        <div class="text-xs text-gray-400">🏢</div>
                    </a>
                `;
            });
        }

        // Posts (no route available yet) - keep links inert
        if (results.posts && results.posts.length > 0) {
            html += '<div class="px-2 py-1 text-xs font-semibold text-gray-500 uppercase tracking-wide bg-gray-50">Posts</div>';
            results.posts.forEach(post => {
                html += `
                    <a href="#" class="flex items-center px-4 py-3 hover:bg-gray-100 border-b border-gray-100">
                        <div class="flex-shrink-0">
                            ${post.image_path 
                                ? `<img class="w-10 h-10 rounded object-cover" src="${post.image_path}" alt="Post">` 
                                : `<div class="w-10 h-10 bg-purple-500 rounded flex items-center justify-center text-white font-semibold">📝</div>`
                            }
                        </div>
                        <div class="ml-3 flex-1">
                            <div class="text-sm font-medium text-gray-900">${(post.description || '').substring(0, 50)}${(post.description || '').length > 50 ? '...' : ''}</div>
                            <div class="text-sm text-gray-500">by ${post.author_name || 'Unknown'}</div>
                        </div>
                        <div class="text-xs text-gray-400">📄</div>
                    </a>
                `;
            });
        }

        searchResultsContent.innerHTML = html;
        showSearchResults();
    }

    // Utility functions
    function showSearchResults() {
        searchResults.classList.remove('hidden');
        noResults.classList.add('hidden');
    }

    function hideSearchResults() {
        searchResults.classList.add('hidden');
    }

    function showLoading() {
        searchLoading.classList.remove('hidden');
        searchResultsContent.innerHTML = '';
        noResults.classList.add('hidden');
        showSearchResults();
    }

    function hideLoading() {
        searchLoading.classList.add('hidden');
    }

    function showNoResults() {
        noResults.classList.remove('hidden');
        searchResultsContent.innerHTML = '';
        showSearchResults();
    }

    // Event listeners for desktop search
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            const query = e.target.value.trim();
            performSearch(query);
        });

        searchInput.addEventListener('focus', function(e) {
            const query = e.target.value.trim();
            if (query.length >= 2) {
                performSearch(query);
            }
        });

        // Hide results when clicking outside
        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
                hideSearchResults();
            }
        });

        // Handle Enter key to submit form for full search results
        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                document.getElementById('searchForm').submit();
            }
        });
    }

    // Event listeners for mobile search
    if (mobileSearchInput) {
        mobileSearchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                document.getElementById('mobileSearchForm').submit();
            }
        });
    }

    // Mobile menu toggle
    const mobileMenuButton = document.getElementById('mobileMenuButton');
    const mobileMenu = document.getElementById('mobileMenu');
    const menuOpenIcon = document.getElementById('menuOpenIcon');
    const menuCloseIcon = document.getElementById('menuCloseIcon');

    if (mobileMenuButton) {
        mobileMenuButton.addEventListener('click', function() {
            const isHidden = mobileMenu.classList.contains('hidden');
            
            if (isHidden) {
                mobileMenu.classList.remove('hidden');
                menuOpenIcon.classList.add('hidden');
                menuCloseIcon.classList.remove('hidden');
            } else {
                mobileMenu.classList.add('hidden');
                menuOpenIcon.classList.remove('hidden');
                menuCloseIcon.classList.add('hidden');
            }
        });
    }

    // Delete post functionality (for profile page)
    document.addEventListener('click', function(e) {
        if (e.target.closest('.delete-post-btn')) {
            e.preventDefault();
            const button = e.target.closest('.delete-post-btn');
            const postId = button.getAttribute('data-post-id');
            
            showDeleteConfirmation(postId, button);
        }
    });

    // Image modal functionality (for profile page)
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
        const originalContent = buttonElement && buttonElement.innerHTML;
        const csrfTokenLocal = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        try {
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
                data = { success: response.ok, message: response.ok ? 'Deleted' : 'Error' };
            }

            if (response.ok && data && data.success) {
                showNotification('Post deleted successfully!', 'success');

                // Remove post element by data attribute if possible
                let target = document.querySelector(`[data-post-id="${postId}"]`);
                let postElement = target ? target.closest('article, div') : null;

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

    // Show notification function
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 p-4 rounded-2xl shadow-lg backdrop-blur-xl transform translate-x-full transition-transform duration-300 ${
            type === 'success' ? 'bg-green-500/90 text-white' : 
            type === 'error' ? 'bg-red-500/90 text-white' : 
            'bg-blue-500/90 text-white'
        }`;
        
        notification.innerHTML = `
            <div class="flex items-center gap-3">
                <div class="flex-shrink-0">
                    ${type === 'success' ? '✅' : type === 'error' ? '❌' : 'ℹ️'}
                </div>
                <div class="text-sm font-medium">${message}</div>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Animate in
        setTimeout(() => {
            notification.style.transform = 'translateX(0)';
        }, 100);
        
        // Auto remove after 4 seconds
        setTimeout(() => {
            notification.style.transform = 'translateX(full)';
            setTimeout(() => {
                if (document.body.contains(notification)) {
                    document.body.removeChild(notification);
                }
            }, 300);
        }, 4000);
    }

    // Extract post data from image element (for profile page)
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

    // Show image modal (for profile page)
    function showImageModal(postData) {
        console.log('showImageModal called with:', postData); // Debug log
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
                    if (content) {
                        addComment(postData.id, content, commentInput, modal);
                    }
                }
            });
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
                // Clear input
                commentInput.value = '';
                
                // Add comment to the list
                addCommentToModal(data.comment, modal);
                
                // Update comment count
                const commentCount = modal.querySelector('.comment-count');
                if (commentCount) {
                    commentCount.textContent = parseInt(commentCount.textContent) + 1;
                }
            }
        } catch (error) {
            console.error('Error adding comment:', error);
        }
    }

    // Load comments function
    async function loadComments(postId, modal) {
        // Check if this is a sample post (not a real database post)
        if (postId.startsWith('sample-')) {
            console.log('This is a sample post, comments not available');
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
        
        // Check if user has an avatar
        let avatarHtml = '';
        if (comment.user_avatar) {
            avatarHtml = `<img src="/storage/${comment.user_avatar}" alt="${userName}" class="w-8 h-8 rounded-full object-cover">`;
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
});