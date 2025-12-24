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
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('type'); // deposit, withdrawal, payment, refund
            $table->string('status')->default('pending'); // pending, completed, failed, cancelled
            $table->decimal('amount', 10, 2);
            $table->decimal('balance_before', 10, 2);
            $table->decimal('balance_after', 10, 2);
            $table->string('payment_method')->nullable(); // stripe, paypal, wallet
            $table->string('payment_provider_transaction_id')->nullable(); // Stripe/PayPal transaction ID
            $table->foreignId('deal_id')->nullable()->constrained()->onDelete('set null'); // If transaction is related to a deal
            $table->text('description')->nullable();
            $table->text('metadata')->nullable(); // JSON for additional data
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
    }
};
