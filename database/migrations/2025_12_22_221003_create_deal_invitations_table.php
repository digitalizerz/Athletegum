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
        Schema::create('deal_invitations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('deal_id')->constrained()->cascadeOnDelete();
            $table->string('token')->unique(); // Unique invitation token
            $table->string('athlete_email')->nullable(); // Email of intended athlete
            $table->foreignId('athlete_id')->nullable()->constrained('athletes')->nullOnDelete(); // If athlete already exists
            $table->enum('status', ['pending', 'accepted', 'expired'])->default('pending');
            $table->timestamp('expires_at')->nullable(); // Optional expiration
            $table->timestamp('accepted_at')->nullable();
            $table->timestamps();
            
            $table->index(['token', 'status']);
            $table->index(['athlete_email', 'status']);
            $table->index(['athlete_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deal_invitations');
    }
};
