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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('action'); // e.g., 'user.suspended', 'deal.cancelled', 'profile.hidden'
            $table->string('entity_type'); // e.g., 'User', 'Deal', 'Athlete'
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->text('description');
            $table->json('metadata')->nullable(); // Additional context data
            $table->ipAddress('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();

            $table->index(['entity_type', 'entity_id']);
            $table->index('action');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
