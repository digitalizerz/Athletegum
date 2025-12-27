<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <!-- Prevent browser dark mode on mobile -->
        <meta name="color-scheme" content="light">
        <meta name="theme-color" content="#ffffff">

        <title>{{ config('app.name', 'AthleteGum') }}</title>

        <!-- Favicon -->
        <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><circle cx='50' cy='50' r='45' fill='%23111'/><text x='50' y='65' font-size='50' font-weight='bold' fill='white' text-anchor='middle'>A</text></svg>" />

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script>
            // Prevent browser dark mode immediately on page load
            (function() {
                document.documentElement.style.colorScheme = 'light';
                document.documentElement.style.backgroundColor = '#ffffff';
                if (document.body) {
                    document.body.style.backgroundColor = '#f3f4f6';
                    document.body.style.color = '#111827';
                }
            })();
        </script>
    </head>
    <body class="font-sans text-gray-900 antialiased" style="background-color: #f3f4f6 !important; color: #111827 !important;">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
            <div class="mb-6">
                <a href="{{ route('welcome') }}">
                    <x-athletegum-logo size="lg" text-color="default" />
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
