<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('group_post_favorites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('group_post_id')->constrained('group_posts')->onDelete('cascade');
            $table->timestamps();
            
            // Một user chỉ có thể favorite một bài viết một lần
            $table->unique(['user_id', 'group_post_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('group_post_favorites');
    }
};