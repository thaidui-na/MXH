<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Carbon\Carbon;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('vi_VN');
        $users = User::all();

        if ($users->isEmpty()) {
            $this->command->error('Không tìm thấy người dùng nào. Vui lòng chạy UserSeeder trước.');
            return;
        }

        $eventTypes = [
            'Hội thảo', 'Workshop', 'Seminar', 'Họp mặt', 'Lễ hội',
            'Triển lãm', 'Hội chợ', 'Cuộc thi', 'Gala dinner', 'Team building'
        ];

        $locations = [
            'Hà Nội' => ['Cầu Giấy', 'Đống Đa', 'Hai Bà Trưng', 'Hoàn Kiếm', 'Tây Hồ'],
            'TP.HCM' => ['Quận 1', 'Quận 2', 'Quận 3', 'Quận 7', 'Thủ Đức'],
            'Đà Nẵng' => ['Hải Châu', 'Sơn Trà', 'Ngũ Hành Sơn', 'Liên Chiểu'],
            'Cần Thơ' => ['Ninh Kiều', 'Bình Thủy', 'Cái Răng', 'Ô Môn'],
            'Hải Phòng' => ['Hồng Bàng', 'Ngô Quyền', 'Lê Chân', 'Hải An']
        ];

        for ($i = 0; $i < 100; $i++) {
            $city = $faker->randomElement(array_keys($locations));
            $district = $faker->randomElement($locations[$city]);
            $street = $faker->streetName();
            $number = $faker->buildingNumber();

            $eventTime = Carbon::now()->addDays($faker->numberBetween(1, 60))
                                    ->setHour($faker->numberBetween(8, 20))
                                    ->setMinute($faker->randomElement([0, 15, 30, 45]));

            Event::create([
                'title' => $faker->randomElement($eventTypes) . ' ' . $faker->words(3, true),
                'description' => $faker->paragraphs(3, true),
                'event_time' => $eventTime,
                'location' => $number . ' ' . $street . ', ' . $district . ', ' . $city,
                'user_id' => $users->random()->id,
                'image_path' => null // Có thể thêm ảnh mẫu sau nếu cần
            ]);
        }

        $this->command->info('Đã tạo 100 sự kiện mẫu thành công!');
    }
} 