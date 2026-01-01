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
            $table->string('stripe_transfer_id')->nullable()->after('release_transaction_id');
            $table->string('stripe_transfer_status')->nullable()->after('stripe_transfer_id'); // pending, paid, failed, reversed
            $table->index('stripe_transfer_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deals', function (Blueprint $table) {
            $table->dropIndex(['stripe_transfer_id']);
            $table->dropColumn(['stripe_transfer_id', 'stripe_transfer_status']);
        });
    }
};
