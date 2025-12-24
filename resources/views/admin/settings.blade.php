<x-superadmin-dashboard-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">Platform Settings</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto">
            <div class="card bg-base-100 shadow-sm">
                <div class="card-body">
                    <h3 class="text-lg font-semibold mb-5">Platform Fee</h3>

                    @if(session('success'))
                        <div role="alert" class="alert alert-success mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>{{ session('success') }}</span>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.settings.update') }}">
                        @csrf

                        <div class="mb-6">
                            <label for="platform_fee_percentage" class="label">
                                <span class="label-text">Platform Fee Percentage</span>
                            </label>
                            <div class="relative">
                                <x-text-input
                                    id="platform_fee_percentage"
                                    type="number"
                                    name="platform_fee_percentage"
                                    value="{{ old('platform_fee_percentage', $platformFee) }}"
                                    step="0.01"
                                    min="0"
                                    max="100"
                                    class="input input-bordered w-full pr-8"
                                    required
                                />
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <span class="text-base-content/60 text-sm">%</span>
                                </div>
                            </div>
                            <label class="label">
                                <span class="label-text-alt text-base-content/60">
                                    This percentage will be charged on top of the compensation amount for each deal.
                                </span>
                            </label>
                            <x-input-error :messages="$errors->get('platform_fee_percentage')" class="mt-1" />
                        </div>

                        <div class="flex justify-end pt-4 border-t border-base-300">
                            <button type="submit" class="btn btn-primary">
                                Save Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-superadmin-dashboard-layout>
