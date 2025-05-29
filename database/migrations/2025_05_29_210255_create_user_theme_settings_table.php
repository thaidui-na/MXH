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
        Schema::create('user_theme_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('theme')->default('light'); // light, dark, custom
            $table->string('font_family')->default('Arial');
            $table->string('font_size')->default('medium'); // small, medium, large
            $table->string('primary_color')->default('#3498db');
            $table->string('secondary_color')->default('#2c3e50');
            $table->string('background_color')->default('#ffffff');
            $table->string('text_color')->default('#333333');
            $table->boolean('compact_mode')->default(false);
            $table->json('custom_css')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_theme_settings');
    }
};
