<div>
    <header class="mb-6">
        <h3 class="text-lg font-medium text-error">
            {{ __('Delete Account') }}
        </h3>
        <p class="mt-1 text-sm text-base-content/60">
            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
        </p>
    </header>

    <button
        type="button"
        class="btn btn-error"
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
    >
        {{ __('Delete Account') }}
    </button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <h3 class="font-bold text-lg text-error mb-4">
                {{ __('Are you sure you want to delete your account?') }}
            </h3>

            <p class="text-sm text-base-content/70 mb-6">
                {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
            </p>

            <div class="form-control mb-6">
                <label class="label" for="password">
                    <span class="label-text">{{ __('Password') }} <span class="text-error">*</span></span>
                </label>
                <input
                    id="password"
                    name="password"
                    type="password"
                    class="input input-bordered w-full"
                    placeholder="{{ __('Enter your password to confirm') }}"
                    required
                />
                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="modal-action">
                <button type="button" class="btn btn-ghost" x-on:click="$dispatch('close-modal', 'confirm-user-deletion')">
                    {{ __('Cancel') }}
                </button>
                <button type="submit" class="btn btn-error">
                    {{ __('Delete Account') }}
                </button>
            </div>
        </form>
    </x-modal>
</div>

