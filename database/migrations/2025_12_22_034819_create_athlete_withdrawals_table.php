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
        Schema::create('athlete_withdrawals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('athlete_id')->constrained()->onDelete('cascade');
            $table->foreignId('athlete_payment_method_id')->constrained()->onDelete('restrict');
            $table->decimal('amount', 10, 2);
            $table->string('currency')->default('USD');
            $table->string('status')->default('pending'); // pending, processing, completed, failed, cancelled
            $table->string('provider_transaction_id')->nullable(); // Transaction ID from payment provider
            $table->text('failure_reason')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->json('metadata')->nullable(); // Additional transaction data
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('athlete_withdrawals');
    }
};
