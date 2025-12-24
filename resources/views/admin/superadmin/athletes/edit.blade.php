<x-superadmin-dashboard-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold">Edit Athlete</h2>
            <a href="{{ route('admin.athletes.index') }}" class="btn btn-ghost btn-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Athletes
            </a>
        </div>
    </x-slot>

    <div class="max-w-4xl">
        <div class="card bg-base-100 shadow-sm border border-base-300">
            <div class="card-body">
                <form method="POST" action="{{ route('admin.athletes.update', $athlete) }}">
                    @csrf
                    @method('PUT')

                    <div class="space-y-6">
                        <div>
                            <h3 class="text-lg font-semibold mb-4">Basic Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="form-control">
                                    <label class="label" for="name">
                                        <span class="label-text font-medium">Name</span>
                                    </label>
                                    <input type="text" id="name" name="name" value="{{ old('name', $athlete->name) }}" 
                                           class="input input-bordered w-full @error('name') input-error @enderror" required>
                                    @error('name')
                                        <label class="label">
                                            <span class="label-text-alt text-error">{{ $message }}</span>
                                        </label>
                                    @enderror
                                </div>

                                <div class="form-control">
                                    <label class="label" for="email">
                                        <span class="label-text font-medium">Email</span>
                                    </label>
                                    <input type="email" id="email" name="email" value="{{ old('email', $athlete->email) }}" 
                                           class="input input-bordered w-full @error('email') input-error @enderror" required>
                                    @error('email')
                                        <label class="label">
                                            <span class="label-text-alt text-error">{{ $message }}</span>
                                        </label>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div>
                            <h3 class="text-lg font-semibold mb-4">Athlete Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="form-control">
                                    <label class="label" for="sport">
                                        <span class="label-text font-medium">Sport</span>
                                    </label>
                                    <input type="text" id="sport" name="sport" value="{{ old('sport', $athlete->sport) }}" 
                                           class="input input-bordered w-full @error('sport') input-error @enderror">
                                    @error('sport')
                                        <label class="label">
                                            <span class="label-text-alt text-error">{{ $message }}</span>
                                        </label>
                                    @enderror
                                </div>

                                <div class="form-control">
                                    <label class="label" for="school">
                                        <span class="label-text font-medium">School</span>
                                    </label>
                                    <input type="text" id="school" name="school" value="{{ old('school', $athlete->school) }}" 
                                           class="input input-bordered w-full @error('school') input-error @enderror">
                                    @error('school')
                                        <label class="label">
                                            <span class="label-text-alt text-error">{{ $message }}</span>
                                        </label>
                                    @enderror
                                </div>

                                <div class="form-control">
                                    <label class="label" for="athlete_level">
                                        <span class="label-text font-medium">Level</span>
                                    </label>
                                    <select id="athlete_level" name="athlete_level" 
                                            class="select select-bordered w-full @error('athlete_level') select-error @enderror">
                                        <option value="">Select Level</option>
                                        <option value="pro" {{ old('athlete_level', $athlete->athlete_level) === 'pro' ? 'selected' : '' }}>Pro</option>
                                        <option value="college" {{ old('athlete_level', $athlete->athlete_level) === 'college' ? 'selected' : '' }}>College</option>
                                        <option value="highschool" {{ old('athlete_level', $athlete->athlete_level) === 'highschool' ? 'selected' : '' }}>High School</option>
                                    </select>
                                    @error('athlete_level')
                                        <label class="label">
                                            <span class="label-text-alt text-error">{{ $message }}</span>
                                        </label>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div>
                            <h3 class="text-lg font-semibold mb-4">Social Media</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="form-control">
                                    <label class="label" for="instagram_handle">
                                        <span class="label-text font-medium">Instagram</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="bg-base-200 px-3 flex items-center">@</span>
                                        <input type="text" id="instagram_handle" name="instagram_handle" value="{{ old('instagram_handle', $athlete->instagram_handle) }}" 
                                               class="input input-bordered flex-1 @error('instagram_handle') input-error @enderror" 
                                               placeholder="username">
                                    </div>
                                    @error('instagram_handle')
                                        <label class="label">
                                            <span class="label-text-alt text-error">{{ $message }}</span>
                                        </label>
                                    @enderror
                                </div>

                                <div class="form-control">
                                    <label class="label" for="tiktok_handle">
                                        <span class="label-text font-medium">TikTok</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="bg-base-200 px-3 flex items-center">@</span>
                                        <input type="text" id="tiktok_handle" name="tiktok_handle" value="{{ old('tiktok_handle', $athlete->tiktok_handle) }}" 
                                               class="input input-bordered flex-1 @error('tiktok_handle') input-error @enderror" 
                                               placeholder="username">
                                    </div>
                                    @error('tiktok_handle')
                                        <label class="label">
                                            <span class="label-text-alt text-error">{{ $message }}</span>
                                        </label>
                                    @enderror
                                </div>

                                <div class="form-control">
                                    <label class="label" for="twitter_handle">
                                        <span class="label-text font-medium">X (Twitter)</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="bg-base-200 px-3 flex items-center">@</span>
                                        <input type="text" id="twitter_handle" name="twitter_handle" value="{{ old('twitter_handle', $athlete->twitter_handle) }}" 
                                               class="input input-bordered flex-1 @error('twitter_handle') input-error @enderror" 
                                               placeholder="username">
                                    </div>
                                    @error('twitter_handle')
                                        <label class="label">
                                            <span class="label-text-alt text-error">{{ $message }}</span>
                                        </label>
                                    @enderror
                                </div>

                                <div class="form-control">
                                    <label class="label" for="youtube_handle">
                                        <span class="label-text font-medium">YouTube</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="bg-base-200 px-3 flex items-center">@</span>
                                        <input type="text" id="youtube_handle" name="youtube_handle" value="{{ old('youtube_handle', $athlete->youtube_handle) }}" 
                                               class="input input-bordered flex-1 @error('youtube_handle') input-error @enderror" 
                                               placeholder="username">
                                    </div>
                                    @error('youtube_handle')
                                        <label class="label">
                                            <span class="label-text-alt text-error">{{ $message }}</span>
                                        </label>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-base-300">
                            <a href="{{ route('admin.athletes.index') }}" class="btn btn-ghost">Cancel</a>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-superadmin-dashboard-layout>

