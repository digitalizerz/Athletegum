@if(!$user->is_superadmin)
    <div x-data="{ editing: {{ ($errors->any() || session('status') === 'address-updated') ? 'true' : 'false' }} }">
        <header class="mb-6 flex items-center justify-between">
            <div>
                <h3 class="text-lg font-medium text-base-content">
                    {{ __('Business Address') }}
                </h3>
                <p class="mt-1 text-sm text-base-content/60">
                    {{ __('Update your business address information.') }}
                </p>
            </div>
            <button 
                x-show="!editing" 
                @click="editing = true" 
                type="button"
                class="btn btn-sm btn-outline"
                style="display: none;"
            >
                Edit
            </button>
        </header>

        {{-- Read-only View --}}
        <div x-show="!editing" style="display: none;">
            <div class="space-y-4">
                @if($user->address_line1 || $user->city || $user->state)
                    <div>
                        <div class="text-sm text-base-content/60 mb-1">Address</div>
                        <div class="text-base">
                            @if($user->address_line1){{ $user->address_line1 }}@endif
                            @if($user->address_line2)<br>{{ $user->address_line2 }}@endif
                            @if($user->city || $user->state || $user->postal_code)
                                <br>@if($user->city){{ $user->city }}@endif
                                @if($user->city && $user->state), @endif
                                @if($user->state){{ $user->state }}@endif
                                @if($user->postal_code) {{ $user->postal_code }}@endif
                            @endif
                            @if($user->country)<br>{{ $user->country }}@endif
                        </div>
                    </div>
                @else
                    <div class="text-base-content/60">No address information provided.</div>
                @endif
            </div>
        </div>

        {{-- Edit Form --}}
        <form x-show="editing" method="post" action="{{ route('profile.update') }}" class="space-y-6">
            @csrf
            @method('patch')

            <div class="form-control">
                <label class="label" for="address_line1">
                    <span class="label-text">{{ __('Address Line 1') }}</span>
                </label>
                <input id="address_line1" name="address_line1" type="text" class="input input-bordered w-full" value="{{ old('address_line1', $user->address_line1) }}" autocomplete="street-address" />
                <x-input-error class="mt-2" :messages="$errors->get('address_line1')" />
            </div>

            <div class="form-control">
                <label class="label" for="address_line2">
                    <span class="label-text">{{ __('Address Line 2') }}</span>
                </label>
                <input id="address_line2" name="address_line2" type="text" class="input input-bordered w-full" value="{{ old('address_line2', $user->address_line2) }}" autocomplete="address-line2" />
                <x-input-error class="mt-2" :messages="$errors->get('address_line2')" />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
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
                    <select id="state" name="state" class="select select-bordered w-full" autocomplete="address-level1">
                        <option value="">Select a state</option>
                        <option value="AL" {{ old('state', $user->state) === 'AL' ? 'selected' : '' }}>Alabama</option>
                        <option value="AK" {{ old('state', $user->state) === 'AK' ? 'selected' : '' }}>Alaska</option>
                        <option value="AZ" {{ old('state', $user->state) === 'AZ' ? 'selected' : '' }}>Arizona</option>
                        <option value="AR" {{ old('state', $user->state) === 'AR' ? 'selected' : '' }}>Arkansas</option>
                        <option value="CA" {{ old('state', $user->state) === 'CA' ? 'selected' : '' }}>California</option>
                        <option value="CO" {{ old('state', $user->state) === 'CO' ? 'selected' : '' }}>Colorado</option>
                        <option value="CT" {{ old('state', $user->state) === 'CT' ? 'selected' : '' }}>Connecticut</option>
                        <option value="DE" {{ old('state', $user->state) === 'DE' ? 'selected' : '' }}>Delaware</option>
                        <option value="FL" {{ old('state', $user->state) === 'FL' ? 'selected' : '' }}>Florida</option>
                        <option value="GA" {{ old('state', $user->state) === 'GA' ? 'selected' : '' }}>Georgia</option>
                        <option value="HI" {{ old('state', $user->state) === 'HI' ? 'selected' : '' }}>Hawaii</option>
                        <option value="ID" {{ old('state', $user->state) === 'ID' ? 'selected' : '' }}>Idaho</option>
                        <option value="IL" {{ old('state', $user->state) === 'IL' ? 'selected' : '' }}>Illinois</option>
                        <option value="IN" {{ old('state', $user->state) === 'IN' ? 'selected' : '' }}>Indiana</option>
                        <option value="IA" {{ old('state', $user->state) === 'IA' ? 'selected' : '' }}>Iowa</option>
                        <option value="KS" {{ old('state', $user->state) === 'KS' ? 'selected' : '' }}>Kansas</option>
                        <option value="KY" {{ old('state', $user->state) === 'KY' ? 'selected' : '' }}>Kentucky</option>
                        <option value="LA" {{ old('state', $user->state) === 'LA' ? 'selected' : '' }}>Louisiana</option>
                        <option value="ME" {{ old('state', $user->state) === 'ME' ? 'selected' : '' }}>Maine</option>
                        <option value="MD" {{ old('state', $user->state) === 'MD' ? 'selected' : '' }}>Maryland</option>
                        <option value="MA" {{ old('state', $user->state) === 'MA' ? 'selected' : '' }}>Massachusetts</option>
                        <option value="MI" {{ old('state', $user->state) === 'MI' ? 'selected' : '' }}>Michigan</option>
                        <option value="MN" {{ old('state', $user->state) === 'MN' ? 'selected' : '' }}>Minnesota</option>
                        <option value="MS" {{ old('state', $user->state) === 'MS' ? 'selected' : '' }}>Mississippi</option>
                        <option value="MO" {{ old('state', $user->state) === 'MO' ? 'selected' : '' }}>Missouri</option>
                        <option value="MT" {{ old('state', $user->state) === 'MT' ? 'selected' : '' }}>Montana</option>
                        <option value="NE" {{ old('state', $user->state) === 'NE' ? 'selected' : '' }}>Nebraska</option>
                        <option value="NV" {{ old('state', $user->state) === 'NV' ? 'selected' : '' }}>Nevada</option>
                        <option value="NH" {{ old('state', $user->state) === 'NH' ? 'selected' : '' }}>New Hampshire</option>
                        <option value="NJ" {{ old('state', $user->state) === 'NJ' ? 'selected' : '' }}>New Jersey</option>
                        <option value="NM" {{ old('state', $user->state) === 'NM' ? 'selected' : '' }}>New Mexico</option>
                        <option value="NY" {{ old('state', $user->state) === 'NY' ? 'selected' : '' }}>New York</option>
                        <option value="NC" {{ old('state', $user->state) === 'NC' ? 'selected' : '' }}>North Carolina</option>
                        <option value="ND" {{ old('state', $user->state) === 'ND' ? 'selected' : '' }}>North Dakota</option>
                        <option value="OH" {{ old('state', $user->state) === 'OH' ? 'selected' : '' }}>Ohio</option>
                        <option value="OK" {{ old('state', $user->state) === 'OK' ? 'selected' : '' }}>Oklahoma</option>
                        <option value="OR" {{ old('state', $user->state) === 'OR' ? 'selected' : '' }}>Oregon</option>
                        <option value="PA" {{ old('state', $user->state) === 'PA' ? 'selected' : '' }}>Pennsylvania</option>
                        <option value="RI" {{ old('state', $user->state) === 'RI' ? 'selected' : '' }}>Rhode Island</option>
                        <option value="SC" {{ old('state', $user->state) === 'SC' ? 'selected' : '' }}>South Carolina</option>
                        <option value="SD" {{ old('state', $user->state) === 'SD' ? 'selected' : '' }}>South Dakota</option>
                        <option value="TN" {{ old('state', $user->state) === 'TN' ? 'selected' : '' }}>Tennessee</option>
                        <option value="TX" {{ old('state', $user->state) === 'TX' ? 'selected' : '' }}>Texas</option>
                        <option value="UT" {{ old('state', $user->state) === 'UT' ? 'selected' : '' }}>Utah</option>
                        <option value="VT" {{ old('state', $user->state) === 'VT' ? 'selected' : '' }}>Vermont</option>
                        <option value="VA" {{ old('state', $user->state) === 'VA' ? 'selected' : '' }}>Virginia</option>
                        <option value="WA" {{ old('state', $user->state) === 'WA' ? 'selected' : '' }}>Washington</option>
                        <option value="WV" {{ old('state', $user->state) === 'WV' ? 'selected' : '' }}>West Virginia</option>
                        <option value="WI" {{ old('state', $user->state) === 'WI' ? 'selected' : '' }}>Wisconsin</option>
                        <option value="WY" {{ old('state', $user->state) === 'WY' ? 'selected' : '' }}>Wyoming</option>
                        <option value="DC" {{ old('state', $user->state) === 'DC' ? 'selected' : '' }}>District of Columbia</option>
                        <option value="AS" {{ old('state', $user->state) === 'AS' ? 'selected' : '' }}>American Samoa</option>
                        <option value="GU" {{ old('state', $user->state) === 'GU' ? 'selected' : '' }}>Guam</option>
                        <option value="MP" {{ old('state', $user->state) === 'MP' ? 'selected' : '' }}>Northern Mariana Islands</option>
                        <option value="PR" {{ old('state', $user->state) === 'PR' ? 'selected' : '' }}>Puerto Rico</option>
                        <option value="VI" {{ old('state', $user->state) === 'VI' ? 'selected' : '' }}>U.S. Virgin Islands</option>
                    </select>
                    <x-input-error class="mt-2" :messages="$errors->get('state')" />
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
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
                    <select id="country" name="country" class="select select-bordered w-full" autocomplete="country-name">
                        <option value="">Select a country</option>
                        <option value="US" {{ old('country', $user->country) === 'US' ? 'selected' : '' }}>United States</option>
                        <option value="CA" {{ old('country', $user->country) === 'CA' ? 'selected' : '' }}>Canada</option>
                        <option value="MX" {{ old('country', $user->country) === 'MX' ? 'selected' : '' }}>Mexico</option>
                        <option value="GB" {{ old('country', $user->country) === 'GB' ? 'selected' : '' }}>United Kingdom</option>
                        <option value="AU" {{ old('country', $user->country) === 'AU' ? 'selected' : '' }}>Australia</option>
                        <option value="DE" {{ old('country', $user->country) === 'DE' ? 'selected' : '' }}>Germany</option>
                        <option value="FR" {{ old('country', $user->country) === 'FR' ? 'selected' : '' }}>France</option>
                        <option value="IT" {{ old('country', $user->country) === 'IT' ? 'selected' : '' }}>Italy</option>
                        <option value="ES" {{ old('country', $user->country) === 'ES' ? 'selected' : '' }}>Spain</option>
                        <option value="NL" {{ old('country', $user->country) === 'NL' ? 'selected' : '' }}>Netherlands</option>
                        <option value="BR" {{ old('country', $user->country) === 'BR' ? 'selected' : '' }}>Brazil</option>
                        <option value="JP" {{ old('country', $user->country) === 'JP' ? 'selected' : '' }}>Japan</option>
                        <option value="CN" {{ old('country', $user->country) === 'CN' ? 'selected' : '' }}>China</option>
                        <option value="IN" {{ old('country', $user->country) === 'IN' ? 'selected' : '' }}>India</option>
                        <option value="OTHER" {{ old('country', $user->country) && !in_array(old('country', $user->country), ['US', 'CA', 'MX', 'GB', 'AU', 'DE', 'FR', 'IT', 'ES', 'NL', 'BR', 'JP', 'CN', 'IN']) ? 'selected' : '' }}>Other</option>
                    </select>
                    <x-input-error class="mt-2" :messages="$errors->get('country')" />
                </div>
            </div>

            <div class="flex items-center gap-4 pt-4 border-t border-base-300">
                <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                <button type="button" @click="editing = false" class="btn btn-ghost">Cancel</button>
                @if (session('status') === 'address-updated')
                    <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => { show = false; editing = false; }, 2000)" class="text-sm text-base-content/60">{{ __('Saved.') }}</p>
                @endif
            </div>
        </form>
    </div>
@else
    <p class="text-base-content/60">Address information is not available for admin accounts.</p>
@endif
