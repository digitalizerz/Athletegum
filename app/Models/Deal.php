<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Deal extends Model
{
    protected $fillable = [
        'user_id',
        'athlete_id',
        'payment_method_id',
        'deal_type',
        'platforms',
        'compensation_amount',
        'platform_fee_percentage',
        'platform_fee_amount',
        'escrow_amount',
        'total_amount',
        'wallet_amount_applied',
        'card_amount_charged',
        'deadline',
        'deadline_time',
        'frequency',
        'notes',
        'attachments',
        'status',
        'is_approved',
        'approved_at',
        'approval_notes',
        'payment_status',
        'awaiting_funds',
        'payout_auto_retry_requested_at',
        'payment_intent_id',
        'stripe_charge_id',
        'paid_at',
        'released_at',
        'release_transaction_id',
        'stripe_transfer_id',
        'stripe_transfer_status',
        'contract_text',
        'contract_signed',
        'contract_signed_at',
        'token',
        'completion_notes',
        'deliverables',
        'completed_at',
        'athlete_fee_percentage',
        'athlete_fee_amount',
        'athlete_net_payout',
    ];

    protected function casts(): array
    {
        return [
            'compensation_amount' => 'decimal:2',
            'platform_fee_percentage' => 'decimal:2',
            'platform_fee_amount' => 'decimal:2',
            'escrow_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'wallet_amount_applied' => 'decimal:2',
            'card_amount_charged' => 'decimal:2',
            'athlete_fee_percentage' => 'decimal:2',
            'athlete_fee_amount' => 'decimal:2',
            'athlete_net_payout' => 'decimal:2',
            'deadline' => 'date',
            'platforms' => 'array',
            'attachments' => 'array',
            'contract_signed' => 'boolean',
            'contract_signed_at' => 'datetime',
            'is_approved' => 'boolean',
            'approved_at' => 'datetime',
            'awaiting_funds' => 'boolean',
            'payout_auto_retry_requested_at' => 'datetime',
            'paid_at' => 'datetime',
            'released_at' => 'datetime',
            'deliverables' => 'array',
            'completed_at' => 'datetime',
        ];
    }

    public static function getFrequencyOptions(): array
    {
        return [
            'one-time' => 'One-time',
            'daily' => 'Daily',
            'weekly' => 'Weekly',
            'bi-weekly' => 'Bi-weekly',
            'monthly' => 'Monthly',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($deal) {
            if (empty($deal->token)) {
                $deal->token = Str::random(32);
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function athlete(): BelongsTo
    {
        return $this->belongsTo(Athlete::class);
    }

    public function payouts(): HasMany
    {
        return $this->hasMany(Payout::class);
    }

    public function invitations()
    {
        return $this->hasMany(DealInvitation::class);
    }

    public function activeInvitation()
    {
        return $this->hasOne(DealInvitation::class)->where('status', 'pending');
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class)->orderBy('created_at', 'asc');
    }

    /**
     * Split a deal compensation amount using admin SMB fee + athlete fee settings.
     * Business pays compensation only; platform + athlete fees come out of that amount.
     */
    public static function feeSplitFromCompensation(float $compensation): array
    {
        $smbFee = PlatformSetting::getSMBPlatformFee();

        if ($smbFee['type'] === 'percentage') {
            $platformFeeAmount = round($compensation * ($smbFee['value'] / 100), 2);
            $platformFeePercentage = $smbFee['value'];
        } else {
            $platformFeeAmount = round(min((float) $smbFee['value'], $compensation), 2);
            $platformFeePercentage = null;
        }

        $athletePct = PlatformSetting::getAthletePlatformFeePercentage();
        $athleteFeeAmount = round($compensation * ($athletePct / 100), 2);
        $athleteNetPayout = max(0.0, round($compensation - $platformFeeAmount - $athleteFeeAmount, 2));

        return [
            'platform_fee_type' => $smbFee['type'],
            'platform_fee_percentage' => $platformFeePercentage,
            'platform_fee_value' => $smbFee['value'],
            'platform_fee_amount' => $platformFeeAmount,
            'athlete_fee_percentage' => $athletePct,
            'athlete_fee_amount' => $athleteFeeAmount,
            'athlete_net_payout' => $athleteNetPayout,
            'escrow_amount' => round($compensation, 2),
            'total_amount' => round($compensation, 2),
        ];
    }

    /**
     * Amounts used when releasing escrow (prefers values stored on the deal at creation).
     */
    public function getPayoutAmountsForRelease(): array
    {
        $dealAmount = (float) $this->compensation_amount;
        $platformFeeAmount = (float) ($this->platform_fee_amount ?? 0);
        $athletePct = (float) ($this->athlete_fee_percentage ?? PlatformSetting::getAthletePlatformFeePercentage());
        $athleteFeeAmount = $this->athlete_fee_amount !== null
            ? (float) $this->athlete_fee_amount
            : round($dealAmount * ($athletePct / 100), 2);
        $net = $this->athlete_net_payout !== null
            ? (float) $this->athlete_net_payout
            : max(0.0, round($dealAmount - $platformFeeAmount - $athleteFeeAmount, 2));

        return [
            'deal_amount' => $dealAmount,
            'platform_fee_amount' => $platformFeeAmount,
            'athlete_fee_percentage' => $athletePct,
            'athlete_fee_amount' => $athleteFeeAmount,
            'athlete_net_payout' => max(0.0, round($net, 2)),
        ];
    }

    /**
     * Calculate payment breakdown using current SMB fee settings
     */
    public function calculatePaymentBreakdown(): array
    {
        $compensation = (float) $this->compensation_amount;
        $split = self::feeSplitFromCompensation($compensation);

        return [
            'compensation_amount' => $compensation,
            'platform_fee_type' => $split['platform_fee_type'],
            'platform_fee_percentage' => $split['platform_fee_percentage'],
            'platform_fee_value' => $split['platform_fee_value'],
            'platform_fee_amount' => $split['platform_fee_amount'],
            'athlete_fee_percentage' => $split['athlete_fee_percentage'],
            'athlete_fee_amount' => $split['athlete_fee_amount'],
            'athlete_net_payout' => $split['athlete_net_payout'],
            'escrow_amount' => $split['escrow_amount'],
            'total_amount' => $split['total_amount'],
        ];
    }

    public static function getDealTypes(): array
    {
        return [
            'social_post' => [
                'name' => 'Social Post',
                'description' => 'Post on social media',
                'icon' => '📸',
                'requires_platforms' => true,
            ],
            'short_video' => [
                'name' => 'Short Video',
                'description' => 'Create a short video',
                'icon' => '🎥',
                'requires_platforms' => true,
            ],
            'in_person_appearance' => [
                'name' => 'In-Person Appearance',
                'description' => 'Come to your location',
                'icon' => '🎤',
                'requires_platforms' => false,
            ],
            'monthly_partnership' => [
                'name' => 'Monthly Partnership',
                'description' => 'Ongoing for one month',
                'icon' => '⭐',
                'requires_platforms' => false,
            ],
            'product_review' => [
                'name' => 'Product Review',
                'description' => 'Review your product',
                'icon' => '📦',
                'requires_platforms' => false,
            ],
            'custom' => [
                'name' => 'Custom Deal',
                'description' => 'Custom arrangement',
                'icon' => '✨',
                'requires_platforms' => false,
            ],
        ];
    }

    public static function getPlatforms(): array
    {
        return [
            'instagram' => 'Instagram',
            'tiktok' => 'TikTok',
            'x' => 'X (Twitter)',
            'youtube' => 'YouTube',
            'facebook' => 'Facebook',
        ];
    }

    /**
     * Check if funds are currently in escrow
     */
    public function isInEscrow(): bool
    {
        return ($this->payment_status === 'paid' || $this->payment_status === 'paid_escrowed')
            && $this->released_at === null 
            && !in_array($this->status, ['cancelled']);
    }

    /**
     * Get escrow status description (short, clear format)
     */
    public function getEscrowStatus(): string
    {
        if ($this->payment_status !== 'paid' && $this->payment_status !== 'paid_escrowed') {
            return 'Not Paid';
        }

        if ($this->released_at) {
            return 'Released';
        }

        if ($this->status === 'cancelled') {
            return 'Refunded';
        }

        // Check if funds are still pending in Stripe
        if ($this->awaiting_funds) {
            return 'Payout Pending Clearing';
        }

        // Payment release = Final Approval - check both released_at and is_approved
        if ($this->released_at || $this->is_approved) {
            return 'Ready to Release';
        }

        return 'Awaiting Approval';
    }

    /**
     * Get escrow status badge class
     */
    public function getEscrowStatusBadge(): string
    {
        if ($this->payment_status !== 'paid') {
            return 'badge-ghost';
        }

        if ($this->released_at) {
            return 'badge-success';
        }

        if ($this->status === 'cancelled') {
            return 'badge-warning';
        }

        // Payment release = Final Approval - check both released_at and is_approved
        if ($this->released_at || $this->is_approved) {
            return 'badge-info';
        }

        // In escrow, awaiting approval
        return 'badge-warning';
    }

    /**
     * Check if deal can be released (when completed by athlete or approved by SMB)
     * Note: Release will be blocked if awaiting_funds is true (funds not yet available in Stripe)
     */
    public function canBeReleased(): bool
    {
        return ($this->payment_status === 'paid' || $this->payment_status === 'paid_escrowed')
            && $this->released_at === null
            && $this->status !== 'cancelled'
            && ($this->status === 'completed' || $this->is_approved)
            && !$this->awaiting_funds;
    }

    /**
     * Check if escrowed funds should be returned (cancelled or expired)
     */
    public function shouldReturnEscrow(): bool
    {
        return $this->payment_status === 'paid' 
            && $this->released_at === null
            && ($this->status === 'cancelled' || $this->isExpired());
    }

    /**
     * Check if deal has expired past deadline
     */
    public function isExpired(): bool
    {
        if (!$this->deadline) {
            return false;
        }

        // Deal is expired if past deadline and not approved/released (payment release = approval)
        return now()->isAfter($this->deadline) && !$this->is_approved && !$this->released_at;
    }

    /**
     * Check if revisions have been requested for this deal
     * A deal has pending revisions if:
     * - Status is 'accepted' (deal was reset after submission)
     * - There's a recent system message about revision request
     * - The deal has been completed before (has completed_at)
     */
    public function hasPendingRevisions(): bool
    {
        // If status is not 'accepted', no pending revisions
        if ($this->status !== 'accepted') {
            return false;
        }

        // If deal was never completed, it can't have revisions requested
        if (!$this->completed_at) {
            return false;
        }

        // Check if there's a revision request message after the last completion
        $revisionMessage = $this->messages()
            ->where('is_system_message', true)
            ->where('content', 'like', 'Business requested revisions:%')
            ->where('created_at', '>=', $this->completed_at)
            ->latest()
            ->first();

        return $revisionMessage !== null;
    }

    /**
     * Get the most recent revision request message
     */
    public function getLatestRevisionRequest(): ?Message
    {
        return $this->messages()
            ->where('is_system_message', true)
            ->where('content', 'like', 'Business requested revisions:%')
            ->latest()
            ->first();
    }
}
