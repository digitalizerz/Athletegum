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
                    <h3 class="text-lg font-semibold mb-5">How much are you paying?</h3>

                    <form method="POST" action="{{ route('deals.create.compensation.store') }}">
                        @csrf

                        <div class="mb-6">
                            <div class="relative max-w-xs">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-base-content/60 text-lg font-medium">$</span>
                                </div>
                                <x-text-input
                                    id="compensation_amount"
                                    type="number"
                                    name="compensation_amount"
                                    step="0.01"
                                    min="0.01"
                                    class="block w-full pl-8 text-2xl font-semibold py-3 input input-bordered"
                                    placeholder="0.00"
                                    required
                                    autofocus
                                    inputmode="decimal"
                                />
                            </div>
                            <x-input-error :messages="$errors->get('compensation_amount')" class="mt-1" />
                        </div>

                        <div class="flex justify-between items-center pt-4 border-t border-base-300">
                            @php
                                $dealTypes = \App\Models\Deal::getDealTypes();
                                $dealType = session('deal_type');
                                $backRoute = ($dealTypes[$dealType]['requires_platforms'] ?? false) ? route('deals.create.platforms') : route('deals.create');
                            @endphp
                            <a href="{{ $backRoute }}" class="btn btn-ghost btn-sm">
                                ← Back
                            </a>
                            <button type="submit" class="btn btn-primary">
                                Continue
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
