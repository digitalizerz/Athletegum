@props([
    'title' => 'Unlock This Feature',
    'description' => 'Upgrade to Pro to access this feature.',
    'actionText' => 'Upgrade to Pro',
    'modalId' => 'upgrade-modal',
])

<dialog id="{{ $modalId }}" class="modal">
    <div class="modal-box">
        <div class="flex items-center gap-3 mb-4">
            <svg class="w-6 h-6 text-primary flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
            </svg>
            <h3 class="font-bold text-lg">{{ $title }}</h3>
        </div>
        <p class="mb-6 text-base-content/70">
            {{ $description }}
        </p>
        <div class="modal-action">
            <a href="{{ route('business.billing.index') }}" class="btn btn-primary">
                {{ $actionText }}
            </a>
            <button type="button" class="btn btn-ghost" onclick="document.getElementById('{{ $modalId }}').close()">
                Maybe later
            </button>
        </div>
    </div>
    <form method="dialog" class="modal-backdrop">
        <button>close</button>
    </form>
</dialog>

