<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Conversations - Admin Dashboard</title>
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
                <div class="flex items-center justify-between">
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
                            <h1 class="text-2xl font-bold text-gray-900">{{ $displayName }}'s Conversations</h1>
                            <p class="text-gray-600">{{ $user->email }}</p>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    @if($user->musician) bg-purple-100 text-purple-800
                                    @elseif($user->business) bg-blue-100 text-blue-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ $userType }}
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <a href="{{ route('admin.dashboard') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md text-sm">
                        ← Back to Dashboard
                    </a>
                </div>
            </div>
        </div>

        <!-- Conversations List -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Message Conversations ({{ $conversations->count() }})</h2>
            </div>
            
            @if($conversations->count() > 0)
                <div class="divide-y divide-gray-200">
                    @foreach($conversations as $conversation)
                    @php
                        $otherUser = $conversation['other_user'];
                        $messages = $conversation['messages'];
                        $lastMessage = $conversation['last_message'];
                        
                        $otherAvatar = null;
                        if ($otherUser->musician && $otherUser->musician->profile_picture) {
                            $otherAvatar = getImageUrl($otherUser->musician->profile_picture);
                        } elseif ($otherUser->business && $otherUser->business->profile_picture) {
                            $otherAvatar = getImageUrl($otherUser->business->profile_picture);
                        }
                        
                        $otherDisplayName = $otherUser->musician?->stage_name ?: ($otherUser->business?->business_name ?: $otherUser->name);
                    @endphp
                    
                    <div class="p-6">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center">
                                @if($otherAvatar)
                                    <img class="h-12 w-12 rounded-full object-cover" src="{{ $otherAvatar }}" alt="">
                                @else
                                    <div class="h-12 w-12 rounded-full bg-gray-300 flex items-center justify-center">
                                        <span class="text-lg font-medium text-gray-700">{{ substr($otherDisplayName, 0, 1) }}</span>
                                    </div>
                                @endif
                                
                                <div class="ml-4">
                                    <h3 class="text-lg font-semibold text-gray-900">Conversation with {{ $otherDisplayName }}</h3>
                                    <p class="text-sm text-gray-600">{{ $otherUser->email }}</p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        {{ $conversation['message_count'] }} messages • Last: {{ $lastMessage->created_at->diffForHumans() }}
                                    </p>
                                </div>
                            </div>
                            
                            <button onclick="toggleMessages('conversation-{{ $loop->index }}')" 
                                    class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                                <span class="toggle-text">Hide Messages</span>
                            </button>
                        </div>
                        
                        <div id="conversation-{{ $loop->index }}" class="mt-4 ml-16 space-y-3">
                            @foreach($messages as $message)
                            @php
                                $isFromUser = $message->sender_id == $user->id;
                            @endphp
                            <div class="flex {{ $isFromUser ? 'justify-end' : 'justify-start' }}">
                                <div class="max-w-xl">
                                    <div class="flex items-start {{ $isFromUser ? 'flex-row-reverse' : '' }}">
                                        <div class="{{ $isFromUser ? 'ml-2' : 'mr-2' }}">
                                            @if($isFromUser)
                                                @if($avatar)
                                                    <img class="h-8 w-8 rounded-full object-cover" src="{{ $avatar }}" alt="">
                                                @else
                                                    <div class="h-8 w-8 rounded-full bg-purple-300 flex items-center justify-center">
                                                        <span class="text-xs font-medium text-white">{{ substr($displayName, 0, 1) }}</span>
                                                    </div>
                                                @endif
                                            @else
                                                @if($otherAvatar)
                                                    <img class="h-8 w-8 rounded-full object-cover" src="{{ $otherAvatar }}" alt="">
                                                @else
                                                    <div class="h-8 w-8 rounded-full bg-blue-300 flex items-center justify-center">
                                                        <span class="text-xs font-medium text-white">{{ substr($otherDisplayName, 0, 1) }}</span>
                                                    </div>
                                                @endif
                                            @endif
                                        </div>
                                        <div>
                                            <div class="px-4 py-2 rounded-2xl {{ $isFromUser ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-900' }}">
                                                <p class="text-sm">{{ $message->message }}</p>
                                            </div>
                                            <p class="text-xs text-gray-500 mt-1 {{ $isFromUser ? 'text-right' : '' }}">
                                                {{ $message->created_at->format('M j, Y g:i A') }}
                                                @if($message->is_read)
                                                    <span class="ml-1 text-green-600">✓ Read</span>
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No conversations</h3>
                    <p class="mt-1 text-sm text-gray-500">This user hasn't sent or received any messages yet.</p>
                </div>
            @endif
        </div>
    </div>

    <script>
        function toggleMessages(conversationId) {
            const element = document.getElementById(conversationId);
            const button = event.target.closest('button');
            const toggleText = button.querySelector('.toggle-text');
            
            if (element.classList.contains('hidden')) {
                element.classList.remove('hidden');
                if (toggleText) toggleText.textContent = 'Hide Messages';
            } else {
                element.classList.add('hidden');
                if (toggleText) toggleText.textContent = 'View Messages';
            }
        }
    </script>
</body>
</html>
