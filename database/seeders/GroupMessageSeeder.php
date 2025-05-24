<?php

namespace Database\Seeders;

use App\Models\GroupMessage;
use App\Models\ChatGroup;
use Illuminate\Database\Seeder;

class GroupMessageSeeder extends Seeder
{
    public function run(): void
    {
        $groups = ChatGroup::with('members')->get();

        for ($i = 0; $i < 100; $i++) {
            $group = $groups->random();
            $sender = $group->members->random();
            
            GroupMessage::create([
                'group_id' => $group->id,
                'sender_id' => $sender->id,
                'content' => fake()->sentence(),
            ]);
        }
    }
}
