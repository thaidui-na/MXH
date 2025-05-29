<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            PostSeeder::class,
            ChatGroupSeeder::class,
            GroupMessageSeeder::class,
            MessageSeeder::class,
            GroupDemoSeeder::class,
            CommentSeeder::class,
            FollowSeeder::class,
            EventSeeder::class,
            ReportSeeder::class,
            JoinEventSeeder::class,
        ]);
    }
}
