<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Find Nearby Musicians & Venues - BandMate</title>
    @vite(['resources/css/app.css', 'resources/css/feed.css', 'resources/css/socket.css', 'resources/js/app.js', 'resources/js/socket.js'])
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen relative overflow-x-hidden gradient-bg">
    <div class="floating-elements fixed inset-0 pointer-events-none"></div>
    
    <div class="flex min-h-screen relative z-10">
        <!-- Sidebar -->
        <div class="w-80 bg-white/10 backdrop-blur-md border-r border-white/20 p-6 overflow-y-auto">
            <!-- Header -->
            <div class="flex items-center justify-between mb-8">
                <div class="flex items-center gap-3">
                    <img src="/assets/logo_both.png" alt="BandMate" class="h-10 w-auto">
                    <h1 class="text-2xl font-bold text-white">Map</h1>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="space-y-2 mb-8">
                <a href="{{ route('feed') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-white/10 transition-colors text-white/80 hover:text-white">
                    <span class="text-lg">🏠</span>
                    Feed
                </a>
                <a href="{{ route('messages.index') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-white/10 transition-colors text-white/80 hover:text-white">
                    <span class="text-lg">💬</span>
                    Messages
                </a>
                <a href="{{ route('map') }}" class="flex items-center gap-3 p-3 rounded-xl bg-white/20 text-white">
                    <span class="text-lg">🗺️</span>
                    Map
                </a>
            </nav>

            <!-- Filters -->
            <div class="space-y-6">
                <div>
                    <h3 class="text-white font-semibold mb-3">Show</h3>
                    <div class="space-y-2">
                        <label class="flex items-center gap-2 text-white/80">
                            <input type="checkbox" id="showMusicians" checked class="rounded border-white/30 bg-white/10 text-purple-500 focus:ring-purple-500">
                            Musicians
                        </label>
                        <label class="flex items-center gap-2 text-white/80">
                            <input type="checkbox" id="showBusinesses" checked class="rounded border-white/30 bg-white/10 text-purple-500 focus:ring-purple-500">
                            Venues
                        </label>
                    </div>
                </div>

                <div>
                    <h3 class="text-white font-semibold mb-3">Instruments</h3>
                    <div class="space-y-2" id="instrumentFilters">
                        <!-- Will be populated by JavaScript -->
                    </div>
                </div>

                <div>
                    <h3 class="text-white font-semibold mb-3">Venues</h3>
                    <div class="space-y-2" id="venueFilters">
                        <!-- Will be populated by JavaScript -->
                    </div>
                </div>
            </div>

            <!-- User Profile -->
            <div class="mt-auto pt-6 border-t border-white/20">
                @auth
                    @php
                        $user = Auth::user();
                        $musician = $user->musician;
                        $business = $user->business;
                        $avatar = $musician?->profile_picture ? Storage::url($musician->profile_picture) : 
                                 ($business?->profile_picture ? Storage::url($business->profile_picture) : '/images/sample-profile.jpg');
                        $displayName = $musician?->stage_name ?: ($business?->business_name ?: $user->name);
                    @endphp
                    
                    <div class="flex items-center gap-3 p-3 rounded-xl bg-white/10">
                        <img src="{{ $avatar }}" alt="Profile" class="w-10 h-10 rounded-full object-cover">
                        <div class="flex-1 min-w-0">
                            <p class="text-white font-medium truncate">{{ $displayName }}</p>
                            <p class="text-white/60 text-sm">{{ $musician ? 'Musician' : 'Business' }}</p>
                        </div>
                        <div class="relative">
                            <button class="text-white/60 hover:text-white p-1" onclick="toggleProfileDropdown()">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/>
                                </svg>
                            </button>
                            <div id="profileDropdown" class="hidden absolute right-0 bottom-full mb-2 w-48 bg-white rounded-xl shadow-lg border border-gray-200 py-2">
                                <a href="{{ route('profile', $user->id) }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-100 transition-colors text-gray-700 hover:text-gray-900">
                                    <span class="text-lg">👤</span>
                                    Profile
                                </a>
                                <a href="{{ route('settings.show') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-100 transition-colors text-gray-700 hover:text-gray-900">
                                    <span class="text-lg">⚙️</span>
                                    Settings
                                </a>
                                <form method="POST" action="{{ route('logout') }}" class="block">
                                    @csrf
                                    <button type="submit" class="w-full text-left flex items-center gap-3 p-3 rounded-xl hover:bg-gray-100 transition-colors text-gray-700 hover:text-gray-900">
                                        <span class="text-lg">🚪</span>
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endauth
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col">
            <!-- Map Container -->
            <div class="flex-1 relative">
                <div id="mainMap" class="w-full h-full"></div>
                
                <!-- Map Controls -->
                <div class="absolute top-4 right-4 bg-white/90 backdrop-blur-sm rounded-xl p-4 shadow-lg">
                    <button id="locateMe" class="flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-purple-500 to-pink-500 text-white rounded-lg hover:from-purple-600 hover:to-pink-600 transition-all">
                        <span>📍</span>
                        Find Me
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let mainMap;
        let userMarkers = [];
        let currentLocationMarker = null;

        document.addEventListener('DOMContentLoaded', function() {
            initializeMap();
            initializeFilters();
            loadUsers();
            
            // Event listeners
            document.getElementById('locateMe').addEventListener('click', locateUser);
            document.getElementById('showMusicians').addEventListener('change', filterUsers);
            document.getElementById('showBusinesses').addEventListener('change', filterUsers);
        });

        function initializeMap() {
            // Initialize map centered on New York (default)
            mainMap = L.map('mainMap').setView([40.7128, -74.0060], 10);
            
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(mainMap);
        }

        function initializeFilters() {
            const instruments = ['RnB', 'House', 'Pop Punk', 'Electronic', 'Reggae', 'Jazz', 'Rock'];
            const venues = ['Studio', 'Club', 'Theater', 'Cafe', 'Restaurant', 'Bar', 'Event Venue', 'Music Hall'];
            
            populateFilterSection('instrumentFilters', instruments, 'instrument');
            populateFilterSection('venueFilters', venues, 'venue');
        }

        function populateFilterSection(containerId, options, type) {
            const container = document.getElementById(containerId);
            container.innerHTML = '';
            
            options.forEach(option => {
                const label = document.createElement('label');
                label.className = 'flex items-center gap-2 text-white/80';
                label.innerHTML = `
                    <input type="checkbox" class="filter-checkbox rounded border-white/30 bg-white/10 text-purple-500 focus:ring-purple-500" data-type="${type}" data-value="${option}">
                    ${option}
                `;
                container.appendChild(label);
                
                label.querySelector('input').addEventListener('change', filterUsers);
            });
        }

        async function loadUsers() {
            try {
                const response = await fetch('/api/map/users', {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                if (!response.ok) throw new Error('Failed to load users');
                
                const data = await response.json();
                displayUsers(data.users || []);
            } catch (error) {
                console.error('Error loading users:', error);
            }
        }

        function displayUsers(users) {
            // Clear existing markers
            userMarkers.forEach(marker => mainMap.removeLayer(marker));
            userMarkers = [];
            
            users.forEach(user => {
                if (!user.latitude || !user.longitude) return;
                
                const isMusician = user.type === 'musician';
                const icon = isMusician ? '🎵' : '🏢';
                const color = isMusician ? '#8B5CF6' : '#EC4899';
                
                const marker = L.marker([user.latitude, user.longitude], {
                    icon: L.divIcon({
                        html: `<div style="background-color: ${color}; color: white; border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; font-size: 16px; border: 2px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">${icon}</div>`,
                        className: 'custom-marker',
                        iconSize: [30, 30],
                        iconAnchor: [15, 15]
                    })
                }).addTo(mainMap);
                
                const popupContent = `
                    <div class="p-2 min-w-[200px]">
                        <div class="flex items-center gap-3 mb-2">
                            <img src="${user.avatar}" alt="${user.name}" class="w-10 h-10 rounded-full object-cover">
                            <div>
                                <h3 class="font-semibold text-gray-800">${user.name}</h3>
                                <p class="text-sm text-gray-600">${user.type === 'musician' ? user.instrument || 'Musician' : user.venue || 'Business'}</p>
                            </div>
                        </div>
                        ${user.bio ? `<p class="text-sm text-gray-700 mb-2">${user.bio}</p>` : ''}
                        <div class="flex gap-2">
                            <a href="/profile/${user.user_id}" class="px-3 py-1 bg-purple-500 text-white text-sm rounded hover:bg-purple-600 transition-colors">
                                View Profile
                            </a>
                            <a href="/messages?user=${user.user_id}" class="px-3 py-1 bg-gray-500 text-white text-sm rounded hover:bg-gray-600 transition-colors">
                                Message
                            </a>
                        </div>
                    </div>
                `;
                
                marker.bindPopup(popupContent);
                marker.userData = user;
                userMarkers.push(marker);
            });
        }

        function filterUsers() {
            const showMusicians = document.getElementById('showMusicians').checked;
            const showBusinesses = document.getElementById('showBusinesses').checked;
            
            const selectedInstruments = Array.from(document.querySelectorAll('.filter-checkbox[data-type="instrument"]:checked'))
                .map(cb => cb.dataset.value);
            const selectedVenues = Array.from(document.querySelectorAll('.filter-checkbox[data-type="venue"]:checked'))
                .map(cb => cb.dataset.value);
            
            userMarkers.forEach(marker => {
                const user = marker.userData;
                let show = false;
                
                if (user.type === 'musician' && showMusicians) {
                    show = selectedInstruments.length === 0 || selectedInstruments.includes(user.instrument);
                } else if (user.type === 'business' && showBusinesses) {
                    show = selectedVenues.length === 0 || selectedVenues.includes(user.venue);
                }
                
                if (show) {
                    mainMap.addLayer(marker);
                } else {
                    mainMap.removeLayer(marker);
                }
            });
        }

        function locateUser() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        const lat = position.coords.latitude;
                        const lng = position.coords.longitude;
                        
                        mainMap.setView([lat, lng], 13);
                        
                        if (currentLocationMarker) {
                            mainMap.removeLayer(currentLocationMarker);
                        }
                        
                        currentLocationMarker = L.marker([lat, lng], {
                            icon: L.divIcon({
                                html: '<div style="background-color: #3B82F6; color: white; border-radius: 50%; width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; font-size: 12px; border: 3px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3);">📍</div>',
                                className: 'current-location-marker',
                                iconSize: [20, 20],
                                iconAnchor: [10, 10]
                            })
                        }).addTo(mainMap);
                        
                        currentLocationMarker.bindPopup('Your Location').openPopup();
                    },
                    function(error) {
                        alert('Unable to get your location. Please check your browser settings.');
                    }
                );
            } else {
                alert('Geolocation is not supported by this browser.');
            }
        }

        function toggleProfileDropdown() {
            const dropdown = document.getElementById('profileDropdown');
            dropdown.classList.toggle('hidden');
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('profileDropdown');
            const button = event.target.closest('button');
            
            if (!button || !button.onclick || button.onclick.toString().indexOf('toggleProfileDropdown') === -1) {
                dropdown.classList.add('hidden');
            }
        });

        // Socket.IO integration
        @auth
        document.addEventListener('DOMContentLoaded', function() {
            const userData = {
                id: {{ auth()->user()->id }},
                name: '{{ $displayName }}',
                avatar: '{{ $avatar }}'
            };
            
            if (window.socketManager) {
                window.socketManager.init(userData);
            }
        });
        @endauth
    </script>
</body>
</html>
