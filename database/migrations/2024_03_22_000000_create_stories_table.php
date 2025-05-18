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
        Schema::create('stories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('media_path'); // Đường dẫn đến file media (ảnh/video)
            $table->string('media_type')->default('image'); // Loại media: image hoặc video
            $table->text('caption')->nullable(); // Caption cho story (có thể null)
            $table->timestamp('expires_at'); // Thời điểm story hết hạn (24h sau khi tạo)
            $table->boolean('is_active')->default(true); // Trạng thái story
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stories');
    }
}; 