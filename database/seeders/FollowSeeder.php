<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Follow;
use Faker\Factory as Faker;

class FollowSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        
        // Get all user IDs
        $userIds = User::pluck('id')->toArray();
        
        // For each user, follow 5 random other users
        foreach ($userIds as $followerId) {
            // Get existing follows for this user
            $existingFollows = Follow::where('follower_id', $followerId)
                ->pluck('following_id')
                ->toArray();
            
            // Get available users to follow (excluding self and already followed users)
            $availableUsers = array_filter($userIds, function($id) use ($followerId, $existingFollows) {
                return $id !== $followerId && !in_array($id, $existingFollows);
            });
            
            // If there are available users, follow 5 random ones
            if (!empty($availableUsers)) {
                $availableUsers = array_values($availableUsers); // Re-index array
                $followingIds = $faker->randomElements($availableUsers, min(5, count($availableUsers)));
                
                // Create follow relationships
                foreach ($followingIds as $followingId) {
                    Follow::create([
                        'follower_id' => $followerId,
                        'following_id' => $followingId,
                        'created_at' => $faker->dateTimeBetween('-1 year', 'now'),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}
