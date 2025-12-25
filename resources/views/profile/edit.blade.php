@if(Auth::user()->is_superadmin)
    <x-superadmin-dashboard-layout>
        <x-slot name="header">
            <h1 class="text-2xl font-semibold">Profile Settings</h1>
        </x-slot>

        <div class="max-w-4xl">
            <div class="card bg-base-100 shadow-sm border border-base-300">
                <div class="card-body">
                    <div x-data="{ activeTab: 'profile' }">
                        {{-- Tabs --}}
                        <div class="tabs tabs-boxed mb-6">
                            <a @click="activeTab = 'profile'" :class="activeTab === 'profile' ? 'tab-active' : ''" class="tab">
                                Profile
                            </a>
                            <a @click="activeTab = 'security'" :class="activeTab === 'security' ? 'tab-active' : ''" class="tab">
                                Security
                            </a>
                            <a @click="activeTab = 'danger'" :class="activeTab === 'danger' ? 'tab-active' : ''" class="tab tab-error">
                                Danger Zone
                            </a>
                        </div>

                        {{-- Tab Content --}}
                        <div x-show="activeTab === 'profile'" x-transition>
                            @include('profile.partials.profile-tab-form')
                        </div>

                        <div x-show="activeTab === 'security'" x-transition style="display: none;">
                            @include('profile.partials.update-password-form')
                        </div>

                        <div x-show="activeTab === 'danger'" x-transition style="display: none;">
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
                <div class="card bg-base-100 shadow-sm">
                    <div class="card-body">
                        {{-- Tabs (Responsive) --}}
                        <div x-data="{ activeTab: 'profile' }">
                            {{-- Mobile: Scrollable tabs --}}
                            <div class="lg:hidden overflow-x-auto mb-6">
                                <div class="tabs tabs-boxed inline-flex min-w-full">
                                    <a @click="activeTab = 'profile'" :class="activeTab === 'profile' ? 'tab-active' : ''" class="tab whitespace-nowrap">Profile</a>
                                    <a @click="activeTab = 'business'" :class="activeTab === 'business' ? 'tab-active' : ''" class="tab whitespace-nowrap">Business</a>
                                    <a @click="activeTab = 'address'" :class="activeTab === 'address' ? 'tab-active' : ''" class="tab whitespace-nowrap">Address</a>
                                    <a @click="activeTab = 'security'" :class="activeTab === 'security' ? 'tab-active' : ''" class="tab whitespace-nowrap">Security</a>
                                    <a @click="activeTab = 'danger'" :class="activeTab === 'danger' ? 'tab-active' : ''" class="tab tab-error whitespace-nowrap">Danger Zone</a>
                                </div>
                            </div>

                            {{-- Desktop: Full tabs --}}
                            <div class="hidden lg:block mb-6">
                                <div class="tabs tabs-boxed">
                                    <a @click="activeTab = 'profile'" :class="activeTab === 'profile' ? 'tab-active' : ''" class="tab">Profile</a>
                                    <a @click="activeTab = 'business'" :class="activeTab === 'business' ? 'tab-active' : ''" class="tab">Business</a>
                                    <a @click="activeTab = 'address'" :class="activeTab === 'address' ? 'tab-active' : ''" class="tab">Address</a>
                                    <a @click="activeTab = 'security'" :class="activeTab === 'security' ? 'tab-active' : ''" class="tab">Security</a>
                                    <a @click="activeTab = 'danger'" :class="activeTab === 'danger' ? 'tab-active' : ''" class="tab tab-error">Danger Zone</a>
                                </div>
                            </div>

                            {{-- Tab Content --}}
                            <div x-show="activeTab === 'profile'" x-transition>
                                @include('profile.partials.profile-tab-form')
                            </div>

                            <div x-show="activeTab === 'business'" x-transition style="display: none;">
                                @include('profile.partials.business-tab-form')
                            </div>

                            <div x-show="activeTab === 'address'" x-transition style="display: none;">
                                @include('profile.partials.address-tab-form')
                            </div>

                            <div x-show="activeTab === 'security'" x-transition style="display: none;">
                                @include('profile.partials.update-password-form')
                            </div>

                            <div x-show="activeTab === 'danger'" x-transition style="display: none;">
                                @include('profile.partials.delete-user-form')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-app-layout>
@endif
