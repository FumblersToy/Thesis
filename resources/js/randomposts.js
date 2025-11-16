// Random Posts Loader - Fetches random posts from database on page load
document.addEventListener('DOMContentLoaded', async function() {
    const postsGrid = document.getElementById('postsGrid');
    
    // Skip if postsGrid doesn't exist
    if (!postsGrid) {
        console.warn('Posts grid not found');
        return;
    }

    // Show loading state
    postsGrid.innerHTML = `
        <div class="col-span-full text-center py-12">
            <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-purple-500"></div>
            <p class="mt-4 text-gray-600">Loading posts...</p>
        </div>
    `;

    try {
        // Fetch random posts from the API
        const response = await fetch('/api/posts/random?count=12', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            credentials: 'same-origin'
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        console.log('Random posts loaded:', data);

        if (data.success && data.posts && data.posts.length > 0) {
            // Clear loading state
            postsGrid.innerHTML = '';
            
            // Render each post
            data.posts.forEach(post => {
                const postElement = createPostElement(post);
                postsGrid.appendChild(postElement);
            });
        } else {
            // No posts found
            postsGrid.innerHTML = `
                <div class="col-span-full text-center py-12">
                    <p class="text-gray-600 text-lg">No posts available yet.</p>
                    <p class="text-gray-500 text-sm mt-2">Be the first to post!</p>
                </div>
            `;
        }
    } catch (error) {
        console.error('Error loading random posts:', error);
        postsGrid.innerHTML = `
            <div class="col-span-full text-center py-12">
                <p class="text-red-600 text-lg">Error loading posts.</p>
                <p class="text-gray-500 text-sm mt-2">Please refresh the page.</p>
            </div>
        `;
    }

    // Create post element function
    function createPostElement(post) {
        const hasImage = post.image_path && post.image_path.trim() !== '';
        const userName = post.user_name || 'User';
        const userGenre = post.user_genre || '';
        const userLocation = post.user_location || post.user_city || '';
        const userType = post.user_type || 'member';
        const userAvatar = post.user_avatar || null;
        const createdAt = post.created_at || new Date().toISOString();
        const likeCount = post.like_count || post.likes_count || 0;
        const commentCount = post.comment_count || post.comments_count || 0;
        const isOwner = post.is_owner || false;

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
            <div class="relative">
                <img class="post-image w-full h-80 object-cover cursor-pointer hover:opacity-90 transition-opacity" 
                     src="${post.image_path}" 
                     alt="Post image" 
                     loading="lazy"
                     onerror="this.src='/images/sample-post-1.jpg'"
                     data-post-id="${post.id}"
                     data-image-url="${post.image_path}"
                     data-user-name="${userName}"
                     data-user-genre="${userGenre}"
                     data-user-location="${userLocation}"
                     data-user-type="${userType}"
                     data-user-avatar="${userAvatar || ''}"
                     data-description="${post.description || ''}"
                     data-created-at="${createdAt}"
                     data-like-count="${likeCount}"
                     data-comment-count="${commentCount}"
                     data-is-liked="${post.is_liked ? 'true' : 'false'}">
                <div class="absolute top-4 right-4 bg-black/50 backdrop-blur-sm text-white px-3 py-1 rounded-full text-sm">
                    ${userTypeEmoji} ${userType}
                </div>
                ${isOwner ? `
                    <button class="delete-post-btn absolute top-4 left-4 bg-red-500/80 hover:bg-red-600 text-white p-2 rounded-full transition-colors duration-200" 
                            data-post-id="${post.id}" 
                            title="Delete post">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                ` : ''}
            </div>
        ` : '';

        postDiv.innerHTML = `
            ${imageSection}
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
                        <!-- Like/comment icons available in modal -->
                    </div>
                </div>
            </div>
        `;
        
        return postDiv;
    }
});