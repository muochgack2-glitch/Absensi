<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Creates multiple test users for testing purposes
     */
    public function run(): void
    {
        // Test Administrator Users
        $adminUsers = [
            [
                'name' => 'Kepala Sekolah',
                'email' => 'kepala@spmb.local',
                'password' => 'password123',
                'role' => 'administrator',
            ],
            [
                'name' => 'Wakil Kepala Sekolah',
                'email' => 'wakil@spmb.local',
                'password' => 'password123',
                'role' => 'administrator',
            ],
        ];

        // Test Panitia Users
        $panitiaUsers = [
            [
                'name' => 'Panitia Registrasi 1',
                'email' => 'panitia1@spmb.local',
                'password' => 'password123',
                'role' => 'panitia',
            ],
            [
                'name' => 'Panitia Registrasi 2',
                'email' => 'panitia2@spmb.local',
                'password' => 'password123',
                'role' => 'panitia',
            ],
            [
                'name' => 'Panitia Verifikasi',
                'email' => 'verifikasi@spmb.local',
                'password' => 'password123',
                'role' => 'panitia',
            ],
            [
                'name' => 'Panitia Kasir',
                'email' => 'kasir@spmb.local',
                'password' => 'password123',
                'role' => 'panitia',
            ],
        ];

        // Create admin users
        foreach ($adminUsers as $adminData) {
            User::updateOrCreate(
                ['email' => $adminData['email']],
                [
                    'name' => $adminData['name'],
                    'password' => Hash::make($adminData['password']),
                    'role' => $adminData['role'],
                    'status' => 'aktif',
                    'email_verified_at' => now(),
                ]
            );
        }

        // Create panitia users
        foreach ($panitiaUsers as $panitiaData) {
            User::updateOrCreate(
                ['email' => $panitiaData['email']],
                [
                    'name' => $panitiaData['name'],
                    'password' => Hash::make($panitiaData['password']),
                    'role' => $panitiaData['role'],
                    'status' => 'aktif',
                    'email_verified_at' => now(),
                ]
            );
        }
    }
}
