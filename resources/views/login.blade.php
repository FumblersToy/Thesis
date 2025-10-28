<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bandmate | Login</title>
    @vite(['resources/css/login.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="min-h-screen gradient-bg flex items-center justify-center p-4">
    
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-40 -right-40 w-80 h-80 bg-gradient-to-br from-blue-400/20 to-purple-600/20 rounded-full blur-3xl floating-animation"></div>
        <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-gradient-to-tr from-purple-400/20 to-pink-600/20 rounded-full blur-3xl floating-animation" style="animation-delay: -3s;"></div>
    </div>
    
    <main class="relative flex w-full max-w-6xl glass-effect shadow-2xl rounded-3xl overflow-hidden fade-in">
        
        <div class="hidden lg:flex lg:w-3/5 relative overflow-hidden bg-gradient-to-br from-gray-900 to-gray-700">
            <img src="https://images.unsplash.com/photo-1493225457124-a3eb161ffa5f?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80" 
                 alt="Music Scene" 
                 class="w-full h-full object-cover opacity-80 hover:scale-110 transition-transform duration-700 ease-out">
            
            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-black/20"></div>
            <div class="absolute bottom-0 left-0 p-12 text-white">
                <h2 class="text-4xl font-bold mb-4 text-shadow">Find Your Perfect Bandmate</h2>
                <p class="text-lg opacity-90 max-w-md leading-relaxed">Connect with musicians who share your passion. Create, collaborate, and make music together.</p>
                
                <div class="absolute top-20 right-20 text-white/30 text-6xl floating-animation">♪</div>
                <div class="absolute top-40 right-40 text-white/20 text-4xl floating-animation" style="animation-delay: -2s;">♫</div>
            </div>
        </div>

        <section class="w-full lg:w-2/5 p-8 md:p-12 flex flex-col justify-center bg-white/50 backdrop-blur-sm">
            <div class="slide-in">
                <div class="text-center mb-8">
                    <div class="inline-flex items-center justify-center w-16 h-16 mb-6">
                        <img src="/assets/logo_black.png">
                    </div>
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">Welcome Back</h1>
                    <p class="text-gray-600">Sign in to continue your musical journey</p>
                </div>

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

                <!-- Login Form -->
                <form method="POST" action="{{ route('login') }}" class="space-y-6" id="login-form">
                    @csrf
                    <!-- Email Field -->
                    <div class="relative">
                        <input type="email"
                               id="email"
                               name="email"
                               value="{{ old('email') }}"
                               placeholder=" "
                               required
                               class="input-focus input-glow peer w-full px-4 py-4 text-gray-700 bg-white border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:outline-none transition-all duration-300">
                        <label for="email" 
                               class="floating-label absolute left-4 top-4 text-gray-500 transition-all duration-300 cursor-text peer-focus:text-blue-500">
                            Email Address
                        </label>
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
                        <button type="button" onclick="togglePassword()" class="absolute right-4 top-4 text-gray-400 hover:text-gray-600 transition-colors">
                            <svg id="eye-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>

                    <!-- Remember & Forgot -->
                    <div class="flex items-center justify-between">
                        <label class="flex items-center">
                            <input type="checkbox" 
                                   name="remember" 
                                   id="remember"
                                   value="1"
                                   class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-600">Remember me</span>
                        </label>
                        <a href="{{ route('password.request') }}" class="text-sm text-blue-600 hover:text-blue-800 transition-colors duration-200 font-medium">
                            Forgot Password?
                        </a>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" 
                            id="login-btn"
                            class="btn-hover w-full bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-xl py-4 font-semibold text-lg shadow-lg transition-all duration-300">
                        <span id="btn-text">Sign In</span>
                        <svg id="loading-spinner" class="hidden w-5 h-5 ml-2 animate-spin inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                    </button>
                </form>

                <!-- Social Login -->
                <div class="mt-8">
                    <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-200"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-4 bg-white text-gray-500">Or continue with</span>
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

                <!-- Register Link -->
                <p class="text-center text-gray-600 mt-8">
                    New to Bandmate?
                    <a href="{{ route('register')}}" class="text-blue-600 hover:text-blue-800 font-medium transition-colors duration-200 ml-1">
                        Create an account
                    </a>
                </p>
            </div>
        </section>
    </main>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eye-icon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"/>
                `;
            } else {
                passwordInput.type = 'password';
                eyeIcon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                `;
            }
        }

        // Handle form submission
        document.getElementById('login-form').addEventListener('submit', function(event) {
            event.preventDefault();
            
            const form = this;
            const btn = document.getElementById('login-btn');
            const btnText = document.getElementById('btn-text');
            const spinner = document.getElementById('loading-spinner');
            const errorContainer = document.getElementById('error-container');
            const errorList = document.getElementById('error-list');
            
            // Show loading state
            btnText.textContent = 'Signing In...';
            spinner.classList.remove('hidden');
            btn.disabled = true;
            btn.classList.add('opacity-75');
            errorContainer.classList.add('hidden');
            errorList.innerHTML = '';
            
            // Get form data
            const formData = new FormData(form);
            
            // Submit form
            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (response.ok) {
                    window.location.href = '{{ route("feed") }}';
                } else {
                    return response.json();
                }
            })
            .then(data => {
                if (data) {
                    // Display errors
                    const errors = data.errors || {};
                    errorList.innerHTML = '';
                    
                    Object.keys(errors).forEach(key => {
                        const errorItem = document.createElement('li');
                        errorItem.textContent = errors[key][0];
                        errorList.appendChild(errorItem);
                    });
                    
                    errorContainer.classList.remove('hidden');
                    
                    // Reset button state
                    btnText.textContent = 'Sign In';
                    spinner.classList.add('hidden');
                    btn.disabled = false;
                    btn.classList.remove('opacity-75');
                }
            })
            .catch(error => {
                console.error('Login error:', error);
                errorList.innerHTML = '<li>An error occurred. Please try again.</li>';
                errorContainer.classList.remove('hidden');
                
                // Reset button state
                btnText.textContent = 'Sign In';
                spinner.classList.add('hidden');
                btn.disabled = false;
                btn.classList.remove('opacity-75');
            });
        });

        // Add subtle parallax effect to background elements
        document.addEventListener('mousemove', (e) => {
            const mouseX = e.clientX / window.innerWidth;
            const mouseY = e.clientY / window.innerHeight;
            
            const elements = document.querySelectorAll('.floating-animation');
            elements.forEach((el, index) => {
                const speed = (index + 1) * 0.05;
                const x = (mouseX - 0.5) * speed * 50;
                const y = (mouseY - 0.5) * speed * 50;
                
                el.style.transform = `translate(${x}px, ${y}px)`;
            });
        });
    </script>
</body>
</html>