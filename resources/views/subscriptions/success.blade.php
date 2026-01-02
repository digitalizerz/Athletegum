<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Subscription Successful
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="card bg-base-100 shadow-sm">
                <div class="card-body text-center">
                    <div class="mb-6">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-success/20 mb-4">
                            <svg class="w-8 h-8 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-semibold mb-2">Subscription Activated</h3>
                        <p class="text-base-content/60">Your subscription has been successfully activated.</p>
                    </div>

                    <div class="flex flex-col sm:flex-row justify-center items-center gap-3 sm:gap-4">
                        <a href="{{ route('dashboard') }}" class="btn btn-primary">
                            Go to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

