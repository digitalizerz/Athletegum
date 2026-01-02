<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('subscription_plan')->default('free')->after('stripe_customer_id');
            $table->string('subscription_status')->nullable()->after('subscription_plan');
            $table->string('stripe_subscription_id')->nullable()->after('subscription_status');
        });
        
        // Set existing users to 'free' plan (default is already set in column definition, but this ensures data consistency)
        DB::table('users')->whereNull('subscription_plan')->update(['subscription_plan' => 'free']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'subscription_plan',
                'subscription_status',
                'stripe_subscription_id',
            ]);
        });
    }
};
