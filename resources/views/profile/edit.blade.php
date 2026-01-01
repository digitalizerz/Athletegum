@if(Auth::user()->is_admin)
    <x-superadmin-dashboard-layout>
        <x-slot name="header">
            <h1 class="text-2xl font-semibold">Profile Settings</h1>
        </x-slot>

        <div class="max-w-4xl">
            <div class="card bg-base-100 shadow-sm border border-base-300">
                <div class="card-body">
                    <div x-data="{ activeTab: 'profile' }">
                        {{-- Tabs --}}
                        <div class="flex gap-1 mb-8 border-b border-base-300">
                            <button 
                                @click="activeTab = 'profile'" 
                                :class="activeTab === 'profile' ? 'font-bold text-base-content border-b-2 border-primary pb-2 -mb-[1px]' : 'text-base-content/60 hover:text-base-content pb-2 -mb-[1px]'"
                                class="px-4 py-2 text-sm transition-colors cursor-pointer"
                            >
                                Profile
                            </button>
                            <button 
                                @click="activeTab = 'security'" 
                                :class="activeTab === 'security' ? 'font-bold text-base-content border-b-2 border-primary pb-2 -mb-[1px]' : 'text-base-content/60 hover:text-base-content pb-2 -mb-[1px]'"
                                class="px-4 py-2 text-sm transition-colors cursor-pointer"
                            >
                                Security
                            </button>
                            <div class="flex-1"></div>
                            <button 
                                @click="activeTab = 'danger'" 
                                :class="activeTab === 'danger' ? 'font-bold text-error border-b-2 border-error pb-2 -mb-[1px]' : 'text-error/70 hover:text-error pb-2 -mb-[1px]'"
                                class="px-4 py-2 text-sm transition-colors cursor-pointer ml-auto"
                            >
                                Danger Zone
                            </button>
                        </div>

                        {{-- Tab Content --}}
                        <div x-show="activeTab === 'profile'" x-transition>
                            @include('profile.partials.profile-tab-form')
                        </div>

                        <div x-show="activeTab === 'security'" x-transition x-cloak>
                            @include('profile.partials.update-password-form')
                        </div>

                        <div x-show="activeTab === 'danger'" x-transition x-cloak>
                            @include('profile.partials.delete-user-form')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-superadmin-dashboard-layout>
@else
    <x-app-layout>
        <x-slot name="header">
            <h2 class="font-semibold text-xl leading-tight">
                {{ __('Profile') }}
            </h2>
        </x-slot>

        <div class="py-6">
            <div class="max-w-4xl mx-auto">
                <div class="card bg-base-100 shadow-sm border border-base-300">
                    <div class="card-body">
                        {{-- Tabs --}}
                        <div x-data="{ activeTab: 'profile' }">
                            {{-- Desktop Tabs --}}
                            <div class="hidden lg:flex gap-1 mb-8 border-b border-base-300">
                                <button 
                                    @click="activeTab = 'profile'" 
                                    :class="activeTab === 'profile' ? 'font-bold text-base-content border-b-2 border-primary pb-2 -mb-[1px]' : 'text-base-content/60 hover:text-base-content pb-2 -mb-[1px]'"
                                    class="px-4 py-2 text-sm transition-colors cursor-pointer"
                                >
                                    Profile
                                </button>
                                <button 
                                    @click="activeTab = 'business'" 
                                    :class="activeTab === 'business' ? 'font-bold text-base-content border-b-2 border-primary pb-2 -mb-[1px]' : 'text-base-content/60 hover:text-base-content pb-2 -mb-[1px]'"
                                    class="px-4 py-2 text-sm transition-colors cursor-pointer"
                                >
                                    Business
                                </button>
                                <button 
                                    @click="activeTab = 'address'" 
                                    :class="activeTab === 'address' ? 'font-bold text-base-content border-b-2 border-primary pb-2 -mb-[1px]' : 'text-base-content/60 hover:text-base-content pb-2 -mb-[1px]'"
                                    class="px-4 py-2 text-sm transition-colors cursor-pointer"
                                >
                                    Address
                                </button>
                                <button 
                                    @click="activeTab = 'security'" 
                                    :class="activeTab === 'security' ? 'font-bold text-base-content border-b-2 border-primary pb-2 -mb-[1px]' : 'text-base-content/60 hover:text-base-content pb-2 -mb-[1px]'"
                                    class="px-4 py-2 text-sm transition-colors cursor-pointer"
                                >
                                    Security
                                </button>
                                <div class="flex-1"></div>
                                <button 
                                    @click="activeTab = 'danger'" 
                                    :class="activeTab === 'danger' ? 'font-bold text-error border-b-2 border-error pb-2 -mb-[1px]' : 'text-error/70 hover:text-error pb-2 -mb-[1px]'"
                                    class="px-4 py-2 text-sm transition-colors cursor-pointer"
                                >
                                    Danger Zone
                                </button>
                            </div>

                            {{-- Mobile Tabs (Dropdown) --}}
                            <div class="lg:hidden mb-6">
                                <select 
                                    @change="activeTab = $event.target.value"
                                    class="select select-bordered w-full"
                                    x-model="activeTab"
                                >
                                    <option value="profile">Profile</option>
                                    <option value="business">Business</option>
                                    <option value="address">Address</option>
                                    <option value="security">Security</option>
                                    <option value="danger">Danger Zone</option>
                                </select>
                            </div>

                            {{-- Tab Content --}}
                            <div x-show="activeTab === 'profile'" x-transition>
                                @include('profile.partials.profile-tab-form')
                            </div>

                            <div x-show="activeTab === 'business'" x-transition x-cloak>
                                @include('profile.partials.business-tab-form')
                            </div>

                            <div x-show="activeTab === 'address'" x-transition x-cloak>
                                @include('profile.partials.address-tab-form')
                            </div>

                            <div x-show="activeTab === 'security'" x-transition x-cloak>
                                @include('profile.partials.update-password-form')
                            </div>

                            <div x-show="activeTab === 'danger'" x-transition x-cloak>
                                @include('profile.partials.delete-user-form')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-app-layout>
@endif
