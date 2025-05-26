<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Faker\Factory as Faker;

class CommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        
        // Get all user IDs and post IDs
        $userIds = User::pluck('id')->toArray();
        $postIds = Post::pluck('id')->toArray();
        
        // Create 100 comments for each post
        foreach ($postIds as $postId) {
            for ($i = 0; $i < 100; $i++) {
                $comment = Comment::create([
                    'post_id' => $postId,
                    'user_id' => $faker->randomElement($userIds),
                    'parent_id' => null, // Main comment
                    'content' => $faker->paragraph(2),
                    'created_at' => $faker->dateTimeBetween('-1 year', 'now'),
                    'updated_at' => now(),
                ]);
                
                // 30% chance to create a reply to this comment
                if ($faker->boolean(30)) {
                    Comment::create([
                        'post_id' => $comment->post_id,
                        'user_id' => $faker->randomElement($userIds),
                        'parent_id' => $comment->id,
                        'content' => $faker->paragraph(1),
                        'created_at' => $faker->dateTimeBetween($comment->created_at, 'now'),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}
