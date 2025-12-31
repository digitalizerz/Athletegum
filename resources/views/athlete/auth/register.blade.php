<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" style="color-scheme: light !important; background-color: #ffffff !important;">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Force light mode - prevent browser dark mode -->
    <meta name="color-scheme" content="light">
    <meta name="theme-color" content="#ffffff">
    <title>Create Your Athlete Profile - AthleteGum</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><circle cx='50' cy='50' r='45' fill='%23111'/><text x='50' y='65' font-size='50' font-weight='bold' fill='white' text-anchor='middle'>A</text></svg>" />
    
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        // Prevent browser dark mode immediately on page load
        (function() {
            document.documentElement.style.colorScheme = 'light';
            document.documentElement.style.backgroundColor = '#ffffff';
            if (document.body) {
                document.body.style.backgroundColor = '#f9fafb';
                document.body.style.color = '#111827';
            }
        })();
    </script>
</head>
<body class="font-sans antialiased bg-gray-50" style="background-color: #f9fafb !important; color: #111827 !important;">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-50" style="background-color: #f9fafb !important;">
        <div class="mb-6">
            <a href="{{ route('welcome') }}">
                <x-athletegum-logo size="lg" text-color="default" />
            </a>
        </div>
        <div class="w-full sm:max-w-md mt-6 px-6 py-8 bg-white shadow-md overflow-hidden sm:rounded-lg" style="background-color: #ffffff !important;">
            <div class="mb-6 text-center">
                <h1 class="text-2xl font-bold text-gray-900 mb-2">Create Your Professional Athlete Profile</h1>
                <p class="text-sm text-gray-600">Get a shareable link that showcases your work and makes it easy for businesses to work with you.</p>
            </div>

            @if ($errors->any())
                <div class="mb-4 bg-red-50 border-l-4 border-red-400 p-4 rounded">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-red-800">{{ $errors->first() }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('athlete.register.store') }}">
                @csrf

                <div class="space-y-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                        <x-text-input
                            id="name"
                            type="text"
                            name="name"
                            value="{{ old('name') }}"
                            class="block w-full"
                            required
                            autofocus
                            placeholder="John Doe"
                        />
                        <x-input-error :messages="$errors->get('name')" class="mt-1" />
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                        <x-text-input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            class="block w-full"
                            required
                            placeholder="john@example.com"
                        />
                        <x-input-error :messages="$errors->get('email')" class="mt-1" />
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <x-text-input
                            id="password"
                            type="password"
                            name="password"
                            class="block w-full"
                            required
                            autocomplete="new-password"
                        />
                        <x-input-error :messages="$errors->get('password')" class="mt-1" />
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                        <x-text-input
                            id="password_confirmation"
                            type="password"
                            name="password_confirmation"
                            class="block w-full"
                            required
                            autocomplete="new-password"
                        />
                    </div>
                </div>

                <div class="mt-6">
                    <div class="flex items-start">
                        <input id="terms" type="checkbox" name="terms" value="1" required class="mt-1 h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                        <label for="terms" class="ml-2 block text-sm text-gray-700">
                            I agree to the <a href="#" class="text-indigo-600 hover:text-indigo-800">Terms of Service</a>
                        </label>
                    </div>
                </div>

                <div class="mt-6">
                    <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2.5 bg-gray-900 border border-transparent rounded-md font-semibold text-sm text-white hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition">
                        Create Account
                    </button>
                </div>

                <div class="mt-4 text-center">
                    <p class="text-sm text-gray-600">
                        Already have an account?
                        <a href="{{ route('athlete.login') }}" class="text-indigo-600 hover:text-indigo-800 font-medium">Sign in</a>
                    </p>
                </div>
            </form>

            <div class="mt-6 pt-6 border-t border-gray-200">
                <p class="text-xs text-center text-gray-500 mb-3">
                    AthleteGum is not a marketplace. Your profile is a shareable link that builds trust and makes it easy for businesses to start working with you.
                </p>
                <a href="{{ route('welcome') }}" class="text-xs text-indigo-600 hover:text-indigo-800">
                    ← Back to home
                </a>
            </div>
        </div>
    </div>
</body>
</html>

