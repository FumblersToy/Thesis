<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deleted Posts</title>
    @vite(['resources/css/app.css'])
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Deleted Posts</h1>
                    <p class="text-gray-600 mt-2">Posts deleted by admins. You can appeal within 15 days.</p>
                </div>
                <a href="{{ route('feed') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm">
                    ← Back to Feed
                </a>
            </div>
        </div>

        @if($deletedPosts->count() > 0)
            <div class="space-y-6">
                @foreach($deletedPosts as $post)
                @php
                    $daysLeft = 15 - now()->diffInDays($post->deleted_at);
                    $canAppeal = $daysLeft > 0 && $post->appeal_status === 'none';
                @endphp
                
                <div class="bg-white rounded-xl shadow-md overflow-hidden">
                    <div class="p-6">
                        <div class="flex gap-6">
                            <!-- Post Image -->
                            <div class="flex-shrink-0">
                                @if($post->image_path)
                                    <img src="{{ getImageUrl($post->image_path) }}" alt="Post" class="w-48 h-48 object-cover rounded-lg">
                                @else
                                    <div class="w-48 h-48 bg-gray-200 rounded-lg flex items-center justify-center">
                                        <span class="text-gray-400">No Image</span>
                                    </div>
                                @endif
                            </div>

                            <!-- Post Details -->
                            <div class="flex-1">
                                <!-- Description -->
                                @if($post->description)
                                    <div class="mb-4">
                                        <p class="text-gray-700">{{ $post->description }}</p>
                                    </div>
                                @endif

                                <!-- Deletion Info -->
                                <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-4">
                                    <div class="flex items-start">
                                        <svg class="w-5 h-5 text-red-500 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                        </svg>
                                        <div>
                                            <h3 class="font-semibold text-red-800">Reason for Deletion</h3>
                                            <p class="text-red-700 mt-1">{{ $post->deletion_reason }}</p>
                                            <p class="text-red-600 text-sm mt-2">
                                                Deleted {{ $post->deleted_at->diffForHumans() }} by {{ $post->deletedBy->name }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Appeal Status -->
                                @if($post->appeal_status === 'none')
                                    @if($canAppeal)
                                        <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 mb-4">
                                            <div class="flex items-start">
                                                <svg class="w-5 h-5 text-yellow-500 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                <div>
                                                    <h3 class="font-semibold text-yellow-800">Appeal Deadline</h3>
                                                    <p class="text-yellow-700 mt-1">
                                                        You have <span class="font-bold">{{ $daysLeft }} day{{ $daysLeft != 1 ? 's' : '' }}</span> left to appeal this deletion. 
                                                        After the deadline, the post will be permanently deleted.
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <button onclick="showAppealForm({{ $post->id }})" 
                                                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium flex items-center gap-2">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                                            </svg>
                                            Submit Appeal
                                        </button>
                                    @else
                                        <div class="bg-gray-50 border-l-4 border-gray-500 p-4">
                                            <p class="text-gray-700 font-semibold">Appeal deadline has passed</p>
                                            <p class="text-gray-600 text-sm mt-1">This post will be permanently deleted soon.</p>
                                        </div>
                                    @endif
                                @elseif($post->appeal_status === 'pending')
                                    <div class="bg-blue-50 border-l-4 border-blue-500 p-4">
                                        <div class="flex items-start">
                                            <svg class="w-5 h-5 text-blue-500 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <div>
                                                <h3 class="font-semibold text-blue-800">Appeal Pending</h3>
                                                <p class="text-blue-700 mt-1">Your appeal is being reviewed by an admin.</p>
                                                <div class="mt-3 bg-white border border-blue-200 rounded-lg p-3">
                                                    <p class="text-sm text-blue-900 font-medium">Your message:</p>
                                                    <p class="text-sm text-blue-800 mt-1">{{ $post->appeal_message }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @elseif($post->appeal_status === 'approved')
                                    <div class="bg-green-50 border-l-4 border-green-500 p-4">
                                        <div class="flex items-start">
                                            <svg class="w-5 h-5 text-green-500 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            <div>
                                                <h3 class="font-semibold text-green-800">Appeal Approved!</h3>
                                                <p class="text-green-700 mt-1">Your post has been restored and is now visible again.</p>
                                            </div>
                                        </div>
                                    </div>
                                @elseif($post->appeal_status === 'denied')
                                    <div class="bg-red-50 border-l-4 border-red-500 p-4">
                                        <div class="flex items-start">
                                            <svg class="w-5 h-5 text-red-500 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                            <div>
                                                <h3 class="font-semibold text-red-800">Appeal Denied</h3>
                                                <p class="text-red-700 mt-1">Your appeal has been reviewed and denied. The post will be permanently deleted.</p>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <!-- Post Metadata -->
                                <div class="mt-4 text-sm text-gray-500">
                                    <p>Posted {{ $post->created_at->format('M j, Y g:i A') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="bg-white rounded-xl shadow-md p-12 text-center">
                <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900">No deleted posts</h3>
                <p class="mt-2 text-gray-600">You don't have any deleted posts.</p>
                <a href="{{ route('feed') }}" class="mt-6 inline-block bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
                    Go to Feed
                </a>
            </div>
        @endif
    </div>

    <!-- Appeal Modal -->
    <div id="appealModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-2xl font-bold text-gray-900">Submit Appeal</h2>
                <p class="text-gray-600 mt-1">Explain why you believe your post should be restored.</p>
            </div>
            <form id="appealForm" class="p-6">
                <input type="hidden" id="appealPostId" name="post_id">
                <div class="mb-4">
                    <label for="appealMessage" class="block text-gray-700 font-medium mb-2">Your Appeal Message</label>
                    <textarea 
                        id="appealMessage" 
                        name="message" 
                        rows="6" 
                        class="w-full border-2 border-gray-300 rounded-lg p-3 focus:border-blue-500 focus:outline-none"
                        placeholder="Please explain why you believe this post should not have been deleted..."
                        required
                        maxlength="1000"></textarea>
                    <p class="text-sm text-gray-500 mt-1">Maximum 1000 characters</p>
                </div>
                <div class="flex gap-3">
                    <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium">
                        Submit Appeal
                    </button>
                    <button type="button" onclick="closeAppealModal()" class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 px-6 py-3 rounded-lg font-medium">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        function showAppealForm(postId) {
            document.getElementById('appealPostId').value = postId;
            document.getElementById('appealMessage').value = '';
            document.getElementById('appealModal').classList.remove('hidden');
        }

        function closeAppealModal() {
            document.getElementById('appealModal').classList.add('hidden');
        }

        document.getElementById('appealForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const postId = document.getElementById('appealPostId').value;
            const message = document.getElementById('appealMessage').value;

            try {
                const response = await fetch(`/posts/${postId}/appeal`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ message })
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
        });

        // Close modal on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeAppealModal();
            }
        });
    </script>
</body>
</html>
