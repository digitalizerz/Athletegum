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
                    <h3 class="text-lg font-semibold mb-2">Contract Agreement</h3>
                    <p class="text-sm text-base-content/60 mb-5">Please read and agree to the contract terms before proceeding.</p>

                    <form method="POST" action="{{ route('deals.create.contract.store') }}">
                        @csrf

                        <!-- Contract Text -->
                        <div class="mb-4">
                            <div class="bg-base-200 border border-base-300 rounded-lg p-3 max-h-64 overflow-y-auto">
                                <pre class="whitespace-pre-wrap text-xs font-sans">{{ $contractText }}</pre>
                            </div>
                            <input type="hidden" name="contract_text" value="{{ $contractText }}">
                        </div>

                        <!-- Agreement Checkbox -->
                        <div class="mb-6">
                            <label class="label cursor-pointer justify-start gap-3">
                                <input 
                                    type="checkbox" 
                                    name="contract_signed" 
                                    value="1"
                                    class="checkbox checkbox-primary"
                                    required
                                >
                                <span class="label-text text-xs">
                                    I have read and agree to the contract terms and conditions above.
                                    <span class="text-error">*</span>
                                </span>
                            </label>
                            <x-input-error :messages="$errors->get('contract_signed')" class="mt-1" />
                        </div>

                        <div class="flex justify-between items-center pt-4 border-t border-base-300">
                            <a href="{{ route('deals.create.notes') }}" class="btn btn-ghost btn-sm">
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
