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
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('type')->default('card'); // card, bank_account, etc.
            $table->string('provider')->default('stripe'); // stripe, paypal, etc.
            $table->string('provider_payment_method_id'); // ID from payment provider
            $table->string('last_four')->nullable(); // Last 4 digits of card/account
            $table->string('brand')->nullable(); // visa, mastercard, etc.
            $table->string('exp_month')->nullable();
            $table->string('exp_year')->nullable();
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};
