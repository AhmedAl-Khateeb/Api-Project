<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $manager = User::firstOrCreate(
            ['email' => 'manager@example.com'],
            ['name' => 'Manager One', 'password' => Hash::make('password')]
        );
        $manager->syncRoles(['manager']);

        $user = User::firstOrCreate(
            ['email' => 'user@example.com'],
            ['name' => 'User One', 'password' => Hash::make('password123')]
        );
        $user->syncRoles(['user']);
    }
}
