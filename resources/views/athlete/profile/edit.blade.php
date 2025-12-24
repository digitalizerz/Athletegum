<x-athlete-dashboard-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit Profile
        </h2>
    </x-slot>

    <div class="bg-white rounded-lg shadow-sm p-8">
        <form method="POST" action="{{ route('athlete.profile.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PATCH')

            <div class="max-w-3xl space-y-10">
                <!-- Section 1: Identity -->
                <div class="space-y-6">
                    <h3 class="text-base font-semibold text-gray-900 border-b border-gray-200 pb-2">Identity</h3>
                    
                    <div class="flex items-start gap-6">
                        <!-- Profile Photo -->
                        <div class="flex-shrink-0">
                            <label for="profile_photo" class="block text-sm font-medium text-gray-700 mb-2">Profile Photo</label>
                            <div class="flex flex-col items-start">
                                @if($athlete->profile_photo)
                                    <img src="{{ asset('storage/' . $athlete->profile_photo) }}" alt="Profile photo" class="h-16 w-16 rounded-full object-cover border-2 border-gray-200 mb-3">
                                @else
                                    <div class="h-16 w-16 rounded-full bg-gray-200 flex items-center justify-center border-2 border-gray-200 mb-3">
                                        <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                @endif
                                <input
                                    type="file"
                                    id="profile_photo"
                                    name="profile_photo"
                                    accept="image/*"
                                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-gray-900 file:text-white hover:file:bg-gray-800"
                                />
                                <p class="mt-2 text-xs text-gray-500">JPG, PNG or GIF. Max size 2MB.</p>
                            </div>
                            <x-input-error :messages="$errors->get('profile_photo')" class="mt-1" />
                        </div>

                        <!-- Full Name -->
                        <div class="flex-1">
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
                    </div>
                </div>

                <!-- Section 2: Athlete Info -->
                <div class="space-y-6">
                    <h3 class="text-base font-semibold text-gray-900 border-b border-gray-200 pb-2">Athlete Information</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="athlete_level" class="block text-sm font-medium text-gray-700 mb-2">Level *</label>
                            <select
                                id="athlete_level"
                                name="athlete_level"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5"
                                required
                            >
                                <option value="">Select your level</option>
                                <option value="pro" {{ old('athlete_level', $athlete->athlete_level) === 'pro' ? 'selected' : '' }}>Professional</option>
                                <option value="college" {{ old('athlete_level', $athlete->athlete_level) === 'college' ? 'selected' : '' }}>College</option>
                                <option value="highschool" {{ old('athlete_level', $athlete->athlete_level) === 'highschool' ? 'selected' : '' }}>High School</option>
                            </select>
                            <x-input-error :messages="$errors->get('athlete_level')" class="mt-1" />
                        </div>
                        <div>
                            <label for="sport" class="block text-sm font-medium text-gray-700 mb-2">Sport</label>
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
                            <label for="school" class="block text-sm font-medium text-gray-700 mb-2">School/Team</label>
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

                <!-- Section 3: Shareable Profile Link -->
                <div class="space-y-6">
                    <h3 class="text-base font-semibold text-gray-900 border-b border-gray-200 pb-2">Shareable Profile Link</h3>
                    
                    <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                        <label for="username" class="block text-sm font-semibold text-gray-900 mb-2">Custom Username</label>
                        <div class="flex items-center">
                            <span class="text-gray-600 mr-2 text-base font-medium">athletegum.com/a/</span>
                            <x-text-input
                                id="username"
                                type="text"
                                name="username"
                                value="{{ old('username', $athlete->username) }}"
                                class="block flex-1"
                                placeholder="your-username"
                            />
                        </div>
                        <p class="mt-3 text-sm text-gray-600">Leave blank to use your unique token. This will be your public profile URL.</p>
                        <x-input-error :messages="$errors->get('username')" class="mt-1" />
                    </div>
                </div>

                <!-- Section 4: Social Media Links -->
                <div class="space-y-6">
                    <h3 class="text-base font-semibold text-gray-900 border-b border-gray-200 pb-2">Social Media Links</h3>
                    
                    <div class="space-y-6">
                        <div>
                            <label for="instagram_handle" class="block text-sm font-medium text-gray-700 mb-2">Instagram</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none z-10">
                                    <span class="text-gray-400 font-medium">@</span>
                                </div>
                                <x-text-input
                                    id="instagram_handle"
                                    type="text"
                                    name="instagram_handle"
                                    value="{{ old('instagram_handle', ltrim($athlete->instagram_handle ?? '', '@')) }}"
                                    class="block w-full pl-8 pr-3"
                                    placeholder="username"
                                />
                            </div>
                        </div>

                        <div>
                            <label for="tiktok_handle" class="block text-sm font-medium text-gray-700 mb-2">TikTok</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none z-10">
                                    <span class="text-gray-400 font-medium">@</span>
                                </div>
                                <x-text-input
                                    id="tiktok_handle"
                                    type="text"
                                    name="tiktok_handle"
                                    value="{{ old('tiktok_handle', ltrim($athlete->tiktok_handle ?? '', '@')) }}"
                                    class="block w-full pl-8 pr-3"
                                    placeholder="username"
                                />
                            </div>
                        </div>

                        <div>
                            <label for="twitter_handle" class="block text-sm font-medium text-gray-700 mb-2">X (Twitter)</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none z-10">
                                    <span class="text-gray-400 font-medium">@</span>
                                </div>
                                <x-text-input
                                    id="twitter_handle"
                                    type="text"
                                    name="twitter_handle"
                                    value="{{ old('twitter_handle', ltrim($athlete->twitter_handle ?? '', '@')) }}"
                                    class="block w-full pl-8 pr-3"
                                    placeholder="username"
                                />
                            </div>
                        </div>

                        <div>
                            <label for="youtube_handle" class="block text-sm font-medium text-gray-700 mb-2">YouTube</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none z-10">
                                    <span class="text-gray-400 font-medium">@</span>
                                </div>
                                <x-text-input
                                    id="youtube_handle"
                                    type="text"
                                    name="youtube_handle"
                                    value="{{ old('youtube_handle', ltrim($athlete->youtube_handle ?? '', '@')) }}"
                                    class="block w-full pl-8 pr-3"
                                    placeholder="channel"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="border-t border-gray-200 pt-6">
                    <div class="flex justify-end">
                        <button type="submit" class="inline-flex items-center px-6 py-2.5 bg-gray-900 border border-transparent rounded-md font-semibold text-sm text-white hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition">
                            Save Changes
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</x-athlete-dashboard-layout>

