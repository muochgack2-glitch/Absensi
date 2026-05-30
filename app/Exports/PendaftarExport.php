<?php

namespace App\Exports;

use App\Models\Pendaftar;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PendaftarExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Pendaftar::with(['logistik', 'masterJurusan'])->get();
    }

    /**
     * Define column headings
     */
    public function headings(): array
    {
        return [
            'No. Registrasi',
            'NISN',
            'NIK',
            'Nama Lengkap',
            'Email',
            'No. Telepon',
            'Tempat Lahir',
            'Tanggal Lahir',
            'Jenis Kelamin',
            'Agama',
            'Asal Sekolah',
            'Tahun Lulus',
            'Alamat',
            'Jurusan',
            'Nama Jaringan',
            'Gelombang',
            'Status Siswa',
            'Status Data',
            'Nama Ayah',
            'Pekerjaan Ayah',
            'Nama Ibu',
            'Pekerjaan Ibu',
            'No. HP Orang Tua',
            'Nama Wali',
            'No. HP Wali',
            'Status Bayar',
            'Ukuran Kaos',
            'Tanggal Daftar',
        ];
    }

    /**
     * Map data for each row
     */
    public function map($pendaftar): array
    {
        return [
            $pendaftar->no_registrasi,
            $pendaftar->nisn,
            $pendaftar->nik,
            $pendaftar->nama_lengkap,
            $pendaftar->email,
            $pendaftar->no_telepon,
            $pendaftar->tempat_lahir,
            $pendaftar->tanggal_lahir ? $pendaftar->tanggal_lahir->format('d/m/Y') : '',
            $pendaftar->jenis_kelamin === 'L' ? 'Laki-laki' : ($pendaftar->jenis_kelamin === 'P' ? 'Perempuan' : ''),
            $pendaftar->agama,
            $pendaftar->asal_sekolah,
            $pendaftar->tahun_lulus,
            $pendaftar->alamat,
            $pendaftar->masterJurusan ? $pendaftar->masterJurusan->nama : $pendaftar->jurusan,
            $pendaftar->nama_jaringan ?: '(Langsung)',
            $pendaftar->gelombang,
            $pendaftar->status_siswa,
            ucfirst($pendaftar->status_data),
            $pendaftar->nama_ayah,
            $pendaftar->pekerjaan_ayah,
            $pendaftar->nama_ibu,
            $pendaftar->pekerjaan_ibu,
            $pendaftar->no_hp_ortu,
            $pendaftar->nama_wali,
            $pendaftar->no_hp_wali,
            $pendaftar->logistik ? $pendaftar->logistik->status_bayar : 'Belum',
            $pendaftar->logistik ? $pendaftar->logistik->ukuran_kaos : '-',
            $pendaftar->created_at->format('d/m/Y H:i'),
        ];
    }

    /**
     * Style the worksheet
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row (header)
            1 => [
                'font' => ['bold' => true, 'size' => 12],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4']
                ],
                'font' => ['color' => ['rgb' => 'FFFFFF'], 'bold' => true],
            ],
        ];
    }
}
