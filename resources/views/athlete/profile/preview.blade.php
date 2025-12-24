<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Your Profile is Ready - AthleteGum</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><circle cx='50' cy='50' r='45' fill='%23111'/><text x='50' y='65' font-size='50' font-weight='bold' fill='white' text-anchor='middle'>A</text></svg>" />
    
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 pb-12 bg-gray-50">
        <div class="mb-6">
            <a href="{{ route('welcome') }}">
                <x-athletegum-logo size="lg" text-color="default" />
            </a>
        </div>
        <div class="w-full sm:max-w-2xl mt-6 px-6 py-8 bg-white shadow-md overflow-hidden sm:rounded-lg">
            <div class="text-center mb-8">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
                    <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-gray-900 mb-2">Your AthleteGum profile is live!</h1>
                <p class="text-sm text-gray-600">Share this link with businesses and brands. When someone wants to work with you, they'll create a deal through AthleteGum.</p>
            </div>

            <!-- Profile URL -->
            <div class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                <label class="block text-sm font-medium text-gray-700 mb-2">Your Profile Link</label>
                <div class="flex items-center space-x-2">
                    <input
                        type="text"
                        id="profile-url"
                        value="{{ $athlete->profile_url }}"
                        readonly
                        class="flex-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-white"
                    />
                    <button
                        type="button"
                        onclick="copyProfileLink()"
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gray-900 hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                        Copy Link
                    </button>
                </div>
                <p id="copy-success" class="mt-2 text-sm text-green-600 hidden">Link copied to clipboard!</p>
            </div>

            <!-- Preview -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Preview</label>
                <div class="border border-gray-200 rounded-lg p-6 bg-white">
                    <div class="text-center">
                        @if($athlete->profile_photo)
                            <img src="{{ asset('storage/' . $athlete->profile_photo) }}" alt="{{ $athlete->name }}" class="mx-auto h-24 w-24 rounded-full object-cover mb-4">
                        @else
                            <div class="mx-auto h-24 w-24 rounded-full bg-gray-200 flex items-center justify-center mb-4">
                                <svg class="h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                        @endif
                        <h2 class="text-xl font-bold text-gray-900 mb-1">{{ $athlete->name }}</h2>
                        @if($athlete->sport || $athlete->school)
                            <p class="text-sm text-gray-600 mb-4">
                                @if($athlete->sport){{ $athlete->sport }}@endif
                                @if($athlete->sport && $athlete->school) • @endif
                                @if($athlete->school){{ $athlete->school }}@endif
                            </p>
                        @endif
                        <div class="text-xs text-gray-500">No deals yet</div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex flex-col sm:flex-row gap-3">
                <a
                    href="{{ $athlete->profile_url }}"
                    target="_blank"
                    class="flex-1 inline-flex justify-center items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                    View Profile
                </a>
                <a
                    href="{{ route('athlete.dashboard') }}"
                    class="flex-1 inline-flex justify-center items-center px-4 py-2 bg-gray-900 border border-transparent rounded-md font-semibold text-sm text-white hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
                >
                    Go to Dashboard
                </a>
            </div>

            <!-- Sharing Tips -->
            <div class="mt-6 pt-6 border-t border-gray-200">
                <p class="text-xs font-medium text-gray-700 mb-2">Share your profile:</p>
                <ul class="text-xs text-gray-600 space-y-1">
                    <li>• Add to your Instagram bio</li>
                    <li>• Include in your email signature</li>
                    <li>• Share on Twitter/LinkedIn</li>
                    <li>• Send to brands you want to work with</li>
                </ul>
            </div>
        </div>
    </div>

    <script>
        function copyProfileLink() {
            const urlInput = document.getElementById('profile-url');
            urlInput.select();
            urlInput.setSelectionRange(0, 99999); // For mobile devices
            
            navigator.clipboard.writeText(urlInput.value).then(function() {
                const successMsg = document.getElementById('copy-success');
                successMsg.classList.remove('hidden');
                setTimeout(() => {
                    successMsg.classList.add('hidden');
                }, 3000);
            });
        }
    </script>
</body>
</html>

