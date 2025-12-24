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
            $table->boolean('is_approved')->default(false)->after('status');
            $table->timestamp('approved_at')->nullable()->after('is_approved');
            $table->text('approval_notes')->nullable()->after('approved_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deals', function (Blueprint $table) {
            $table->dropColumn(['is_approved', 'approved_at', 'approval_notes']);
        });
    }
};
