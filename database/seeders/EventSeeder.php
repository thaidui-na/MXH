<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        if ($users->isEmpty()) {
            $this->command->info('No users found. Please run UserSeeder first.');
            return;
        }

        $eventTypes = ['online', 'offline'];
        $locations = [
            'online' => [
                'Online Meeting',
                'Zoom Conference',
                'Google Meet',
                'Microsoft Teams',
                'Webinar Platform'
            ],
            'offline' => [
                'Hội trường A, Tầng 1, Tòa nhà X',
                'Phòng họp B, Tầng 2, Tòa nhà Y',
                'Trung tâm Hội nghị Z',
                'Khách sạn ABC, Phòng họp Grand',
                'Công viên XYZ, Khu vực A'
            ]
        ];

        $titles = [
            'Hội thảo về',
            'Workshop',
            'Buổi chia sẻ',
            'Khóa học',
            'Sự kiện networking',
            'Hội nghị',
            'Seminar',
            'Training session',
            'Meetup',
            'Conference'
        ];

        $topics = [
            'Công nghệ mới',
            'Phát triển bản thân',
            'Kỹ năng lãnh đạo',
            'Marketing số',
            'AI và Machine Learning',
            'Blockchain',
            'Cloud Computing',
            'Mobile Development',
            'UI/UX Design',
            'Data Science'
        ];

        for ($i = 0; $i < 100; $i++) {
            $eventType = $eventTypes[array_rand($eventTypes)];
            $location = $locations[$eventType][array_rand($locations[$eventType])];
            $title = $titles[array_rand($titles)] . ' ' . $topics[array_rand($topics)];
            
            // Tạo thời gian ngẫu nhiên trong 30 ngày tới
            $eventTime = Carbon::now()->addDays(rand(1, 30))->setHour(rand(8, 20))->setMinute(0);

            Event::create([
                'title' => $title,
                'description' => 'Đây là mô tả chi tiết về ' . strtolower($title) . '. Sự kiện này sẽ cung cấp những thông tin và kiến thức hữu ích cho người tham gia.',
                'event_type' => $eventType,
                'event_time' => $eventTime,
                'location' => $location,
                'user_id' => $users->random()->id,
                'image_path' => null // Có thể thêm ảnh mẫu sau
            ]);
        }

        $this->command->info('Created 100 sample events successfully.');
    }
} 