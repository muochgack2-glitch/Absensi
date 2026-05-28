<?php

namespace Database\Seeders;

use App\Models\Jurusan;
use Illuminate\Database\Seeder;

class JurusanSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['kode' => 'MPLB', 'nama' => 'Manajemen Perkantoran dan Layanan Bisnis', 'aktif' => true, 'kuota' => 0],
            ['kode' => 'AKL', 'nama' => 'Akuntansi dan Keuangan Lembaga', 'aktif' => true, 'kuota' => 0],
            ['kode' => 'BUSANA', 'nama' => 'Busana', 'aktif' => true, 'kuota' => 0],
        ];

        foreach ($items as $item) {
            Jurusan::updateOrCreate(['kode' => $item['kode']], $item);
        }
    }
}
