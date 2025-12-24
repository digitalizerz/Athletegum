<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AthletePaymentMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'athlete_id',
        'type',
        'provider',
        'provider_account_id',
        'account_holder_name',
        'account_number',
        'routing_number',
        'bank_name',
        'email',
        'currency',
        'is_default',
        'is_active',
        'metadata',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_active' => 'boolean',
        'metadata' => 'array',
    ];

    public function athlete(): BelongsTo
    {
        return $this->belongsTo(Athlete::class);
    }

    public function withdrawals()
    {
        return $this->hasMany(AthleteWithdrawal::class);
    }

    /**
     * Get display name for the payment method
     */
    public function getDisplayNameAttribute(): string
    {
        if ($this->type === 'stripe') {
            return 'Stripe Account' . ($this->provider_account_id ? ' • ' . substr($this->provider_account_id, 0, 8) : '');
        }
        return ucfirst($this->type);
    }
}
