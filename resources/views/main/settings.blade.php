<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - BandMate</title>
    @vite(['resources/css/app.css', 'resources/css/feed.css', 'resources/css/socket.css', 'resources/js/app.js', 'resources/js/socket.js'])
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script src="https://cdn.tailwindcss.com"></script>
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

            <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data" class="glass-effect backdrop-blur-xl rounded-3xl shadow-2xl border border-white/20 p-6 space-y-8">
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
                        <div class="md:col-span-2">
                            <label class="block text-white/80 mb-1">Bio</label>
                            <textarea name="musician[bio]" rows="4" class="w-full px-4 py-3 rounded-2xl bg-white/10 border border-white/20 text-white placeholder-white/70 focus:outline-none focus:ring-2 focus:ring-white/30">{{ old('musician.bio', $musician->bio) }}</textarea>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-white/80 mb-2">Location</label>
                            <div class="space-y-4">
                                <input type="text" name="musician[location_name]" value="{{ old('musician.location_name', $musician->location_name) }}" placeholder="Enter your location (e.g., New York, NY)" class="w-full px-4 py-3 rounded-2xl bg-white/10 border border-white/20 text-white placeholder-white/70 focus:outline-none focus:ring-2 focus:ring-white/30">
                                <div id="musicianMap" class="h-64 rounded-2xl overflow-hidden border border-white/20"></div>
                                <input type="hidden" name="musician[latitude]" id="musicianLatitude" value="{{ old('musician.latitude', $musician->latitude) }}">
                                <input type="hidden" name="musician[longitude]" id="musicianLongitude" value="{{ old('musician.longitude', $musician->longitude) }}">
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
                                <div id="businessAddressMap" class="h-64 rounded-2xl overflow-hidden border border-white/20"></div>
                                <input type="hidden" name="business[address_latitude]" id="businessAddressLatitude" value="{{ old('business.address_latitude', $business->latitude) }}">
                                <input type="hidden" name="business[address_longitude]" id="businessAddressLongitude" value="{{ old('business.address_longitude', $business->longitude) }}">
                            </div>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-white/80 mb-2">Location</label>
                            <div class="space-y-4">
                                <input type="text" name="business[location_name]" value="{{ old('business.location_name', $business->location_name) }}" placeholder="Enter your location (e.g., New York, NY)" class="w-full px-4 py-3 rounded-2xl bg-white/10 border border-white/20 text-white placeholder-white/70 focus:outline-none focus:ring-2 focus:ring-white/30">
                                <div id="businessMap" class="h-64 rounded-2xl overflow-hidden border border-white/20"></div>
                                <input type="hidden" name="business[latitude]" id="businessLatitude" value="{{ old('business.latitude', $business->latitude) }}">
                                <input type="hidden" name="business[longitude]" id="businessLongitude" value="{{ old('business.longitude', $business->longitude) }}">
                            </div>
                        </div>
                        <div>
                            <label class="block text-white/80 mb-1">Venue Offered</label>
                            <select name="business[venue]" class="w-full px-4 py-3 rounded-2xl bg-white/10 border border-white/20 text-white focus:outline-none focus:ring-2 focus:ring-white/30">
                                @php $venueVal = old('business.venue', $business->venue); @endphp
                                <option value="" disabled {{ $venueVal ? '' : 'selected' }}>Select the venue you offer</option>
                                <option value="Studio" {{ $venueVal === 'Studio' ? 'selected' : '' }}>Studio</option>
                                <option value="Club" {{ $venueVal === 'Club' ? 'selected' : '' }}>Club</option>
                                <option value="Theater" {{ $venueVal === 'Theater' ? 'selected' : '' }}>Theater</option>
                                <option value="Cafe" {{ $venueVal === 'Cafe' ? 'selected' : '' }}>Café</option>
                                <option value="Restaurant" {{ $venueVal === 'Restaurant' ? 'selected' : '' }}>Restaurant</option>
                                <option value="Bar" {{ $venueVal === 'Bar' ? 'selected' : '' }}>Bar & Lounge</option>
                                <option value="Event Venue" {{ $venueVal === 'Event Venue' ? 'selected' : '' }}>Event Venue</option>
                                <option value="Music Hall" {{ $venueVal === 'Music Hall' ? 'selected' : '' }}>Music Hall</option>
                                <option value="Other" {{ $venueVal === 'Other' ? 'selected' : '' }}>Other</option>
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
        let musicianMap, businessMap, businessAddressMap;
        let musicianMarker, businessMarker, businessAddressMarker;

        document.addEventListener('DOMContentLoaded', function() {
            // Initialize maps
            if (document.getElementById('musicianMap')) {
                initMusicianMap();
            }
            if (document.getElementById('businessMap')) {
                initBusinessMap();
            }
            if (document.getElementById('businessAddressMap')) {
                initBusinessAddressMap();
            }
        });

        function initMusicianMap() {
            const lat = parseFloat(document.getElementById('musicianLatitude').value) || 40.7128;
            const lng = parseFloat(document.getElementById('musicianLongitude').value) || -74.0060;
            
            musicianMap = L.map('musicianMap').setView([lat, lng], 10);
            
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(musicianMap);
            
            if (document.getElementById('musicianLatitude').value && document.getElementById('musicianLongitude').value) {
                musicianMarker = L.marker([lat, lng]).addTo(musicianMap);
            }
            
            musicianMap.on('click', function(e) {
                if (musicianMarker) {
                    musicianMap.removeLayer(musicianMarker);
                }
                musicianMarker = L.marker(e.latlng).addTo(musicianMap);
                document.getElementById('musicianLatitude').value = e.latlng.lat;
                document.getElementById('musicianLongitude').value = e.latlng.lng;
                
                // Reverse geocoding to get location name
                fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${e.latlng.lat}&lon=${e.latlng.lng}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.display_name) {
                            document.querySelector('input[name="musician[location_name]"]').value = data.display_name;
                        }
                    })
                    .catch(error => console.log('Geocoding error:', error));
            });
            
            // Search functionality
            const locationInput = document.querySelector('input[name="musician[location_name]"]');
            locationInput.addEventListener('blur', function() {
                if (this.value) {
                    searchLocation(this.value, 'musician');
                }
            });
        }

        function initBusinessMap() {
            const lat = parseFloat(document.getElementById('businessLatitude').value) || 40.7128;
            const lng = parseFloat(document.getElementById('businessLongitude').value) || -74.0060;
            
            businessMap = L.map('businessMap').setView([lat, lng], 10);
            
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(businessMap);
            
            if (document.getElementById('businessLatitude').value && document.getElementById('businessLongitude').value) {
                businessMarker = L.marker([lat, lng]).addTo(businessMap);
            }
            
            businessMap.on('click', function(e) {
                if (businessMarker) {
                    businessMap.removeLayer(businessMarker);
                }
                businessMarker = L.marker(e.latlng).addTo(businessMap);
                document.getElementById('businessLatitude').value = e.latlng.lat;
                document.getElementById('businessLongitude').value = e.latlng.lng;
                
                // Reverse geocoding to get location name
                fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${e.latlng.lat}&lon=${e.latlng.lng}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.display_name) {
                            document.querySelector('input[name="business[location_name]"]').value = data.display_name;
                        }
                    })
                    .catch(error => console.log('Geocoding error:', error));
            });
            
            // Search functionality
            const locationInput = document.querySelector('input[name="business[location_name]"]');
            locationInput.addEventListener('blur', function() {
                if (this.value) {
                    searchLocation(this.value, 'business');
                }
            });
        }

        function searchLocation(query, type) {
            fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    if (data && data.length > 0) {
                        const result = data[0];
                        const lat = parseFloat(result.lat);
                        const lng = parseFloat(result.lon);
                        
                        if (type === 'musician') {
                            musicianMap.setView([lat, lng], 13);
                            if (musicianMarker) {
                                musicianMap.removeLayer(musicianMarker);
                            }
                            musicianMarker = L.marker([lat, lng]).addTo(musicianMap);
                            document.getElementById('musicianLatitude').value = lat;
                            document.getElementById('musicianLongitude').value = lng;
                        } else {
                            businessMap.setView([lat, lng], 13);
                            if (businessMarker) {
                                businessMap.removeLayer(businessMarker);
                            }
                            businessMarker = L.marker([lat, lng]).addTo(businessMap);
                            document.getElementById('businessLatitude').value = lat;
                            document.getElementById('businessLongitude').value = lng;
                        }
                    }
                })
                .catch(error => console.log('Search error:', error));
        }

        function initBusinessAddressMap() {
            const lat = parseFloat(document.getElementById('businessAddressLatitude').value) || 40.7128;
            const lng = parseFloat(document.getElementById('businessAddressLongitude').value) || -74.0060;
            
            businessAddressMap = L.map('businessAddressMap').setView([lat, lng], 10);
            
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(businessAddressMap);
            
            if (document.getElementById('businessAddressLatitude').value && document.getElementById('businessAddressLongitude').value) {
                businessAddressMarker = L.marker([lat, lng]).addTo(businessAddressMap);
            }
            
            businessAddressMap.on('click', function(e) {
                if (businessAddressMarker) {
                    businessAddressMap.removeLayer(businessAddressMarker);
                }
                businessAddressMarker = L.marker(e.latlng).addTo(businessAddressMap);
                document.getElementById('businessAddressLatitude').value = e.latlng.lat;
                document.getElementById('businessAddressLongitude').value = e.latlng.lng;
                
                // Reverse geocoding to get address
                fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${e.latlng.lat}&lon=${e.latlng.lng}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.display_name) {
                            document.querySelector('input[name="business[address]"]').value = data.display_name;
                        }
                    })
                    .catch(error => console.log('Geocoding error:', error));
            });
            
            // Search functionality for address
            const addressInput = document.querySelector('input[name="business[address]"]');
            addressInput.addEventListener('blur', function() {
                if (this.value) {
                    searchAddressLocation(this.value);
                }
            });
        }

        function searchAddressLocation(query) {
            fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    if (data && data.length > 0) {
                        const result = data[0];
                        const lat = parseFloat(result.lat);
                        const lng = parseFloat(result.lon);
                        
                        businessAddressMap.setView([lat, lng], 15);
                        if (businessAddressMarker) {
                            businessAddressMap.removeLayer(businessAddressMarker);
                        }
                        businessAddressMarker = L.marker([lat, lng]).addTo(businessAddressMap);
                        document.getElementById('businessAddressLatitude').value = lat;
                        document.getElementById('businessAddressLongitude').value = lng;
                    }
                })
                .catch(error => console.log('Address search error:', error));
        }
    </script>
</body>
</html>


