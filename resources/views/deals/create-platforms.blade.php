<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl leading-tight">Create a Deal</h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto">
            <div class="card bg-base-100 shadow-sm">
                <div class="card-body">
                    <div class="mb-4">
                        <div class="text-xs text-base-content/60 mb-1">Deal Type</div>
                        <div class="text-sm font-semibold">{{ $dealTypeName }}</div>
                    </div>

                    <h3 class="text-lg font-semibold mb-2">Which platforms?</h3>
                    <p class="text-sm text-base-content/60 mb-5">Select one or more platforms where this will be posted.</p>

                    <form method="POST" action="{{ route('deals.create.platforms.store') }}" x-data="{ selected: {{ json_encode(old('platforms', [])) }} }">
                        @csrf

                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 mb-6">
                            @foreach($platforms as $key => $platform)
                                <label class="flex items-center justify-center p-3 border-2 rounded-lg cursor-pointer hover:bg-base-200 transition-colors" 
                                       :class="selected.includes('{{ $key }}') ? 'border-primary bg-primary/10' : 'border-base-300'">
                                    <input 
                                        type="checkbox" 
                                        name="platforms[]" 
                                        value="{{ $key }}"
                                        x-model="selected"
                                        class="sr-only"
                                    >
                                    <span class="text-sm font-medium">{{ $platform }}</span>
                                    <svg x-show="selected.includes('{{ $key }}')" class="ml-2 h-4 w-4 text-primary flex-shrink-0" fill="currentColor" viewBox="0 0 20 20" style="display: none;">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                </label>
                            @endforeach
                        </div>

                        <x-input-error :messages="$errors->get('platforms')" class="mb-4" />

                        <div class="flex justify-between items-center pt-4 border-t border-base-300">
                            <a href="{{ route('deals.create') }}" class="btn btn-ghost btn-sm">
                                ← Back
                            </a>
                            <button 
                                type="submit"
                                x-bind:disabled="selected.length === 0"
                                class="btn btn-primary"
                                :class="selected.length === 0 ? 'btn-disabled' : ''"
                            >
                                Continue
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
