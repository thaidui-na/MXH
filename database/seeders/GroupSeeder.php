<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Group;


class GroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        $groups = [
            ['name' => 'TDC Lập trình Web', 'description' => 'Nhóm học lập trình web tại TDC', 'privacy' => 'public', 'user_id' => 1],
            ['name' => 'TDC CNTT K22', 'description' => 'Cộng đồng sinh viên CNTT khóa 22', 'privacy' => 'private', 'user_id' => 2],
            ['name' => 'TDC Sale & Marketing', 'description' => 'Chia sẻ kỹ năng bán hàng và marketing', 'privacy' => 'public', 'user_id' => 3],
            ['name' => 'Football Lovers', 'description' => 'Nhóm dành cho những người yêu bóng đá', 'privacy' => 'public', 'user_id' => 4],
            ['name' => 'Anime Community', 'description' => 'Nơi giao lưu fan anime tại TDC', 'privacy' => 'private', 'user_id' => 5],
        ];

        foreach ($groups as $group) {
            Group::create([
                'name' => $group['name'],
                'description' => $group['description'],
                'privacy' => $group['privacy'],
                'image' => null,
                'user_id' => $group['user_id'],
               
            ]);
        }
    
    }
}
