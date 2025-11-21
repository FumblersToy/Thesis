<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/css/welcome.css', 'resources/js/app.js'])
    <title>Bandmate | Welcome</title>
</head>
<body class="min-h-screen bg-white">
    <!-- Floating music notes background -->
    <div class="music-notes">
        <div class="music-note">♪</div>
        <div class="music-note">♫</div>
        <div class="music-note">♪</div>
        <div class="music-note">♬</div>
    </div>

    <header class="w-full h-16 flex items-center justify-between bg-white px-6 border-b border-gray-100">
        <img src="/assets/logo_both.png" class="h-10">
        <div class="flex items-center space-x-3">
            <a href="{{ route('login')}}" class="nav-btn login">
                Login
            </a>
            <a href="{{ route('register') }}" class="nav-btn register">
                Kadmiel Lets Go
            </a>
        </div>
    </header>

    <section class="w-full min-h-screen bg-off-white flex flex-col items-center justify-center px-6 py-20">
        <div class="max-w-4xl mx-auto text-center">
            <h1 class="hero-title mb-6">
                Where Music Meets Connection
            </h1>
            <p class="hero-subtitle mx-auto mb-12">
                Find the perfect talent, connect with passionate performers, and create events that leave lasting memories. Join thousands of musicians building their careers together.
            </p>
            
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4 mb-20">
                <a href="{{ route('register')}}" class="btn-primary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M8 5v14l11-7z"/>
                    </svg>
                    Start Your Journey
                </a>
                <a href="{{ route('login')}}" class="btn-secondary">
                    Sign In
                </a>
            </div>

            <!-- Feature Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-5xl mx-auto">
                <div class="feature-card">
                    <div class="feature-icon">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Connect</h3>
                    <p class="text-gray-600">Network with talented musicians and discover new opportunities in your area and beyond</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Collaborate</h3>
                    <p class="text-gray-600">Work together on projects and create something amazing that showcases your combined talents</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 18V5l12-2v13"></path>
                            <circle cx="6" cy="18" r="3"></circle>
                            <circle cx="18" cy="16" r="3"></circle>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Perform</h3>
                    <p class="text-gray-600">Showcase your talent and share your music with the world through events and performances</p>
                </div>
            </div>
        </div>
    </section>
</body>

</html>