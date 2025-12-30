<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Business model - alias for User model
 * Businesses are stored in the users table but use a separate password broker
 */
class Business extends User
{
    // Business uses the same table and structure as User
    // This allows us to have a separate password broker for businesses
}

