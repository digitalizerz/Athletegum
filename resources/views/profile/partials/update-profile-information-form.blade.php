<section>
    <header>
        <h2 class="text-lg font-medium text-base-content">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-base-content/60">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div class="form-control">
            <label class="label" for="name">
                <span class="label-text">{{ __('Name') }}</span>
            </label>
            <input id="name" name="name" type="text" class="input input-bordered w-full" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div class="form-control">
            <label class="label" for="email">
                <span class="label-text">{{ __('Email') }}</span>
            </label>
            <input id="email" name="email" type="email" class="input input-bordered w-full" value="{{ old('email', $user->email) }}" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-2">
                    <p class="text-sm text-base-content/80">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="btn btn-link btn-sm p-0 h-auto min-h-0 text-primary">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <div role="alert" class="alert alert-success mt-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="text-sm">{{ __('A new verification link has been sent to your email address.') }}</span>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        @if(!$user->is_superadmin)
            {{-- Business Information Section --}}
            <div class="mt-8 pt-6 border-t border-base-300">
                <h3 class="text-lg font-medium text-base-content mb-4">
                    {{ __('Business Information') }}
                </h3>

                <div class="form-control">
                    <label class="label" for="business_name">
                        <span class="label-text">{{ __('Business Name') }}</span>
                    </label>
                    <input id="business_name" name="business_name" type="text" class="input input-bordered w-full" value="{{ old('business_name', $user->business_name) }}" autocomplete="organization" />
                    <x-input-error class="mt-2" :messages="$errors->get('business_name')" />
                </div>

                <div class="form-control mt-4">
                    <label class="label" for="business_information">
                        <span class="label-text">{{ __('Business Information') }}</span>
                    </label>
                    <textarea id="business_information" name="business_information" rows="4" class="textarea textarea-bordered w-full">{{ old('business_information', $user->business_information) }}</textarea>
                    <label class="label">
                        <span class="label-text-alt text-base-content/60">{{ __('A brief description of your business.') }}</span>
                    </label>
                    <x-input-error class="mt-2" :messages="$errors->get('business_information')" />
                </div>

                <div class="form-control mt-4">
                    <label class="label" for="phone">
                        <span class="label-text">{{ __('Phone') }}</span>
                    </label>
                    <input id="phone" name="phone" type="tel" class="input input-bordered w-full" value="{{ old('phone', $user->phone) }}" autocomplete="tel" />
                    <x-input-error class="mt-2" :messages="$errors->get('phone')" />
                </div>

                <div class="form-control mt-4">
                    <label class="label" for="owner_principal">
                        <span class="label-text">{{ __('Owner / Principal Name') }}</span>
                    </label>
                    <input id="owner_principal" name="owner_principal" type="text" class="input input-bordered w-full" value="{{ old('owner_principal', $user->owner_principal) }}" autocomplete="name" />
                    <x-input-error class="mt-2" :messages="$errors->get('owner_principal')" />
                </div>

                {{-- Address Section --}}
                <div class="mt-6 pt-6 border-t border-base-300">
                    <h4 class="text-base font-medium text-base-content mb-4">
                        {{ __('Business Address') }}
                    </h4>

                    <div class="form-control">
                        <label class="label" for="address_line1">
                            <span class="label-text">{{ __('Address Line 1') }}</span>
                        </label>
                        <input id="address_line1" name="address_line1" type="text" class="input input-bordered w-full" value="{{ old('address_line1', $user->address_line1) }}" autocomplete="street-address" />
                        <x-input-error class="mt-2" :messages="$errors->get('address_line1')" />
                    </div>

                    <div class="form-control mt-4">
                        <label class="label" for="address_line2">
                            <span class="label-text">{{ __('Address Line 2') }}</span>
                        </label>
                        <input id="address_line2" name="address_line2" type="text" class="input input-bordered w-full" value="{{ old('address_line2', $user->address_line2) }}" autocomplete="address-line2" />
                        <x-input-error class="mt-2" :messages="$errors->get('address_line2')" />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                        <div class="form-control">
                            <label class="label" for="city">
                                <span class="label-text">{{ __('City') }}</span>
                            </label>
                            <input id="city" name="city" type="text" class="input input-bordered w-full" value="{{ old('city', $user->city) }}" autocomplete="address-level2" />
                            <x-input-error class="mt-2" :messages="$errors->get('city')" />
                        </div>

                        <div class="form-control">
                            <label class="label" for="state">
                                <span class="label-text">{{ __('State / Province') }}</span>
                            </label>
                            <input id="state" name="state" type="text" class="input input-bordered w-full" value="{{ old('state', $user->state) }}" autocomplete="address-level1" />
                            <x-input-error class="mt-2" :messages="$errors->get('state')" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                        <div class="form-control">
                            <label class="label" for="postal_code">
                                <span class="label-text">{{ __('Postal Code') }}</span>
                            </label>
                            <input id="postal_code" name="postal_code" type="text" class="input input-bordered w-full" value="{{ old('postal_code', $user->postal_code) }}" autocomplete="postal-code" />
                            <x-input-error class="mt-2" :messages="$errors->get('postal_code')" />
                        </div>

                        <div class="form-control">
                            <label class="label" for="country">
                                <span class="label-text">{{ __('Country') }}</span>
                            </label>
                            <input id="country" name="country" type="text" class="input input-bordered w-full" value="{{ old('country', $user->country) }}" autocomplete="country-name" />
                            <x-input-error class="mt-2" :messages="$errors->get('country')" />
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="flex items-center gap-4 pt-4 border-t border-base-300">
            <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-base-content/60"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
