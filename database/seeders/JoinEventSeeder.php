<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; // Import DB facade
use App\Models\User; // Import model User
use App\Models\Event; // Import model Event
use Carbon\Carbon; // Import Carbon

class JoinEventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lấy danh sách tất cả user_id và event_id hiện có
        $userIds = User::pluck('id')->toArray();
        $eventIds = Event::pluck('id')->toArray();

        // Kiểm tra xem có đủ User và Event không
        if (empty($userIds) || count($eventIds) < 100) {
            echo "Không đủ User hoặc Event (cần ít nhất 100 sự kiện) để tạo bản ghi tham gia sự kiện mẫu cho 1 user.\n";
            return;
        }

        // Chọn một user ID cố định (ví dụ: user đầu tiên)
        $targetUserId = $userIds[0];
        // Hoặc chọn ngẫu nhiên 1 user:
        // $targetUserId = $userIds[array_rand($userIds)];

        $count = 0;
        $maxAttempts = 500; // Giới hạn số lần thử tìm sự kiện unique
        $joinedEventIds = []; // Mảng lưu các event_id user đã tham gia trong seeder này

        echo "Đang tạo 100 bản ghi tham gia sự kiện cho User ID: {$targetUserId} ...\n";

        while ($count < 100 && $maxAttempts > 0) {
            // Chọn ngẫu nhiên một event ID
            $randomEventId = $eventIds[array_rand($eventIds)];

            // Kiểm tra xem cặp user_id và event_id đã tồn tại chưa
            $exists = DB::table('event_user')
                        ->where('user_id', $targetUserId)
                        ->where('event_id', $randomEventId)
                        ->exists();

            // Kiểm tra thêm trong danh sách các event đã được gán trong lần chạy seeder này
            if (!$exists && !in_array($randomEventId, $joinedEventIds)) {
                // Thêm bản ghi tham gia sự kiện
                DB::table('event_user')->insert([
                    'user_id' => $targetUserId,
                    'event_id' => $randomEventId,
                    'status' => 'joined',
                    'joined_at' => Carbon::now(),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
                $joinedEventIds[] = $randomEventId; // Thêm event_id vào danh sách đã gán
                $count++;
            }

            $maxAttempts--;
        }

        echo "Đã tạo thành công {$count} bản ghi tham gia sự kiện mẫu cho User ID {$targetUserId}.\n";

        if ($count < 100) {
            echo "Lưu ý: Chỉ tạo được {$count} bản ghi tham gia duy nhất do giới hạn số lượng sự kiện hoặc số lần thử.\n";
        }
    }
}
