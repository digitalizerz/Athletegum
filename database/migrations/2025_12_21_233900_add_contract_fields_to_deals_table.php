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
            $table->text('contract_text')->nullable()->after('status');
            $table->boolean('contract_signed')->default(false)->after('contract_text');
            $table->timestamp('contract_signed_at')->nullable()->after('contract_signed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deals', function (Blueprint $table) {
            $table->dropColumn(['contract_text', 'contract_signed', 'contract_signed_at']);
        });
    }
};
