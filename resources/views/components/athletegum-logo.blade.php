@props(['size' => 'md', 'showText' => true, 'textColor' => 'default'])

@php
    $sizes = [
        'sm' => ['circle' => 'w-6 h-6', 'text' => 'text-sm', 'letter' => 'text-xs'],
        'md' => ['circle' => 'w-8 h-8', 'text' => 'text-lg', 'letter' => 'text-sm'],
        'lg' => ['circle' => 'w-12 h-12', 'text' => 'text-2xl', 'letter' => 'text-lg'],
    ];
    $sizeClasses = $sizes[$size] ?? $sizes['md'];
    
    $textColors = [
        'default' => 'text-gray-900',
        'white' => 'text-white',
        'dark' => 'text-gray-900',
    ];
    $textColorClass = $textColors[$textColor] ?? $textColors['default'];
@endphp

<div class="flex items-center space-x-2">
    <div class="{{ $sizeClasses['circle'] }} rounded-full bg-gray-900 flex items-center justify-center flex-shrink-0">
        <span class="{{ $sizeClasses['letter'] }} font-bold text-white">A</span>
    </div>
    @if($showText)
        <span class="{{ $sizeClasses['text'] }} font-semibold {{ $textColorClass }}" style="color: {{ $textColor === 'white' ? '#ffffff' : ($textColor === 'default' ? '#111827' : '#111827') }};">AthleteGum</span>
    @endif
</div>

