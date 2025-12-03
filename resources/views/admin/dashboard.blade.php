<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - BandMate</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
        // Prevent back navigation to login page
        window.history.pushState(null, null, window.location.href);
        window.onpopstate = function() {
            window.history.pushState(null, null, window.location.href);
        };
        
        // Reload page if coming from cache (e.g., after logout)
        window.addEventListener('pageshow', function(event) {
            if (event.persisted) {
                window.location.reload();
            }
        });
    </script>
</head>
<body class="bg-gray-100">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <img src="{{ asset('assets/logo_black.png') }}" alt="BandMate" class="h-8 md:hidden">
                    <img src="{{ asset('assets/logo_both.png') }}" alt="BandMate" class="h-8 hidden md:block">
                    <span class="ml-2 text-xl font-semibold text-gray-800">Admin Dashboard</span>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('admin.appeals') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        View Appeals
                    </a>
                    <span class="text-gray-600">{{ auth('admin')->user()->name }}</span>
                    <form method="POST" action="{{ route('admin.logout') }}" class="inline" id="logoutForm">
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
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Users</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $stats['total_users'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Posts</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $stats['total_posts'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM21 16c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Musicians</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $stats['total_musicians'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H9m0 0H5m0 0h2M7 7h10M7 11h4m6 0h4" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Businesses</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $stats['total_businesses'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Activity Analytics -->
        <div class="bg-white shadow rounded-lg mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Activity Analytics</h3>
                <p class="mt-1 text-sm text-gray-500">Platform activity across different time periods</p>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Today's Activity -->
                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg p-6 border border-blue-200">
                        <h4 class="text-sm font-semibold text-blue-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Today
                        </h4>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-blue-700">Posts</span>
                                <span class="text-lg font-bold text-blue-900">{{ $stats['posts_today'] }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-blue-700">Likes</span>
                                <span class="text-lg font-bold text-blue-900">{{ $stats['likes_today'] }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-blue-700">Comments</span>
                                <span class="text-lg font-bold text-blue-900">{{ $stats['comments_today'] }}</span>
                            </div>
                            <div class="pt-2 border-t border-blue-200">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm font-semibold text-blue-800">Total Activity</span>
                                    <span class="text-xl font-bold text-blue-900">{{ $stats['posts_today'] + $stats['likes_today'] + $stats['comments_today'] }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- This Week's Activity -->
                    <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg p-6 border border-purple-200">
                        <h4 class="text-sm font-semibold text-purple-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            This Week
                        </h4>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-purple-700">Posts</span>
                                <span class="text-lg font-bold text-purple-900">{{ $stats['posts_week'] }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-purple-700">Likes</span>
                                <span class="text-lg font-bold text-purple-900">{{ $stats['likes_week'] }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-purple-700">Comments</span>
                                <span class="text-lg font-bold text-purple-900">{{ $stats['comments_week'] }}</span>
                            </div>
                            <div class="pt-2 border-t border-purple-200">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm font-semibold text-purple-800">Total Activity</span>
                                    <span class="text-xl font-bold text-purple-900">{{ $stats['posts_week'] + $stats['likes_week'] + $stats['comments_week'] }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- This Month's Activity -->
                    <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg p-6 border border-green-200">
                        <h4 class="text-sm font-semibold text-green-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            This Month
                        </h4>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-green-700">Posts</span>
                                <span class="text-lg font-bold text-green-900">{{ $stats['posts_month'] }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-green-700">Likes</span>
                                <span class="text-lg font-bold text-green-900">{{ $stats['likes_month'] }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-green-700">Comments</span>
                                <span class="text-lg font-bold text-green-900">{{ $stats['comments_month'] }}</span>
                            </div>
                            <div class="pt-2 border-t border-green-200">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm font-semibold text-green-800">Total Activity</span>
                                    <span class="text-xl font-bold text-green-900">{{ $stats['posts_month'] + $stats['likes_month'] + $stats['comments_month'] }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Users Table -->
        <div class="bg-white shadow overflow-hidden sm:rounded-md">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Users Management</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">Manage all registered users and their content.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Posts</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Likes</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Comments</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Followers</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Following</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($users as $user)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        @php
                                            $avatar = null;
                                            if ($user->musician && $user->musician->profile_picture) {
                                                $avatar = getImageUrl($user->musician->profile_picture);
                                            } elseif ($user->business && $user->business->profile_picture) {
                                                $avatar = getImageUrl($user->business->profile_picture);
                                            }
                                        @endphp
                                        @if($avatar)
                                            <img class="h-10 w-10 rounded-full object-cover" src="{{ $avatar }}" alt="">
                                        @else
                                            <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                                <span class="text-sm font-medium text-gray-700">{{ substr($user->name, 0, 1) }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            @if($user->musician)
                                                {{ $user->musician->stage_name ?: $user->name }}
                                            @elseif($user->business)
                                                {{ $user->business->business_name ?: $user->name }}
                                            @else
                                                {{ $user->name }}
                                            @endif
                                        </div>
                                        <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @if($user->musician) bg-purple-100 text-purple-800
                                    @elseif($user->business) bg-blue-100 text-blue-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    @if($user->musician) Musician
                                    @elseif($user->business) Business
                                    @else User @endif
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $user->posts_count }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $user->likes_count }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $user->comments_count }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $user->followers_count }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $user->following_count }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('admin.user.posts', $user->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">View Posts</a>
                                <a href="{{ route('admin.user.conversations', $user->id) }}" class="text-green-600 hover:text-green-900 mr-3">View Messages</a>
                                <button onclick="deleteUser({{ $user->id }})" class="text-red-600 hover:text-red-900">Delete User</button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="px-6 py-4">
                {{ $users->links() }}
            </div>
        </div>
    </div>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Handle logout to prevent back button after logout
        document.getElementById('logoutForm').addEventListener('submit', function(e) {
            // Clear the history stack before logout
            if (window.history) {
                window.history.replaceState(null, null, '{{ route("admin.login") }}');
            }
        });

        async function deleteUser(userId) {
            if (!confirm('Are you sure you want to delete this user? This will also delete all their posts and cannot be undone.')) {
                return;
            }

            try {
                const response = await fetch(`/admin/users/${userId}`, {
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
                    alert('Error deleting user: ' + data.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Network error occurred');
            }
        }
    </script>
</body>
</html>
