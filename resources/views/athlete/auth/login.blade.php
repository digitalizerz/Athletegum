<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sign In - AthleteGum</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-50">
        <div class="mb-6">
            <a href="{{ route('welcome') }}">
                <x-athletegum-logo size="lg" text-color="default" />
            </a>
        </div>
        <div class="w-full sm:max-w-md mt-6 px-6 py-8 bg-white shadow-md overflow-hidden sm:rounded-lg">
            <div class="mb-6 text-center">
                <h1 class="text-2xl font-bold text-gray-900 mb-2">Sign In</h1>
                <p class="text-sm text-gray-600">Access your athlete profile</p>
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

            <form method="POST" action="{{ route('athlete.login.store') }}">
                @csrf

                <div class="space-y-4">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                        <x-text-input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            class="block w-full"
                            required
                            autofocus
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
                            autocomplete="current-password"
                        />
                        <x-input-error :messages="$errors->get('password')" class="mt-1" />
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input id="remember" type="checkbox" name="remember" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <label for="remember" class="ml-2 block text-sm text-gray-700">Remember me</label>
                        </div>
                        @if (Route::has('athlete.password.request'))
                            <a href="{{ route('athlete.password.request') }}" class="text-sm text-indigo-600 hover:text-indigo-800">
                                Forgot password?
                            </a>
                        @endif
                    </div>
                </div>

                <div class="mt-6">
                    <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2.5 bg-gray-900 border border-transparent rounded-md font-semibold text-sm text-white hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition">
                        Sign In
                    </button>
                </div>

                <div class="mt-4 text-center">
                    <p class="text-sm text-gray-600">
                        Don't have an account?
                        <a href="{{ route('athlete.register') }}" class="text-indigo-600 hover:text-indigo-800 font-medium">Create one</a>
                    </p>
                </div>

                <div class="mt-4 pt-4 border-t border-gray-200 text-center">
                    <p class="text-xs text-gray-500 mb-2">Looking for something else?</p>
                    <a href="{{ route('welcome') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                        ← Back to home
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>

