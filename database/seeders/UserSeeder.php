<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password')
        ]);

        for ($i = 1; $i <= 5; $i++) {
            User::create([
                'name' => 'User ' . $i,
                'email' => 'user'.$i.'@example.com',
                'password' => Hash::make('password')
            ]);
        }
    
    }
}
