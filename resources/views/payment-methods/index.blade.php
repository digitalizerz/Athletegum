<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl leading-tight">Payment Methods</h2>
            <a href="{{ route('payment-methods.create') }}">
                <button class="btn btn-primary">Add Payment Method</button>
            </a>
        </div>
    </x-slot>

    <div x-data="{
        showDeleteModal: false,
        deleteMethodId: null,
        deleteMethodLastFour: null,
    }" class="py-6">
        <div class="max-w-4xl mx-auto">
            @if(session('success'))
                <div role="alert" class="alert alert-success mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div role="alert" class="alert alert-error mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if($paymentMethods->isEmpty())
                <div class="card bg-base-100 shadow-sm">
                    <div class="card-body text-center py-12">
                        <p class="text-base-content/60 mb-4">You don't have any payment methods yet.</p>
                        <a href="{{ route('payment-methods.create') }}">
                            <button class="btn btn-primary">Add Your First Payment Method</button>
                        </a>
                    </div>
                </div>
            @else
                <div class="card bg-base-100 shadow-sm">
                    <div class="card-body p-0">
                        <div class="divide-y divide-base-300">
                            @foreach($paymentMethods as $method)
                                <div class="p-6 flex items-center justify-between">
                                    <div class="flex items-center space-x-4">
                                        <div class="flex-shrink-0">
                                            @if($method->brand === 'visa')
                                                <div class="w-12 h-8 bg-blue-600 rounded flex items-center justify-center text-white font-bold text-xs">VISA</div>
                                            @elseif($method->brand === 'mastercard')
                                                <div class="w-12 h-8 bg-red-600 rounded flex items-center justify-center text-white font-bold text-xs">MC</div>
                                            @elseif($method->brand === 'amex')
                                                <div class="w-12 h-8 bg-blue-500 rounded flex items-center justify-center text-white font-bold text-xs">AMEX</div>
                                            @else
                                                <div class="w-12 h-8 bg-base-300 rounded flex items-center justify-center text-base-content/60 font-bold text-xs">CARD</div>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="flex items-center space-x-2">
                                                <span class="font-semibold">
                                                    •••• •••• •••• {{ $method->last_four }}
                                                </span>
                                                @if($method->is_default)
                                                    <span class="badge badge-primary">Default</span>
                                                @endif
                                            </div>
                                            <div class="text-sm text-base-content/60">
                                                Expires {{ $method->exp_month }}/{{ $method->exp_year }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        @if(!$method->is_default)
                                            <form action="{{ route('payment-methods.set-default', $method) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-ghost btn-sm">Set as Default</button>
                                            </form>
                                        @endif
                                        <button 
                                            @click="deleteMethodId = {{ $method->id }}; deleteMethodLastFour = '{{ $method->last_four }}'; showDeleteModal = true;" 
                                            class="btn btn-ghost btn-sm text-error">Delete</button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Delete Payment Method Modal -->
        <dialog x-show="showDeleteModal" 
                @click.away="showDeleteModal = false"
                class="modal"
                :class="{ 'modal-open': showDeleteModal }">
            <div class="modal-box">
                <h3 class="font-bold text-lg">Delete Payment Method</h3>
                <p class="py-4">
                    Are you sure you want to delete payment method ending in <span x-text="deleteMethodLastFour"></span>? This action cannot be undone.
                </p>
                <div class="modal-action">
                    <form method="POST" :action="'{{ url('/payment-methods') }}/' + deleteMethodId">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-error">Delete</button>
                    </form>
                    <button @click="showDeleteModal = false" class="btn btn-ghost">Cancel</button>
                </div>
            </div>
            <form method="dialog" class="modal-backdrop">
                <button>close</button>
            </form>
        </dialog>
    </div>
</x-app-layout>
