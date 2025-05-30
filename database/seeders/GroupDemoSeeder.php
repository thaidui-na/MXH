<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GroupDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        // truncate các bảng
        \App\Models\GroupMember::truncate();
        \App\Models\GroupPost::truncate();
        \App\Models\Group::truncate();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Tạo 5 user mẫu
        $users = \App\Models\User::factory()->count(5)->create();

        // Thêm các nhóm mẫu liên quan đến TDC
        $tdcGroups = [
            [
                'name' => 'TDC Confessions',
                'description' => 'Nơi chia sẻ tâm sự, hỏi đáp, confession của sinh viên TDC.',
                'created_by' => $users->random()->id,
                'is_private' => false,
            ],
            [
                'name' => 'TDC Thủ Đức',
                'description' => 'Cộng đồng sinh viên Cao đẳng Công nghệ Thủ Đức (TDC).',
                'created_by' => $users->random()->id,
                'is_private' => false,
            ],
            [
                'name' => 'TDC Học tập & Việc làm',
                'description' => 'Chia sẻ kinh nghiệm học tập, việc làm cho sinh viên TDC.',
                'created_by' => $users->random()->id,
                'is_private' => false,
            ],
            [
                'name' => 'TDC IT Club',
                'description' => 'Câu lạc bộ Công nghệ thông tin TDC.',
                'created_by' => $users->random()->id,
                'is_private' => false,
            ],
        ];
        foreach ($tdcGroups as $groupData) {
            $group = \App\Models\Group::create($groupData);
            foreach ($users as $user) {
                \App\Models\GroupMember::create([
                    'group_id' => $group->id,
                    'user_id' => $user->id,
                    'role' => 'member',
                    'is_approved' => true,
                ]);
            }
            for ($i = 0; $i < 2; $i++) {
                \App\Models\GroupPost::create([
                    'group_id' => $group->id,
                    'user_id' => $users->random()->id,
                    'title' => 'Bài viết TDC ' . ($i + 1) . ' trong nhóm ' . $group->name,
                    'content' => 'Nội dung bài viết mẫu TDC ' . ($i + 1),
                    'is_approved' => true,
                ]);
            }
        }
    }
    
}
