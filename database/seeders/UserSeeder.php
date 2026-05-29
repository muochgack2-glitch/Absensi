<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Administrator
        User::updateOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@spmb.local')],
            [
                'name' => env('ADMIN_NAME', 'Administrator Sistem'),
                'email' => env('ADMIN_EMAIL', 'admin@spmb.local'),
                'password' => Hash::make(env('ADMIN_PASSWORD', 'admin123')),
                'role' => 'administrator',
                'status' => 'aktif',
                'email_verified_at' => now(),
            ]
        );

        // Create Panitia
        User::updateOrCreate(
            ['email' => env('PANITIA_EMAIL', 'panitia@spmb.pgri')],
            [
                'name' => env('PANITIA_NAME', 'Panitia Pendaftaran'),
                'email' => env('PANITIA_EMAIL', 'panitia@spmb.pgri'),
                'password' => Hash::make(env('PANITIA_PASSWORD', 'panitia123')),
                'role' => 'panitia',
                'status' => 'aktif',
                'email_verified_at' => now(),
            ]
        );
    }
}
