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
        Schema::table('deals', function (Blueprint $table) {
            $table->foreignId('payment_method_id')->nullable()->after('user_id')->constrained('payment_methods')->onDelete('set null');
            $table->decimal('platform_fee_percentage', 5, 2)->default(5.00)->after('compensation_amount');
            $table->decimal('platform_fee_amount', 10, 2)->nullable()->after('platform_fee_percentage');
            $table->decimal('escrow_amount', 10, 2)->nullable()->after('platform_fee_amount');
            $table->decimal('total_amount', 10, 2)->nullable()->after('escrow_amount');
            $table->string('payment_status')->default('pending')->after('status'); // pending, processing, paid, failed, refunded
            $table->string('payment_intent_id')->nullable()->after('payment_status'); // Stripe payment intent ID
            $table->timestamp('paid_at')->nullable()->after('payment_intent_id');
            $table->timestamp('released_at')->nullable()->after('paid_at');
            $table->string('release_transaction_id')->nullable()->after('released_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deals', function (Blueprint $table) {
            $table->dropForeign(['payment_method_id']);
            $table->dropColumn([
                'payment_method_id',
                'platform_fee_percentage',
                'platform_fee_amount',
                'escrow_amount',
                'total_amount',
                'payment_status',
                'payment_intent_id',
                'paid_at',
                'released_at',
                'release_transaction_id',
            ]);
        });
    }
};
