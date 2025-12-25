<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class Athlete extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'stripe_account_id',
        'profile_token',
        'username',
        'profile_photo',
        'sport',
        'school',
        'athlete_level',
        'instagram_handle',
        'tiktok_handle',
        'twitter_handle',
        'youtube_handle',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Boot the model and generate profile token on creation
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($athlete) {
            if (empty($athlete->profile_token)) {
                $athlete->profile_token = Str::random(32);
            }
        });
    }

    /**
     * Get the athlete's public profile URL
     */
    public function getProfileUrlAttribute(): string
    {
        if ($this->username) {
            return url('/a/' . $this->username);
        }
        return url('/a/' . $this->profile_token);
    }

    /**
     * Get completed deals for this athlete (status = completed)
     */
    public function completedDeals()
    {
        return $this->hasMany(Deal::class, 'athlete_id')
            ->where('status', 'completed')
            ->orderBy('released_at', 'desc')
            ->orderBy('created_at', 'desc');
    }

    /**
     * Get released deals for this athlete (funds released from escrow)
     */
    public function releasedDeals()
    {
        return $this->hasMany(Deal::class, 'athlete_id')
            ->whereNotNull('released_at')
            ->where('payment_status', 'paid')
            ->orderBy('released_at', 'desc');
    }

    /**
     * Get all deals for this athlete
     */
    public function deals()
    {
        return $this->hasMany(Deal::class, 'athlete_id');
    }

    /**
     * Get unique businesses this athlete has worked with
     */
    public function businessesWorkedWith()
    {
        return $this->completedDeals()
            ->with('user')
            ->get()
            ->pluck('user')
            ->unique('id')
            ->values();
    }

    /**
     * Get deal types this athlete has completed
     */
    public function getCompletedDealTypesAttribute(): array
    {
        return $this->completedDeals()
            ->pluck('deal_type')
            ->unique()
            ->values()
            ->toArray();
    }

    /**
     * Get payment methods for this athlete
     */
    public function paymentMethods()
    {
        return $this->hasMany(\App\Models\AthletePaymentMethod::class);
    }

    /**
     * Get default payment method
     */
    public function defaultPaymentMethod()
    {
        return $this->hasOne(\App\Models\AthletePaymentMethod::class)->where('is_default', true);
    }

    /**
     * Get withdrawals for this athlete
     */
    public function withdrawals()
    {
        return $this->hasMany(\App\Models\AthleteWithdrawal::class);
    }

    /**
     * Get available balance (only released funds, not escrow)
     * Athletes can only withdraw funds that have been released from escrow
     */
    public function getAvailableBalanceAttribute(): float
    {
        // Only count deals where funds have been released (released_at is not null)
        // Use athlete_net_payout if available, otherwise calculate from escrow_amount
        $totalReleased = $this->releasedDeals()
            ->get()
            ->sum(function($deal) {
                return $deal->athlete_net_payout ?? 
                       ($deal->escrow_amount - ($deal->escrow_amount * ($deal->athlete_fee_percentage ?? 0) / 100));
            });
        
        $pendingWithdrawals = $this->withdrawals()
            ->whereIn('status', ['pending', 'processing'])
            ->sum('amount');
        
        return max(0, $totalReleased - $pendingWithdrawals);
    }

    /**
     * Get total escrow balance (funds in escrow, not yet released)
     */
    public function getEscrowBalanceAttribute(): float
    {
        return $this->deals()
            ->where('payment_status', 'paid')
            ->whereNull('released_at')
            ->where('status', '!=', 'cancelled')
            ->sum('escrow_amount');
    }
}
