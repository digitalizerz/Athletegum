<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_superadmin',
        'wallet_balance',
        'stripe_customer_id',
        'business_name',
        'business_information',
        'phone',
        'owner_principal',
        'address_line1',
        'address_line2',
        'city',
        'state',
        'postal_code',
        'country',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
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
            'is_superadmin' => 'boolean',
            'wallet_balance' => 'decimal:2',
        ];
    }

    public function deals(): HasMany
    {
        return $this->hasMany(Deal::class);
    }

    public function paymentMethods(): HasMany
    {
        return $this->hasMany(PaymentMethod::class);
    }

    public function defaultPaymentMethod()
    {
        return $this->hasOne(PaymentMethod::class)->where('is_default', true);
    }

    public function walletTransactions(): HasMany
    {
        return $this->hasMany(WalletTransaction::class);
    }

    /**
     * Add funds to wallet
     */
    public function addToWallet(float $amount, string $type = 'deposit', ?int $dealId = null, array $metadata = []): WalletTransaction
    {
        $balanceBefore = (float) $this->wallet_balance;
        $balanceAfter = $balanceBefore + $amount;

        $transaction = WalletTransaction::create([
            'user_id' => $this->id,
            'type' => $type,
            'status' => 'completed',
            'amount' => $amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $balanceAfter,
            'deal_id' => $dealId,
            'metadata' => !empty($metadata) ? json_encode($metadata) : null,
        ]);

        $this->wallet_balance = $balanceAfter;
        $this->save();

        return $transaction;
    }

    /**
     * Deduct funds from wallet
     */
    public function deductFromWallet(float $amount, string $type = 'payment', ?int $dealId = null, array $metadata = []): WalletTransaction
    {
        $balanceBefore = (float) $this->wallet_balance;
        
        if ($balanceBefore < $amount) {
            throw new \Exception('Insufficient wallet balance.');
        }

        $balanceAfter = $balanceBefore - $amount;

        $transaction = WalletTransaction::create([
            'user_id' => $this->id,
            'type' => $type,
            'status' => 'completed',
            'amount' => -$amount, // Negative for deduction
            'balance_before' => $balanceBefore,
            'balance_after' => $balanceAfter,
            'deal_id' => $dealId,
            'metadata' => !empty($metadata) ? json_encode($metadata) : null,
        ]);

        $this->wallet_balance = $balanceAfter;
        $this->save();

        return $transaction;
    }
}
