// Add this to your feed.js file

document.addEventListener('DOMContentLoaded', function() {
    // Load posts immediately on page load
    loadPosts();

    // Apply filters button
    const applyFiltersBtn = document.getElementById('applyFilters');
    if (applyFiltersBtn) {
        applyFiltersBtn.addEventListener('click', function() {
            loadPosts();
        });
    }

    // Load more button
    const loadMoreBtn = document.getElementById('loadMore');
    if (loadMoreBtn) {
        loadMoreBtn.addEventListener('click', function() {
            loadMorePosts();
        });
    }
});

// Function to load posts with current filters
function loadPosts() {
    const postsGrid = document.getElementById('postsGrid');
    if (!postsGrid) return;

    // Show loading state
    postsGrid.innerHTML = '<div class="col-span-full text-center py-8"><div class="animate-spin rounded-full h-12 w-12 border-b-2 border-purple-500 mx-auto"></div><p class="text-white mt-4">Loading posts...</p></div>';

    // Gather filter data
    const filters = gatherFilters();

    // Fetch posts from API
    fetch('/api/posts?' + new URLSearchParams(filters))
        .then(response => response.json())
        .then(data => {
            if (data.posts && data.posts.length > 0) {
                renderPosts(data.posts);
            } else {
                postsGrid.innerHTML = '<div class="col-span-full text-center py-12"><p class="text-white text-xl">No posts found. Try adjusting your filters.</p></div>';
            }
        })
        .catch(error => {
            console.error('Error loading posts:', error);
            postsGrid.innerHTML = '<div class="col-span-full text-center py-12"><p class="text-red-400 text-xl">Error loading posts. Please try again.</p></div>';
        });
}

// Function to gather current filter values
function gatherFilters() {
    const filters = {};

    // Gather checked instruments
    const instruments = [];
    document.querySelectorAll('#instruments input[type="checkbox"]:checked').forEach(checkbox => {
        instruments.push(checkbox.parentElement.textContent.trim());
    });
    if (instruments.length > 0) {
        filters.instruments = instruments.join(',');
    }

    // Gather checked venues
    const venues = [];
    document.querySelectorAll('#venues input[type="checkbox"]:checked').forEach(checkbox => {
        venues.push(checkbox.parentElement.textContent.trim());
    });
    if (venues.length > 0) {
        filters.venues = venues.join(',');
    }

    // Get sort option
    const sortBy = document.getElementById('sortBy');
    if (sortBy) {
        filters.sort = sortBy.value;
    }

    // Get distance filter if location is set
    const maxDistance = document.getElementById('maxDistance');
    if (maxDistance && maxDistance.value) {
        filters.maxDistance = maxDistance.value;
        
        // Include user location if available
        if (window.userLocation) {
            filters.lat = window.userLocation.lat;
            filters.lng = window.userLocation.lng;
        }
    }

    return filters;
}

// Function to render posts in the grid
function renderPosts(posts) {
    const postsGrid = document.getElementById('postsGrid');
    if (!postsGrid) return;

    postsGrid.innerHTML = posts.map(post => `
        <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-300 animate-fade-in border border-gray-200">
            ${post.image_url ? `
                <div class="relative h-64 overflow-hidden">
                    <img src="${post.image_url}" alt="Post image" class="w-full h-full object-cover">
                </div>
            ` : ''}
            
            <div class="p-6">
                <div class="flex items-center gap-3 mb-4">
                    <img src="${post.author_image || '/images/sample-profile.jpg'}" 
                         alt="${post.author_name}" 
                         class="w-12 h-12 rounded-full object-cover border-2 border-gray-200">
                    <div>
                        <a href="/profile/${post.author_id}" class="font-semibold text-gray-800 hover:text-purple-600 transition-colors">
                            ${post.author_name}
                        </a>
                        <p class="text-gray-500 text-sm">${post.created_at}</p>
                    </div>