@props([
    'feature' => 'this feature',
    'requiredPlan' => 'Pro',
])

<div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 text-center">
    <div class="flex justify-center mb-4">
        <svg class="h-12 w-12 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
        </svg>
    </div>
    
    <h3 class="text-lg font-semibold text-gray-900 mb-2">
        {{ $feature }} is locked
    </h3>
    
    <p class="text-sm text-gray-600 mb-4">
        Upgrade to {{ $requiredPlan }} to unlock {{ $feature }}.
    </p>
    
    <a href="{{ route('business.billing.index') }}" 
       class="inline-flex items-center px-4 py-2 bg-black border border-black rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-black transition">
        Upgrade to {{ $requiredPlan }}
    </a>
</div>

