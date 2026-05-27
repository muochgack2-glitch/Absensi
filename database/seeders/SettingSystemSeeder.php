<?php

namespace Database\Seeders;

use App\Models\SettingSystem;
use Illuminate\Database\Seeder;

class SettingSystemSeeder extends Seeder
{
    public function run(): void
    {
        SettingSystem::firstOrCreate([], [
            'gelombang_aktif'     => 2,
            'school_name'         => 'SMK PGRI BLORA',
            'academic_year'       => date('Y') . '/' . (date('Y') + 1),
            'registration_status' => 'open',
            'registration_fee'    => 0,
            'active_wave'         => 'Gelombang 2',
            'principal_name'      => 'Meiranti Trisnaning Savitri, S.Pd',
            'school_address'      => 'Jl. RA. Kartini 38 A Blora',
            'school_contact'      => '08985411895',
            'school_city'         => 'BLora',
            'school_phone'        => '0296531540',
            'school_email'        => 'smkgriblora@gmail.com',
            'school_logo'         => '',
            'print_footer_text'   => '',
        ]);
    }
}
