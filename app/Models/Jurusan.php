<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Jurusan extends Model
{
    protected $table = 'jurusan';

    protected $fillable = [
        'kode',
        'nama',
        'aktif',
        'kuota',
    ];

    protected $casts = [
        'aktif' => 'boolean',
        'kuota' => 'integer',
    ];

    public function pendaftars(): HasMany
    {
        return $this->hasMany(Pendaftar::class, 'jurusan_id');
    }
}
