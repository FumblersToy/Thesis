<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post Appeals - Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-gray-100">
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center">
                        <img src="{{ asset('assets/logo_both.png') }}" alt="BandMate" class="h-8">
                        <span class="ml-2 text-xl font-semibold text-gray-800">Admin Dashboard - Appeals</span>
                    </a>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('admin.dashboard') }}" class="text-gray-600 hover:text-gray-900">← Back to Dashboard</a>
                    <span class="text-gray-600">{{ auth('admin')->user()->name }}</span>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Pending Post Deletion Appeals ({{ $appeals->count() }})</h2>
            </div>
            
            @if($appeals->count() > 0)
                <div class="divide-y divide-gray-200">
                    @foreach($appeals as $post)
                    <div class="p-6" id="appeal-{{ $post->id }}">
                        <div class="flex gap-6">
                            <!-- Post Preview -->
                            <div class="w-48 h-48 flex-shrink-0">
                                @php
                                    $imageUrl = $post->image_path ? getImageUrl($post->image_path) : '/images/sample-post-1.jpg';
                                @endphp
                                <img src="{{ $imageUrl }}" alt="Post" class="w-full h-full object-cover rounded-lg">
                            </div>
                            
                            <!-- Appeal Details -->
                            <div class="flex-1">
                                <div class="flex items-start justify-between mb-4">
                                    <div>
                                        @php
                                            $displayName = $post->user->musician?->stage_name ?: ($post->user->business?->business_name ?: $post->user->name);
                                        @endphp
                                        <h3 class="text-lg font-semibold text-gray-900">{{ $displayName }}</h3>
                                        <p class="text-sm text-gray-500">{{ $post->user->email }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm text-gray-500">Deleted {{ $post->deleted_at->diffForHumans() }}</p>
                                        <p class="text-sm text-gray-500">Appeal {{ $post->appeal_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                                
                                <!-- Original Post Description -->
                                @if($post->description)
                                <div class="mb-3">
                                    <p class="text-sm font-medium text-gray-700">Post Description:</p>
                                    <p class="text-sm text-gray-600">{{ $post->description }}</p>
                                </div>
                                @endif
                                
                                <!-- Deletion Reason -->
                                <div class="mb-3 bg-red-50 p-3 rounded-lg">
                                    <p class="text-sm font-medium text-red-900">Deletion Reason:</p>
                                    <p class="text-sm text-red-700">{{ $post->deletion_reason }}</p>
                                    <p class="text-xs text-red-600 mt-1">Deleted by: {{ $post->deletedBy->name ?? 'Unknown Admin' }}</p>
                                </div>
                                
                                <!-- Appeal Message -->
                                <div class="mb-4 bg-blue-50 p-3 rounded-lg">
                                    <p class="text-sm font-medium text-blue-900">Appeal Message:</p>
                                    <p class="text-sm text-blue-700">{{ $post->appeal_message }}</p>
                                </div>
                                
                                <!-- Action Buttons -->
                                <div class="flex gap-3">
                                    <button onclick="respondToAppeal({{ $post->id }}, 'approved')" 
                                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                                        ✓ Approve Appeal
                                    </button>
                                    <button onclick="respondToAppeal({{ $post->id }}, 'denied')" 
                                            class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                                        ✗ Deny Appeal
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No pending appeals</h3>
                    <p class="mt-1 text-sm text-gray-500">All post deletion appeals have been processed.</p>
                </div>
            @endif
        </div>
    </div>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        async function respondToAppeal(postId, decision) {
            let response = null;
            
            if (decision === 'denied') {
                response = prompt('Optional: Provide a reason for denying the appeal:');
            }

            const confirmMsg = decision === 'approved' 
                ? 'Approve this appeal and restore the post?' 
                : 'Deny this appeal? The post will remain deleted.';
                
            if (!confirm(confirmMsg)) {
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
                    document.getElementById(`appeal-${postId}`).remove();
                    
                    // Reload if no more appeals
                    if (document.querySelectorAll('[id^="appeal-"]').length === 0) {
                        location.reload();
                    }
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
