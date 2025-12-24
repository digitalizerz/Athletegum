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
            $table->time('deadline_time')->nullable()->after('deadline');
            $table->string('frequency')->nullable()->after('deadline_time'); // one-time, daily, weekly, monthly
            $table->json('attachments')->nullable()->after('notes'); // Store file paths/names
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deals', function (Blueprint $table) {
            $table->dropColumn(['deadline_time', 'frequency', 'attachments']);
        });
    }
};
