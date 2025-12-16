<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $musician->stage_name ?? 'Musician' }}'s Music - Bandmate</title>
    @vite(['resources/css/app.css'])
    <style>
        body {
            background: #d1d5db;
            min-height: 100vh;
        }
    </style>
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen">
        <!-- Navigation -->
        <nav class="bg-white/10 backdrop-blur-xl border-b border-white/20 sticky top-0 z-40">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-20">
                    <a href="{{ route('feed') }}">
                        <img src="{{ asset('assets/logo_black.png') }}" alt="Bandmate Logo" class="h-10 md:hidden cursor-pointer hover:opacity-90 transition-opacity">
                        <img src="{{ asset('assets/logo_both.png') }}" alt="Bandmate Logo" class="h-12 hidden md:block cursor-pointer hover:opacity-90 transition-opacity">
                    </a>

                    <a href="{{ route('profile.show', $profileUser->id) }}" class="flex items-center gap-2 bg-white/20 hover:bg-white/30 backdrop-blur-xl px-6 py-3 rounded-full transition-all duration-300 font-medium" style="color: #e8e6eb;">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Profile
                    </a>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <!-- Header -->
            <div class="bg-white/10 backdrop-blur-xl rounded-3xl p-8 mb-8 border border-white/20">
                <div class="flex items-center gap-6">
                    <img class="w-24 h-24 rounded-full object-cover border-4 border-white shadow-xl" 
                         src="{{ $musician->profile_picture ?? 'https://via.placeholder.com/150' }}" 
                         alt="{{ $musician->stage_name }}">
                    <div>
                        <h1 class="text-4xl font-bold mb-2" style="color: #e8e6eb;">{{ $musician->stage_name ?? 'Musician' }}'s Music</h1>
                        <p class="text-lg" style="color: #b5b0bd;">{{ $musician->genre ?? 'Music Collection' }}</p>
                    </div>
                </div>
            </div>

            <!-- Music Library -->
            <div class="bg-white/10 backdrop-blur-xl rounded-3xl p-8 border border-white/20">
                <h2 class="text-2xl font-bold mb-6" style="color: #e8e6eb;">Music Library</h2>
                
                @if($musicTracks->isEmpty())
                    <div class="text-center py-16">
                        <div class="text-6xl mb-4">🎵</div>
                        <p class="text-xl" style="color: #b5b0bd;">No music tracks yet</p>
                        <p class="mt-2" style="color: #8a8595;">This musician hasn't uploaded any music yet</p>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach($musicTracks as $track)
                            <div class="bg-white/10 backdrop-blur-xl rounded-2xl p-6 hover:bg-white/20 transition-all duration-300 border border-white/10">
                                <div class="flex items-center gap-4">
                                    <div class="flex-shrink-0">
                                        <div class="w-16 h-16 rounded-xl flex items-center justify-center" style="background: linear-gradient(to bottom right, #9b87c5, #d98ba8);">
                                            <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M18 3a1 1 0 00-1.196-.98l-10 2A1 1 0 006 5v9.114A4.369 4.369 0 005 14c-1.657 0-3 .895-3 2s1.343 2 3 2 3-.895 3-2V7.82l8-1.6v5.894A4.37 4.37 0 0015 12c-1.657 0-3 .895-3 2s1.343 2 3 2 3-.895 3-2V3z"/>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h3 class="font-semibold text-lg truncate" style="color: #e8e6eb;">{{ $track->title }}</h3>
                                        <p class="text-sm" style="color: #b5b0bd;">{{ $track->duration ? gmdate("i:s", $track->duration) : 'Unknown duration' }}</p>
                                        <p class="text-xs mt-1" style="color: #8a8595;">Uploaded {{ $track->created_at->diffForHumans() }}</p>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <audio controls class="h-10" style="max-width: 300px;">
                                            <source src="{{ $track->audio_url }}" type="audio/mpeg">
                                            Your browser does not support the audio element.
                                        </audio>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</body>
</html>
