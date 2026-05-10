<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Track how much of the business payment came from internal wallet vs Stripe card.
     * Used for payout messaging and mixed wallet+card liquidity checks.
     */
    public function up(): void
    {
        Schema::table('deals', function (Blueprint $table) {
            $table->decimal('wallet_amount_applied', 12, 2)->nullable()->after('total_amount');
            $table->decimal('card_amount_charged', 12, 2)->nullable()->after('wallet_amount_applied');
        });
    }

    public function down(): void
    {
        Schema::table('deals', function (Blueprint $table) {
            $table->dropColumn(['wallet_amount_applied', 'card_amount_charged']);
        });
    }
};
