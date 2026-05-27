<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogistikBayar extends Model
{
    protected $table = 'logistik_bayar';
    protected $primaryKey = 'id_logistik';
    
    protected $fillable = [
        'id_pendaftar',
        'status_bayar',
        'ukuran_kaos',
        'status_kain',
        'status_kaos',
    ];

    /**
     * Get the pendaftar for this logistik
     */
    public function pendaftar(): BelongsTo
    {
        return $this->belongsTo(Pendaftar::class, 'id_pendaftar', 'id_pendaftar');
    }

    /**
     * Get the status color for traffic light system
     */
    public function getStatusColorAttribute(): string
    {
        if ($this->status_bayar === 'Belum') {
            return 'red'; // Belum bayar
        } elseif ($this->status_kaos === 'Proses') {
            return 'yellow'; // Kaos dalam proses
        } elseif ($this->status_kaos === 'Sudah') {
            return 'green'; // Kaos sudah diterima
        }
        return 'gray';
    }
}
