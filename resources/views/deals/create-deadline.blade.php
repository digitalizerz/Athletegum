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
                    <h3 class="text-lg font-semibold mb-5">When do you need this done?</h3>

                    <form method="POST" action="{{ route('deals.create.deadline.store') }}" x-data="{ frequency: '{{ old('frequency', 'one-time') }}', showRecurring: false }">
                        @csrf

                        {{-- Athlete Email --}}
                        <div class="mb-6">
                            <label for="athlete_email" class="label">
                                <span class="label-text">Athlete Email <span class="text-error">*</span></span>
                            </label>
                            <x-text-input
                                id="athlete_email"
                                type="email"
                                name="athlete_email"
                                value="{{ old('athlete_email', $preselectedAthleteEmail ?? '') }}"
                                class="input input-bordered w-full {{ isset($preselectedAthleteEmail) ? 'bg-gray-50' : '' }}"
                                placeholder="athlete@example.com"
                                {{ isset($preselectedAthleteEmail) ? 'readonly' : '' }}
                                {{ !isset($preselectedAthleteEmail) ? 'required' : '' }}
                            />
                            @if(isset($preselectedAthleteEmail))
                                <p class="mt-1.5 text-xs text-blue-600">
                                    Athlete preselected from profile.
                                </p>
                            @else
                                <p class="mt-1.5 text-xs text-base-content/60">
                                    This deal invitation will be sent specifically to this athlete. Only they can accept it.
                                </p>
                            @endif
                            <x-input-error :messages="$errors->get('athlete_email')" class="mt-1" />
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                            <!-- Date -->
                            <div class="sm:col-span-1">
                                <label for="deadline" class="label">
                                    <span class="label-text">Date</span>
                                </label>
                                <x-text-input
                                    id="deadline"
                                    type="date"
                                    name="deadline"
                                    value="{{ old('deadline') }}"
                                    class="input input-bordered w-full"
                                    required
                                    autofocus
                                    min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                />
                                <x-input-error :messages="$errors->get('deadline')" class="mt-1" />
                            </div>

                            <!-- Time (Optional) -->
                            <div class="sm:col-span-1">
                                <label for="deadline_time" class="label">
                                    <span class="label-text">Time <span class="text-base-content/60 font-normal">(Optional)</span></span>
                                </label>
                                <x-text-input
                                    id="deadline_time"
                                    type="time"
                                    name="deadline_time"
                                    value="{{ old('deadline_time') }}"
                                    class="input input-bordered w-full"
                                />
                                <x-input-error :messages="$errors->get('deadline_time')" class="mt-1" />
                            </div>

                            <!-- Frequency -->
                            <div class="sm:col-span-1">
                                <label for="frequency" class="label">
                                    <span class="label-text">Frequency</span>
                                </label>
                                <select 
                                    id="frequency" 
                                    name="frequency" 
                                    x-model="frequency"
                                    @change="showRecurring = frequency !== 'one-time'"
                                    class="select select-bordered w-full"
                                >
                                    @foreach(\App\Models\Deal::getFrequencyOptions() as $key => $label)
                                        <option value="{{ $key }}" {{ old('frequency', 'one-time') === $key ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <p class="mt-1.5 text-xs text-base-content/60">
                                    <span x-show="frequency === 'one-time'">One-time deal</span>
                                    <span x-show="frequency !== 'one-time'" x-text="'Repeats ' + frequency"></span>
                                </p>
                                <x-input-error :messages="$errors->get('frequency')" class="mt-1" />
                            </div>
                        </div>

                        <div class="flex justify-between items-center pt-4 border-t border-base-300">
                            <a href="{{ route('deals.create.compensation') }}" class="btn btn-ghost btn-sm">
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
