<?php

namespace Database\Seeders;

use App\Models\LogistikBayar;
use App\Models\Pendaftar;
use Illuminate\Database\Seeder;

class PendaftarSeeder extends Seeder
{
    public function run(): void
    {
        $pendaftars = array (
  0 => 
  array (
    'no_registrasi' => 'SPMB-2026-0001',
    'nisn' => '1234567890',
    'nik' => NULL,
    'nama_lengkap' => 'Alimatul Ufma',
    'email' => NULL,
    'no_telepon' => '62883139147095',
    'tempat_lahir' => NULL,
    'tanggal_lahir' => NULL,
    'jenis_kelamin' => NULL,
    'agama' => NULL,
    'asal_sekolah' => 'SMP EDIT',
    'tahun_lulus' => NULL,
    'alamat' => 'Edit',
    'nama_ayah' => NULL,
    'pekerjaan_ayah' => NULL,
    'nama_ibu' => NULL,
    'pekerjaan_ibu' => NULL,
    'no_hp_ortu' => NULL,
    'jurusan' => 'AKL',
    'jurusan_id' => 2,
    'nama_jaringan' => 'PANITIA',
    'gelombang' => 2,
    'tgl_daftar' => '2026-05-28 00:53:17',
    'status_siswa' => 'Diterima',
    'status_data' => 'Awal',
    'logistik' => 
    array (
      'status_bayar' => 'Lunas',
      'status_kain' => 'Sudah',
      'status_kaos' => 'Proses',
      'ukuran_kaos' => 'M',
    ),
  ),
);

        foreach ($pendaftars as $data) {
            $logistik = $data['logistik'] ?? null;
            unset($data['logistik']);

            $jurusanMap = [
                'MPLB' => 1,
                'AKL' => 2,
                'BUSANA' => 3,
            ];
            $data['jurusan_id'] = $jurusanMap[$data['jurusan']] ?? null;

            $pendaftar = Pendaftar::updateOrCreate(
                ['no_registrasi' => $data['no_registrasi']],
                $data
            );

            if ($logistik) {
                LogistikBayar::updateOrCreate(
                    ['id_pendaftar' => $pendaftar->id_pendaftar],
                    $logistik
                );
            }
        }
    }
}