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
        Schema::create('athletes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('profile_token')->unique()->nullable(); // For shareable link
            $table->string('username')->unique()->nullable(); // Optional username for URL
            $table->string('profile_photo')->nullable();
            $table->string('sport')->nullable();
            $table->string('school')->nullable();
            $table->string('instagram_handle')->nullable();
            $table->string('tiktok_handle')->nullable();
            $table->string('twitter_handle')->nullable();
            $table->string('youtube_handle')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('athletes');
    }
};
