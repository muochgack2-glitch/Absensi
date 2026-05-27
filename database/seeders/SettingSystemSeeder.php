<?php

namespace Database\Seeders;

use App\Models\SettingSystem;
use Illuminate\Database\Seeder;

class SettingSystemSeeder extends Seeder
{
    public function run(): void
    {
        SettingSystem::firstOrCreate([], [
            'gelombang_aktif'     => 1,
            'school_name'         => 'SMK Contoh Indonesia',
            'academic_year'       => date('Y') . '/' . (date('Y') + 1),
            'registration_status' => 'open',
            'registration_fee'    => 150000,
            'active_wave'         => 'Gelombang 1',
            'principal_name'      => '',
            'school_address'      => '',
            'school_contact'      => '',
            'school_city'         => '',
            'school_phone'        => '',
            'school_email'        => '',
            'school_logo'         => '',
            'print_footer_text'   => '',
        ]);
    }
}
