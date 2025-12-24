<div x-data="{ editing: {{ ($errors->any() || session('status') === 'profile-updated') ? 'true' : 'false' }} }">
    <header class="mb-6 flex items-center justify-between">
        <div>
            <h3 class="text-lg font-medium text-base-content">
                {{ __('Profile Information') }}
            </h3>
            <p class="mt-1 text-sm text-base-content/60">
                {{ __('Update your personal information and email address.') }}
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
            <div>
                <div class="text-sm text-base-content/60 mb-1">Name</div>
                <div class="text-base font-medium">{{ $user->name }}</div>
            </div>
            
            <div>
                <div class="text-sm text-base-content/60 mb-1">Email</div>
                <div class="text-base">{{ $user->email }}</div>
                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                    <div class="mt-2">
                        <div role="alert" class="alert alert-warning">
                            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <span class="text-sm">Your email address is unverified.</span>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Edit Form --}}
    <form x-show="editing" method="post" action="{{ route('profile.update') }}" class="space-y-6">
        @csrf
        @method('patch')

        <div class="form-control">
            <label class="label" for="name">
                <span class="label-text">{{ __('Name') }} <span class="text-error">*</span></span>
            </label>
            <input id="name" name="name" type="text" class="input input-bordered w-full" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div class="form-control">
            <label class="label" for="email">
                <span class="label-text">{{ __('Email') }} <span class="text-error">*</span></span>
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

        <div class="flex items-center gap-4 pt-4 border-t border-base-300">
            <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
            <button type="button" @click="editing = false" class="btn btn-ghost">Cancel</button>
            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => { show = false; editing = false; }, 2000)" class="text-sm text-base-content/60">{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>
</div>
