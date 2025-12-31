<?php

namespace App\Models;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Business model - businesses are stored in the users table
 * but use a separate password broker (businesses) for authentication
 */
class Business extends Authenticatable
{
    use Notifiable;

    /**
     * Explicitly set the table name to 'users'
     * since businesses are stored in the users table
     */
    protected $table = 'users';

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

    /**
     * Send the password reset notification.
     * This forces Laravel to send a reset email for business accounts
     * instead of resolving to the default user broker.
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }
}

