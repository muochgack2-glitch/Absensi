<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Admin::updateOrCreate(
            ['username' => env('ADMIN_USERNAME', 'admin')],
            [
                'password' => Hash::make(env('ADMIN_PASSWORD', 'admin123')),
                'nama_petugas' => env('ADMIN_NAME', 'Administrator Sistem'),
            ]
        );

        Admin::updateOrCreate(
            ['username' => env('STAFF_USERNAME', 'staff1')],
            [
                'password' => Hash::make(env('STAFF_PASSWORD', 'staff123')),
                'nama_petugas' => env('STAFF_NAME', 'Staff Pendaftaran'),
            ]
        );
    }
}
