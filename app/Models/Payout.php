<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payout extends Model
{
    protected $fillable = [
        'deal_id',
        'athlete_id',
        'stripe_transfer_id',
        'amount',
        'currency',
        'status',
        'released_by_admin_id',
        'released_at',
        'idempotency_key',
        'error_message',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'released_at' => 'datetime',
        ];
    }

    public function deal(): BelongsTo
    {
        return $this->belongsTo(Deal::class);
    }

    public function athlete(): BelongsTo
    {
        return $this->belongsTo(Athlete::class);
    }

    public function releasedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'released_by_admin_id');
    }

    /**
     * Get human-readable payout status label for athletes
     * Status is derived from payout status and deal transfer status
     * 
     * @return string 'Processing' or 'Completed'
     */
    public function getPayoutStatusLabelAttribute(): string
    {
        // Load deal relationship if not already loaded
        if (!$this->relationLoaded('deal')) {
            $this->load('deal');
        }

        $deal = $this->deal;
        
        // If payout is completed, it's completed
        if ($this->status === 'completed') {
            return 'Completed';
        }

        // If deal transfer status is 'paid', payout is completed
        if ($deal && $deal->stripe_transfer_status === 'paid') {
            return 'Completed';
        }

        // Otherwise, it's processing (transfer created but not yet deposited)
        return 'Processing';
    }
}
