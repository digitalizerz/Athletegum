@props([
    'currentStep' => 1,
    'totalSteps' => 5,
])

@php
    $percentage = ($currentStep / $totalSteps) * 100;
    $steps = range(1, $totalSteps);
@endphp

<div class="w-full mb-6 sm:mb-8">
    <!-- Progress Bar -->
    <div class="w-full bg-gray-200 rounded-full h-2 sm:h-3 mb-4">
        <div 
            class="bg-gray-900 rounded-full h-2 sm:h-3 transition-all duration-300 ease-out"
            style="width: {{ $percentage }}%"
        ></div>
    </div>
    
    <!-- Step Indicators -->
    <div class="flex justify-between items-center relative">
        @foreach($steps as $step)
            <div class="flex flex-col items-center flex-1 relative z-10">
                <!-- Step Circle -->
                <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-full flex items-center justify-center text-sm sm:text-base font-semibold transition-all duration-300
                    {{ $step <= $currentStep 
                        ? 'bg-gray-900 text-white' 
                        : 'bg-gray-200 text-gray-500' }}">
                    @if($step < $currentStep)
                        <!-- Checkmark for completed steps -->
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                    @else
                        {{ $step }}
                    @endif
                </div>
            </div>
            
            <!-- Connecting Line (between steps) -->
            @if($step < $totalSteps)
                <div class="absolute top-1/2 left-0 right-0 h-0.5 sm:h-1 -z-10"
                     style="left: calc({{ ($step * 100) / $totalSteps }}% + 1.25rem); right: calc({{ (($totalSteps - $step - 1) * 100) / $totalSteps }}% + 1.25rem);">
                    <div class="h-full {{ $step < $currentStep ? 'bg-gray-900' : 'bg-gray-200' }} transition-colors duration-300"></div>
                </div>
            @endif
        @endforeach
    </div>
</div>
