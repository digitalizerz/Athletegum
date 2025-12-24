<x-superadmin-dashboard-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold">Edit Business</h2>
            <a href="{{ route('admin.businesses.index') }}" class="btn btn-ghost btn-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Businesses
            </a>
        </div>
    </x-slot>

    <div class="max-w-4xl">
        <div class="card bg-base-100 shadow-sm border border-base-300">
            <div class="card-body">
                <form method="POST" action="{{ route('admin.businesses.update', $user) }}">
                    @csrf
                    @method('PUT')

                    <div class="space-y-6">
                        <div>
                            <h3 class="text-lg font-semibold mb-4">Basic Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="form-control">
                                    <label class="label" for="name">
                                        <span class="label-text font-medium">Name</span>
                                    </label>
                                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" 
                                           class="input input-bordered w-full @error('name') input-error @enderror" required>
                                    @error('name')
                                        <label class="label">
                                            <span class="label-text-alt text-error">{{ $message }}</span>
                                        </label>
                                    @enderror
                                </div>

                                <div class="form-control">
                                    <label class="label" for="email">
                                        <span class="label-text font-medium">Email</span>
                                    </label>
                                    <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" 
                                           class="input input-bordered w-full @error('email') input-error @enderror" required>
                                    @error('email')
                                        <label class="label">
                                            <span class="label-text-alt text-error">{{ $message }}</span>
                                        </label>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div>
                            <h3 class="text-lg font-semibold mb-4">Business Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="form-control">
                                    <label class="label" for="business_name">
                                        <span class="label-text font-medium">Business Name</span>
                                    </label>
                                    <input type="text" id="business_name" name="business_name" value="{{ old('business_name', $user->business_name) }}" 
                                           class="input input-bordered w-full @error('business_name') input-error @enderror">
                                    @error('business_name')
                                        <label class="label">
                                            <span class="label-text-alt text-error">{{ $message }}</span>
                                        </label>
                                    @enderror
                                </div>

                                <div class="form-control">
                                    <label class="label" for="owner_principal">
                                        <span class="label-text font-medium">Owner / Principal</span>
                                    </label>
                                    <input type="text" id="owner_principal" name="owner_principal" value="{{ old('owner_principal', $user->owner_principal) }}" 
                                           class="input input-bordered w-full @error('owner_principal') input-error @enderror">
                                    @error('owner_principal')
                                        <label class="label">
                                            <span class="label-text-alt text-error">{{ $message }}</span>
                                        </label>
                                    @enderror
                                </div>

                                <div class="form-control md:col-span-2">
                                    <label class="label" for="business_information">
                                        <span class="label-text font-medium">Business Information</span>
                                    </label>
                                    <textarea id="business_information" name="business_information" rows="4" 
                                              class="textarea textarea-bordered w-full @error('business_information') textarea-error @enderror">{{ old('business_information', $user->business_information) }}</textarea>
                                    @error('business_information')
                                        <label class="label">
                                            <span class="label-text-alt text-error">{{ $message }}</span>
                                        </label>
                                    @enderror
                                </div>

                                <div class="form-control">
                                    <label class="label" for="phone">
                                        <span class="label-text font-medium">Phone</span>
                                    </label>
                                    <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" 
                                           class="input input-bordered w-full @error('phone') input-error @enderror">
                                    @error('phone')
                                        <label class="label">
                                            <span class="label-text-alt text-error">{{ $message }}</span>
                                        </label>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-base-300">
                            <a href="{{ route('admin.businesses.index') }}" class="btn btn-ghost">Cancel</a>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-superadmin-dashboard-layout>

