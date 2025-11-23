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
                <p class="text-gray-600">We've sent a verification link to your email address</p>
            </div>

            <!-- Email Icon -->
            <div class="text-center mb-6">
                <svg class="w-24 h-24 mx-auto text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>

            <!-- Status Message -->
            @if (session('status') == 'verification-link-sent')
                <div class="mb-6 bg-green-50 border-l-4 border-green-400 text-green-700 p-4 rounded-r-lg">
                    <p class="text-sm">A new verification link has been sent to your email address!</p>
                </div>
            @endif

            <!-- Instructions -->
            <div class="mb-6 text-center">
                <p class="text-gray-700 mb-4">
                    Please check your email and click the verification link to continue setting up your profile.
                </p>
                <p class="text-sm text-gray-600">
                    Didn't receive the email? Check your spam folder or request a new link below.
                </p>
            </div>

            <!-- Resend Button -->
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="w-full gradient-bg text-white py-3 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all duration-300 mb-4">
                    Resend Verification Email
                </button>
            </form>

            <!-- Logout Button -->
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full border-2 border-gray-300 text-gray-700 py-3 rounded-xl font-semibold hover:bg-gray-50 transition-all duration-300">
                    Logout
                </button>
            </form>
        </div>
    </div>
</body>
</html>
