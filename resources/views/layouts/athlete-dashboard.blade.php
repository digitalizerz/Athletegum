<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light" style="color-scheme: light !important;">
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
            // Prevent browser dark mode immediately on page load - MUST run before any styles
            (function() {
                // Force light color scheme
                document.documentElement.style.colorScheme = 'light';
                document.documentElement.setAttribute('data-theme', 'light');
                document.documentElement.classList.remove('dark', 'dark-mode');
                document.documentElement.classList.add('light');
                document.documentElement.style.backgroundColor = '#ffffff';
                
                if (document.body) {
                    document.body.classList.remove('dark', 'dark-mode');
                    document.body.style.colorScheme = 'light';
                    document.body.style.backgroundColor = '#f9fafb';
                    document.body.style.color = '#111827';
                }
                
                // Continuously monitor and prevent dark mode
                const observer = new MutationObserver(function(mutations) {
                    if (document.documentElement.classList.contains('dark') || 
                        document.documentElement.classList.contains('dark-mode') ||
                        document.documentElement.getAttribute('data-theme') === 'dark') {
                        document.documentElement.classList.remove('dark', 'dark-mode');
                        document.documentElement.setAttribute('data-theme', 'light');
                        document.documentElement.style.colorScheme = 'light';
                    }
                    if (document.body && (document.body.classList.contains('dark') || 
                        document.body.classList.contains('dark-mode'))) {
                        document.body.classList.remove('dark', 'dark-mode');
                        document.body.style.colorScheme = 'light';
                    }
                });
                
                observer.observe(document.documentElement, {
                    attributes: true,
                    attributeFilter: ['class', 'data-theme', 'style'],
                    childList: false,
                    subtree: false
                });
                
                if (document.body) {
                    observer.observe(document.body, {
                        attributes: true,
                        attributeFilter: ['class', 'style'],
                        childList: false,
                        subtree: false
                    });
                }
            })();
        </script>
    </head>
    <body class="font-sans antialiased bg-gray-50" style="background-color: #f9fafb !important; color: #111827 !important;">
        <div x-data="{ 
                sidebarOpen: true,
                init() {
                    // On desktop, check localStorage; on mobile, default to closed
                    if (window.innerWidth >= 1024) {
                        const saved = localStorage.getItem('athleteSidebarOpen');
                        this.sidebarOpen = saved !== null ? saved === 'true' : true;
                    } else {
                        this.sidebarOpen = false;
                    }
                },
                toggleSidebar() {
                    this.sidebarOpen = !this.sidebarOpen;
                    if (window.innerWidth >= 1024) {
                        localStorage.setItem('athleteSidebarOpen', this.sidebarOpen);
                    }
                }
            }" 
            x-init="init()"
            class="flex h-screen overflow-hidden">
            <!-- Sidebar -->
            @include('layouts.athlete-sidebar')

            <!-- Main Content -->
            <div class="flex flex-col flex-1 overflow-hidden transition-all duration-300" :class="sidebarOpen ? 'lg:ml-64' : 'lg:ml-0'">
                <!-- Top Bar -->
                @include('layouts.athlete-topbar')

                <!-- Page Content -->
                <main class="flex-1 overflow-y-auto bg-gray-50" style="background-color: #f9fafb !important;">
                    @isset($header)
                        <div class="bg-white shadow-sm border-b border-gray-200">
                            <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                                {{ $header }}
                            </div>
                        </div>
                    @endisset

                    <div class="w-full py-6 px-4 sm:px-6 lg:px-8">
                        {{ $slot }}
                    </div>
                </main>
            </div>

            <!-- Sidebar Overlay (mobile) -->
            <div x-show="sidebarOpen"
                 x-transition:enter="transition-opacity ease-linear duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition-opacity ease-linear duration-300"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="toggleSidebar()"
                 class="fixed inset-0 bg-gray-600 bg-opacity-75 z-20 lg:hidden"
                 style="display: none;">
            </div>
        </div>
    </body>
</html>

