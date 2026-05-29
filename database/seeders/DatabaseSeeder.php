<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create test user if not exists
        User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => \Illuminate\Support\Facades\Hash::make('password123'),
                'role' => 'panitia',
                'status' => 'aktif',
                'email_verified_at' => now(),
            ]
        );

        $this->call([
            AdminSeeder::class,           // Create default admin & panitia
            UserSeeder::class,            // Create admin & panitia from .env
            TestUserSeeder::class,        // Create additional test users
            SettingSystemSeeder::class,
            JurusanSeeder::class,
            PendaftarSeeder::class,
        ]);
    }
}

