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
        Schema::create('deals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('deal_type'); // e.g., 'instagram_post', 'appearance', etc.
            $table->decimal('compensation_amount', 10, 2);
            $table->date('deadline');
            $table->text('notes')->nullable();
            $table->string('status')->default('pending'); // pending, accepted, completed, cancelled
            $table->string('token')->unique(); // For shareable link
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deals');
    }
};
