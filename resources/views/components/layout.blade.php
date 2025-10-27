<html lang="en">
<head>
    @php
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");
    @endphp
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/layout.js', 'resources/js/app.js'])
    <title>Document</title>
</head>
<body class="bg-off-white">
    <nav class="bg-stone-100 shadow-md relative">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex items-center justify-between h-16">
                <div class="flex-shrink-0 flex items-center">
                    <a href="{{ route('feed') }}">
                        <img src="{{ asset('assets/logo_black.png') }}" class="h-8" alt="Bandmate logo">
                    </a>
                </div>

                <div class="hidden md:flex items-center justify-center flex-1">
                    <div class="w-full max-w-md relative">
                        <form action="{{ route('search') }}" method="GET" id="searchForm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input 
                                type="text" 
                                name="query" 
                                id="searchInput"
                                class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" 
                                placeholder="Search musicians, bands, venues..."
                                value="{{ request('query') }}"
                                autocomplete="off">
                        </form>
                        
                        <!-- Search Results Dropdown -->
                        <div id="searchResults" class="absolute top-full left-0 right-0 bg-white border border-gray-300 rounded-md shadow-lg mt-1 max-h-96 overflow-y-auto z-50 hidden">
                            <div id="searchResultsContent">
                                <!-- Results will be populated here -->
                            </div>
                            <div id="searchLoading" class="p-4 text-center text-gray-500 hidden">
                                <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-blue-500 mx-auto"></div>
                                <span class="ml-2">Searching...</span>
                            </div>
                            <div id="noResults" class="p-4 text-center text-gray-500 hidden">
                                No results found
                            </div>
                        </div>
                    </div>
                </div>

                <div class="hidden md:flex items-center space-x-4">
                    <div class="relative">
                        <a href="{{ Auth::check() ? route('profile.show', Auth::id()) : route('login') }}" id="profileButton" class="flex items-center space-x-2 text-gray-700 hover:text-blue-600 focus:outline-none cursor-pointer">
                            <span>Profile</span>
                        </a>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-md text-sm font-medium cursor-pointer">
                            Logout
                        </button>
                    </form>
                </div>

                <div class="flex md:hidden items-center">
                    <button id="mobileMenuButton" class="inline-flex items-center justify-center p-2 rounded-md text-gray-500 hover:text-blue-600 hover:bg-gray-100 focus:outline-none">
                        <svg id="menuOpenIcon" class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <svg id="menuCloseIcon" class="h-6 w-6 hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <div id="mobileMenu" class="hidden md:hidden absolute top-16 left-0 right-0 bg-white shadow-lg z-20">
            <div class="px-4 py-3 space-y-3">
                <form action="{{ route('search') }}" method="GET" id="mobileSearchForm">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input 
                            type="text" 
                            name="query" 
                            id="mobileSearchInput"
                            class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" 
                            placeholder="Search musicians, bands, venues..."
                            value="{{ request('query') }}">
                    </div>
                </form>
                
                <a href="{{ Auth::check() ? route('profile.show', Auth::id()) : route('login') }}" class="flex items-center px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-100">
                    <span>Profile</span>
                </a>
                
                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <button type="submit" class="w-full text-left px-3 py-2 rounded-md text-base font-medium text-white bg-red-500 hover:bg-red-600">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </nav>

    {{ $slot }}
</body>
</html>