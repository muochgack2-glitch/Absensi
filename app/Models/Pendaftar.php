<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Pendaftar extends Model
{
    protected $table = 'pendaftar';
    protected $primaryKey = 'id_pendaftar';
    
    protected $fillable = [
        'no_registrasi',
        'nisn',
        'nik',
        'no_kip',
        'nama_lengkap',
        'email',
        'no_telepon',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'agama',
        'asal_sekolah',
        'tahun_lulus',
        'alamat',
        'alamat_jalan',
        'alamat_dukuh',
        'alamat_rt',
        'alamat_rw',
        'alamat_kelurahan',
        'alamat_kecamatan',
        'alamat_kabupaten',
        'alamat_provinsi',
        'nama_ayah',
        'pekerjaan_ayah',
        'alamat_ayah',
        'nama_ibu',
        'pekerjaan_ibu',
        'alamat_ibu',
        'no_hp_ortu',
        'nama_wali',
        'pekerjaan_wali',
        'alamat_wali',
        'jurusan',
        'jurusan_id',
        'nama_jaringan',
        'gelombang',
        'tgl_daftar',
        'status_siswa',
        'status_data',
    ];

    protected $casts = [
        'tgl_daftar' => 'datetime',
        'tanggal_lahir' => 'date',
    ];

    /**
     * Get the logistik for this pendaftar
     */
    public function logistik(): HasOne
    {
        return $this->hasOne(LogistikBayar::class, 'id_pendaftar', 'id_pendaftar');
    }

    public function masterJurusan(): BelongsTo
    {
        return $this->belongsTo(Jurusan::class, 'jurusan_id');
    }
}
