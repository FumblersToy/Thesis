<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post Deletion Appeals - Admin Dashboard</title>
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
        <!-- Header -->
        <div class="bg-white shadow rounded-lg mb-6">
            <div class="px-6 py-4 flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Post Deletion Appeals</h1>
                    <p class="text-gray-600 mt-1">Review and respond to user appeals for deleted posts</p>
                </div>
                <a href="{{ route('admin.dashboard') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md text-sm">
                    ← Back to Dashboard
                </a>
            </div>
        </div>

        <!-- Appeals List -->
        <div class="bg-white shadow rounded-lg">
            @if($appeals->count() > 0)
                <div class="divide-y divide-gray-200">
                    @foreach($appeals as $post)
                    @php
                        $user = $post->user;
                        $musician = $user->musician;
                        $business = $user->business;
                        $avatar = null;
                        if ($musician && $musician->profile_picture) {
                            $avatar = getImageUrl($musician->profile_picture);
                        } elseif ($business && $business->profile_picture) {
                            $avatar = getImageUrl($business->profile_picture);
                        }
                        $displayName = $musician?->stage_name ?: ($business?->business_name ?: $user->name);
                        $daysLeft = 15 - now()->diffInDays($post->deleted_at);
                    @endphp
                    
                    <div class="p-6">
                        <div class="flex gap-6">
                            <!-- Post Image -->
                            <div class="flex-shrink-0">
                                @if($post->image_path)
                                    <img src="{{ getImageUrl($post->image_path) }}" alt="Post" class="w-32 h-32 object-cover rounded-lg">
                                @else
                                    <div class="w-32 h-32 bg-gray-200 rounded-lg flex items-center justify-center">
                                        <span class="text-gray-400">No Image</span>
                                    </div>
                                @endif
                            </div>

                            <!-- Appeal Details -->
                            <div class="flex-1">
                                <!-- User Info -->
                                <div class="flex items-center gap-3 mb-3">
                                    @if($avatar)
                                        <img class="h-10 w-10 rounded-full object-cover" src="{{ $avatar }}" alt="">
                                    @else
                                        <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                            <span class="text-sm font-medium text-gray-700">{{ substr($displayName, 0, 1) }}</span>
                                        </div>
                                    @endif
                                    <div>
                                        <h3 class="font-semibold text-gray-900">{{ $displayName }}</h3>
                                        <p class="text-sm text-gray-600">{{ $user->email }}</p>
                                    </div>
                                </div>

                                <!-- Post Description -->
                                @if($post->description)
                                    <div class="mb-3">
                                        <p class="text-gray-700">{{ Str::limit($post->description, 200) }}</p>
                                    </div>
                                @endif

                                <!-- Deletion Info -->
                                <div class="bg-red-50 border border-red-200 rounded-lg p-3 mb-3">
                                    <div class="flex items-start gap-2">
                                        <svg class="w-5 h-5 text-red-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                        </svg>
                                        <div class="flex-1">
                                            <h4 class="font-semibold text-red-900">Deletion Reason</h4>
                                            <p class="text-red-800 text-sm mt-1">{{ $post->deletion_reason }}</p>
                                            <p class="text-red-700 text-xs mt-1">
                                                Deleted by {{ $post->deletedBy->name }} on {{ $post->deleted_at->format('M j, Y g:i A') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Appeal Message -->
                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-3">
                                    <div class="flex items-start gap-2">
                                        <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                                        </svg>
                                        <div class="flex-1">
                                            <h4 class="font-semibold text-blue-900">User's Appeal</h4>
                                            <p class="text-blue-800 text-sm mt-1">{{ $post->appeal_message }}</p>
                                            <p class="text-blue-700 text-xs mt-1">
                                                Submitted {{ $post->appeal_at->diffForHumans() }} 
                                                @if($daysLeft > 0)
                                                    ({{ $daysLeft }} days left to decide)
                                                @else
                                                    (expired)
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex gap-3">
                                    <button onclick="respondToAppeal({{ $post->id }}, 'approved')" 
                                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Approve Appeal & Restore Post
                                    </button>
                                    <button onclick="respondToAppeal({{ $post->id }}, 'denied')" 
                                            class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                        Deny Appeal
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No pending appeals</h3>
                    <p class="mt-1 text-sm text-gray-500">All post deletion appeals have been reviewed.</p>
                </div>
            @endif
        </div>
    </div>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        async function respondToAppeal(postId, decision) {
            let response = null;
            
            if (decision === 'denied') {
                response = prompt('Optional: Provide a message to the user explaining why the appeal was denied:');
            }

            const confirmMessage = decision === 'approved' 
                ? 'Are you sure you want to approve this appeal and restore the post?' 
                : 'Are you sure you want to deny this appeal? The post will be permanently deleted after 15 days.';
            
            if (!confirm(confirmMessage)) {
                return;
            }

            try {
                const res = await fetch(`/admin/appeals/${postId}/respond`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ 
                        decision: decision,
                        response: response 
                    })
                });

                const data = await res.json();

                if (data.success) {
                    alert(data.message);
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Network error occurred');
            }
        }
    </script>
</body>
</html>
