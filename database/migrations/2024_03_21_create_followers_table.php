<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if table exists and drop it if it does
        if (Schema::hasTable('followers')) {
            // Drop foreign keys first
            Schema::table('followers', function (Blueprint $table) {
                $table->dropForeign(['follower_id']);
                $table->dropForeign(['following_id']);
            });
            
            // Then drop the table
            Schema::drop('followers');
        }

        Schema::create('followers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('follower_id');
            $table->unsignedBigInteger('following_id');
            $table->timestamps();
            
            // Tạo unique constraint để tránh duplicate follows
            $table->unique(['follower_id', 'following_id']);

            // Add foreign key constraints
            $table->foreign('follower_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
                  
            $table->foreign('following_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('followers')) {
            Schema::table('followers', function (Blueprint $table) {
                $table->dropForeign(['follower_id']);
                $table->dropForeign(['following_id']);
            });
            Schema::drop('followers');
        }
    }
}; 