<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bandmate | Forgot Password</title>
    @vite(['resources/css/app.css', 'resources/css/login.css', 'resources/js/app.js'])
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
                <h2 class="text-3xl font-bold mb-4 text-shadow">Reset Your Password</h2>
                <p class="text-lg opacity-90 max-w-md leading-relaxed">Don't worry, it happens to the best of us. We'll help you get back to making music.</p>
                
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
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">Forgot Password?</h1>
                    <p class="text-gray-600">Enter your email address and we'll send you a link to reset your password.</p>
                </div>

                @if (session('status'))
                    <div class="mb-6 bg-green-50 border-l-4 border-green-400 text-green-700 text-sm rounded-r-lg p-4">
                        <div class="flex">
                            <svg class="w-5 h-5 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span>{{ session('status') }}</span>
                        </div>
                    </div>
                @endif

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

                <!-- Forgot Password Form -->
                <form method="POST" action="{{ route('password.email') }}" class="space-y-6" id="forgot-form">
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

                    <!-- Submit Button -->
                    <button type="submit" 
                            id="submit-btn"
                            class="btn-hover w-full bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-xl py-4 font-semibold text-lg shadow-lg transition-all duration-300">
                        <span id="btn-text">Send Reset Link</span>
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
        // Handle form submission
        document.getElementById('forgot-form').addEventListener('submit', function(event) {
            event.preventDefault();
            console.log('[FORGOT PASSWORD] Form submission started');
            
            const form = this;
            const btn = document.getElementById('submit-btn');
            const btnText = document.getElementById('btn-text');
            const spinner = document.getElementById('loading-spinner');
            const errorContainer = document.getElementById('error-container');
            const errorList = document.getElementById('error-list');
            
            console.log('[FORGOT PASSWORD] Form action URL:', form.action);
            
            // Show loading state
            btnText.textContent = 'Sending...';
            spinner.classList.remove('hidden');
            btn.disabled = true;
            btn.classList.add('opacity-75');
            errorContainer.classList.add('hidden');
            errorList.innerHTML = '';
            
            // Get form data
            const formData = new FormData(form);
            const email = formData.get('email');
            console.log('[FORGOT PASSWORD] Submitting for email:', email);
            
            // Submit form
            console.log('[FORGOT PASSWORD] Starting fetch request...');
            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                console.log('[FORGOT PASSWORD] Response received:', {
                    status: response.status,
                    statusText: response.statusText,
                    ok: response.ok,
                    headers: Object.fromEntries(response.headers.entries())
                });
                
                // Check if response is ok (status 200-299)
                if (response.ok) {
                    console.log('[FORGOT PASSWORD] Response OK, attempting to parse JSON...');
                    return response.json().catch(err => {
                        console.log('[FORGOT PASSWORD] No JSON in response, will reload page', err);
                        // If no JSON, just reload
                        window.location.reload();
                        return null;
                    });
                } else {
                    console.log('[FORGOT PASSWORD] Response not OK, parsing error JSON...');
                    // For error responses, parse the JSON
                    return response.json().then(data => {
                        console.error('[FORGOT PASSWORD] Error data:', data);
                        throw data;
                    }).catch(err => {
                        console.error('[FORGOT PASSWORD] Failed to parse error JSON:', err);
                        throw { message: `Server error: ${response.status} ${response.statusText}` };
                    });
                }
            })
            .then(data => {
                console.log('[FORGOT PASSWORD] Final data:', data);
                // Success - reload to show success message
                if (data !== null) {
                    console.log('[FORGOT PASSWORD] Success! Reloading page...');
                    window.location.reload();
                }
            })
            .catch(error => {
                console.error('[FORGOT PASSWORD] Caught error:', error);
                
                // Display errors
                if (error && error.errors) {
                    console.log('[FORGOT PASSWORD] Displaying validation errors:', error.errors);
                    const errors = error.errors;
                    errorList.innerHTML = '';
                    
                    Object.keys(errors).forEach(key => {
                        const errorItem = document.createElement('li');
                        errorItem.textContent = errors[key][0];
                        errorList.appendChild(errorItem);
                    });
                } else if (error && error.message) {
                    console.log('[FORGOT PASSWORD] Displaying error message:', error.message);
                    errorList.innerHTML = `<li>${error.message}</li>`;
                } else {
                    console.log('[FORGOT PASSWORD] Displaying generic error');
                    errorList.innerHTML = '<li>An error occurred. Please try again.</li>';
                }
                
                errorContainer.classList.remove('hidden');
                
                // Reset button state
                btnText.textContent = 'Send Reset Link';
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
