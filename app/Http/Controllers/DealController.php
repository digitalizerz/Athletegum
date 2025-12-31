<?php

namespace App\Http\Controllers;

use App\Models\Deal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class DealController extends Controller
{
    public function index(Request $request)
    {
        $query = Deal::where('user_id', Auth::id());

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            // By default, exclude drafts unless specifically filtered
            // This can be changed if you want drafts shown by default
        }

        if ($request->filled('deal_type')) {
            $query->where('deal_type', $request->deal_type);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('deal_type', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        $deals = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('deals.index', [
            'deals' => $deals,
        ]);
    }

    public function create()
    {
        $user = Auth::user();
        
        // Check if business info is complete
        $hasCompleteInfo = $this->hasCompleteBusinessInfo($user);
        
        return view('deals.create-type', [
            'dealTypes' => Deal::getDealTypes(),
            'hasCompleteBusinessInfo' => $hasCompleteInfo,
        ]);
    }

    /**
     * Check if user has complete business and address information
     */
    private function hasCompleteBusinessInfo($user): bool
    {
        // Required business fields
        $requiredBusinessFields = [
            'business_name',
            'business_information',
            'owner_principal',
            'phone',
        ];
        
        // Required address fields
        $requiredAddressFields = [
            'address_line1',
            'city',
            'state',
            'postal_code',
            'country',
        ];
        
        // Check if all required business fields are filled
        foreach ($requiredBusinessFields as $field) {
            if (empty($user->$field)) {
                return false;
            }
        }
        
        // Check if all required address fields are filled
        foreach ($requiredAddressFields as $field) {
            if (empty($user->$field)) {
                return false;
            }
        }
        
        return true;
    }

    public function storeType(Request $request)
    {
        $validated = $request->validate([
            'deal_type' => ['required', Rule::in(array_keys(Deal::getDealTypes()))],
        ]);

        $dealType = $validated['deal_type'];
        $dealTypes = Deal::getDealTypes();
        
        // Store deal type in session
        $request->session()->put('deal_type', $dealType);
        
        // If this deal type requires platforms, go to platform selection first
        if (($dealTypes[$dealType]['requires_platforms'] ?? false)) {
            return redirect()->route('deals.create.platforms');
        }

        // Otherwise, skip to compensation
        return redirect()->route('deals.create.compensation');
    }

    public function createPlatforms(Request $request)
    {
        $dealType = $request->session()->get('deal_type');
        
        if (!$dealType) {
            return redirect()->route('deals.create');
        }

        $dealTypes = Deal::getDealTypes();
        if (!($dealTypes[$dealType]['requires_platforms'] ?? false)) {
            return redirect()->route('deals.create.compensation');
        }

        return view('deals.create-platforms', [
            'dealType' => $dealType,
            'dealTypeName' => $dealTypes[$dealType]['name'] ?? $dealType,
            'platforms' => Deal::getPlatforms(),
        ]);
    }

    public function storePlatforms(Request $request)
    {
        $validated = $request->validate([
            'platforms' => ['required', 'array', 'min:1'],
            'platforms.*' => ['required', Rule::in(array_keys(Deal::getPlatforms()))],
        ]);

        $request->session()->put('platforms', $validated['platforms']);

        return redirect()->route('deals.create.compensation');
    }

    public function createCompensation(Request $request)
    {
        $dealType = $request->session()->get('deal_type');
        
        if (!$dealType) {
            return redirect()->route('deals.create');
        }

        return view('deals.create-compensation', [
            'dealType' => $dealType,
            'dealTypeName' => Deal::getDealTypes()[$dealType]['name'] ?? $dealType,
        ]);
    }

    public function storeCompensation(Request $request)
    {
        $request->validate([
            'compensation_amount' => ['required', 'numeric', 'min:0.01'],
        ]);

        $request->session()->put('compensation_amount', $request->compensation_amount);

        return redirect()->route('deals.create.deadline');
    }

    public function createDeadline()
    {
        if (!session()->has('deal_type') || !session()->has('compensation_amount')) {
            return redirect()->route('deals.create');
        }

        return view('deals.create-deadline');
    }

    public function storeDeadline(Request $request)
    {
        $validated = $request->validate([
            'athlete_email' => ['required', 'email', 'max:255'],
            'deadline' => ['required', 'date', 'after:today'],
            'deadline_time' => ['nullable', 'date_format:H:i'],
            'frequency' => ['nullable', 'in:one-time,daily,weekly,bi-weekly,monthly'],
        ]);

        $request->session()->put('athlete_email', strtolower(trim($validated['athlete_email'])));
        $request->session()->put('deadline', $validated['deadline']);
        $request->session()->put('deadline_time', $validated['deadline_time'] ?? null);
        $request->session()->put('frequency', $validated['frequency'] ?? 'one-time');

        return redirect()->route('deals.create.notes');
    }

    public function createNotes()
    {
        if (!session()->has('deal_type') || !session()->has('compensation_amount') || !session()->has('deadline')) {
            return redirect()->route('deals.create');
        }

        $dealType = session('deal_type');
        $dealTypes = Deal::getDealTypes();
        $isCustomDeal = $dealType === 'custom';

        return view('deals.create-notes', [
            'isCustomDeal' => $isCustomDeal,
        ]);
    }

    public function storeNotes(Request $request)
    {
        $dealType = session('deal_type');
        $isCustomDeal = $dealType === 'custom';

        $rules = [
            'notes' => [$isCustomDeal ? 'required' : 'nullable', 'string', 'max:5000'],
            'attachments' => ['nullable', 'array', 'max:5'],
            'attachments.*' => ['file', 'max:10240', 'mimes:pdf,doc,docx,jpg,jpeg,png,gif'],
        ];

        $validated = $request->validate($rules);

        // Handle file uploads
        $attachments = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('deal-attachments', 'public');
                $attachments[] = [
                    'path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                ];
            }
        }

        $request->session()->put('notes', $validated['notes'] ?? '');
        $request->session()->put('attachments', $attachments);

        return redirect()->route('deals.create.contract');
    }

    public function createContract()
    {
        $session = session();
        
        if (!$session->has('deal_type') || !$session->has('compensation_amount') || !$session->has('deadline')) {
            return redirect()->route('deals.create');
        }

        // For now, we'll use a default contract template
        // In the future, this could be customizable per SMB
        $defaultContract = "By accepting this deal, you agree to:\n\n1. Complete the work as specified in the deal terms\n2. Submit proof of completion\n3. Follow all guidelines and instructions provided\n4. Maintain professional standards\n\nPayment will be released upon approval of completed work.";

        return view('deals.create-contract', [
            'contractText' => $defaultContract,
        ]);
    }

    public function storeContract(Request $request)
    {
        $validated = $request->validate([
            'contract_signed' => ['required', 'accepted'],
        ]);

        $request->session()->put('contract_text', $request->contract_text ?? '');
        $request->session()->put('contract_signed', true);

        // Don't block - allow proceeding to payment step
        // Payment method check will happen before final submission
        return redirect()->route('deals.create.payment');
    }

    public function createPayment()
    {
        $session = session();
        
        if (!$session->has('deal_type') || !$session->has('compensation_amount') || !$session->has('deadline')) {
            return redirect()->route('deals.create');
        }

        $paymentMethods = Auth::user()->paymentMethods()->where('is_active', true)->orderBy('is_default', 'desc')->get();

        // Calculate payment breakdown using SMB platform fee
        $compensationAmount = (float) $session->get('compensation_amount');
        $smbFee = \App\Models\PlatformSetting::getSMBPlatformFee();
        
        if ($smbFee['type'] === 'percentage') {
            $platformFeeAmount = round($compensationAmount * ($smbFee['value'] / 100), 2);
            $platformFeePercentage = $smbFee['value'];
        } else {
            $platformFeeAmount = round($smbFee['value'], 2);
            $platformFeePercentage = null; // Fixed fee, no percentage
        }
        
        $escrowAmount = round($compensationAmount, 2);
        $totalAmount = round($compensationAmount + $platformFeeAmount, 2);

        // Get user's wallet balance
        $walletBalance = (float) Auth::user()->wallet_balance ?? 0.00;
        $hasSufficientBalance = $walletBalance >= $totalAmount;

        return view('deals.create-payment', [
            'paymentMethods' => $paymentMethods,
            'compensationAmount' => $compensationAmount,
            'platformFeePercentage' => $platformFeePercentage,
            'platformFeeAmount' => $platformFeeAmount,
            'escrowAmount' => $escrowAmount,
            'totalAmount' => $totalAmount,
            'walletBalance' => $walletBalance,
            'hasSufficientBalance' => $hasSufficientBalance,
        ]);
    }

    public function review()
    {
        $session = session();
        
        if (!$session->has('deal_type') || !$session->has('compensation_amount') || !$session->has('deadline')) {
            return redirect()->route('deals.create');
        }

        // Check if user has payment methods - required before final submission
        $paymentMethods = Auth::user()->paymentMethods()->where('is_active', true)->get();
        $hasPaymentMethod = $paymentMethods->isNotEmpty();

        $dealTypes = Deal::getDealTypes();
        $dealType = $session->get('deal_type');
        $platforms = $session->get('platforms', []);
        $attachments = $session->get('attachments', []);
        
        // Payment breakdown
        // Get platform fee from session, or calculate it if not present
        $platformFeePercentage = $session->get('platform_fee_percentage');
        if ($platformFeePercentage === null) {
            $smbFee = \App\Models\PlatformSetting::getSMBPlatformFee();
            $platformFeePercentage = $smbFee['type'] === 'percentage' ? $smbFee['value'] : null;
        }
        $platformFeeAmount = $session->get('platform_fee_amount');
        $escrowAmount = $session->get('escrow_amount');
        $totalAmount = $session->get('total_amount');
        $paymentMethod = $session->get('payment_method'); // 'wallet', 'wallet_card', or 'card'
        $walletAmountUsed = $session->get('wallet_amount_used', 0);
        $cardAmount = $session->get('card_amount', 0);
        $cardPaymentMethodId = $session->get('card_payment_method_id');
        $cardPaymentMethod = $cardPaymentMethodId ? \App\Models\PaymentMethod::find($cardPaymentMethodId) : null;

        return view('deals.review', [
            'dealType' => $dealType,
            'dealTypeName' => $dealTypes[$dealType]['name'] ?? $dealType,
            'dealTypeIcon' => $dealTypes[$dealType]['icon'] ?? '📋',
            'platforms' => $platforms,
            'platformNames' => array_intersect_key(Deal::getPlatforms(), array_flip($platforms)),
            'compensationAmount' => $session->get('compensation_amount'),
            'deadline' => $session->get('deadline'),
            'deadlineTime' => $session->get('deadline_time'),
            'frequency' => $session->get('frequency', 'one-time'),
            'notes' => $session->get('notes', ''),
            'attachments' => $attachments,
            'contractText' => $session->get('contract_text', ''),
            'platformFeePercentage' => $platformFeePercentage,
            'platformFeeAmount' => $platformFeeAmount,
            'escrowAmount' => $escrowAmount,
            'totalAmount' => $totalAmount,
            'paymentMethod' => $paymentMethod,
            'walletAmountUsed' => $walletAmountUsed,
            'cardAmount' => $cardAmount,
            'cardPaymentMethod' => $cardPaymentMethod,
            'hasPaymentMethod' => $hasPaymentMethod,
        ]);
    }

    public function store(Request $request)
    {
        $session = $request->session();
        
        // Validate that all required session data exists
        $request->validate([
            'deal_type' => ['required', Rule::in(array_keys(Deal::getDealTypes()))],
            'compensation_amount' => ['required', 'numeric', 'min:0.01'],
            'deadline' => ['required', 'date', 'after:today'],
        ]);

        // Get data from session (previously validated)
        $dealType = $session->get('deal_type');
        $compensationAmount = $session->get('compensation_amount');
        $deadline = $session->get('deadline');
        $notes = $session->get('notes');
        $platforms = $session->get('platforms', []);

        // Validate session data matches request (security check)
        if ($dealType !== $request->deal_type || 
            (string)$compensationAmount !== (string)$request->compensation_amount ||
            $deadline !== $request->deadline) {
            return redirect()->route('deals.create')->withErrors(['error' => 'Session expired. Please start over.']);
        }

        // Validate platforms if required for this deal type
        $dealTypes = Deal::getDealTypes();
        if (($dealTypes[$dealType]['requires_platforms'] ?? false) && empty($platforms)) {
            return redirect()->route('deals.create')->withErrors(['error' => 'Platforms are required for this deal type.']);
        }

        $deadlineTime = $session->get('deadline_time');
        $frequency = $session->get('frequency', 'one-time');
        $attachments = $session->get('attachments', []);
        $contractText = $session->get('contract_text');
        $contractSigned = $session->get('contract_signed', false);
        
        // Get payment data from session
        $paymentMethod = $session->get('payment_method'); // wallet, wallet_card, or card
        $cardPaymentMethodId = $session->get('card_payment_method_id');
        $walletAmountUsed = $session->get('wallet_amount_used', 0);
        $cardAmount = $session->get('card_amount', 0);
        $platformFeeType = $session->get('platform_fee_type', 'percentage');
        $platformFeePercentage = $session->get('platform_fee_percentage');
        $platformFeeValue = $session->get('platform_fee_value');
        $platformFeeAmount = $session->get('platform_fee_amount');
        $escrowAmount = $session->get('escrow_amount');
        $totalAmount = $session->get('total_amount');
        $paymentIntentId = $session->get('payment_intent_id');
        
        // Calculate athlete fees (for when payment is released)
        // Athlete receives: deal_amount - (deal_amount × 5%)
        $athleteFeePercentage = 5.0; // Fixed 5% athlete service fee
        $athleteFeeAmount = round($compensationAmount * ($athleteFeePercentage / 100), 2);
        $athleteNetPayout = round($compensationAmount - $athleteFeeAmount, 2); // Athlete receives deal_amount - 5%
        $paymentStatus = $session->get('payment_status', 'pending');

        // Check if user has payment methods - required before final submission
        $paymentMethods = Auth::user()->paymentMethods()->where('is_active', true)->get();
        if ($paymentMethods->isEmpty()) {
            return redirect()->route('deals.review')->withErrors([
                'error' => 'Please add a payment method before submitting the deal. You can save your progress as a draft and come back later.'
            ]);
        }

        // Validate payment was processed
        // For wallet payments, status must be 'paid'
        // For Stripe payments, status can be 'pending' (webhook will confirm)
        if (!$paymentIntentId) {
            return redirect()->route('deals.create.payment')->withErrors(['error' => 'Payment must be processed before creating the deal.']);
        }

        // For Stripe payments, verify the PaymentIntent exists and is valid
        if (str_starts_with($paymentIntentId, 'pi_')) {
            try {
                $stripeService = app(\App\Services\StripeService::class);
                $paymentIntent = $stripeService->getPaymentIntent($paymentIntentId);
                
                // Only allow if payment succeeded or is processing
                if (!in_array($paymentIntent->status, ['succeeded', 'processing'])) {
                    return redirect()->route('deals.create.payment')->withErrors([
                        'error' => 'Payment is not confirmed. Please try again.'
                    ]);
                }
                
                // Update payment status from Stripe
                $paymentStatus = $paymentIntent->status === 'succeeded' ? 'paid' : 'pending';
            } catch (\Exception $e) {
                \Log::error('Failed to verify Stripe PaymentIntent', [
                    'payment_intent_id' => $paymentIntentId,
                    'error' => $e->getMessage(),
                ]);
                return redirect()->route('deals.create.payment')->withErrors([
                    'error' => 'Payment verification failed. Please contact support.'
                ]);
            }
        } elseif ($paymentStatus !== 'paid') {
            // Wallet payments must be 'paid'
            return redirect()->route('deals.create.payment')->withErrors(['error' => 'Payment must be processed before creating the deal.']);
        }

        // Get athlete email from session (if provided during deal creation)
        $athleteEmail = $session->get('athlete_email');

        // Check if we're resuming a draft (deal ID in session)
        $draftId = $session->get('resuming_draft_id');
        $existingDraft = null;
        if ($draftId) {
            $existingDraft = Deal::where('id', $draftId)
                ->where('user_id', Auth::id())
                ->where('status', 'draft')
                ->first();
        }

        if ($existingDraft) {
            // Update existing draft to final deal
            $existingDraft->update([
                'payment_method_id' => $cardPaymentMethodId,
                'status' => 'pending',
                'payment_status' => $paymentStatus,
                'payment_intent_id' => $paymentIntentId,
                'paid_at' => $paymentStatus === 'paid' ? now() : null,
            ]);
            $deal = $existingDraft;
        } else {
            // Create new deal
            $deal = Deal::create([
                'user_id' => Auth::id(),
                'payment_method_id' => $cardPaymentMethodId, // Store card payment method if used
                'deal_type' => $dealType,
                'platforms' => $platforms,
                'compensation_amount' => $compensationAmount,
                'platform_fee_percentage' => $platformFeePercentage,
                'platform_fee_amount' => $platformFeeAmount,
                'escrow_amount' => $escrowAmount,
                'total_amount' => $totalAmount,
                'athlete_fee_percentage' => $athleteFeePercentage,
                'athlete_fee_amount' => $athleteFeeAmount,
                'athlete_net_payout' => $athleteNetPayout,
                'deadline' => $deadline,
                'deadline_time' => $deadlineTime ? ($deadlineTime . ':00') : null,
                'frequency' => $frequency,
                'notes' => $notes ?? null,
                'attachments' => !empty($attachments) ? $attachments : null,
                'contract_text' => $contractText,
                'contract_signed' => $contractSigned,
                'contract_signed_at' => $contractSigned ? now() : null,
                'status' => 'pending',
                'payment_status' => $paymentStatus === 'paid' ? 'paid_escrowed' : $paymentStatus, // Mark as paid_escrowed when payment succeeds
                'payment_intent_id' => $paymentIntentId,
                'stripe_charge_id' => $session->get('stripe_charge_id'), // Store charge ID for reference
                'paid_at' => $paymentStatus === 'paid' ? now() : null,
            ]);

            // Create deal invitation (required for identity guardrails)
            // For now, if no email provided, we'll create invitation without email (backward compatibility)
            // In production, athlete_email should be required
            $athlete = null;
            if ($athleteEmail) {
                // Check if athlete already exists
                $athlete = \App\Models\Athlete::where('email', $athleteEmail)->first();
            }

            $invitation = \App\Models\DealInvitation::create([
                'deal_id' => $deal->id,
                'athlete_email' => $athleteEmail,
                'athlete_id' => $athlete?->id,
                'status' => 'pending',
            ]);

            // Send email to athlete if email is provided
            if ($athleteEmail) {
                try {
                    $athleteName = $athlete?->name ?? explode('@', $athleteEmail)[0];
                    \Illuminate\Support\Facades\Mail::to($athleteEmail)->send(
                        new \App\Mail\NewDealCreatedMail($athleteName, $deal)
                    );
                    \Log::info('Deal creation email sent', [
                        'deal_id' => $deal->id,
                        'athlete_email' => $athleteEmail,
                    ]);
                } catch (\Exception $e) {
                    \Log::error('Failed to send deal creation email', [
                        'deal_id' => $deal->id,
                        'athlete_email' => $athleteEmail,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                    ]);
                    // Don't fail the deal creation if email fails
                }
            }
        }

        // Create wallet transaction for the deal payment (if wallet was used)
        if ($walletAmountUsed > 0) {
            \App\Models\WalletTransaction::create([
                'user_id' => Auth::id(),
                'type' => 'payment',
                'status' => 'completed',
                'amount' => -$walletAmountUsed, // Negative for payment
                'balance_before' => Auth::user()->wallet_balance + $walletAmountUsed, // Balance before this deduction
                'balance_after' => Auth::user()->wallet_balance, // Current balance (already deducted)
                'payment_method' => $paymentMethod === 'wallet_card' ? 'wallet_partial' : 'wallet',
                'payment_provider_transaction_id' => $paymentIntentId,
                'deal_id' => $deal->id,
                'description' => $paymentMethod === 'wallet_card' 
                    ? "Partial payment for deal #{$deal->id} (wallet: $" . number_format($walletAmountUsed, 2) . ", card: $" . number_format($cardAmount, 2) . ")"
                    : "Payment for deal #{$deal->id}",
                'metadata' => json_encode([
                    'platform_fee_percentage' => $platformFeePercentage,
                    'platform_fee_amount' => $platformFeeAmount,
                    'escrow_amount' => $escrowAmount,
                    'compensation_amount' => $compensationAmount,
                    'wallet_amount_used' => $walletAmountUsed,
                    'card_amount' => $cardAmount,
                    'payment_method' => $paymentMethod,
                ]),
            ]);
        }

        // Clear session data
        $session->forget([
            'deal_type', 'platforms', 'compensation_amount', 'deadline', 'deadline_time', 
            'frequency', 'notes', 'attachments', 'contract_text', 'contract_signed',
            'payment_method', 'payment_method_id', 'card_payment_method_id', 'wallet_amount_used', 'card_amount',
            'platform_fee_percentage', 'platform_fee_amount', 'platform_fee_type', 'platform_fee_value',
            'escrow_amount', 'total_amount', 'payment_intent_id', 'payment_status', 'athlete_email',
            'resuming_draft_id'
        ]);

        return redirect()->route('deals.success', $deal);
    }

    public function success(Deal $deal)
    {
        // Ensure the deal belongs to the authenticated user
        if ($deal->user_id !== Auth::id()) {
            abort(403);
        }

        // Load athlete and messages relationships for deliverables and revision checks
        $deal->load(['athlete', 'messages']);

        return view('deals.success', [
            'deal' => $deal,
        ]);
    }

    /**
     * Save deal as draft (without payment)
     */
    public function saveDraft(Request $request)
    {
        $session = $request->session();
        
        // Validate minimum required data
        if (!$session->has('deal_type') || !$session->has('compensation_amount') || !$session->has('deadline')) {
            return redirect()->route('deals.create')->withErrors(['error' => 'Incomplete deal information. Please fill in all required fields.']);
        }

        // Get all session data
        $dealType = $session->get('deal_type');
        $compensationAmount = $session->get('compensation_amount');
        $deadline = $session->get('deadline');
        $deadlineTime = $session->get('deadline_time');
        $frequency = $session->get('frequency', 'one-time');
        $platforms = $session->get('platforms', []);
        $notes = $session->get('notes');
        $attachments = $session->get('attachments', []);
        $contractText = $session->get('contract_text');
        $contractSigned = $session->get('contract_signed', false);
        $athleteEmail = $session->get('athlete_email');

        // Calculate payment breakdown
        $smbFee = \App\Models\PlatformSetting::getSMBPlatformFee();
        if ($smbFee['type'] === 'percentage') {
            $platformFeeAmount = round($compensationAmount * ($smbFee['value'] / 100), 2);
            $platformFeePercentage = $smbFee['value'];
        } else {
            $platformFeeAmount = round($smbFee['value'], 2);
            $platformFeePercentage = null;
        }
        $escrowAmount = round($compensationAmount, 2);
        $totalAmount = round($compensationAmount + $platformFeeAmount, 2);

        // Create or update draft deal
        $draft = Deal::where('user_id', Auth::id())
            ->where('status', 'draft')
            ->where('deal_type', $dealType)
            ->latest()
            ->first();

        if ($draft) {
            // Update existing draft
            $draft->update([
                'platforms' => $platforms,
                'compensation_amount' => $compensationAmount,
                'platform_fee_percentage' => $platformFeePercentage,
                'platform_fee_amount' => $platformFeeAmount,
                'escrow_amount' => $escrowAmount,
                'total_amount' => $totalAmount,
                'deadline' => $deadline,
                'deadline_time' => $deadlineTime ? ($deadlineTime . ':00') : null,
                'frequency' => $frequency,
                'notes' => $notes ?? null,
                'attachments' => !empty($attachments) ? $attachments : null,
                'contract_text' => $contractText,
                'contract_signed' => $contractSigned,
                'contract_signed_at' => $contractSigned ? now() : null,
            ]);
        } else {
            // Create new draft
            $draft = Deal::create([
                'user_id' => Auth::id(),
                'deal_type' => $dealType,
                'platforms' => $platforms,
                'compensation_amount' => $compensationAmount,
                'platform_fee_percentage' => $platformFeePercentage,
                'platform_fee_amount' => $platformFeeAmount,
                'escrow_amount' => $escrowAmount,
                'total_amount' => $totalAmount,
                'deadline' => $deadline,
                'deadline_time' => $deadlineTime ? ($deadlineTime . ':00') : null,
                'frequency' => $frequency,
                'notes' => $notes ?? null,
                'attachments' => !empty($attachments) ? $attachments : null,
                'contract_text' => $contractText,
                'contract_signed' => $contractSigned,
                'contract_signed_at' => $contractSigned ? now() : null,
                'status' => 'draft',
                'payment_status' => null,
            ]);

            // Create draft invitation if email provided
            if ($athleteEmail) {
                $athlete = \App\Models\Athlete::where('email', $athleteEmail)->first();
                \App\Models\DealInvitation::create([
                    'deal_id' => $draft->id,
                    'athlete_email' => $athleteEmail,
                    'athlete_id' => $athlete?->id,
                    'status' => 'pending',
                ]);
            }
        }

        return redirect()->route('deals.index')->with('success', 'Deal saved as draft. You can resume it later after adding a payment method.');
    }

    /**
     * Resume a draft deal
     */
    public function resumeDraft(Deal $deal)
    {
        // Ensure the deal belongs to the authenticated user and is a draft
        if ($deal->user_id !== Auth::id() || $deal->status !== 'draft') {
            abort(403);
        }

        $session = session();

        // Load draft data into session
        $session->put('deal_type', $deal->deal_type);
        $session->put('platforms', $deal->platforms ?? []);
        $session->put('compensation_amount', $deal->compensation_amount);
        $session->put('deadline', $deal->deadline->format('Y-m-d'));
        $session->put('deadline_time', $deal->deadline_time ? substr($deal->deadline_time, 0, 5) : null);
        $session->put('frequency', $deal->frequency ?? 'one-time');
        $session->put('notes', $deal->notes ?? '');
        $session->put('attachments', $deal->attachments ?? []);
        $session->put('contract_text', $deal->contract_text ?? '');
        $session->put('contract_signed', $deal->contract_signed ?? false);
        $session->put('platform_fee_percentage', $deal->platform_fee_percentage);
        $session->put('platform_fee_amount', $deal->platform_fee_amount);
        $session->put('escrow_amount', $deal->escrow_amount);
        $session->put('total_amount', $deal->total_amount);

        // Get athlete email from invitation if exists
        $invitation = $deal->invitations()->first();
        if ($invitation && $invitation->athlete_email) {
            $session->put('athlete_email', $invitation->athlete_email);
        }

        // Mark that we're resuming a draft (so store method can update instead of create)
        $session->put('resuming_draft_id', $deal->id);

        // Redirect to payment step (where they can proceed to review)
        return redirect()->route('deals.create.payment');
    }

    /**
     * Show deal by token (public route for athletes)
     * Now uses invitation token for identity guardrails
     */
    public function showByToken(string $token)
    {
        // First try to find by invitation token (new system)
        $invitation = \App\Models\DealInvitation::where('token', $token)->first();
        
        if ($invitation) {
            $deal = $invitation->deal;
            
            // Check if invitation is valid
            if (!$invitation->isValid()) {
                if ($invitation->status === 'accepted') {
                    abort(404, 'This invitation has already been accepted.');
                }
                if ($invitation->status === 'expired') {
                    abort(404, 'This invitation has expired.');
                }
            }

            // Check if deal is cancelled
            if ($deal->status === 'cancelled') {
                abort(404, 'This deal has been cancelled.');
            }

            // Check if athlete is logged in and matches invitation
            $currentAthlete = Auth::guard('athlete')->user();
            $canAccept = false;
            $identityMismatch = false;

            if ($currentAthlete) {
                // Check if logged-in athlete matches invitation
                if ($invitation->athlete_id && $invitation->athlete_id === $currentAthlete->id) {
                    $canAccept = true;
                } elseif ($invitation->athlete_email && $invitation->matchesAthleteEmail($currentAthlete->email)) {
                    $canAccept = true;
                } else {
                    $identityMismatch = true;
                }
            } elseif ($invitation->athlete_email) {
                // Not logged in, but invitation has email - they need to log in
                $canAccept = false;
            }

            return view('deals.show', [
                'deal' => $deal,
                'invitation' => $invitation,
                'canAccept' => $canAccept,
                'identityMismatch' => $identityMismatch,
            ]);
        }

        // Fallback: Try to find by deal token (backward compatibility)
        $deal = Deal::where('token', $token)->first();
        
        if ($deal) {
            // Check if deal is cancelled
            if ($deal->status === 'cancelled') {
                abort(404, 'This deal has been cancelled.');
            }

            return view('deals.show', [
                'deal' => $deal,
                'invitation' => null,
                'canAccept' => true, // Legacy deals without invitations
                'identityMismatch' => false,
            ]);
        }

        abort(404, 'Deal invitation not found.');
    }

    public function edit(Deal $deal)
    {
        // Ensure the deal belongs to the authenticated user
        if ($deal->user_id !== Auth::id()) {
            abort(403);
        }

        // Prevent editing completed or cancelled deals
        if (in_array($deal->status, ['completed', 'cancelled']) || $deal->released_at) {
            return redirect()->route('deals.index')
                ->withErrors(['error' => 'Cannot edit a deal that is completed or cancelled.']);
        }

        $dealTypes = Deal::getDealTypes();
        
        return view('deals.edit', [
            'deal' => $deal,
            'dealTypes' => $dealTypes,
            'platforms' => Deal::getPlatforms(),
        ]);
    }

    public function update(Request $request, Deal $deal)
    {
        // Ensure the deal belongs to the authenticated user
        if ($deal->user_id !== Auth::id()) {
            abort(403);
        }

        // Prevent editing completed or cancelled deals
        if (in_array($deal->status, ['completed', 'cancelled']) || $deal->released_at) {
            return redirect()->route('deals.index')
                ->withErrors(['error' => 'Cannot edit a deal that is completed or cancelled.']);
        }

        $dealTypes = Deal::getDealTypes();
        $dealType = $deal->deal_type;
        $requiresPlatforms = $dealTypes[$dealType]['requires_platforms'] ?? false;

        $validated = $request->validate([
            'compensation_amount' => ['required', 'numeric', 'min:0.01'],
            'deadline' => ['required', 'date', 'after:today'],
            'notes' => ['nullable', 'string', 'max:5000'],
            'status' => ['required', 'in:pending,accepted,completed,cancelled'],
        ]);
        
        // Prevent changing compensation amount or requirements for completed deals
        if ($deal->status === 'completed') {
            unset($validated['compensation_amount']);
            unset($validated['notes']);
        }

        if ($requiresPlatforms) {
            $request->validate([
                'platforms' => ['required', 'array', 'min:1'],
                'platforms.*' => ['required', Rule::in(array_keys(Deal::getPlatforms()))],
            ]);
            $validated['platforms'] = $request->platforms;
        }

        $deal->update($validated);

        return redirect()->route('deals.index')
            ->with('success', 'Deal updated successfully.');
    }

    public function destroy(Deal $deal)
    {
        // Ensure the deal belongs to the authenticated user
        if ($deal->user_id !== Auth::id()) {
            abort(403);
        }

        // Prevent deletion if athlete has submitted work
        // Once work is submitted, the deal cannot be deleted to protect both parties
        if ($deal->completed_at || $deal->status === 'completed' || !empty($deal->deliverables)) {
            return redirect()->back()->withErrors([
                'error' => 'Cannot delete deal. The athlete has already submitted their work. You must either approve or request changes instead.'
            ]);
        }

        // If deal has escrowed funds, return them before deletion
        if ($deal->shouldReturnEscrow()) {
            try {
                DB::beginTransaction();

                $escrowAmount = (float) $deal->escrow_amount;
                $platformFeeAmount = (float) ($deal->platform_fee_amount ?? 0);
                $totalToReturn = $escrowAmount + $platformFeeAmount;

                // Return funds to SMB wallet
                $deal->user->addToWallet($totalToReturn, 'refund', $deal->id, [
                    'escrow_amount' => $escrowAmount,
                    'platform_fee_amount' => $platformFeeAmount,
                    'reason' => 'Deal deleted - escrow returned',
                ]);

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                return redirect()->route('deals.index')
                    ->withErrors(['error' => 'Failed to return escrowed funds. Please contact support.']);
            }
        }

        $deal->delete();

        return redirect()->route('deals.index')
            ->with('success', 'Deal deleted successfully.' . ($deal->shouldReturnEscrow() ? ' Escrowed funds have been returned to your wallet.' : ''));
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'deal_ids' => ['required', 'array'],
            'deal_ids.*' => ['required', 'exists:deals,id'],
        ]);

        // Ensure all deals belong to the authenticated user
        $dealIds = $request->deal_ids;
        $deals = Deal::where('user_id', Auth::id())
            ->whereIn('id', $dealIds)
            ->get();

        if ($deals->count() !== count($dealIds)) {
            return redirect()->route('deals.index')
                ->withErrors(['error' => 'Some deals could not be found or you do not have permission to delete them.']);
        }

        // Check if any deals have submitted work (cannot be deleted)
        $dealsWithSubmittedWork = $deals->filter(function ($deal) {
            return $deal->completed_at || $deal->status === 'completed' || !empty($deal->deliverables);
        });

        if ($dealsWithSubmittedWork->count() > 0) {
            return redirect()->route('deals.index')
                ->withErrors([
                    'error' => 'Cannot delete ' . $dealsWithSubmittedWork->count() . ' deal(s). The athlete has already submitted their work. You must either approve or request changes instead.'
                ]);
        }

        Deal::whereIn('id', $dealIds)->delete();

        return redirect()->route('deals.index')
            ->with('success', count($dealIds) . ' deal(s) deleted successfully.');
    }
}
