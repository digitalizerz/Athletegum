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
        Schema::create('athlete_payment_methods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('athlete_id')->constrained()->onDelete('cascade');
            $table->string('type'); // bank_account, stripe, paypal
            $table->string('provider')->default('stripe'); // stripe, paypal, bank
            $table->string('provider_account_id')->nullable(); // Account ID from provider
            $table->string('account_holder_name')->nullable();
            $table->string('account_number')->nullable(); // Last 4 digits or masked
            $table->string('routing_number')->nullable(); // For bank accounts
            $table->string('bank_name')->nullable();
            $table->string('email')->nullable(); // For PayPal
            $table->string('currency')->default('USD');
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->json('metadata')->nullable(); // Additional provider-specific data
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('athlete_payment_methods');
    }
};
