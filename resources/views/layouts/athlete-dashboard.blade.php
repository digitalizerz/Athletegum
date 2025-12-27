<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'AthleteGum') }}</title>

        <!-- Favicon -->
        <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><circle cx='50' cy='50' r='45' fill='%23111'/><text x='50' y='65' font-size='50' font-weight='bold' fill='white' text-anchor='middle'>A</text></svg>" />

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-50 dark:bg-gray-900">
        <div x-data="{ 
                sidebarOpen: true,
                darkMode: false,
                init() {
                    // Initialize theme
                    const savedTheme = localStorage.getItem('theme');
                    const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
                    this.darkMode = savedTheme === 'dark' || (!savedTheme && prefersDark);
                    if (this.darkMode) {
                        document.documentElement.classList.add('dark');
                    }
                    
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
                },
                toggleTheme() {
                    this.darkMode = !this.darkMode;
                    if (this.darkMode) {
                        document.documentElement.classList.add('dark');
                        localStorage.setItem('theme', 'dark');
                    } else {
                        document.documentElement.classList.remove('dark');
                        localStorage.setItem('theme', 'light');
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
                <main class="flex-1 overflow-y-auto bg-gray-50 dark:bg-gray-900">
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

