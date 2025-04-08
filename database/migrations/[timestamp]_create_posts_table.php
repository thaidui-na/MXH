<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tạo bảng posts
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id(); // ID tự tăng
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Liên kết với user
            $table->string('title'); // Tiêu đề bài viết
            $table->text('content'); // Nội dung bài viết
            $table->boolean('is_public')->default(true); // Trạng thái công khai
            $table->timestamps(); // Thời gian tạo và cập nhật
        });
    }

    /**
     * Xóa bảng posts
     */
    public function down()
    {
        Schema::dropIfExists('posts');
    }
}; 