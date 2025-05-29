<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Report; // Import model Report
use App\Models\Post; // Import model Post
use App\Models\User; // Import model User

class ReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lấy danh sách tất cả post_id và user_id hiện có
        $postIds = Post::pluck('id')->toArray();
        $userIds = User::pluck('id')->toArray();

        // Kiểm tra xem có post và user nào trong database không
        if (empty($postIds) || empty($userIds)) {
            echo "Không đủ Post hoặc User để tạo báo cáo mẫu.\n";
            return;
        }

        // Tạo 100 báo cáo mẫu
        for ($i = 0; $i < 100; $i++) {
            // Chọn ngẫu nhiên một post_id và user_id
            $randomPostId = $postIds[array_rand($postIds)];
            $randomUserId = $userIds[array_rand($userIds)];

            // Tạo báo cáo mới
            Report::create([
                'post_id' => $randomPostId,
                'user_id' => $randomUserId,
                'reason' => 'Nội dung báo cáo mẫu số ' . ($i + 1), // Lý do báo cáo mẫu
            ]);
        }

        echo "Đã tạo thành công 100 báo cáo mẫu.\n";
    }
}
