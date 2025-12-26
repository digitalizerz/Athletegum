<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add stripe_customer_id to users table if it doesn't exist
        if (!Schema::hasColumn('users', 'stripe_customer_id')) {
            Schema::table('users', function (Blueprint $table) {
                // Try to add after wallet_balance, but if that column doesn't exist, just add it
                if (Schema::hasColumn('users', 'wallet_balance')) {
                    $table->string('stripe_customer_id')->nullable()->after('wallet_balance');
                } else {
                    $table->string('stripe_customer_id')->nullable();
                }
            });
        }

        // Add stripe_account_id to athletes table if it doesn't exist
        if (!Schema::hasColumn('athletes', 'stripe_account_id')) {
            Schema::table('athletes', function (Blueprint $table) {
                $table->string('stripe_account_id')->nullable()->after('email');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'stripe_customer_id')) {
                $table->dropColumn('stripe_customer_id');
            }
        });

        Schema::table('athletes', function (Blueprint $table) {
            if (Schema::hasColumn('athletes', 'stripe_account_id')) {
                $table->dropColumn('stripe_account_id');
            }
        });
    }
};
