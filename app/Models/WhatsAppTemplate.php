<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WhatsAppTemplate extends Model
{
    use HasFactory;

    protected $table = 'whatsapp_templates';

    protected $fillable = [
        'name',
        'label',
        'message',
        'description',
        'type',
        'is_active',
        'auto_send',
        'variables',
        'usage_count',
        'last_used_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'auto_send' => 'boolean',
        'variables' => 'array',
        'last_used_at' => 'datetime',
    ];

    /**
     * Relasi ke WhatsApp Logs
     */
    public function logs(): HasMany
    {
        return $this->hasMany(WhatsAppLog::class, 'template_id');
    }

    /**
     * Scope untuk template aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope untuk template auto send
     */
    public function scopeAutoSend($query)
    {
        return $query->where('auto_send', true);
    }

    /**
     * Scope untuk filter berdasarkan type
     */
    public function scopeType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Parse template dengan data
     * 
     * @param array $data Data untuk replace variables
     * @return string Pesan yang sudah di-parse
     */
    public function parse(array $data): string
    {
        $message = $this->message;

        // Replace variables dengan data
        foreach ($data as $key => $value) {
            $message = str_replace('{' . $key . '}', $value, $message);
        }

        return $message;
    }

    /**
     * Increment usage count
     */
    public function incrementUsage()
    {
        $this->increment('usage_count');
        $this->update(['last_used_at' => now()]);
    }

    /**
     * Get available variables from template
     */
    public function getAvailableVariables(): array
    {
        if (is_array($this->variables)) {
            return $this->variables;
        }

        // Extract variables from message
        preg_match_all('/\{([a-z_]+)\}/', $this->message, $matches);
        return array_unique($matches[1]);
    }

    /**
     * Get type badge color
     */
    public function getTypeColorAttribute()
    {
        return match($this->type) {
            'registration' => 'primary',
            'payment' => 'success',
            'reminder' => 'warning',
            'notification' => 'info',
            'custom' => 'secondary',
            default => 'secondary',
        };
    }

    /**
     * Get type label
     */
    public function getTypeLabelAttribute()
    {
        return match($this->type) {
            'registration' => 'Registrasi',
            'payment' => 'Pembayaran',
            'reminder' => 'Pengingat',
            'notification' => 'Notifikasi',
            'custom' => 'Custom',
            default => ucfirst($this->type),
        };
    }

    /**
     * Check if template is being used
     */
    public function isUsed(): bool
    {
        return $this->usage_count > 0;
    }

    /**
     * Get preview message with sample data
     */
    public function getPreview(): string
    {
        $settings = \App\Models\SettingSystem::instance()->toSettingsArray();
        
        $sampleData = [
            'nama' => 'John Doe',
            'nama_lengkap' => 'John Doe',
            'no_pendaftaran' => 'SPMB-2026-0001',
            'no_registrasi' => 'SPMB-2026-0001',
            'jurusan' => 'Teknik Komputer dan Jaringan',
            'nisn' => '1234567890',
            'asal_sekolah' => 'SMP Negeri 1 Jakarta',
            'gelombang' => '1',
            'portal_url' => url('/'),
            'sekolah' => $settings['school_name'] ?? 'SMK PGRI BLORA',
            'tanggal' => now()->format('d-m-Y'),
            'tahun' => now()->format('Y'),
            'tanggal_tes' => '15 Juni 2026',
            'waktu_tes' => '08:00 WIB',
            'tempat_tes' => 'Ruang Lab Komputer',
        ];

        return $this->parse($sampleData);
    }
}
