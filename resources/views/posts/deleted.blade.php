<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deleted Posts - BandMate</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-gray-100">
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('feed') }}" class="flex items-center">
                        <img src="{{ asset('assets/logo_both.png') }}" alt="BandMate" class="h-8">
                        <span class="ml-2 text-xl font-semibold text-gray-800">Deleted Posts</span>
                    </a>
                </div>
                <div class="flex items-center">
                    <a href="{{ route('feed') }}" class="text-gray-600 hover:text-gray-900">← Back to Feed</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Your Deleted Posts ({{ $deletedPosts->count() }})</h2>
                <p class="text-sm text-gray-500 mt-1">Posts deleted by administrators. You can appeal if you believe the deletion was unfair.</p>
            </div>
            
            @if($deletedPosts->count() > 0)
                <div class="divide-y divide-gray-200">
                    @foreach($deletedPosts as $post)
                    <div class="p-6">
                        <div class="flex gap-6">
                            <div class="w-48 h-48 flex-shrink-0">
                                @php
                                    $imageUrl = $post->image_path ? getImageUrl($post->image_path) : '/images/sample-post-1.jpg';
                                @endphp
                                <img src="{{ $imageUrl }}" alt="Post" class="w-full h-full object-cover rounded-lg opacity-75">
                            </div>
                            
                            <div class="flex-1">
                                <div class="flex items-start justify-between mb-3">
                                    <div>
                                        <p class="text-sm text-gray-500">Deleted {{ $post->deleted_at->diffForHumans() }}</p>
                                    </div>
                                    @if($post->appeal_status === 'none')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            Can Appeal
                                        </span>
                                    @elseif($post->appeal_status === 'pending')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            Appeal Pending
                                        </span>
                                    @elseif($post->appeal_status === 'approved')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Appeal Approved
                                        </span>
                                    @elseif($post->appeal_status === 'denied')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Appeal Denied
                                        </span>
                                    @endif
                                </div>
                                
                                @if($post->description)
                                <div class="mb-3">
                                    <p class="text-sm font-medium text-gray-700">Post Description:</p>
                                    <p class="text-sm text-gray-600">{{ $post->description }}</p>
                                </div>
                                @endif
                                
                                <div class="mb-3 bg-red-50 p-3 rounded-lg">
                                    <p class="text-sm font-medium text-red-900">Reason for Deletion:</p>
                                    <p class="text-sm text-red-700">{{ $post->deletion_reason }}</p>
                                </div>
                                
                                @if($post->appeal_status === 'pending' && $post->appeal_message)
                                <div class="mb-3 bg-blue-50 p-3 rounded-lg">
                                    <p class="text-sm font-medium text-blue-900">Your Appeal Message:</p>
                                    <p class="text-sm text-blue-700">{{ $post->appeal_message }}</p>
                                    <p class="text-xs text-blue-600 mt-1">Submitted {{ $post->appeal_at->diffForHumans() }}</p>
                                </div>
                                @endif
                                
                                @if($post->appeal_status === 'none')
                                <button onclick="openAppealModal({{ $post->id }})" 
                                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                                    Submit Appeal
                                </button>
                                @elseif($post->appeal_status === 'pending')
                                <p class="text-sm text-gray-500 italic">Your appeal is being reviewed by administrators.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No deleted posts</h3>
                    <p class="mt-1 text-sm text-gray-500">You don't have any posts that have been deleted by administrators.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Appeal Modal -->
    <div id="appealModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Submit Appeal</h3>
                <textarea id="appealMessage" 
                          class="w-full border border-gray-300 rounded-md p-3 text-sm" 
                          rows="4" 
                          placeholder="Explain why you believe this post should be restored..."></textarea>
                <div class="text-xs text-gray-500 mt-1">Maximum 500 characters</div>
                <div class="flex gap-2 mt-4">
                    <button onclick="submitAppeal()" 
                            class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                        Submit Appeal
                    </button>
                    <button onclick="closeAppealModal()" 
                            class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-md text-sm font-medium">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        let currentPostId = null;

        function openAppealModal(postId) {
            currentPostId = postId;
            document.getElementById('appealModal').classList.remove('hidden');
            document.getElementById('appealMessage').value = '';
        }

        function closeAppealModal() {
            document.getElementById('appealModal').classList.add('hidden');
            currentPostId = null;
        }

        async function submitAppeal() {
            const message = document.getElementById('appealMessage').value.trim();
            
            if (!message) {
                alert('Please provide a message for your appeal.');
                return;
            }

            if (message.length > 500) {
                alert('Appeal message must be 500 characters or less.');
                return;
            }

            try {
                const response = await fetch(`/posts/${currentPostId}/appeal`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ message: message })
                });

                const data = await response.json();

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
