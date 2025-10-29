<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bandmate | Register</title>
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
                <form method="POST" action="{{ route('register') }}" class="space-y-6" onsubmit="return validateForm()">
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
                            class="input-focus input-glow peer w-full px-4 py-4 pr-12 text-gray-700 bg-white border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:outline-none transition-all duration-300">
                        <label for="password" 
                            class="floating-label absolute left-4 top-4 text-gray-500 transition-all duration-300 cursor-text peer-focus:text-blue-500 pointer-events-none">
                            Password
                        </label>
                        <button type="button" onclick="togglePassword('password', 'eye-icon-password')" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors z-10">
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
                            class="input-focus input-glow peer w-full px-4 py-4 pr-12 text-gray-700 bg-white border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:outline-none transition-all duration-300">
                        <label for="confirm-password" 
                            class="floating-label absolute left-4 top-4 text-gray-500 transition-all duration-300 cursor-text peer-focus:text-blue-500 pointer-events-none">
                            Confirm Password
                        </label>
                        <button type="button" onclick="togglePassword('confirm-password', 'eye-icon-confirm-password')" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors z-10">
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
    <script>
    function togglePassword(fieldId, iconId) {
        const passwordInput = document.getElementById(fieldId);
        const eyeIcon = document.getElementById(iconId);
        
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

    function validateForm() {
        const email = document.getElementById('email').value;
        const confirmEmail = document.getElementById('confirm-email').value;
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirm-password').value;
        const emailMatchError = document.getElementById('email-match-error');
        const passwordMatchError = document.getElementById('password-match-error');
        const errorContainer = document.getElementById('error-container');
        const errorList = document.getElementById('error-list');
        
        let isValid = true;
        errorList.innerHTML = '';
        emailMatchError.classList.add('hidden');
        passwordMatchError.classList.add('hidden');
        errorContainer.classList.add('hidden');

        // Check if emails match
        if (email !== confirmEmail) {
            emailMatchError.classList.remove('hidden');
            isValid = false;
        }

        // Check if passwords match
        if (password !== confirmPassword) {
            passwordMatchError.classList.remove('hidden');
            isValid = false;
        }

        // Check password length
        if (password.length < 8) {
            const errorItem = document.createElement('li');
            errorItem.textContent = 'Password must be at least 8 characters long.';
            errorList.appendChild(errorItem);
            errorContainer.classList.remove('hidden');
            isValid = false;
        }

        return isValid;
    }

    // Real-time email validation
    document.getElementById('confirm-email').addEventListener('input', function() {
        const email = document.getElementById('email').value;
        const confirmEmail = this.value;
        const emailMatchError = document.getElementById('email-match-error');
        
        if (confirmEmail && email !== confirmEmail) {
            emailMatchError.classList.remove('hidden');
        } else {
            emailMatchError.classList.add('hidden');
        }
    });

    // Real-time password validation
    document.getElementById('confirm-password').addEventListener('input', function() {
        const password = document.getElementById('password').value;
        const confirmPassword = this.value;
        const passwordMatchError = document.getElementById('password-match-error');
        
        if (confirmPassword && password !== confirmPassword) {
            passwordMatchError.classList.remove('hidden');
        } else {
            passwordMatchError.classList.add('hidden');
        }
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