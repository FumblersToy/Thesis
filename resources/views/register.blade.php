<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bandmate | Register</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @vite(['resources/css/app.css', 'resources/css/register.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="min-h-screen gradient-bg flex items-center justify-center p-4">
    
    <!-- Background decorative elements -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-40 -right-40 w-80 h-80 bg-gradient-to-br from-blue-400/20 to-purple-600/20 rounded-full blur-3xl floating-animation"></div>
        <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-gradient-to-tr from-purple-400/20 to-pink-600/20 rounded-full blur-3xl floating-animation" style="animation-delay: -3s;"></div>
    </div>
    
    <main class="relative flex w-full max-w-6xl glass-effect shadow-2xl rounded-3xl overflow-hidden fade-in">
        
        <!-- Image Section -->
        <div class="hidden lg:flex lg:w-3/5 relative overflow-hidden bg-gradient-to-br from-gray-900 to-gray-700">
            <img src="https://images.unsplash.com/photo-1516280440614-37939bbacd81?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80" 
                 alt="Musicians Collaborating" 
                 class="w-full h-full object-cover hover:scale-110 transition-transform duration-700 ease-out">
            
            <!-- Overlay content -->
            <div class="absolute inset-x-0 bottom-0 h-48 bg-gradient-to-t from-black/60 to-transparent"></div>
            <div class="absolute bottom-0 left-0 p-12 text-white">
                <h2 class="text-4xl font-bold mb-4 text-shadow">Start Your Musical Journey</h2>
                <p class="text-lg opacity-90 max-w-md leading-relaxed">Join thousands of musicians creating amazing music together. Your next collaboration is just a click away.</p>
                
                <!-- Floating music notes -->
                <div class="absolute top-20 right-20 text-white/30 text-6xl floating-animation">♪</div>
                <div class="absolute top-40 right-40 text-white/20 text-4xl floating-animation" style="animation-delay: -2s;">♫</div>
            </div>
        </div>

        <!-- Registration Form Section -->
        <section class="w-full lg:w-2/5 p-8 md:p-12 flex flex-col justify-center bg-white/50 backdrop-blur-sm">
            <div class="slide-in">
                <!-- Header -->
                <div class="text-center mb-8">
                    <div class="inline-flex items-center justify-center w-16 h-16 mb-6">
                        <img src="/assets/logo_black.png">
                    </div>
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">Join Bandmate</h1>
                    <p class="text-gray-600">Create your account and start making music</p>
                </div>

                <!-- Error Messages -->
                <div id="error-container" class="mb-6 bg-red-50 border-l-4 border-red-400 text-red-700 text-sm rounded-r-lg p-4 hidden">
                    <div class="flex">
                        <svg class="w-5 h-5 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        <ul id="error-list" class="list-none">
                            <!-- Errors will be inserted here -->
                        </ul>
                    </div>
                </div>

                <!-- Registration Form -->
                <form method="POST" action="{{ route('register') }}" class="space-y-6" onsubmit="return validateForm()"
                <!-- Email Field -->
                @csrf
                    <div class="relative">
                        <input type="email"
                               id="email"
                               name="email"
                               placeholder=" "
                               required
                               class="input-focus input-glow peer w-full px-4 py-4 text-gray-700 bg-white border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:outline-none transition-all duration-300">
                        <label for="email" 
                               class="floating-label absolute left-4 top-4 text-gray-500 transition-all duration-300 cursor-text peer-focus:text-blue-500">
                            Email Address
                        </label>
                    </div>

                    <!-- Confirm Email Field -->
                    <div class="relative">
                        <input type="email"
                               id="confirm-email"
                               name="email_confirmation"
                               placeholder=" "
                               required
                               class="input-focus input-glow peer w-full px-4 py-4 text-gray-700 bg-white border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:outline-none transition-all duration-300">
                        <label for="confirm-email" 
                               class="floating-label absolute left-4 top-4 text-gray-500 transition-all duration-300 cursor-text peer-focus:text-blue-500">
                            Confirm Email Address
                        </label>
                        <span id="email-match-error" class="text-red-500 text-xs mt-1 hidden">Email addresses do not match</span>
                    </div>

                    <!-- Password Field -->
                    <div class="relative">
                        <input type="password"
                               id="password"
                               name="password"
                               placeholder=" "
                               required
                               class="input-focus input-glow peer w-full px-4 py-4 text-gray-700 bg-white border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:outline-none transition-all duration-300">
                        <label for="password" 
                               class="floating-label absolute left-4 top-4 text-gray-500 transition-all duration-300 cursor-text peer-focus:text-blue-500">
                            Password
                        </label>
                        <button type="button" onclick="togglePassword('password')" class="absolute right-4 top-4 text-gray-400 hover:text-gray-600 transition-colors">
                            <svg id="eye-icon-password" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>

                    <!-- Confirm Password Field -->
                    <div class="relative">
                        <input type="password"
                               id="confirm-password"
                               name="password_confirmation"
                               placeholder=" "
                               required
                               class="input-focus input-glow peer w-full px-4 py-4 text-gray-700 bg-white border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:outline-none transition-all duration-300">
                        <label for="confirm-password" 
                               class="floating-label absolute left-4 top-4 text-gray-500 transition-all duration-300 cursor-text peer-focus:text-blue-500">
                            Confirm Password
                        </label>
                        <button type="button" onclick="togglePassword('confirm-password')" class="absolute right-4 top-4 text-gray-400 hover:text-gray-600 transition-colors">
                            <svg id="eye-icon-confirm-password" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                        <span id="password-match-error" class="text-red-500 text-xs mt-1 hidden">Passwords do not match</span>
                    </div>

                    <!-- Terms and Conditions -->
                    <div class="flex items-start">
                        <input type="checkbox" id="terms" name="terms" required class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 mt-1">
                        <label for="terms" class="ml-3 text-sm text-gray-600">
                            I agree to the 
                            <a href="#" class="text-blue-600 hover:text-blue-800 transition-colors duration-200 font-medium">Terms of Service</a>
                            and 
                            <a href="#" class="text-blue-600 hover:text-blue-800 transition-colors duration-200 font-medium">Privacy Policy</a>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" 
                            id="register-btn"
                            class="gradient-bg btn-hover w-full text-white py-4 rounded-xl font-semibold text-lg shadow-lg transition-all duration-300">
                        <span id="btn-text">Create Account</span>
                    </button>
                </form>

                <!-- Social Registration -->
                <div class="mt-8">
                    <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-200"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-4 bg-white text-gray-500">Or register with</span>
                        </div>
                    </div>
                    
                    <div class="mt-6 grid grid-cols-2 gap-4">
                        <button class="flex items-center justify-center px-4 py-3 border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors duration-200">
                            <svg class="w-5 h-5 mr-2" viewBox="0 0 24 24">
                                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                            </svg>
                            Google
                        </button>
                        <button class="flex items-center justify-center px-4 py-3 border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors duration-200">
                            <svg class="w-5 h-5 mr-2" fill="#1877F2" viewBox="0 0 24 24">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                            Facebook
                        </button>
                    </div>
                </div>

                <!-- Login Link -->
                <p class="text-center text-gray-600 mt-8">
                    Already have an account?
                    <a href="{{route('login') }}" class="text-blue-600 hover:text-blue-800 font-medium transition-colors duration-200 ml-1">
                        Sign in here
                    </a>
                </p>
            </div>
        </section>
    </main>
</body>
</html>