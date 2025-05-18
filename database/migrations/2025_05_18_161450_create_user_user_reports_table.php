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
        Schema::create('user_user_reports', function (Blueprint $table) {
            $table->id();

            // ID của người báo cáo (reporter)
            $table->foreignId('reporter_id')->constrained('users')->onDelete('cascade');

            // ID của người bị báo cáo (reported user)
            $table->foreignId('reported_user_id')->constrained('users')->onDelete('cascade');

            $table->text('reason'); // Lý do báo cáo

            $table->boolean('is_resolved')->default(false); // Trạng thái báo cáo (đã xử lý hay chưa)
            $table->text('admin_note')->nullable(); // Ghi chú của admin (tùy chọn)

            $table->timestamps(); // created_at và updated_at

            // Đảm bảo một cặp người báo cáo và người bị báo cáo là duy nhất
            // Điều này ngăn chặn một người báo cáo cùng một người nhiều lần
            $table->unique(['reporter_id', 'reported_user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_user_reports');
    }
};
