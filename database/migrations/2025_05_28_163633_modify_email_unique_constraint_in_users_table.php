<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ModifyEmailUniqueConstraintInUsersTable extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Xóa ràng buộc unique cũ
            $table->dropUnique(['email']);
        });

        // Thêm ràng buộc unique mới chỉ áp dụng cho các tài khoản chưa bị xóa
        DB::statement('CREATE UNIQUE INDEX users_email_unique ON users (email) WHERE deleted_at IS NULL');
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Xóa ràng buộc unique mới
            DB::statement('DROP INDEX IF EXISTS users_email_unique');
            
            // Khôi phục ràng buộc unique cũ
            $table->unique('email');
        });
    }
} 