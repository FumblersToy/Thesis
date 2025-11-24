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
                <h1 class="text-2xl font-bold text-white">Settings</h1>
            </div>

            @if (session('status'))
                <div class="mb-4 p-4 rounded-xl bg-green-500/80 text-white shadow-lg">{{ session('status') }}</div>
            @endif

            <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data" class="glass-effect backdrop-blur-xl rounded-3xl shadow-2xl border border-white/20 p-6 space-y-8" id="settingsForm">
                @csrf

                <div>
                    <h2 class="text-xl font-semibold text-white mb-4">Account</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @if ($musician)
                        <div>
                            <label class="block text-white/80 mb-1">Stage Name</label>
                            <input type="text" name="musician[stage_name]" value="{{ old('musician.stage_name', $musician->stage_name) }}" class="w-full px-4 py-3 rounded-2xl bg-white/10 border border-white/20 text-white placeholder-white/70 focus:outline-none focus:ring-2 focus:ring-white/30">
                        </div>
                        @endif
                        <div>
                            <label class="block text-white/80 mb-1">Email</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full px-4 py-3 rounded-2xl bg-white/10 border border-white/20 text-white placeholder-white/70 focus:outline-none focus:ring-2 focus:ring-white/30">
                        </div>
                        <div>
                            <label class="block text-white/80 mb-1">New Password</label>
                            <input type="password" name="password" class="w-full px-4 py-3 rounded-2xl bg-white/10 border border-white/20 text-white placeholder-white/70 focus:outline-none focus:ring-2 focus:ring-white/30">
                        </div>
                        <div>
                            <label class="block text-white/80 mb-1">Confirm Password</label>
                            <input type="password" name="password_confirmation" class="w-full px-4 py-3 rounded-2xl bg-white/10 border border-white/20 text-white placeholder-white/70 focus:outline-none focus:ring-2 focus:ring-white/30">
                        </div>
                    </div>
                </div>

                @if ($musician)
                <div>
                    <h2 class="text-xl font-semibold text-white mb-4">Musician Profile</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-white/80 mb-2 text-center">Profile Picture</label>
                            <div class="flex items-center justify-center gap-4">
                                @php
                                    $musicianAvatar = $musician->profile_picture ? getImageUrl($musician->profile_picture) : '/images/sample-profile.jpg';
                                @endphp
                                <img src="{{ $musicianAvatar }}" alt="Current avatar" class="w-20 h-20 rounded-full object-cover border-2 border-white/30 shadow">
                                <div>
                                    <input id="musician_profile_picture" type="file" name="musician[profile_picture]" accept="image/*" class="hidden">
                                    <label for="musician_profile_picture" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl cursor-pointer bg-gradient-to-r from-purple-500 to-pink-500 text-white hover:from-purple-600 hover:to-pink-600 transition-all shadow-lg">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        Change Photo
                                    </label>
                                    <p class="text-xs text-white/60 mt-1">JPG, PNG up to 3MB.</p>
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
                            <select name="musician[genre]" class="w-full px-4 py-3 rounded-2xl bg-white/10 border border-white/20 text-white focus:outline-none focus:ring-2 focus:ring-white/30">
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
                        <div>
                            <label class="block text-white/80 mb-1">Primary Instrument</label>
                            <select name="musician[instrument]" class="w-full px-4 py-3 rounded-2xl bg-white/10 border border-white/20 text-white focus:outline-none focus:ring-2 focus:ring-white/30">
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
                        <div>
                            <label class="block text-white/80 mb-1">Location</label>
                            <select name="musician[location]" class="w-full px-4 py-3 rounded-2xl bg-white/10 border border-white/20 text-white focus:outline-none focus:ring-2 focus:ring-white/30">
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
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-white/80 mb-1">Bio</label>
                            <textarea name="musician[bio]" rows="4" class="w-full px-4 py-3 rounded-2xl bg-white/10 border border-white/20 text-white placeholder-white/70 focus:outline-none focus:ring-2 focus:ring-white/30">{{ old('musician.bio', $musician->bio) }}</textarea>
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
                            <select name="business[location]" class="w-full px-4 py-3 rounded-2xl bg-white/10 border border-white/20 text-white focus:outline-none focus:ring-2 focus:ring-white/30">
                                @php $locationVal = old('business.location', $business->location); @endphp
                                <option class="text-black" value="" disabled {{ $locationVal ? '' : 'selected' }}>Select your location</option>
                                <option class="text-black" value="Balibago" {{ $locationVal === 'Balibago' ? 'selected' : '' }}>Balibago</option>
                                <option class="text-black" value="CM Recto" {{ $locationVal === 'CM Recto' ? 'selected' : '' }}>CM Recto</option>
                                <option class="text-black" value="Clark" {{ $locationVal === 'Clark' ? 'selected' : '' }}>Clark</option>
                                <option class="text-black" value="Malabanias" {{ $locationVal === 'Malabanias' ? 'selected' : '' }}>Malabanias</option>
                                <option class="text-black" value="Friendship" {{ $locationVal === 'Friendship' ? 'selected' : '' }}>Friendship</option>
                                <option class="text-black" value="Other" {{ $locationVal === 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
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


