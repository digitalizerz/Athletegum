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
        Schema::create('payouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('deal_id')->constrained()->onDelete('cascade');
            $table->foreignId('athlete_id')->constrained('athletes')->onDelete('cascade');
            $table->string('stripe_transfer_id')->nullable(); // tr_xxx
            $table->decimal('amount', 10, 2); // Amount in dollars
            $table->string('currency', 3)->default('usd');
            $table->string('status')->default('pending'); // pending, completed, failed, cancelled
            $table->foreignId('released_by_admin_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('released_at')->nullable();
            $table->string('idempotency_key')->unique(); // Prevent double payouts
            $table->text('error_message')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('deal_id');
            $table->index('athlete_id');
            $table->index('stripe_transfer_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payouts');
    }
};
