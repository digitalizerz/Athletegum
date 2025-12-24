<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Setup Your Profile - AthleteGum</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><circle cx='50' cy='50' r='45' fill='%23111'/><text x='50' y='65' font-size='50' font-weight='bold' fill='white' text-anchor='middle'>A</text></svg>" />
    
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
        <div class="w-full sm:max-w-2xl mt-6 px-6 py-8 bg-white shadow-md overflow-hidden sm:rounded-lg">
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-900 mb-2">Tell us about yourself</h1>
                <p class="text-sm text-gray-600">This information will appear on your public profile. You can update this anytime.</p>
            </div>

            <form method="POST" action="{{ route('athlete.profile.setup.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="space-y-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
                        <x-text-input
                            id="name"
                            type="text"
                            name="name"
                            value="{{ old('name', $athlete->name) }}"
                            class="block w-full"
                            required
                            autofocus
                        />
                        <x-input-error :messages="$errors->get('name')" class="mt-1" />
                    </div>

                    <div>
                        <label for="profile_photo" class="block text-sm font-medium text-gray-700 mb-2">Profile Photo (Optional)</label>
                        <div class="flex items-center space-x-4">
                            @if($athlete->profile_photo)
                                <img src="{{ asset('storage/' . $athlete->profile_photo) }}" alt="Profile photo" class="h-20 w-20 rounded-full object-cover">
                            @else
                                <div class="h-20 w-20 rounded-full bg-gray-200 flex items-center justify-center">
                                    <svg class="h-10 w-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                            @endif
                            <div class="flex-1">
                                <input
                                    type="file"
                                    id="profile_photo"
                                    name="profile_photo"
                                    accept="image/*"
                                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
                                />
                                <p class="mt-1 text-xs text-gray-500">A professional photo helps build trust</p>
                            </div>
                        </div>
                        <x-input-error :messages="$errors->get('profile_photo')" class="mt-1" />
                    </div>

                    <div>
                        <label for="athlete_level" class="block text-sm font-medium text-gray-700 mb-2">Level *</label>
                        <select
                            id="athlete_level"
                            name="athlete_level"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            required
                        >
                            <option value="">Select your level</option>
                            <option value="pro" {{ old('athlete_level', $athlete->athlete_level) === 'pro' ? 'selected' : '' }}>Professional</option>
                            <option value="college" {{ old('athlete_level', $athlete->athlete_level) === 'college' ? 'selected' : '' }}>College</option>
                            <option value="highschool" {{ old('athlete_level', $athlete->athlete_level) === 'highschool' ? 'selected' : '' }}>High School</option>
                        </select>
                        <x-input-error :messages="$errors->get('athlete_level')" class="mt-1" />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="sport" class="block text-sm font-medium text-gray-700 mb-2">Sport (Optional)</label>
                            <x-text-input
                                id="sport"
                                type="text"
                                name="sport"
                                value="{{ old('sport', $athlete->sport) }}"
                                class="block w-full"
                                placeholder="Basketball"
                            />
                            <x-input-error :messages="$errors->get('sport')" class="mt-1" />
                        </div>

                        <div>
                            <label for="school" class="block text-sm font-medium text-gray-700 mb-2">School/Team (Optional)</label>
                            <x-text-input
                                id="school"
                                type="text"
                                name="school"
                                value="{{ old('school', $athlete->school) }}"
                                class="block w-full"
                                placeholder="University of California"
                            />
                            <x-input-error :messages="$errors->get('school')" class="mt-1" />
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex justify-end">
                    <button type="submit" class="inline-flex items-center px-5 py-2.5 bg-gray-900 border border-transparent rounded-md font-semibold text-sm text-white hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition">
                        Continue
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>

