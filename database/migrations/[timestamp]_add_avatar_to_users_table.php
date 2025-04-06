<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Thêm cột avatar với giá trị mặc định là null
            $table->string('avatar')->nullable()->after('email');
            // Thêm các trường thông tin cá nhân bổ sung
            $table->string('phone')->nullable()->after('avatar');
            $table->text('bio')->nullable()->after('phone');
            $table->date('birthday')->nullable()->after('bio');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['avatar', 'phone', 'bio', 'birthday']);
        });
    }
}; 