<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * Semua seeder menggunakan updateOrCreate sehingga aman dijalankan berulang kali
     */
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,           // Create admin & panitia (both admins table & users table)
            SettingSystemSeeder::class,   // System settings
            JurusanSeeder::class,         // Jurusan/Program studi
            PendaftarSeeder::class,       // Sample pendaftar data
            WhatsAppSeeder::class,        // WhatsApp templates & settings
        ]);
    }
}

