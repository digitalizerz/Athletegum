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
            $table->decimal('athlete_fee_percentage', 5, 2)->nullable()->after('platform_fee_amount');
            $table->decimal('athlete_fee_amount', 10, 2)->nullable()->after('athlete_fee_percentage');
            $table->decimal('athlete_net_payout', 10, 2)->nullable()->after('athlete_fee_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deals', function (Blueprint $table) {
            $table->dropColumn(['athlete_fee_percentage', 'athlete_fee_amount', 'athlete_net_payout']);
        });
    }
};
