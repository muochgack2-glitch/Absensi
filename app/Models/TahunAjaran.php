<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TahunAjaran extends Model
{
    protected $table = 'tahun_ajaran';
    
    protected $fillable = [
        'tahun',
        'status',
        'reg_number_pattern',
        'reg_number_current',
        'total_pendaftar',
        'total_diterima',
        'total_belum_daftar_ulang',
        'started_at',
        'closed_at',
        'created_by',
    ];
    
    protected $casts = [
        'started_at' => 'date',
        'closed_at' => 'date',
        'reg_number_current' => 'integer',
        'total_pendaftar' => 'integer',
        'total_diterima' => 'integer',
        'total_belum_daftar_ulang' => 'integer',
    ];
    
    // Relationships
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    
    public function pendaftars()
    {
        return $this->hasMany(Pendaftar::class, 'tahun_ajaran', 'tahun');
    }
    
    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
    
    public function scopeArchived($query)
    {
        return $query->where('status', 'archived');
    }
    
    public function scopeUpcoming($query)
    {
        return $query->where('status', 'upcoming');
    }
    
    // Helper Methods
    
    /**
     * Get tahun angka saja (ambil tahun pertama)
     * Contoh: "2026/2027" -> "2026"
     */
    public function getYearNumber()
    {
        $parts = explode('/', $this->tahun);
        return $parts[0]; // Ambil tahun pertama
    }
    
    /**
     * Update statistics dari database
     */
    public function updateStatistics()
    {
        $this->total_pendaftar = Pendaftar::where('tahun_ajaran', $this->tahun)->count();
        $this->total_diterima = Pendaftar::where('tahun_ajaran', $this->tahun)
            ->where('status_siswa', 'Diterima')->count();
        $this->total_belum_daftar_ulang = Pendaftar::where('tahun_ajaran', $this->tahun)
            ->where('status_siswa', 'Belum Daftar Ulang')->count();
        $this->save();
        
        return $this;
    }
    
    /**
     * Check apakah tahun ajaran ini sedang aktif
     */
    public function isActive()
    {
        return $this->status === 'active';
    }
    
    /**
     * Check apakah tahun ajaran ini sudah diarsipkan
     */
    public function isArchived()
    {
        return $this->status === 'archived';
    }
    
    /**
     * Archive tahun ajaran ini
     */
    public function archive()
    {
        $this->status = 'archived';
        $this->closed_at = now();
        $this->save();
        
        return $this;
    }
    
    /**
     * Activate tahun ajaran ini
     */
    public function activate()
    {
        // Arsipkan tahun ajaran aktif lainnya
        static::where('status', 'active')->update([
            'status' => 'archived',
            'closed_at' => now()
        ]);
        
        $this->status = 'active';
        $this->started_at = $this->started_at ?? now();
        $this->save();
        
        // Update setting system
        \App\Models\SettingSystem::set('active_tahun_ajaran', $this->tahun);
        
        return $this;
    }
    
    /**
     * Get badge color berdasarkan status
     */
    public function getStatusBadgeColor()
    {
        return match($this->status) {
            'active' => 'success',
            'archived' => 'secondary',
            'upcoming' => 'info',
            default => 'secondary'
        };
    }
    
    /**
     * Get status label dalam Bahasa Indonesia
     */
    public function getStatusLabel()
    {
        return match($this->status) {
            'active' => 'Aktif',
            'archived' => 'Arsip',
            'upcoming' => 'Akan Datang',
            default => 'Unknown'
        };
    }
}
