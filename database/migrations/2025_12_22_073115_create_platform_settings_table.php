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
        Schema::create('platform_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('string'); // string, number, boolean, json
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Insert default settings
        DB::table('platform_settings')->insert([
            [
                'key' => 'stripe_connected',
                'value' => 'false',
                'type' => 'boolean',
                'description' => 'Whether Stripe account is connected',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'stripe_account_id',
                'value' => null,
                'type' => 'string',
                'description' => 'Stripe account ID',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'stripe_publishable_key',
                'value' => null,
                'type' => 'string',
                'description' => 'Stripe publishable key',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'stripe_secret_key',
                'value' => null,
                'type' => 'string',
                'description' => 'Stripe secret key (encrypted)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'smb_platform_fee_type',
                'value' => 'percentage',
                'type' => 'string',
                'description' => 'SMB platform fee type: percentage or fixed',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'smb_platform_fee_value',
                'value' => '10',
                'type' => 'number',
                'description' => 'SMB platform fee value (percentage or fixed amount)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'athlete_platform_fee_percentage',
                'value' => '2.5',
                'type' => 'number',
                'description' => 'Athlete platform fee percentage',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('platform_settings');
    }
};
