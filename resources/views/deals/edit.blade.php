<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-semibold">Edit Deal</h2>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto">
        <div class="card bg-base-100 shadow-sm">
            <form method="POST" action="{{ route('deals.update', $deal) }}">
                @csrf
                @method('PATCH')

                <div class="card-body space-y-6">
                    <!-- Deal Type (Read-only) -->
                    <div class="pb-4 border-b border-base-300">
                        <label class="label">
                            <span class="label-text text-xs font-medium uppercase">Deal Type</span>
                        </label>
                        <div class="flex items-center space-x-3">
                            <span class="text-3xl">{{ $dealTypes[$deal->deal_type]['icon'] ?? '📋' }}</span>
                            <div>
                                <div class="text-xl font-semibold">{{ $dealTypes[$deal->deal_type]['name'] ?? $deal->deal_type }}</div>
                                <p class="mt-1 text-sm text-base-content/60">Deal type cannot be changed after creation</p>
                            </div>
                        </div>
                    </div>

                    <!-- Platforms (if required) -->
                    @if(($dealTypes[$deal->deal_type]['requires_platforms'] ?? false))
                        <div>
                            <label class="label">
                                <span class="label-text">Platforms</span>
                            </label>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                                @foreach($platforms as $key => $platform)
                                    <label class="relative flex items-center p-3 border-2 rounded-lg cursor-pointer hover:bg-base-200 transition-colors" 
                                           :class="in_array('{{ $key }}', {{ json_encode($deal->platforms ?? []) }}) ? 'border-primary bg-primary/10' : 'border-base-300'">
                                        <input 
                                            type="checkbox" 
                                            name="platforms[]" 
                                            value="{{ $key }}"
                                            {{ in_array($key, $deal->platforms ?? []) ? 'checked' : '' }}
                                            class="sr-only"
                                        >
                                        <span class="text-sm font-medium">{{ $platform }}</span>
                                        @if(in_array($key, $deal->platforms ?? []))
                                            <svg class="ml-auto h-5 w-5 text-primary" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                        @endif
                                    </label>
                                @endforeach
                            </div>
                            <x-input-error :messages="$errors->get('platforms')" class="mt-2" />
                        </div>
                    @endif

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <!-- Compensation Amount -->
                        <div>
                            <label for="compensation_amount" class="label">
                                <span class="label-text">Compensation Amount</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <span class="text-base-content/60 text-lg font-medium">$</span>
                                </div>
                                <input 
                                    type="number" 
                                    id="compensation_amount" 
                                    name="compensation_amount" 
                                    value="{{ old('compensation_amount', $deal->compensation_amount) }}"
                                    step="0.01" 
                                    min="0.01"
                                    required
                                    class="input input-bordered w-full pl-8 text-lg font-semibold"
                                    placeholder="0.00"
                                >
                            </div>
                            <x-input-error :messages="$errors->get('compensation_amount')" class="mt-2" />
                        </div>

                        <!-- Deadline -->
                        <div>
                            <label for="deadline" class="label">
                                <span class="label-text">Deadline</span>
                            </label>
                            <input 
                                type="date" 
                                id="deadline" 
                                name="deadline" 
                                value="{{ old('deadline', $deal->deadline->format('Y-m-d')) }}"
                                min="{{ now()->addDay()->format('Y-m-d') }}"
                                required
                                class="input input-bordered w-full"
                            >
                            <x-input-error :messages="$errors->get('deadline')" class="mt-2" />
                        </div>
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status" class="label">
                            <span class="label-text">Status</span>
                        </label>
                        <select 
                            id="status" 
                            name="status" 
                            required
                            class="select select-bordered w-full"
                        >
                            <option value="pending" {{ old('status', $deal->status) === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="accepted" {{ old('status', $deal->status) === 'accepted' ? 'selected' : '' }}>Accepted</option>
                            <option value="completed" {{ old('status', $deal->status) === 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ old('status', $deal->status) === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                        <x-input-error :messages="$errors->get('status')" class="mt-2" />
                    </div>

                    <!-- Notes -->
                    <div>
                        <label for="notes" class="label">
                            <span class="label-text">Notes <span class="text-base-content/60 font-normal">(Optional)</span></span>
                        </label>
                        <textarea 
                            id="notes" 
                            name="notes" 
                            rows="4"
                            class="textarea textarea-bordered w-full resize-none"
                            placeholder="Add any additional notes or instructions for this deal..."
                        >{{ old('notes', $deal->notes) }}</textarea>
                        <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="card-body border-t border-base-300 flex items-center justify-between">
                    <a href="{{ route('deals.index') }}" class="btn btn-ghost">
                        Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
