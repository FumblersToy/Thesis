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
                <h1 class="text-3xl font-bold text-gray-800 mb-2">Check Your Email</h1>
                <p class="text-gray-600">We've sent a verification link to</p>
                <p class="text-gray-800 font-semibold mt-2">{{ session('email') }}</p>
            </div>

            <!-- Email Icon -->
            <div class="text-center mb-6">
                <svg class="w-24 h-24 mx-auto text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>

            <!-- Instructions -->
            <div class="mb-6 text-center">
                <p class="text-gray-700 mb-4">
                    Please check your email and click the verification link to complete your registration.
                </p>
                <p class="text-sm text-gray-600">
                    The link will expire in 24 hours. If you don't see the email, check your spam folder.
                </p>
            </div>

            <!-- Back to Register -->
            <div class="text-center">
                <a href="{{ route('register') }}" class="text-gray-600 hover:text-gray-800 text-sm transition-colors duration-200">
                    ← Back to Registration
                </a>
            </div>
        </div>
    </div>
</body>
</html>
