<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        // Lấy tất cả user id để gán ngẫu nhiên cho bài viết
        $userIds = User::pluck('id')->toArray();

        for ($i = 0; $i < 100; $i++) {
            Post::create([
                'user_id'   => fake()->randomElement($userIds),
                'title'     => fake()->sentence(6),
                'content'   => fake()->paragraph(5),
                'is_public' => true,
            ]);
        }
    }
}
