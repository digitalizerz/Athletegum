@props(['currentFilter' => 'all', 'routeName'])

<div class="flex gap-2 items-center">
    <span class="text-sm text-base-content/60">Filter:</span>
    <div class="btn-group">
        <a href="{{ route($routeName, ['filter' => '7d']) }}" 
           class="btn btn-sm {{ $currentFilter === '7d' ? 'btn-active' : 'btn-ghost' }}">
            7 days
        </a>
        <a href="{{ route($routeName, ['filter' => '30d']) }}" 
           class="btn btn-sm {{ $currentFilter === '30d' ? 'btn-active' : 'btn-ghost' }}">
            30 days
        </a>
        <a href="{{ route($routeName, ['filter' => '90d']) }}" 
           class="btn btn-sm {{ $currentFilter === '90d' ? 'btn-active' : 'btn-ghost' }}">
            90 days
        </a>
        <a href="{{ route($routeName, ['filter' => 'all']) }}" 
           class="btn btn-sm {{ $currentFilter === 'all' ? 'btn-active' : 'btn-ghost' }}">
            All time
        </a>
    </div>
</div>

