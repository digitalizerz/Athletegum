<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl leading-tight">Create a Deal</h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto">
            <div class="card bg-base-100 shadow-sm">
                <div class="card-body">
                    <h3 class="text-lg font-semibold mb-5">What do you need?</h3>

                    <form method="POST" action="{{ route('deals.create.type') }}" x-data="{ selected: null }">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-6">
                            @foreach($dealTypes as $key => $type)
                                <label class="relative block cursor-pointer">
                                    <input 
                                        type="radio" 
                                        name="deal_type" 
                                        value="{{ $key }}"
                                        class="peer sr-only"
                                        x-model="selected"
                                        required
                                    >
                                    <div class="border-2 border-base-300 rounded-lg p-4 transition-all active:scale-[0.98] hover:border-primary hover:shadow-md peer-checked:border-primary peer-checked:bg-primary/10 peer-checked:shadow-lg min-h-[80px] flex items-center" 
                                         :class="selected === '{{ $key }}' ? 'border-primary bg-primary/10 shadow-lg' : ''">
                                        <div class="flex items-center space-x-3 w-full">
                                            <div class="text-2xl sm:text-3xl flex-shrink-0">{{ $type['icon'] }}</div>
                                            <div class="flex-1 min-w-0">
                                                <div class="font-semibold text-sm sm:text-base">
                                                    {{ $type['name'] }}
                                                </div>
                                                <div class="text-xs sm:text-sm text-base-content/60">
                                                    {{ $type['description'] }}
                                                </div>
                                            </div>
                                            <svg class="w-4 h-4 text-primary opacity-0 peer-checked:opacity-100 transition-opacity flex-shrink-0" 
                                                 :class="selected === '{{ $key }}' ? 'opacity-100' : 'opacity-0'"
                                                 fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                    </div>
                                </label>
                            @endforeach
                        </div>

                        <div class="flex justify-end pt-4 border-t border-base-300">
                            <button 
                                type="submit" 
                                x-bind:disabled="!selected"
                                class="btn btn-primary"
                                :class="!selected ? 'btn-disabled' : ''"
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
