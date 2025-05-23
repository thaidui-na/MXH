<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Tạo tài khoản admin
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('8865512b'),
            'is_admin' => true,
            'account_status' => 'active',
            'phone' => '0123456789',
            'bio' => 'Quản trị viên hệ thống',
            'birthday' => '1990-01-01'
        ]);

        // Tạo 100 user thông thường
        for ($i = 0; $i < 100; $i++) {
            User::create([
                'name' => fake()->name(),
                'email' => fake()->unique()->safeEmail(),
                'password' => Hash::make('password'),
                'is_admin' => false,
                'account_status' => 'active',
                'phone' => fake()->phoneNumber(),
                'bio' => fake()->sentence(),
                'birthday' => fake()->date(),
            ]);
        }
    }
}
