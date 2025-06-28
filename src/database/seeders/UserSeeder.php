<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'id' => 1,
            'name' => 'テストユーザー1',
            'username' => 'user1',
            'email' => 'user1@example.com',
            'password' => Hash::make('password'),
        ]);


        User::create([
            'id' => 2,
            'name' => 'テストユーザー2',
            'username' => 'user2',
            'email' => 'user2@example.com',
            'password' => Hash::make('password'),
        ]);

        User::create([
            'id' => 3,
            'name' => 'テストユーザー3',
            'username' => 'user3',
            'email' => 'user3@example.com',
            'password' => Hash::make('password'),
        ]);
    }
}
