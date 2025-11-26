<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - BandMate</title>
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    @vite(['resources/css/app.css', 'resources/css/feed.css', 'resources/css/socket.css', 'resources/js/app.js', 'resources/js/socket.js'])
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="min-h-screen relative overflow-x-hidden gradient-bg">
    <div class="floating-elements fixed inset-0 pointer-events-none"></div>

    <div class="flex min-h-screen relative z-10">
        <section class="flex-1 p-6 lg:p-8 flex flex-col">
            <div class="flex justify-between items-center mb-8">
                <a href="{{ route('feed') }}" class="flex items-center gap-3 bg-white/80 backdrop-blur-xl p-3 rounded-2xl hover:bg-white/90 shadow-lg transition-all duration-300 border border-gray-200">
                    <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    <span class="text-gray-800 font-semibold">Back</span>
                </a>
                <h1 class="text-2xl font-bold text-gray-100">Settings</h1>
            </div>

            @if (session('status'))
                <div class="mb-4 p-4 rounded-xl bg-green-500/80 text-white shadow-lg">{{ session('status') }}</div>
            @endif

            <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data" class="glass-effect backdrop-blur-xl rounded-3xl shadow-2xl border border-white/20 p-6 space-y-8" id="settingsForm">
                @csrf

                <div>
                    <h2 class="text-xl font-semibold text-gray-100 mb-4">Account</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @if ($musician)
                        <div>
                            <label class="block text-gray-300 mb-1">Stage Name</label>
                            <input type="text" name="musician[stage_name]" value="{{ old('musician.stage_name', $musician->stage_name) }}" class="w-full px-4 py-3 rounded-2xl bg-white/10 border border-gray-500 text-gray-200 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-300">
                        </div>
                        @endif
                        <div>
                            <label class="block text-gray-300 mb-1">Email</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full px-4 py-3 rounded-2xl bg-white/10 border border-gray-500 text-gray-200 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-300">
                        </div>
                        <div>
                            <label class="block text-gray-300 mb-1">New Password</label>
                            <input type="password" name="password" class="w-full px-4 py-3 rounded-2xl bg-white/10 border border-gray-500 text-gray-200 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-300">
                        </div>
                        <div>
                            <label class="block text-gray-300 mb-1">Confirm Password</label>
                            <input type="password" name="password_confirmation" class="w-full px-4 py-3 rounded-2xl bg-white/10 border border-gray-500 text-gray-200 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-300">
                        </div>
                    </div>
                </div>

                @if ($musician)
                <div>
                    <h2 class="text-xl font-semibold text-gray-100 mb-4">Musician Profile</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-gray-300 mb-2 text-center">Profile Picture</label>
                            <div class="flex items-center justify-center gap-4">
                                @php
                                    $musicianAvatar = $musician->profile_picture ? getImageUrl($musician->profile_picture) : '/images/sample-profile.jpg';
                                @endphp
                                <img src="{{ $musicianAvatar }}" alt="Current avatar" class="w-20 h-20 rounded-full object-cover border-2 border-gray-500 shadow">
                                <div>
                                    <input id="musician_profile_picture" type="file" name="musician[profile_picture]" accept="image/*" class="hidden">
                                    <label for="musician_profile_picture" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl cursor-pointer bg-gradient-to-r from-purple-400 to-purple-300 text-gray-800 hover:from-purple-500 hover:to-purple-400 transition-all shadow-lg">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        Change Photo
                                    </label>
                                    <p class="text-xs text-gray-400 mt-1">JPG, PNG up to 3MB.</p>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-white/80 mb-1">First Name</label>
                            <input type="text" name="musician[first_name]" value="{{ old('musician.first_name', $musician->first_name) }}" class="w-full px-4 py-3 rounded-2xl bg-white/10 border border-white/20 text-white placeholder-white/70 focus:outline-none focus:ring-2 focus:ring-white/30">
                        </div>
                        <div>
                            <label class="block text-white/80 mb-1">Last Name</label>
                            <input type="text" name="musician[last_name]" value="{{ old('musician.last_name', $musician->last_name) }}" class="w-full px-4 py-3 rounded-2xl bg-white/10 border border-white/20 text-white placeholder-white/70 focus:outline-none focus:ring-2 focus:ring-white/30">
                        </div>
                        <div>
                            <label class="block text-white/80 mb-1">Primary Genre</label>
                            <select name="musician[genre]" id="genre" class="w-full px-4 py-3 rounded-2xl bg-white/10 border border-white/20 text-white focus:outline-none focus:ring-2 focus:ring-white/30">
                                @php $genreVal = old('musician.genre', $musician->genre); @endphp
                                <option selected value="" disabled {{ $genreVal ? '' : 'selected' }}>Choose your main genre</option>
                                <option class="text-black" value="RnB" {{ $genreVal === 'RnB' ? 'selected' : '' }}>RnB</option>
                                <option class="text-black" value="House" {{ $genreVal === 'House' ? 'selected' : '' }}>House</option>
                                <option class="text-black" value="Pop Punk" {{ $genreVal === 'Pop Punk' ? 'selected' : '' }}>Pop Punk</option>
                                <option class="text-black" value="Electronic" {{ $genreVal === 'Electronic' ? 'selected' : '' }}>Electronic</option>
                                <option class="text-black" value="Reggae" {{ $genreVal === 'Reggae' ? 'selected' : '' }}>Reggae</option>
                                <option class="text-black" value="Jazz" {{ $genreVal === 'Jazz' ? 'selected' : '' }}>Jazz</option>
                                <option class="text-black" value="Rock" {{ $genreVal === 'Rock' ? 'selected' : '' }}>Rock</option>
                            </select>
                        </div>

                        <!-- Add Genre Button -->
                        <div class="flex items-end">
                            <button type="button" id="add-genre-btn" class="w-full px-4 py-3 rounded-2xl bg-white/10 border border-white/30 text-white hover:bg-white/20 transition-colors">
                                + Add Secondary Genre
                            </button>
                        </div>

                        <!-- Secondary Genre (Hidden by default) -->
                        <div id="genre2-group" style="display: {{ old('musician.genre2', $musician->genre2) ? 'block' : 'none' }};">
                            <label class="block text-white/80 mb-1">Secondary Genre <span style="opacity: 0.7;">(Optional)</span></label>
                            <select name="musician[genre2]" id="genre2" class="w-full px-4 py-3 rounded-2xl bg-white/10 border border-white/20 text-white focus:outline-none focus:ring-2 focus:ring-white/30">
                                @php $genre2Val = old('musician.genre2', $musician->genre2); @endphp
                                <option class="text-black" value="">None</option>
                                <option class="text-black" value="RnB" {{ $genre2Val === 'RnB' ? 'selected' : '' }}>RnB</option>
                                <option class="text-black" value="House" {{ $genre2Val === 'House' ? 'selected' : '' }}>House</option>
                                <option class="text-black" value="Pop Punk" {{ $genre2Val === 'Pop Punk' ? 'selected' : '' }}>Pop Punk</option>
                                <option class="text-black" value="Electronic" {{ $genre2Val === 'Electronic' ? 'selected' : '' }}>Electronic</option>
                                <option class="text-black" value="Reggae" {{ $genre2Val === 'Reggae' ? 'selected' : '' }}>Reggae</option>
                                <option class="text-black" value="Jazz" {{ $genre2Val === 'Jazz' ? 'selected' : '' }}>Jazz</option>
                                <option class="text-black" value="Rock" {{ $genre2Val === 'Rock' ? 'selected' : '' }}>Rock</option>
                            </select>
                        </div>

                        <!-- Third Genre (Hidden by default) -->
                        <div id="genre3-group" style="display: {{ old('musician.genre3', $musician->genre3) ? 'block' : 'none' }};">
                            <label class="block text-white/80 mb-1">Third Genre <span style="opacity: 0.7;">(Optional)</span></label>
                            <select name="musician[genre3]" id="genre3" class="w-full px-4 py-3 rounded-2xl bg-white/10 border border-white/20 text-white focus:outline-none focus:ring-2 focus:ring-white/30">
                                @php $genre3Val = old('musician.genre3', $musician->genre3); @endphp
                                <option class="text-black" value="">None</option>
                                <option class="text-black" value="RnB" {{ $genre3Val === 'RnB' ? 'selected' : '' }}>RnB</option>
                                <option class="text-black" value="House" {{ $genre3Val === 'House' ? 'selected' : '' }}>House</option>
                                <option class="text-black" value="Pop Punk" {{ $genre3Val === 'Pop Punk' ? 'selected' : '' }}>Pop Punk</option>
                                <option class="text-black" value="Electronic" {{ $genre3Val === 'Electronic' ? 'selected' : '' }}>Electronic</option>
                                <option class="text-black" value="Reggae" {{ $genre3Val === 'Reggae' ? 'selected' : '' }}>Reggae</option>
                                <option class="text-black" value="Jazz" {{ $genre3Val === 'Jazz' ? 'selected' : '' }}>Jazz</option>
                                <option class="text-black" value="Rock" {{ $genre3Val === 'Rock' ? 'selected' : '' }}>Rock</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-white/80 mb-1">Primary Instrument</label>
                            <select name="musician[instrument]" id="instrument" class="w-full px-4 py-3 rounded-2xl bg-white/10 border border-white/20 text-white focus:outline-none focus:ring-2 focus:ring-white/30">
                                @php $instrVal = old('musician.instrument', $musician->instrument); @endphp
                                <option value="" disabled {{ $instrVal ? '' : 'selected' }}>Choose your main instrument</option>
                                <option class="text-black" value="Guitar" {{ $instrVal === 'Guitar' ? 'selected' : '' }}>Guitar</option>
                                <option class="text-black" value="Drums" {{ $instrVal === 'Drums' ? 'selected' : '' }}>Drums</option>
                                <option class="text-black" value="Piano" {{ $instrVal === 'Piano' ? 'selected' : '' }}>Piano</option>
                                <option class="text-black" value="Bass" {{ $instrVal === 'Bass' ? 'selected' : '' }}>Bass</option>
                                <option class="text-black" value="Vocals" {{ $instrVal === 'Vocals' ? 'selected' : '' }}>Vocals</option>
                                <option class="text-black" value="Violin" {{ $instrVal === 'Violin' ? 'selected' : '' }}>Violin</option>
                                <option class="text-black" value="Saxophone" {{ $instrVal === 'Saxophone' ? 'selected' : '' }}>Saxophone</option>
                                <option class="text-black" value="Other" {{ $instrVal === 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>

                        <!-- Add Instrument Button -->
                        <div class="flex items-end">
                            <button type="button" id="add-instrument-btn" class="w-full px-4 py-3 rounded-2xl bg-white/10 border border-white/30 text-white hover:bg-white/20 transition-colors">
                                + Add Secondary Instrument
                            </button>
                        </div>

                        <!-- Secondary Instrument (Hidden by default) -->
                        <div id="instrument2-group" style="display: {{ old('musician.instrument2', $musician->instrument2) ? 'block' : 'none' }};">
                            <label class="block text-white/80 mb-1">Secondary Instrument <span style="opacity: 0.7;">(Optional)</span></label>
                            <select name="musician[instrument2]" id="instrument2" class="w-full px-4 py-3 rounded-2xl bg-white/10 border border-white/20 text-white focus:outline-none focus:ring-2 focus:ring-white/30">
                                @php $instr2Val = old('musician.instrument2', $musician->instrument2); @endphp
                                <option class="text-black" value="">None</option>
                                <option class="text-black" value="Guitar" {{ $instr2Val === 'Guitar' ? 'selected' : '' }}>Guitar</option>
                                <option class="text-black" value="Drums" {{ $instr2Val === 'Drums' ? 'selected' : '' }}>Drums</option>
                                <option class="text-black" value="Piano" {{ $instr2Val === 'Piano' ? 'selected' : '' }}>Piano</option>
                                <option class="text-black" value="Bass" {{ $instr2Val === 'Bass' ? 'selected' : '' }}>Bass</option>
                                <option class="text-black" value="Vocals" {{ $instr2Val === 'Vocals' ? 'selected' : '' }}>Vocals</option>
                                <option class="text-black" value="Violin" {{ $instr2Val === 'Violin' ? 'selected' : '' }}>Violin</option>
                                <option class="text-black" value="Saxophone" {{ $instr2Val === 'Saxophone' ? 'selected' : '' }}>Saxophone</option>
                                <option class="text-black" value="Other" {{ $instr2Val === 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>

                        <!-- Third Instrument (Hidden by default) -->
                        <div id="instrument3-group" style="display: {{ old('musician.instrument3', $musician->instrument3) ? 'block' : 'none' }};">
                            <label class="block text-white/80 mb-1">Third Instrument <span style="opacity: 0.7;">(Optional)</span></label>
                            <select name="musician[instrument3]" id="instrument3" class="w-full px-4 py-3 rounded-2xl bg-white/10 border border-white/20 text-white focus:outline-none focus:ring-2 focus:ring-white/30">
                                @php $instr3Val = old('musician.instrument3', $musician->instrument3); @endphp
                                <option class="text-black" value="">None</option>
                                <option class="text-black" value="Guitar" {{ $instr3Val === 'Guitar' ? 'selected' : '' }}>Guitar</option>
                                <option class="text-black" value="Drums" {{ $instr3Val === 'Drums' ? 'selected' : '' }}>Drums</option>
                                <option class="text-black" value="Piano" {{ $instr3Val === 'Piano' ? 'selected' : '' }}>Piano</option>
                                <option class="text-black" value="Bass" {{ $instr3Val === 'Bass' ? 'selected' : '' }}>Bass</option>
                                <option class="text-black" value="Vocals" {{ $instr3Val === 'Vocals' ? 'selected' : '' }}>Vocals</option>
                                <option class="text-black" value="Violin" {{ $instr3Val === 'Violin' ? 'selected' : '' }}>Violin</option>
                                <option class="text-black" value="Saxophone" {{ $instr3Val === 'Saxophone' ? 'selected' : '' }}>Saxophone</option>
                                <option class="text-black" value="Other" {{ $instr3Val === 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-white/80 mb-1">Location</label>
                            <select name="musician[location]" id="musicianLocation" class="w-full px-4 py-3 rounded-2xl bg-white/10 border border-white/20 text-white focus:outline-none focus:ring-2 focus:ring-white/30">
                                @php $locVal = old('musician.location', $musician->location); @endphp
                                <option value="" disabled {{ $locVal ? '' : 'selected' }}>Select your location</option>
                                <option class="text-black" value="Balibago" {{ $locVal === 'Balibago' ? 'selected' : '' }}>Balibago</option>
                                <option class="text-black" value="CM Recto" {{ $locVal === 'CM Recto' ? 'selected' : '' }}>CM Recto</option></option>
                                <option class="text-black" value="Pampang" {{ $locVal === 'Pampang' ? 'selected' : '' }}>Pampang</option>
                                <option class="text-black" value="San Nicolas" {{ $locVal === 'San Nicolas' ? 'selected' : '' }}>San Nicolas</option>
                                <option class="text-black" value="Santa Teresa" {{ $locVal === 'Santa Teresa' ? 'selected' : '' }}>Santa Teresa</option>
                                <option class="text-black" value="Anunas" {{ $locVal === 'Anunas' ? 'selected' : '' }}>Anunas</option>
                                <option class="text-black" value="Agapito del Rosario" {{ $locVal === 'Agapito del Rosario' ? 'selected' : '' }}>Agapito del Rosario</option>
                                <option class="text-black" value="Cutcut" {{ $locVal === 'Cutcut' ? 'selected' : '' }}>Cutcut</option>
                                <option class="text-black" value="Capaya" {{ $locVal === 'Capaya' ? 'selected' : '' }}>Capaya</option>
                                <option class="text-black" value="Telabastagan" {{ $locVal === 'Telabastagan' ? 'selected' : '' }}>Telabastagan</option>
                                <option class="text-black" value="Lourdes" {{ $locVal === 'Lourdes' ? 'selected' : '' }}>Lourdes</option>
                                <option class="text-black" value="Malabanias" {{ $locVal === 'Malabanias' ? 'selected' : '' }}>Malabanias</option>
                                <option class="text-black" value="Tabun" {{ $locVal === 'Tabun' ? 'selected' : '' }}>Tabun</option>
                            </select>
                            <input type="hidden" name="musician[latitude]" id="musicianLatitude" value="{{ old('musician.latitude', $musician->latitude) }}">
                            <input type="hidden" name="musician[longitude]" id="musicianLongitude" value="{{ old('musician.longitude', $musician->longitude) }}">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-white/80 mb-1">Bio</label>
                            <textarea name="musician[bio]" rows="4" class="w-full px-4 py-3 rounded-2xl bg-white/10 border border-white/20 text-white placeholder-white/70 focus:outline-none focus:ring-2 focus:ring-white/30">{{ old('musician.bio', $musician->bio) }}</textarea>
                        </div>

                        <!-- Social Media Links -->
                        <div class="md:col-span-2">
                            <h3 class="text-lg font-semibold text-white mb-3">Social Media Links</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-white/80 mb-1">
                                        <span class="inline-flex items-center gap-2">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                                            Instagram
                                        </span>
                                    </label>
                                    <input type="url" name="musician[instagram_url]" value="{{ old('musician.instagram_url', $musician->instagram_url) }}" placeholder="https://instagram.com/yourprofile" class="w-full px-4 py-3 rounded-2xl bg-white/10 border border-white/20 text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-white/30">
                                </div>
                                <div>
                                    <label class="block text-white/80 mb-1">
                                        <span class="inline-flex items-center gap-2">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                                            Facebook
                                        </span>
                                    </label>
                                    <input type="url" name="musician[facebook_url]" value="{{ old('musician.facebook_url', $musician->facebook_url) }}" placeholder="https://facebook.com/yourprofile" class="w-full px-4 py-3 rounded-2xl bg-white/10 border border-white/20 text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-white/30">
                                </div>
                                <div>
                                    <label class="block text-white/80 mb-1">
                                        <span class="inline-flex items-center gap-2">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                                            Twitter
                                        </span>
                                    </label>
                                    <input type="url" name="musician[twitter_url]" value="{{ old('musician.twitter_url', $musician->twitter_url) }}" placeholder="https://twitter.com/yourprofile" class="w-full px-4 py-3 rounded-2xl bg-white/10 border border-white/20 text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-white/30">
                                </div>
                                <div>
                                    <label class="block text-white/80 mb-1">
                                        <span class="inline-flex items-center gap-2">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                                            YouTube
                                        </span>
                                    </label>
                                    <input type="url" name="musician[youtube_url]" value="{{ old('musician.youtube_url', $musician->youtube_url) }}" placeholder="https://youtube.com/@yourchannel" class="w-full px-4 py-3 rounded-2xl bg-white/10 border border-white/20 text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-white/30">
                                </div>
                            </div>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-white/80 mb-2 text-center">Musician Credentials</label>
                            <div class="flex items-center justify-center gap-4">
                                @if($musician->credential_document)
                                    <a href="{{ $musician->credential_document }}" target="_blank" class="text-purple-400 hover:text-purple-300 underline text-sm">View Current Credentials</a>
                                @else
                                    <span class="text-white/50 text-sm">No credentials uploaded</span>
                                @endif
                                <div>
                                    <input id="musician_credential" type="file" name="musician[credential_document]" accept=".jpg,.jpeg,.png,.pdf" class="hidden">
                                    <label for="musician_credential" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl cursor-pointer bg-gradient-to-r from-blue-500 to-cyan-500 text-white hover:from-blue-600 hover:to-cyan-600 transition-all shadow-lg">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                        Upload Credentials
                                    </label>
                                    <p class="text-xs text-white/60 mt-1">JPG, PNG, PDF up to 5MB. (e.g., certificates, awards, performance history)</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                @if ($business)
                <div>
                    <h2 class="text-xl font-semibold text-white mb-4">Business Profile</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-white/80 mb-2 text-center">Profile Picture</label>
                            <div class="flex items-center justify-center gap-4">
                                @php
                                    $businessAvatar = $business->profile_picture ? getImageUrl($business->profile_picture) : '/images/sample-profile.jpg';
                                @endphp
                                <img src="{{ $businessAvatar }}" alt="Current logo" class="w-20 h-20 rounded-full object-cover border-2 border-white/30 shadow">
                                <div>
                                    <input id="business_profile_picture" type="file" name="business[profile_picture]" accept="image/*" class="hidden">
                                    <label for="business_profile_picture" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl cursor-pointer bg-gradient-to-r from-purple-500 to-pink-500 text-white hover:from-purple-600 hover:to-pink-600 transition-all shadow-lg">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        Change Logo
                                    </label>
                                    <p class="text-xs text-white/60 mt-1">JPG, PNG up to 3MB.</p>
                                </div>
                            </div>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-white/80 mb-2 text-center">Business Permit</label>
                            <div class="flex items-center justify-center gap-4">
                                @if($business->business_permit)
                                    <a href="{{ $business->business_permit }}" target="_blank" class="text-purple-400 hover:text-purple-300 underline text-sm">View Current Permit</a>
                                @else
                                    <span class="text-white/50 text-sm">No permit uploaded</span>
                                @endif
                                <div>
                                    <input id="business_permit" type="file" name="business[business_permit]" accept=".jpg,.jpeg,.png,.pdf" class="hidden">
                                    <label for="business_permit" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl cursor-pointer bg-gradient-to-r from-blue-500 to-cyan-500 text-white hover:from-blue-600 hover:to-cyan-600 transition-all shadow-lg">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                        Upload Permit
                                    </label>
                                    <p class="text-xs text-white/60 mt-1">JPG, PNG, PDF up to 5MB.</p>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="block text-white/80 mb-1">Business Name</label>
                            <input type="text" name="business[business_name]" value="{{ old('business.business_name', $business->business_name) }}" class="w-full px-4 py-3 rounded-2xl bg-white/10 border border-white/20 text-white placeholder-white/70 focus:outline-none focus:ring-2 focus:ring-white/30">
                        </div>
                        <div>
                            <label class="block text-white/80 mb-1">Contact Email</label>
                            <input type="email" name="business[contact_email]" value="{{ old('business.contact_email', $business->contact_email) }}" class="w-full px-4 py-3 rounded-2xl bg-white/10 border border-white/20 text-white placeholder-white/70 focus:outline-none focus:ring-2 focus:ring-white/30">
                        </div>
                        <div>
                            <label class="block text-white/80 mb-1">Phone Number</label>
                            <input type="text" name="business[phone_number]" value="{{ old('business.phone_number', $business->phone_number) }}" class="w-full px-4 py-3 rounded-2xl bg-white/10 border border-white/20 text-white placeholder-white/70 focus:outline-none focus:ring-2 focus:ring-white/30">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-white/80 mb-2">Address</label>
                            <div class="space-y-4">
                                <input type="text" name="business[address]" value="{{ old('business.address', $business->address) }}" placeholder="Enter your address (e.g., 123 Main St, New York, NY)" class="w-full px-4 py-3 rounded-2xl bg-white/10 border border-white/20 text-white placeholder-white/70 focus:outline-none focus:ring-2 focus:ring-white/30">
                                <!-- businessAddressMap removed to reduce duplicate maps; address still stored in hidden inputs -->
                                <input type="hidden" name="business[address_latitude]" id="businessAddressLatitude" value="{{ old('business.address_latitude', $business->latitude) }}">
                                <input type="hidden" name="business[address_longitude]" id="businessAddressLongitude" value="{{ old('business.address_longitude', $business->longitude) }}">
                            </div>
                        </div>
                        <div>
                            <label class="block text-white/80 mb-1">Location</label>
                            <select name="business[location]" id="businessLocation" class="w-full px-4 py-3 rounded-2xl bg-white/10 border border-white/20 text-white focus:outline-none focus:ring-2 focus:ring-white/30">
                                @php $locationVal = old('business.location', $business->location); @endphp
                                <option class="text-black" value="" disabled {{ $locationVal ? '' : 'selected' }}>Select your location</option>
                                <option class="text-black" value="Balibago" {{ $locationVal === 'Balibago' ? 'selected' : '' }}>Balibago</option>
                                <option class="text-black" value="CM Recto" {{ $locationVal === 'CM Recto' ? 'selected' : '' }}>CM Recto</option>
                                <option class="text-black" value="Clark" {{ $locationVal === 'Clark' ? 'selected' : '' }}>Clark</option>
                                <option class="text-black" value="Malabanias" {{ $locationVal === 'Malabanias' ? 'selected' : '' }}>Malabanias</option>
                                <option class="text-black" value="Friendship" {{ $locationVal === 'Friendship' ? 'selected' : '' }}>Friendship</option>
                                <option class="text-black" value="Other" {{ $locationVal === 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                            <input type="hidden" name="business[latitude]" id="businessLatitude" value="{{ old('business.latitude', $business->latitude) }}">
                            <input type="hidden" name="business[longitude]" id="businessLongitude" value="{{ old('business.longitude', $business->longitude) }}">
                        </div>
                        <div>
                            <label class="block text-white/80 mb-1">Venue Offered</label>
                            <select name="business[venue]" class="w-full px-4 py-3 rounded-2xl bg-white/10 border border-white/20 text-white focus:outline-none focus:ring-2 focus:ring-white/30"">
                                @php $venueVal = old('business.venue', $business->venue); @endphp
                                <option class="text-black" value="" disabled {{ $venueVal ? '' : 'selected' }}>Select the venue you offer</option>
                                <option class="text-black" value="Studio" {{ $venueVal === 'Studio' ? 'selected' : '' }}>Studio</option>
                                <option class="text-black" value="Club" {{ $venueVal === 'Club' ? 'selected' : '' }}>Club</option>
                                <option class="text-black" value="Theater" {{ $venueVal === 'Theater' ? 'selected' : '' }}>Theater</option>
                                <option class="text-black" value="Cafe" {{ $venueVal === 'Cafe' ? 'selected' : '' }}>Café</option>
                                <option class="text-black" value="Restaurant" {{ $venueVal === 'Restaurant' ? 'selected' : '' }}>Restaurant</option>
                                <option class="text-black" value="Bar" {{ $venueVal === 'Bar' ? 'selected' : '' }}>Bar & Lounge</option>
                                <option class="text-black" value="Event Venue" {{ $venueVal === 'Event Venue' ? 'selected' : '' }}>Event Venue</option>
                                <option class="text-black" value="Music Hall" {{ $venueVal === 'Music Hall' ? 'selected' : '' }}>Music Hall</option>
                                <option class="text-black" value="Other" {{ $venueVal === 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>

                        <!-- Social Media Links -->
                        <div class="md:col-span-2">
                            <h3 class="text-lg font-semibold text-white mb-3">Social Media Links</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-white/80 mb-1">
                                        <span class="inline-flex items-center gap-2">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                                            Instagram
                                        </span>
                                    </label>
                                    <input type="url" name="business[instagram_url]" value="{{ old('business.instagram_url', $business->instagram_url) }}" placeholder="https://instagram.com/yourbusiness" class="w-full px-4 py-3 rounded-2xl bg-white/10 border border-white/20 text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-white/30">
                                </div>
                                <div>
                                    <label class="block text-white/80 mb-1">
                                        <span class="inline-flex items-center gap-2">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                                            Facebook
                                        </span>
                                    </label>
                                    <input type="url" name="business[facebook_url]" value="{{ old('business.facebook_url', $business->facebook_url) }}" placeholder="https://facebook.com/yourbusiness" class="w-full px-4 py-3 rounded-2xl bg-white/10 border border-white/20 text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-white/30">
                                </div>
                                <div>
                                    <label class="block text-white/80 mb-1">
                                        <span class="inline-flex items-center gap-2">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                                            Twitter
                                        </span>
                                    </label>
                                    <input type="url" name="business[twitter_url]" value="{{ old('business.twitter_url', $business->twitter_url) }}" placeholder="https://twitter.com/yourbusiness" class="w-full px-4 py-3 rounded-2xl bg-white/10 border border-white/20 text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-white/30">
                                </div>
                                <div>
                                    <label class="block text-white/80 mb-1">
                                        <span class="inline-flex items-center gap-2">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm1 16.057v1.833c0 .609-.448 1.11-1 1.11-.552 0-1-.501-1-1.11v-1.833c-1.329-.424-2.352-1.562-2.622-2.946-.094-.487.334-.941.832-.941.346 0 .663.221.781.55.25.703.975 1.215 1.839 1.215 1.036 0 1.875-.791 1.875-1.766s-.839-1.766-1.875-1.766c-1.768 0-3.204-1.364-3.204-3.045s1.436-3.045 3.204-3.045c1.329-.424 2.352-1.562 2.622-2.946.094-.487-.334-.941-.832-.941-.346 0-.663.221-.781.55-.25.703-.975 1.215-1.839 1.215-1.036 0-1.875.791-1.875 1.766s.839 1.766 1.875 1.766c1.768 0 3.204 1.364 3.204 3.045S14.768 14 13 14c-1.036 0-1.875-.791-1.875-1.766 0-.975.839-1.766 1.875-1.766.552 0 1-.448 1-1s-.448-1-1-1c-1.768 0-3.204 1.364-3.204 3.045 0 1.681 1.436 3.045 3.204 3.045.552 0 1 .448 1 1z"/></svg>
                                            Website
                                        </span>
                                    </label>
                                    <input type="url" name="business[website_url]" value="{{ old('business.website_url', $business->website_url) }}" placeholder="https://yourwebsite.com" class="w-full px-4 py-3 rounded-2xl bg-white/10 border border-white/20 text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-white/30">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <div class="flex justify-end">
                    <button type="submit" class="px-6 py-3 bg-gradient-to-r from-purple-500 to-pink-500 text-white rounded-2xl hover:from-purple-600 hover:to-pink-600 transition-all duration-300 shadow-lg hover-glow font-semibold">Save Changes</button>
                </div>
            </form>
        </section>
    </div>

    <script>
        let musicianMap, businessMap;
        let musicianMarker, businessMarker;

        document.addEventListener('DOMContentLoaded', function() {
            // Initialize maps
            if (document.getElementById('musicianMap')) {
                initMusicianMap();
            }
            if (document.getElementById('businessMap')) {
                initBusinessMap();
            }
            // businessAddressMap removed — address is still saved via hidden inputs
            
            // Genre and Instrument duplicate prevention for musicians
            const genreSelects = ['genre', 'genre2', 'genre3'];
            const instrumentSelects = ['instrument', 'instrument2', 'instrument3'];
            
            function updateSelectOptions(selectIds) {
                const selectedValues = selectIds.map(id => {
                    const select = document.getElementById(id);
                    return select ? select.value : null;
                }).filter(v => v);
                
                selectIds.forEach(id => {
                    const select = document.getElementById(id);
                    if (!select) return;
                    
                    const currentValue = select.value;
                    const options = select.querySelectorAll('option');
                    
                    options.forEach(option => {
                        if (option.value === '' || option.value === currentValue) {
                            option.disabled = false;
                            option.style.display = '';
                        } else if (selectedValues.includes(option.value)) {
                            option.disabled = true;
                            option.style.display = 'none';
                        } else {
                            option.disabled = false;
                            option.style.display = '';
                        }
                    });
                });
            }
            
            genreSelects.forEach(id => {
                const select = document.getElementById(id);
                if (select) {
                    select.addEventListener('change', () => updateSelectOptions(genreSelects));
                }
            });
            
            instrumentSelects.forEach(id => {
                const select = document.getElementById(id);
                if (select) {
                    select.addEventListener('change', () => updateSelectOptions(instrumentSelects));
                }
            });
            
            // Initial update to handle pre-selected values
            updateSelectOptions(genreSelects);
            updateSelectOptions(instrumentSelects);
        });

        // Maps removed: provide simple geocoding-only helpers that set hidden coords and fill the location input.
        // Location inputs removed; geocoding/search helpers are not necessary anymore.

        // businessAddressMap functionality removed. Address field remains and hidden inputs
        // `businessAddressLatitude` / `businessAddressLongitude` are preserved so server-side
        // processing still receives saved coordinates if present.

        document.addEventListener('DOMContentLoaded', () => {
    const passwordInput = document.querySelector('input[name="password"]');
    const form = document.querySelector('#settingsForm');
    
    if (!passwordInput || !form) {
        console.error('Form or password input not found');
        return;
    }
    
    // Create a warning element
    const warning = document.createElement('p');
    warning.className = 'text-red-400 text-sm mt-1';
    warning.style.display = 'none';
    warning.textContent = 'Password must be at least 8 characters long.';
    passwordInput.parentNode.appendChild(warning);

    // Live validation as the user types
    passwordInput.addEventListener('input', () => {
        if (passwordInput.value.length > 0 && passwordInput.value.length < 8) {
            warning.style.display = 'block';
        } else {
            warning.style.display = 'none';
        }
    });

    // Add Genre/Instrument buttons functionality
    const addGenreBtn = document.getElementById('add-genre-btn');
    const addInstrumentBtn = document.getElementById('add-instrument-btn');
    const genre2Group = document.getElementById('genre2-group');
    const genre3Group = document.getElementById('genre3-group');
    const instrument2Group = document.getElementById('instrument2-group');
    const instrument3Group = document.getElementById('instrument3-group');

    let genreCount = genre2Group && genre2Group.style.display !== 'none' ? (genre3Group && genre3Group.style.display !== 'none' ? 3 : 2) : 1;
    let instrumentCount = instrument2Group && instrument2Group.style.display !== 'none' ? (instrument3Group && instrument3Group.style.display !== 'none' ? 3 : 2) : 1;

    // Update button text and visibility based on initial state
    if (addGenreBtn) {
        if (genreCount === 2) {
            addGenreBtn.textContent = '+ Add Third Genre';
        } else if (genreCount === 3) {
            addGenreBtn.style.display = 'none';
        }

        addGenreBtn.addEventListener('click', function() {
            if (genreCount === 1) {
                genre2Group.style.display = 'block';
                genreCount = 2;
                addGenreBtn.textContent = '+ Add Third Genre';
            } else if (genreCount === 2) {
                genre3Group.style.display = 'block';
                genreCount = 3;
                addGenreBtn.style.display = 'none';
            }
        });
    }

    if (addInstrumentBtn) {
        if (instrumentCount === 2) {
            addInstrumentBtn.textContent = '+ Add Third Instrument';
        } else if (instrumentCount === 3) {
            addInstrumentBtn.style.display = 'none';
        }

        addInstrumentBtn.addEventListener('click', function() {
            if (instrumentCount === 1) {
                instrument2Group.style.display = 'block';
                instrumentCount = 2;
                addInstrumentBtn.textContent = '+ Add Third Instrument';
            } else if (instrumentCount === 2) {
                instrument3Group.style.display = 'block';
                instrumentCount = 3;
                addInstrumentBtn.style.display = 'none';
            }
        });
    }

    // Location coordinate mapping
    const locationCoordinates = {
        'Balibago': { lat: 15.1455, lng: 120.5896 },
        'CM Recto': { lat: 15.1467, lng: 120.5847 },
        'Pampang': { lat: 15.1589, lng: 120.5881 },
        'San Nicolas': { lat: 15.1391, lng: 120.5869 },
        'Santa Teresa': { lat: 15.1523, lng: 120.5934 },
        'Anunas': { lat: 15.1401, lng: 120.5761 },
        'Agapito del Rosario': { lat: 15.1356, lng: 120.5923 },
        'Cutcut': { lat: 15.1612, lng: 120.5734 },
        'Capaya': { lat: 15.1723, lng: 120.5645 },
        'Telabastagan': { lat: 15.1289, lng: 120.5678 },
        'Lourdes': { lat: 15.1478, lng: 120.5812 },
        'Malabanias': { lat: 15.1634, lng: 120.5923 },
        'Tabun': { lat: 15.1556, lng: 120.5667 },
        'Clark': { lat: 15.1859, lng: 120.5600 },
        'Friendship': { lat: 15.1789, lng: 120.5523 }
    };

    // Update musician location coordinates
    const musicianLocation = document.getElementById('musicianLocation');
    const musicianLatitude = document.getElementById('musicianLatitude');
    const musicianLongitude = document.getElementById('musicianLongitude');
    
    if (musicianLocation && musicianLatitude && musicianLongitude) {
        musicianLocation.addEventListener('change', function() {
            const selectedLocation = this.value;
            if (locationCoordinates[selectedLocation]) {
                musicianLatitude.value = locationCoordinates[selectedLocation].lat;
                musicianLongitude.value = locationCoordinates[selectedLocation].lng;
            }
        });
    }

    // Update business location coordinates
    const businessLocation = document.getElementById('businessLocation');
    const businessLatitude = document.getElementById('businessLatitude');
    const businessLongitude = document.getElementById('businessLongitude');
    
    if (businessLocation && businessLatitude && businessLongitude) {
        businessLocation.addEventListener('change', function() {
            const selectedLocation = this.value;
            if (locationCoordinates[selectedLocation]) {
                businessLatitude.value = locationCoordinates[selectedLocation].lat;
                businessLongitude.value = locationCoordinates[selectedLocation].lng;
            }
        });
    }

    // Prevent submission if too short
    form.addEventListener('submit', (e) => {
        console.log('Form submitting...', {
            action: form.action,
            method: form.method,
            passwordLength: passwordInput.value.length
        });

        if (passwordInput.value.length > 0 && passwordInput.value.length < 8) {
            e.preventDefault();
            console.log('Form submission prevented - password too short');
            warning.style.display = 'block';

            // Optional: add a small shake animation
            passwordInput.classList.add('ring-2', 'ring-red-500');
            setTimeout(() => passwordInput.classList.remove('ring-2', 'ring-red-500'), 500);
        } else {
            console.log('Form validation passed, submitting...');
        }
    });
    });
    </script>
</body>
</html>


