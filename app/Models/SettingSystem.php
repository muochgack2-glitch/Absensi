<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SettingSystem extends Model
{
    protected $table = 'setting_system';
    protected $primaryKey = 'id_setting';

    protected $fillable = [
        'gelombang_aktif',
        'school_name',
        'academic_year',
        'registration_status',
        'registration_fee',
        'active_wave',
        'principal_name',
        'school_address',
        'school_contact',
        'school_city',
        'school_phone',
        'school_email',
        'school_website',
        'instagram_url',
        'school_logo',
        'favicon',
        'theme_preset',
        'theme_primary',
        'theme_secondary',
        'print_footer_text',
        'document_header_text',
        'document_city',
        'document_sign_name',
        'document_sign_title',
    ];

    protected $casts = [
        'registration_fee' => 'integer',
        'gelombang_aktif'  => 'integer',
    ];

    /**
     * Ambil satu-satunya row settings, buat jika belum ada.
     */
    public static function instance(): static
    {
        return static::firstOrCreate([], [
            'gelombang_aktif'     => 1,
            'school_name'         => 'SMK Contoh Indonesia',
            'academic_year'       => date('Y') . '/' . (date('Y') + 1),
            'registration_status' => 'open',
            'registration_fee'    => 150000,
            'active_wave'         => 'Gelombang 1',
        ]);
    }

    /**
     * Kembalikan settings sebagai array (kompatibel dengan kode lama).
     */
    public function toSettingsArray(): array
    {
        return [
            'school_name'         => $this->school_name         ?? 'SMK Contoh Indonesia',
            'academic_year'       => $this->academic_year       ?? date('Y') . '/' . (date('Y') + 1),
            'registration_status' => $this->registration_status ?? 'open',
            'registration_fee'    => $this->registration_fee    ?? 150000,
            'active_wave'         => $this->active_wave         ?? 'Gelombang 1',
            'principal_name'      => $this->principal_name      ?? '',
            'school_address'      => $this->school_address      ?? '',
            'school_contact'      => $this->school_contact      ?? '',
            'school_city'         => $this->school_city         ?? '',
            'school_phone'        => $this->school_phone        ?? '',
            'school_email'          => $this->school_email          ?? '',
            'school_website'        => $this->school_website        ?? '',
            'instagram_url'         => $this->instagram_url         ?? '',
            'school_logo'           => $this->school_logo           ?? '',
            'favicon'               => $this->favicon               ?? '',
            'theme_preset'          => $this->theme_preset          ?? 'purple',
            'theme_primary'         => $this->theme_primary         ?? '',
            'theme_secondary'       => $this->theme_secondary       ?? '',
            'print_footer_text'     => $this->print_footer_text     ?? '',
            'document_header_text'  => $this->document_header_text  ?? '',
            'document_city'         => $this->document_city         ?? '',
            'document_sign_name'    => $this->document_sign_name    ?? '',
            'document_sign_title'   => $this->document_sign_title   ?? '',
            'gelombang_aktif'       => $this->gelombang_aktif       ?? 1,
        ];
    }
}
