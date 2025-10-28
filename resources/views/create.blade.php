<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/app.css', 'resources/css/create.css', 'resources/js/create.js', 'resources/js/app.js'])
    <title>Bandmate | Welcome</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body class="min-h-screen gradient-bg flex items-center justify-center p-4 relative">
    
    <!-- Animated background elements -->
    <div class="fixed inset-0 pointer-events-none overflow-hidden">
        <div class="absolute top-1/4 left-1/4 w-64 h-64 bg-white/10 rounded-full blur-3xl floating-element"></div>
        <div class="absolute bottom-1/4 right-1/4 w-80 h-80 bg-purple-500/10 rounded-full blur-3xl floating-element" style="animation-delay: -2s;"></div>
        <div class="absolute top-3/4 left-1/2 w-48 h-48 bg-pink-500/10 rounded-full blur-3xl floating-element" style="animation-delay: -4s;"></div>
        
        <!-- Floating musical notes -->
        <div class="absolute top-20 left-20 text-white/20 text-4xl floating-element">♪</div>
        <div class="absolute top-40 right-32 text-white/15 text-6xl floating-element" style="animation-delay: -1s;">♫</div>
        <div class="absolute bottom-32 left-40 text-white/10 text-5xl floating-element" style="animation-delay: -3s;">♩</div>
        <div class="absolute bottom-20 right-20 text-white/20 text-3xl floating-element" style="animation-delay: -5s;">♬</div>
    </div>
    
    <main class="relative w-full max-w-6xl z-10">
        
        <!-- Header Section -->
        <div class="text-center mb-12 fade-in">
            <div class="inline-flex items-center justify-center w-20 h-20 glass-card rounded-full mb-6 relative">
                <div class="absolute inset-0 bg-white/20 rounded-full pulse-ring"></div>
                <span class="text-white text-3xl font-bold text-shadow relative z-10">B</span>
            </div>
            <h1 class="text-5xl md:text-6xl font-extrabold text-white mb-4 text-shadow">
                Welcome to <span class="text-yellow-300 font-black">Bandmate</span>
            </h1>
            <p class="text-xl text-white/90 max-w-2xl mx-auto leading-relaxed">
                Choose your journey and connect with the perfect musical collaborators
            </p>
        </div>
        
        <!-- Profile Selection Cards -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            
            <!-- Musician Card -->
            <div class="slide-in-left group">
                <div class="musician-card glass-card rounded-3xl p-8 hover-lift glow-effect relative overflow-hidden">
                    <div class="absolute top-4 right-4 text-white/30 text-6xl floating-element">🎸</div>
                    
                    <div class="relative z-10">
                        <div class="flex items-center mb-6">
                            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center mr-4">
                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M18 3a1 1 0 00-1.196-.98l-10 2A1 1 0 006 5v9.114A4.369 4.369 0 005 14c-1.657 0-3 .895-3 2s1.343 2 3 2 3-.895 3-2V7.82l8-1.6v5.894A4.369 4.369 0 0015 12c-1.657 0-3 .895-3 2s1.343 2 3 2 3-.895 3-2V3z"/>
                                </svg>
                            </div>
                            <h2 class="text-3xl font-bold text-white text-shadow">Musician</h2>
                        </div>
                        
                        <p class="text-white/90 text-lg mb-8 leading-relaxed">
                            Showcase your musical talent, connect with other artists, and discover amazing venues. Build your network and create unforgettable collaborations.
                        </p>
                        
                        <div class="space-y-3 mb-8">
                            <div class="flex items-center text-white/80">
                                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                Create stunning music profiles
                            </div>
                            <div class="flex items-center text-white/80">
                                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                Connect with bandmates
                            </div>
                            <div class="flex items-center text-white/80">
                                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                Find gig opportunities
                            </div>
                        </div>
                        
                        <a href="{{ route('create.musician')}}" 
                           class="block w-full bg-white text-purple-600 text-center py-4 rounded-xl font-semibold text-lg hover:bg-white/90 hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                            Start as Musician
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Business Card -->
            <div class="slide-in-right group">
                <div class="business-card glass-card rounded-3xl p-8 hover-lift glow-effect relative overflow-hidden">
                    <div class="absolute top-4 right-4 text-white/30 text-6xl floating-element" style="animation-delay: -1s;">🏢</div>
                    
                    <div class="relative z-10">
                        <div class="flex items-center mb-6">
                            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center mr-4">
                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H4zm3 3a1 1 0 011-1h4a1 1 0 110 2H8a1 1 0 01-1-1zm0 3a1 1 0 011-1h4a1 1 0 110 2H8a1 1 0 01-1-1z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <h2 class="text-3xl font-bold text-white text-shadow">Business</h2>
                        </div>
                        
                        <p class="text-white/90 text-lg mb-8 leading-relaxed">
                            Perfect for venues, studios, and music businesses. Connect with talented musicians and grow your musical community effortlessly.
                        </p>
                        
                        <div class="space-y-3 mb-8">
                            <div class="flex items-center text-white/80">
                                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                Manage venue bookings
                            </div>
                            <div class="flex items-center text-white/80">
                                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                Discover new talent
                            </div>
                            <div class="flex items-center text-white/80">
                                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                Build music community
                            </div>
                        </div>
                        
                        <a href="{{ route('create.business')}}" 
                           class="block w-full bg-white text-pink-600 text-center py-4 rounded-xl font-semibold text-lg hover:bg-white/90 hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                            Start as Business
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>