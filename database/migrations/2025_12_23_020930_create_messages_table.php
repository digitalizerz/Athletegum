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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('deal_id')->constrained()->onDelete('cascade');
            $table->enum('sender_type', ['athlete', 'user', 'system'])->default('user');
            $table->foreignId('sender_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('athlete_sender_id')->nullable()->constrained('athletes')->onDelete('set null');
            $table->enum('message_type', ['text', 'attachment', 'system'])->default('text');
            $table->text('content')->nullable();
            $table->string('attachment_path')->nullable();
            $table->string('attachment_original_name')->nullable();
            $table->string('attachment_mime_type')->nullable();
            $table->unsignedBigInteger('attachment_size')->nullable();
            $table->boolean('is_system_message')->default(false);
            $table->timestamps();

            $table->index(['deal_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
