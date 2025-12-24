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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->enum('user_type', ['user', 'athlete'])->default('user');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('athlete_id')->nullable()->constrained('athletes')->onDelete('cascade');
            $table->string('type'); // e.g., 'message', 'deal_accepted', 'deal_completed', 'payment_released'
            $table->string('title');
            $table->text('message');
            $table->string('action_url')->nullable(); // URL to navigate to when clicked
            $table->foreignId('deal_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('message_id')->nullable()->constrained()->onDelete('cascade');
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['user_type', 'user_id', 'is_read']);
            $table->index(['user_type', 'athlete_id', 'is_read']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
