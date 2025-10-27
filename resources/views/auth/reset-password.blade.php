<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bandmate | Reset Password</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @vite(['resources/css/login.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="min-h-screen gradient-bg flex items-center justify-center p-4">
    
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-40 -right-40 w-80 h-80 bg-gradient-to-br from-blue-400/20 to-purple-600/20 rounded-full blur-3xl floating-animation"></div>
        <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-gradient-to-tr from-purple-400/20 to-pink-600/20 rounded-full blur-3xl floating-animation" style="animation-delay: -3s;"></div>
    </div>
    
    <main class="relative flex w-full max-w-2xl glass-effect shadow-2xl rounded-3xl overflow-hidden fade-in">
        
        <div class="hidden lg:flex lg:w-2/5 relative overflow-hidden bg-gradient-to-br from-gray-900 to-gray-700">
            <img src="https://images.unsplash.com/photo-1493225457124-a3eb161ffa5f?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80" 
                 alt="Music Scene" 
                 class="w-full h-full object-cover opacity-80 hover:scale-110 transition-transform duration-700 ease-out">
            
            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-black/20"></div>
            <div class="absolute bottom-0 left-0 p-8 text-white">
                <h2 class="text-3xl font-bold mb-4 text-shadow">Set New Password</h2>
                <p class="text-lg opacity-90 max-w-md leading-relaxed">Choose a strong password to keep your account secure.</p>
                
                <div class="absolute top-20 right-20 text-white/30 text-6xl floating-animation">♪</div>
                <div class="absolute top-40 right-40 text-white/20 text-4xl floating-animation" style="animation-delay: -2s;">♫</div>
            </div>
        </div>

        <section class="w-full lg:w-3/5 p-8 md:p-12 flex flex-col justify-center bg-white/50 backdrop-blur-sm">
            <div class="slide-in">
                <div class="text-center mb-8">
                    <div class="inline-flex items-center justify-center w-16 h-16 mb-6">
                        <img src="/assets/logo_black.png">
                    </div>
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">Reset Password</h1>
                    <p class="text-gray-600">Enter your new password below.</p>
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

                <!-- Reset Password Form -->
                <form method="POST" action="{{ route('password.update') }}" class="space-y-6" id="reset-form">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">
                    <input type="hidden" name="email" value="{{ $email }}">

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
                            New Password
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
                               id="password_confirmation"
                               name="password_confirmation"
                               placeholder=" "
                               required
                               class="input-focus input-glow peer w-full px-4 py-4 text-gray-700 bg-white border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:outline-none transition-all duration-300">
                        <label for="password_confirmation" 
                               class="floating-label absolute left-4 top-4 text-gray-500 transition-all duration-300 cursor-text peer-focus:text-blue-500">
                            Confirm New Password
                        </label>
                        <button type="button" onclick="togglePassword('password_confirmation')" class="absolute right-4 top-4 text-gray-400 hover:text-gray-600 transition-colors">
                            <svg id="eye-icon-password_confirmation" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" 
                            id="submit-btn"
                            class="btn-hover w-full bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-xl py-4 font-semibold text-lg shadow-lg transition-all duration-300">
                        <span id="btn-text">Reset Password</span>
                        <svg id="loading-spinner" class="hidden w-5 h-5 ml-2 animate-spin inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                    </button>
                </form>

                <!-- Back to Login -->
                <div class="text-center mt-8">
                    <p class="text-gray-600">
                        Remember your password?
                        <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-800 font-medium transition-colors duration-200 ml-1">
                            Back to Login
                        </a>
                    </p>
                </div>
            </div>
        </section>
    </main>

    <script>
        function togglePassword(fieldId) {
            const passwordInput = document.getElementById(fieldId);
            const eyeIcon = document.getElementById(`eye-icon-${fieldId}`);
            
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
        document.getElementById('reset-form').addEventListener('submit', function(event) {
            event.preventDefault();
            
            const form = this;
            const btn = document.getElementById('submit-btn');
            const btnText = document.getElementById('btn-text');
            const spinner = document.getElementById('loading-spinner');
            const errorContainer = document.getElementById('error-container');
            const errorList = document.getElementById('error-list');
            
            // Show loading state
            btnText.textContent = 'Resetting...';
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
                    // Redirect to login with success message
                    window.location.href = '{{ route("login") }}?reset=success';
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
                    btnText.textContent = 'Reset Password';
                    spinner.classList.add('hidden');
                    btn.disabled = false;
                    btn.classList.remove('opacity-75');
                }
            })
            .catch(error => {
                console.error('Reset password error:', error);
                errorList.innerHTML = '<li>An error occurred. Please try again.</li>';
                errorContainer.classList.remove('hidden');
                
                // Reset button state
                btnText.textContent = 'Reset Password';
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
