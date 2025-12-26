@if(!$user->is_superadmin)
    <div x-data="{ editing: {{ ($errors->any() || session('status') === 'business-updated') ? 'true' : 'false' }} }">
        {{-- Card Container --}}
        <div class="card bg-base-100 border border-base-300">
            <div class="card-body">
                {{-- Header Row --}}
                <div class="flex items-center justify-between mb-6 pb-4 border-b border-base-300">
                    <div>
                        <h3 class="text-lg font-semibold text-base-content">
                            {{ __('Business Information') }}
                        </h3>
                        <p class="mt-2 text-sm text-base-content/50">
                            {{ __('Update your business details and contact information.') }}
                        </p>
                    </div>
                    <button 
                        x-show="!editing" 
                        @click="editing = true" 
                        type="button"
                        class="btn btn-sm btn-outline"
                        style="display: none;"
                    >
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Edit Profile
                    </button>
                </div>

                {{-- Read-only View --}}
                <div x-show="!editing" x-cloak>
                    <div class="space-y-6">
                        <div>
                            <div class="text-xs font-medium text-base-content/50 mb-2">Business Name</div>
                            <div class="text-base font-semibold text-base-content">{{ $user->business_name ?: '—' }}</div>
                        </div>
                        
                        <div>
                            <div class="text-xs font-medium text-base-content/50 mb-2">Business Information</div>
                            <div class="text-base font-semibold text-base-content whitespace-pre-wrap">{{ $user->business_information ?: '—' }}</div>
                        </div>
                        
                        <div>
                            <div class="text-xs font-medium text-base-content/50 mb-2">Business Phone</div>
                            <div class="text-base font-semibold text-base-content">{{ $user->phone ?: '—' }}</div>
                        </div>
                        
                        <div>
                            <div class="text-xs font-medium text-base-content/50 mb-2">Owner / Principal Name</div>
                            <div class="text-base font-semibold text-base-content">{{ $user->owner_principal ?: '—' }}</div>
                        </div>
                    </div>
                </div>

                {{-- Edit Form --}}
                <form x-show="editing" method="post" action="{{ Auth::user()->is_superadmin ? route('admin.profile.update') : route('profile.update') }}" class="space-y-6">
                    @csrf
                    @method('patch')

                    <div class="form-control">
                        <label class="label" for="business_name">
                            <span class="label-text">{{ __('Business Name') }}</span>
                        </label>
                        <input id="business_name" name="business_name" type="text" class="input input-bordered w-full" value="{{ old('business_name', $user->business_name) }}" autocomplete="organization" />
                        <x-input-error class="mt-2" :messages="$errors->get('business_name')" />
                    </div>

                    <div class="form-control">
                        <label class="label" for="business_information">
                            <span class="label-text">{{ __('Business Information') }}</span>
                        </label>
                        <textarea id="business_information" name="business_information" rows="4" class="textarea textarea-bordered w-full">{{ old('business_information', $user->business_information) }}</textarea>
                        <label class="label">
                            <span class="label-text-alt text-base-content/60">{{ __('A brief description of your business.') }}</span>
                        </label>
                        <x-input-error class="mt-2" :messages="$errors->get('business_information')" />
                    </div>

                    <div class="form-control">
                        <label class="label" for="phone">
                            <span class="label-text">{{ __('Business Phone') }}</span>
                        </label>
                        <input id="phone" name="phone" type="tel" class="input input-bordered w-full" value="{{ old('phone', $user->phone) }}" autocomplete="tel" />
                        <x-input-error class="mt-2" :messages="$errors->get('phone')" />
                    </div>

                    <div class="form-control">
                        <label class="label" for="owner_principal">
                            <span class="label-text">{{ __('Owner / Principal Name') }}</span>
                        </label>
                        <input id="owner_principal" name="owner_principal" type="text" class="input input-bordered w-full" value="{{ old('owner_principal', $user->owner_principal) }}" autocomplete="name" />
                        <x-input-error class="mt-2" :messages="$errors->get('owner_principal')" />
                    </div>

                    <div class="flex items-center gap-4 pt-4 border-t border-base-300">
                        <button type="submit" class="btn btn-primary">{{ __('Save changes') }}</button>
                        <button type="button" @click="editing = false" class="btn btn-ghost">Cancel</button>
                        @if (session('status') === 'business-updated')
                            <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => { show = false; editing = false; }, 2000)" class="text-sm text-success">{{ __('Saved.') }}</p>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
@else
    <div class="card bg-base-100 border border-base-300">
        <div class="card-body">
            <p class="text-base-content/60">Business information is not available for admin accounts.</p>
        </div>
    </div>
@endif
