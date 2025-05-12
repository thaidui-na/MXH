<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Tạo admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'is_admin' => true
        ]);

        // Tạo một số user mẫu
        $users = [
            [
                'name' => 'Nguyễn Văn A',
                'email' => 'nguyenvana@example.com',
                'password' => Hash::make('password')
            ],
            [
                'name' => 'Trần Thị B',
                'email' => 'tranthib@example.com',
                'password' => Hash::make('password')
            ],
            [
                'name' => 'Lê Văn C',
                'email' => 'levanc@example.com',
                'password' => Hash::make('password')
            ],
            [
                'name' => 'Phạm Thị D',
                'email' => 'phamthid@example.com',
                'password' => Hash::make('password')
            ],
            [
                'name' => 'Hoàng Văn E',
                'email' => 'hoangvane@example.com',
                'password' => Hash::make('password')
            ]
        ];

        foreach ($users as $user) {
            User::create($user);
        }

        // Chạy GroupDemoSeeder
        $this->call([
            GroupDemoSeeder::class
        ]);
    }
}
