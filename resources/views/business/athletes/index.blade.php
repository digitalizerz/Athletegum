<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-semibold">Browse Athletes</h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Feature Locked Banner (Free users only) -->
            @if(!$canSearch)
                <div class="mb-6">
                    <x-feature-locked 
                        feature="athlete search & filters" 
                        requiredPlan="Pro" 
                    />
                </div>
            @endif

            <!-- Search & Filters (Pro/Growth only) -->
            @if($canSearch && $canFilter)
                <div class="card bg-base-100 shadow-sm mb-6">
                    <div class="card-body">
                        <form method="GET" action="{{ route('business.athletes.index') }}" class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <!-- Search -->
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text">Search by Name</span>
                                    </label>
                                    <input 
                                        type="text" 
                                        name="search" 
                                        value="{{ $filters['search'] }}"
                                        placeholder="Search athletes..."
                                        class="input input-bordered w-full"
                                    />
                                </div>

                                <!-- Sport Filter -->
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text">Sport</span>
                                    </label>
                                    <select name="sport" class="select select-bordered w-full">
                                        <option value="">All Sports</option>
                                        @foreach($sports as $sport)
                                            <option value="{{ $sport }}" {{ $filters['sport'] === $sport ? 'selected' : '' }}>
                                                {{ $sport }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- School Filter -->
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text">School</span>
                                    </label>
                                    <select name="school" class="select select-bordered w-full">
                                        <option value="">All Schools</option>
                                        @foreach($schools as $school)
                                            <option value="{{ $school }}" {{ $filters['school'] === $school ? 'selected' : '' }}>
                                                {{ $school }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                    Apply Filters
                                </button>
                                @if($filters['search'] || $filters['sport'] || $filters['school'])
                                    <a href="{{ route('business.athletes.index') }}" class="btn btn-ghost">
                                        Clear Filters
                                    </a>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
            @endif

            <!-- Results Header -->
            <div class="mb-4">
                <p class="text-sm text-gray-600">
                    @if($canSearch)
                        Showing {{ $athletes->count() }} of {{ $totalCount }} athletes
                    @else
                        Showing {{ $athletes->count() }} athletes (limited view)
                    @endif
                </p>
            </div>

            <!-- Athlete Cards Grid -->
            @if($athletes->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach($athletes as $athlete)
                        <div class="card bg-base-100 shadow-sm hover:shadow-md transition-shadow">
                            <figure class="px-6 pt-6">
                                @if($athlete->profile_photo)
                                    <img 
                                        src="{{ asset('storage/' . $athlete->profile_photo) }}" 
                                        alt="{{ $athlete->name }}"
                                        class="w-24 h-24 rounded-full object-cover"
                                    />
                                @else
                                    <div class="w-24 h-24 rounded-full bg-gray-200 flex items-center justify-center">
                                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                @endif
                            </figure>
                            
                            <div class="card-body items-center text-center pt-2">
                                <h3 class="card-title text-lg">{{ $athlete->name }}</h3>
                                
                                @if($athlete->sport)
                                    <p class="text-sm text-gray-600">
                                        <span class="font-medium">Sport:</span> {{ $athlete->sport }}
                                    </p>
                                @endif
                                
                                @if($athlete->school)
                                    <p class="text-sm text-gray-600">
                                        <span class="font-medium">School:</span> {{ $athlete->school }}
                                    </p>
                                @endif
                                
                                <div class="card-actions mt-4">
                                    <a 
                                        href="{{ route('business.athletes.show', $athlete) }}" 
                                        class="btn btn-primary btn-sm"
                                    >
                                        View Profile
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination (Pro/Growth only) -->
                @if($canSearch && method_exists($athletes, 'links'))
                    <div class="mt-6">
                        {{ $athletes->links() }}
                    </div>
                @endif
            @else
                <div class="card bg-base-100 shadow-sm">
                    <div class="card-body text-center py-12">
                        <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <h3 class="text-lg font-medium mb-2">No athletes found</h3>
                        <p class="text-sm text-gray-600">
                            @if($filters['search'] || $filters['sport'] || $filters['school'])
                                Try adjusting your search or filters.
                            @else
                                No athletes are currently available.
                            @endif
                        </p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

