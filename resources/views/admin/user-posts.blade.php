<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Posts - Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-gray-100">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center">
                        <img src="{{ asset('assets/logo_black.png') }}" alt="BandMate" class="h-8 md:hidden">
                        <img src="{{ asset('assets/logo_both.png') }}" alt="BandMate" class="h-8 hidden md:block">
                        <span class="ml-2 text-xl font-semibold text-gray-800">Admin Dashboard</span>
                    </a>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-gray-600">{{ auth('admin')->user()->name }}</span>
                    <form method="POST" action="{{ route('admin.logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <!-- User Info Header -->
        <div class="bg-white shadow rounded-lg mb-6">
            <div class="px-6 py-4">
                <div class="flex items-center">
                    @php
                        $avatar = null;
                        if ($user->musician && $user->musician->profile_picture) {
                            $avatar = getImageUrl($user->musician->profile_picture);
                        } elseif ($user->business && $user->business->profile_picture) {
                            $avatar = getImageUrl($user->business->profile_picture);
                        }
                        
                        $displayName = $user->musician?->stage_name ?: ($user->business?->business_name ?: $user->name);
                        $userType = $user->musician ? 'Musician' : ($user->business ? 'Business' : 'User');
                    @endphp
                    
                    @if($avatar)
                        <img class="h-16 w-16 rounded-full object-cover" src="{{ $avatar }}" alt="">
                    @else
                        <div class="h-16 w-16 rounded-full bg-gray-300 flex items-center justify-center">
                            <span class="text-xl font-medium text-gray-700">{{ substr($displayName, 0, 1) }}</span>
                        </div>
                    @endif
                    
                    <div class="ml-6">
                        <h1 class="text-2xl font-bold text-gray-900">{{ $displayName }}</h1>
                        <p class="text-gray-600">{{ $user->email }}</p>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                @if($user->musician) bg-purple-100 text-purple-800
                                @elseif($user->business) bg-blue-100 text-blue-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ $userType }}
                            </span>
                            @if($user->musician && $user->musician->verified)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    ✓ Verified
                                </span>
                            @elseif($user->business && $user->business->verified)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    ✓ Verified
                                </span>
                            @endif
                        </div>
                    </div>
                    
                    @if($user->musician)
                    <div class="ml-auto flex flex-col gap-2">
                        @if($user->musician->credential_document)
                            <a href="{{ $user->musician->credential_document }}" target="_blank" 
                               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm text-center">
                                📄 View Musician Credentials
                            </a>
                        @else
                            <span class="bg-gray-300 text-gray-600 px-4 py-2 rounded-md text-sm text-center">
                                No Credentials Uploaded
                            </span>
                        @endif
                        
                        @if($user->musician->verified)
                            <button onclick="toggleMusicianVerification({{ $user->musician->id }}, false)" 
                                    class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm">
                                Unverify Musician
                            </button>
                        @else
                            <button onclick="toggleMusicianVerification({{ $user->musician->id }}, true)" 
                                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm">
                                Verify Musician
                            </button>
                        @endif
                        
                        <a href="{{ route('admin.dashboard') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md text-sm text-center">
                            ← Back to Dashboard
                        </a>
                    </div>
                    @elseif($user->business)
                    <div class="ml-auto flex flex-col gap-2">
                        @if($user->business->business_permit)
                            <a href="{{ $user->business->business_permit }}" target="_blank" 
                               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm text-center">
                                📄 View Business Permit
                            </a>
                        @else
                            <span class="bg-gray-300 text-gray-600 px-4 py-2 rounded-md text-sm text-center">
                                No Permit Uploaded
                            </span>
                        @endif
                        
                        @if($user->business->verified)
                            <button onclick="toggleVerification({{ $user->business->id }}, false)" 
                                    class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm">
                                Unverify Business
                            </button>
                        @else
                            <button onclick="toggleVerification({{ $user->business->id }}, true)" 
                                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm">
                                Verify Business
                            </button>
                        @endif
                        
                        <a href="{{ route('admin.dashboard') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md text-sm text-center">
                            ← Back to Dashboard
                        </a>
                    </div>
                    @else
                    <div class="ml-auto">
                        <a href="{{ route('admin.dashboard') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md text-sm">
                            ← Back to Dashboard
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Posts Grid -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">User Posts ({{ $posts->total() }})</h2>
            </div>
            
            @if($posts->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-6">
                    @foreach($posts as $post)
                    <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
                        <div class="relative">
                            @php
                                $imageUrl = $post->image_path ? getImageUrl($post->image_path) : '/images/sample-post-1.jpg';
                            @endphp
                            <img src="{{ $imageUrl }}" alt="Post image" class="w-full h-48 object-cover">
                            <button onclick="deletePost({{ $post->id }})" 
                                    class="absolute top-2 right-2 bg-red-600 hover:bg-red-700 text-white p-2 rounded-full shadow-lg">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </div>
                        <div class="p-4">
                            @if($post->description)
                                <p class="text-gray-700 text-sm mb-3">{{ Str::limit($post->description, 100) }}</p>
                            @endif
                            <div class="flex justify-between items-center text-sm text-gray-500">
                                <span>{{ $post->created_at->format('M j, Y') }}</span>
                                <div class="flex space-x-4">
                                    <span class="flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"></path>
                                        </svg>
                                        {{ $post->likes_count }}
                                    </span>
                                    <span class="flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                        </svg>
                                        {{ $post->comments_count }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                
                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $posts->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No posts</h3>
                    <p class="mt-1 text-sm text-gray-500">This user hasn't created any posts yet.</p>
                </div>
            @endif
        </div>
    </div>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        async function toggleVerification(businessId, verify) {
            const action = verify ? 'verify' : 'unverify';
            if (!confirm(`Are you sure you want to ${action} this business?`)) {
                return;
            }

            try {
                const response = await fetch(`/admin/businesses/${businessId}/verify`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ verified: verify })
                });

                const data = await response.json();

                if (data.success) {
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Network error occurred');
            }
        }

        async function toggleMusicianVerification(musicianId, verify) {
            const action = verify ? 'verify' : 'unverify';
            if (!confirm(`Are you sure you want to ${action} this musician?`)) {
                return;
            }

            try {
                const response = await fetch(`/admin/musicians/${musicianId}/verify`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ verified: verify })
                });

                const data = await response.json();

                if (data.success) {
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Network error occurred');
            }
        }

        async function deletePost(postId) {
            if (!confirm('Are you sure you want to delete this post? This action cannot be undone.')) {
                return;
            }

            try {
                const response = await fetch(`/admin/posts/${postId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    location.reload();
                } else {
                    alert('Error deleting post: ' + data.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Network error occurred');
            }
        }
    </script>
</body>
</html>
