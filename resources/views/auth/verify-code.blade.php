<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Email - Bandmate</title>
    @vite(['resources/css/app.css', 'resources/css/login.css'])
</head>
<body class="min-h-screen gradient-bg flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <div class="glass-effect rounded-2xl shadow-2xl p-8 fade-in">
            <!-- Logo -->
            <div class="text-center mb-8">
                <img src="/assets/logo_black.png" alt="Bandmate Logo" class="w-16 h-16 mx-auto mb-4">
                <h1 class="text-3xl font-bold text-gray-800 mb-2">Verify Your Email</h1>
                <p class="text-gray-600">We've sent a 6-digit code to</p>
                <p class="text-gray-800 font-semibold">{{ session('email') ?? session('pending_registration.email') }}</p>
            </div>

            <!-- Email Icon -->
            <div class="text-center mb-6">
                <svg class="w-24 h-24 mx-auto text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>

            <!-- Status Message -->
            @if (session('status'))
                <div class="mb-6 bg-green-50 border-l-4 border-green-400 text-green-700 p-4 rounded-r-lg">
                    <p class="text-sm">{{ session('status') }}</p>
                </div>
            @endif

            <!-- Error Messages -->
            @if ($errors->any())
                <div class="mb-6 bg-red-50 border-l-4 border-red-400 text-red-700 p-4 rounded-r-lg">
                    @foreach ($errors->all() as $error)
                        <p class="text-sm">{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <!-- Verification Code Form -->
            <form method="POST" action="{{ route('verification.verify') }}" class="space-y-6">
                @csrf
                
                <div class="relative">
                    <input type="text" 
                           id="code"
                           name="code" 
                           maxlength="6"
                           pattern="[0-9]{6}"
                           placeholder="Enter 6-digit code"
                           required
                           class="w-full px-4 py-4 text-center text-2xl tracking-widest font-bold text-gray-700 bg-white border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:outline-none transition-all duration-300 @error('code') border-red-500 @enderror"
                           value="{{ old('code') }}"
                           autofocus>
                </div>

                <button type="submit" 
                        class="w-full gradient-bg text-white py-3 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all duration-300">
                    Verify Email
                </button>
            </form>

            <!-- Resend Code -->
            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600 mb-3">Didn't receive the code?</p>
                <form method="POST" action="{{ route('verification.resend') }}">
                    @csrf
                    <button type="submit" 
                            class="text-blue-600 hover:text-blue-800 font-medium text-sm transition-colors duration-200">
                        Resend Verification Code
                    </button>
                </form>
            </div>

            <!-- Back to Register -->
            <div class="mt-6 text-center">
                <a href="{{ route('register') }}" class="text-gray-600 hover:text-gray-800 text-sm transition-colors duration-200">
                    ← Back to Registration
                </a>
            </div>
        </div>
    </div>

    <script>
        // Auto-format code input (only numbers)
        const codeInput = document.getElementById('code');
        codeInput.addEventListener('input', function(e) {
            // Remove non-numeric characters
            this.value = this.value.replace(/[^0-9]/g, '');
            
            // Limit to 6 digits
            if (this.value.length > 6) {
                this.value = this.value.slice(0, 6);
            }
        });

        // Auto-submit when 6 digits are entered
        codeInput.addEventListener('input', function() {
            if (this.value.length === 6) {
                this.form.submit();
            }
        });
    </script>
</body>
</html>
