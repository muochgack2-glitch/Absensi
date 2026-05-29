<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Creates/Updates admin users in both Admin table (legacy) and Users table (new system)
     * Password akan selalu diupdate sesuai .env setiap kali seed dijalankan
     */
    public function run(): void
    {
        $adminName = env('ADMIN_NAME', 'Administrator Sistem');
        $adminUsername = env('ADMIN_USERNAME', 'admin');
        $adminPassword = env('ADMIN_PASSWORD', 'admin123');
        $adminEmail = env('ADMIN_EMAIL', 'admin@spmb.local');

        // Create/Update in Admin table (legacy system - for backward compatibility)
        Admin::updateOrCreate(
            ['username' => $adminUsername],
            [
                'password' => Hash::make($adminPassword),
                'nama_petugas' => $adminName,
            ]
        );

        // Create/Update in Users table (new system)
        User::updateOrCreate(
            ['email' => $adminEmail],
            [
                'name' => $adminName,
                'password' => Hash::make($adminPassword),
                'role' => 'administrator',
                'status' => 'aktif',
                'email_verified_at' => now(),
            ]
        );

        // Create/Update Panitia in Users table (new system)
        $panitiaName = env('PANITIA_NAME', 'Panitia Pendaftaran');
        $panitiaUsername = env('PANITIA_USERNAME', 'panitia1');
        $panitiaPassword = env('PANITIA_PASSWORD', 'panitia123');
        $panitiaEmail = env('PANITIA_EMAIL', 'panitia@spmb.local');

        // Create/Update in Admin table (legacy system)
        Admin::updateOrCreate(
            ['username' => $panitiaUsername],
            [
                'password' => Hash::make($panitiaPassword),
                'nama_petugas' => $panitiaName,
            ]
        );

        // Create/Update in Users table (new system)
        User::updateOrCreate(
            ['email' => $panitiaEmail],
            [
                'name' => $panitiaName,
                'password' => Hash::make($panitiaPassword),
                'role' => 'panitia',
                'status' => 'aktif',
                'email_verified_at' => now(),
            ]
        );
    }
}
