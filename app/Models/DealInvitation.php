<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class DealInvitation extends Model
{
    protected $fillable = [
        'deal_id',
        'token',
        'athlete_email',
        'athlete_id',
        'status',
        'expires_at',
        'accepted_at',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'accepted_at' => 'datetime',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($invitation) {
            if (empty($invitation->token)) {
                $invitation->token = Str::random(64);
            }
        });
    }

    public function deal(): BelongsTo
    {
        return $this->belongsTo(Deal::class);
    }

    public function athlete(): BelongsTo
    {
        return $this->belongsTo(Athlete::class);
    }

    /**
     * Check if invitation is valid (not expired, not accepted)
     */
    public function isValid(): bool
    {
        if ($this->status !== 'pending') {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        return true;
    }

    /**
     * Check if the given athlete email matches this invitation
     */
    public function matchesAthleteEmail(string $email): bool
    {
        return strtolower(trim($this->athlete_email ?? '')) === strtolower(trim($email));
    }

    /**
     * Check if the given athlete ID matches this invitation
     */
    public function matchesAthleteId(?int $athleteId): bool
    {
        if (!$athleteId) {
            return false;
        }

        return $this->athlete_id === $athleteId;
    }

    /**
     * Mark invitation as accepted
     */
    public function markAsAccepted(): void
    {
        $this->update([
            'status' => 'accepted',
            'accepted_at' => now(),
        ]);
    }
}
