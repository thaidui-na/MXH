<?php

namespace Database\Seeders;

use App\Models\ChatGroup;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChatGroupSeeder extends Seeder
{
    public function run(): void
    {
        $userIds = User::pluck('id')->toArray();
        $groupNames = [
            'Nhóm Học Tập',
            'Nhóm Dự Án',
            'Nhóm Thảo Luận',
            'Nhóm Chia Sẻ',
            'Nhóm Kết Nối',
            'Nhóm Sinh Viên',
            'Nhóm Công Nghệ',
            'Nhóm Giải Trí',
            'Nhóm Thể Thao',
            'Nhóm Âm Nhạc'
        ];

        for ($i = 0; $i < 100; $i++) {
            $creatorId = fake()->randomElement($userIds);
            
            // Tạo nhóm chat
            $group = ChatGroup::create([
                'name' => fake()->randomElement($groupNames) . ' ' . fake()->numberBetween(1, 100),
                'description' => fake()->sentence(),
                'created_by' => $creatorId,
                'avatar' => fake()->imageUrl(),
            ]);

            // Thêm thành viên vào nhóm (từ 3-10 thành viên)
            $memberCount = fake()->numberBetween(3, 10);
            $selectedMembers = fake()->randomElements($userIds, $memberCount);
            
            foreach ($selectedMembers as $memberId) {
                DB::table('chat_group_members')->insert([
                    'group_id' => $group->id,
                    'user_id' => $memberId,
                    'is_admin_group_chat' => $memberId === $creatorId, // Người tạo là admin
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
