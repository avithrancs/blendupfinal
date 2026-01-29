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
        // Admin
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@blendup.local',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);

        // User
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'user@blendup.local',
            'password' => Hash::make('password'),
            'role' => 'user',
        ]);

        $this->call([
            DrinkSeeder::class,
        ]);
    }
}
