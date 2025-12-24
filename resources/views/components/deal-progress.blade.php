@php
    try {
        $routeName = request()->route()->getName();
    } catch (\Exception $e) {
        $routeName = '';
    }
    
    // Determine current step and total steps based on route
    $dealTypes = \App\Models\Deal::getDealTypes();
    $dealType = session('deal_type');
    $hasPlatforms = $dealType ? (($dealTypes[$dealType]['requires_platforms'] ?? false)) : true;
    $totalSteps = $hasPlatforms ? 8 : 7; // Updated to include payment step
    
    // Map routes to step numbers
    $stepMap = [
        'deals.create' => 1,
        'deals.create.platforms' => 2,
        'deals.create.compensation' => $hasPlatforms ? 3 : 2,
        'deals.create.deadline' => $hasPlatforms ? 4 : 3,
        'deals.create.notes' => $hasPlatforms ? 5 : 4,
        'deals.create.contract' => $hasPlatforms ? 6 : 5,
        'deals.create.payment' => $hasPlatforms ? 7 : 6,
        'deals.review' => $hasPlatforms ? 8 : 7,
    ];
    
    // Update total steps to include payment
    $totalSteps = $hasPlatforms ? 8 : 7;
    
    $currentStep = $stepMap[$routeName] ?? 1;
    $percentage = ($currentStep / $totalSteps) * 100;
    $steps = range(1, $totalSteps);
@endphp

@if(str_starts_with($routeName, 'deals.create') || $routeName === 'deals.review')
<div class="w-full bg-white border-b border-gray-200 sticky top-0 z-40 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <!-- Progress Bar -->
        <div class="w-full bg-gray-200 rounded-full h-1.5 mb-4">
            <div 
                class="bg-gray-900 rounded-full h-1.5 transition-all duration-300 ease-out"
                style="width: {{ $percentage }}%"
            ></div>
        </div>
        
        <!-- Step Indicators -->
        <div class="flex justify-between items-center relative pb-1">
            @foreach($steps as $step)
                <div class="flex flex-col items-center flex-1 relative z-10">
                    <!-- Step Circle -->
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-semibold transition-all duration-300
                        {{ $step <= $currentStep 
                            ? 'bg-gray-900 text-white' 
                            : 'bg-gray-200 text-gray-500' }}">
                        @if($step < $currentStep)
                            <!-- Checkmark for completed steps -->
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                        @else
                            {{ $step }}
                        @endif
                    </div>
                </div>
                
                <!-- Connecting Line (between steps) -->
                @if($step < $totalSteps)
                    <div class="absolute top-1/2 h-0.5 -z-10 transition-colors duration-300
                        {{ $step < $currentStep ? 'bg-gray-900' : 'bg-gray-200' }}"
                        style="left: calc({{ ($step * 100) / $totalSteps }}% + 1rem); width: calc({{ (100 / $totalSteps) }}% - 2rem);">
                    </div>
                @endif
            @endforeach
        </div>
    </div>
</div>
@endif

