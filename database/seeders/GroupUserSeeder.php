<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Group;


class GroupUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $groupUserData = [
            ['group_id' => 1, 'user_id' => 1],
            ['group_id' => 1, 'user_id' => 2],
            ['group_id' => 2, 'user_id' => 2],
            ['group_id' => 2, 'user_id' => 3],
            ['group_id' => 3, 'user_id' => 1],
            ['group_id' => 3, 'user_id' => 4],
            ['group_id' => 4, 'user_id' => 5],
            ['group_id' => 5, 'user_id' => 1],
            ['group_id' => 5, 'user_id' => 3],
        ];

        DB::table('group_user')->insert($groupUserData);
        
    }
}
