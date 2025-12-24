<div>
    <header class="mb-6">
        <h3 class="text-lg font-medium text-base-content">
            {{ __('Update Password') }}
        </h3>
        <p class="mt-1 text-sm text-base-content/60">
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="space-y-6">
        @csrf
        @method('put')

        <div class="form-control">
            <label class="label" for="update_password_current_password">
                <span class="label-text">{{ __('Current Password') }} <span class="text-error">*</span></span>
            </label>
            <input id="update_password_current_password" name="current_password" type="password" class="input input-bordered w-full" autocomplete="current-password" required />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div class="form-control">
            <label class="label" for="update_password_password">
                <span class="label-text">{{ __('New Password') }} <span class="text-error">*</span></span>
            </label>
            <input id="update_password_password" name="password" type="password" class="input input-bordered w-full" autocomplete="new-password" required />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <div class="form-control">
            <label class="label" for="update_password_password_confirmation">
                <span class="label-text">{{ __('Confirm Password') }} <span class="text-error">*</span></span>
            </label>
            <input id="update_password_password_confirmation" name="password_confirmation" type="password" class="input input-bordered w-full" autocomplete="new-password" required />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4 pt-4 border-t border-base-300">
            <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
            @if (session('status') === 'password-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)" class="text-sm text-base-content/60">{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</div>
