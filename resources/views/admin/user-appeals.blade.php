<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Appeals - Admin Dashboard</title>
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
                        <span class="ml-2 text-xl font-semibold text-gray-800">Admin Dashboard - User Appeals</span>
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
                <h2 class="text-lg font-medium text-gray-900">Pending Account Deletion Appeals ({{ $appeals->count() }})</h2>
            </div>
            
            @if($appeals->count() > 0)
                <div class="divide-y divide-gray-200">
                    @foreach($appeals as $user)
                    <div class="p-6" id="appeal-{{ $user->id }}">
                        <div class="flex gap-6">
                            <!-- User Profile -->
                            <div class="w-48 flex-shrink-0">
                                @php
                                    $displayName = $user->musician?->stage_name ?: ($user->business?->business_name ?: $user->name);
                                    $profileImage = null;
                                    if ($user->musician && $user->musician->profile_picture) {
                                        $profileImage = getImageUrl($user->musician->profile_picture);
                                    } elseif ($user->business && $user->business->profile_picture) {
                                        $profileImage = getImageUrl($user->business->profile_picture);
                                    } else {
                                        $profileImage = '/images/sample-profile.jpg';
                                    }
                                    $userType = $user->musician ? 'Musician' : ($user->business ? 'Business' : 'User');
                                    $genre = $user->musician?->instrument ?: ($user->business?->venue ?: 'N/A');
                                @endphp
                                <div class="text-center">
                                    <img src="{{ $profileImage }}" alt="Profile" class="w-32 h-32 rounded-full object-cover mx-auto border-4 border-gray-200">
                                    <h3 class="text-lg font-semibold text-gray-900 mt-3">{{ $displayName }}</h3>
                                    <p class="text-sm text-gray-600">{{ $userType }}</p>
                                    <p class="text-xs text-gray-500">{{ $genre }}</p>
                                </div>
                            </div>
                            
                            <!-- Appeal Details -->
                            <div class="flex-1">
                                <div class="mb-4">
                                    <div class="grid grid-cols-2 gap-4 mb-3">
                                        <div>
                                            <p class="text-sm font-medium text-gray-700">Email:</p>
                                            <p class="text-sm text-gray-600">{{ $user->email }}</p>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-700">Account Created:</p>
                                            <p class="text-sm text-gray-600">{{ $user->created_at->format('M d, Y') }}</p>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-700">Posts:</p>
                                            <p class="text-sm text-gray-600">{{ $user->posts->count() }}</p>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-700">Followers:</p>
                                            <p class="text-sm text-gray-600">{{ $user->followers->count() }}</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Deletion Info -->
                                <div class="mb-3 bg-red-50 p-4 rounded-lg">
                                    <div class="flex items-center justify-between mb-2">
                                        <p class="text-sm font-semibold text-red-900">Scheduled for Deletion</p>
                                        <p class="text-sm text-red-700 font-medium">
                                            @php
                                                $daysLeft = now()->diffInDays($user->deletion_scheduled_at, false);
                                            @endphp
                                            {{ $daysLeft }} days remaining
                                        </p>
                                    </div>
                                    <p class="text-sm font-medium text-red-900 mb-1">Reason:</p>
                                    <p class="text-sm text-red-700">{{ $user->deletion_reason }}</p>
                                    <p class="text-xs text-red-600 mt-2">Deleted by: {{ $user->deletedBy->name ?? 'Unknown Admin' }}</p>
                                    <p class="text-xs text-red-600">Deletion Date: {{ $user->deletion_scheduled_at->format('M d, Y h:i A') }}</p>
                                </div>
                                
                                <!-- Appeal Message -->
                                <div class="mb-4 bg-blue-50 p-4 rounded-lg">
                                    <p class="text-sm font-medium text-blue-900 mb-2">Appeal Message:</p>
                                    <p class="text-sm text-blue-700">{{ $user->appeal_message }}</p>
                                    <p class="text-xs text-blue-600 mt-2">Submitted: {{ $user->appeal_at->format('M d, Y h:i A') }}</p>
                                </div>
                                
                                <!-- Admin Response Form -->
                                <div class="mb-4">
                                    <label class="text-sm font-medium text-gray-700 block mb-1">Admin Response (optional):</label>
                                    <textarea id="response-{{ $user->id }}" 
                                              class="w-full border-gray-300 rounded-md shadow-sm text-sm"
                                              rows="2"
                                              placeholder="Add a message to the user about your decision..."></textarea>
                                </div>
                                
                                <!-- Action Buttons -->
                                <div class="flex gap-3">
                                    <button onclick="respondToUserAppeal({{ $user->id }}, 'approved')" 
                                            class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-md text-sm font-medium">
                                        ✓ Approve Appeal & Restore Account
                                    </button>
                                    <button onclick="respondToUserAppeal({{ $user->id }}, 'denied')" 
                                            class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-md text-sm font-medium">
                                        ✗ Deny Appeal
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="p-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No pending appeals</h3>
                    <p class="mt-1 text-sm text-gray-500">All account deletion appeals have been reviewed.</p>
                </div>
            @endif
        </div>
    </div>

    <script>
        async function respondToUserAppeal(userId, decision) {
            const response = document.getElementById(`response-${userId}`).value;
            
            if (!confirm(`Are you sure you want to ${decision} this appeal?`)) {
                return;
            }
            
            try {
                const res = await fetch(`/admin/user-appeals/${userId}/respond`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ decision, response })
                });
                
                const data = await res.json();
                
                if (data.success) {
                    alert(data.message);
                    // Remove the appeal from the list
                    document.getElementById(`appeal-${userId}`).remove();
                    
                    // Check if no more appeals
                    const remainingAppeals = document.querySelectorAll('[id^="appeal-"]').length;
                    if (remainingAppeals === 0) {
                        location.reload();
                    }
                } else {
                    alert('Error: ' + data.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred while processing the appeal.');
            }
        }
    </script>
</body>
</html>
