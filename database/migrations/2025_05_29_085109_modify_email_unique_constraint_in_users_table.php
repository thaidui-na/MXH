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
        Schema::table('users', function (Blueprint $table) {
            // Xóa unique constraint cũ nếu có
            $table->dropUnique(['email']);
            
            // Thêm unique constraint mới
            $table->unique('email', 'users_email_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Xóa unique constraint
            $table->dropUnique('users_email_unique');
            
            // Thêm lại unique constraint cũ
            $table->unique('email');
        });
    }
};
