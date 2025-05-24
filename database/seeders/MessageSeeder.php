<?php

namespace Database\Seeders;

use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Seeder;

class MessageSeeder extends Seeder
{
    public function run(): void
    {
        $userIds = User::pluck('id')->toArray();
        $stickers = ['sticker1.png', 'sticker2.png', 'sticker3.png', 'sticker4.png'];
        $emojis = ['😊', '👍', '❤️', '🎉', ''];

        for ($i = 0; $i < 100; $i++) {
            $senderId = fake()->randomElement($userIds);
            $receiverId = fake()->randomElement($userIds);
            
            // Đảm bảo không gửi tin nhắn cho chính mình
            if ($senderId !== $receiverId) {
                Message::create([
                    'sender_id' => $senderId,
                    'receiver_id' => $receiverId,
                    'content' => fake()->optional(0.7)->sentence(), // 70% tin nhắn có nội dung
                    'image_path' => fake()->optional(0.2)->imageUrl(), // 20% tin nhắn có ảnh
                    'sticker' => fake()->optional(0.2)->randomElement($stickers), // 20% tin nhắn có sticker
                    'emoji' => fake()->optional(0.1)->randomElement($emojis), // 10% tin nhắn có emoji
                    'is_read' => fake()->boolean(),
                ]);
            }
        }
    }
}
