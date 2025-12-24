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
            $table->text('completion_notes')->nullable()->after('approval_notes');
            $table->json('deliverables')->nullable()->after('completion_notes');
            $table->timestamp('completed_at')->nullable()->after('deliverables');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deals', function (Blueprint $table) {
            $table->dropColumn(['completion_notes', 'deliverables', 'completed_at']);
        });
    }
};
