<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AthleteWithdrawal extends Model
{
    use HasFactory;

    protected $fillable = [
        'athlete_id',
        'athlete_payment_method_id',
        'amount',
        'currency',
        'status',
        'provider_transaction_id',
        'failure_reason',
        'processed_at',
        'metadata',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'processed_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function athlete(): BelongsTo
    {
        return $this->belongsTo(Athlete::class);
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(AthletePaymentMethod::class, 'athlete_payment_method_id');
    }
}
