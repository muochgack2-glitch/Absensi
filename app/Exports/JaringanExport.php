<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class JaringanExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize, WithTitle
{
    protected $perJaringan;
    protected $jurusanAktif;
    protected $totalPendaftar;

    public function __construct($perJaringan, $jurusanAktif, $totalPendaftar)
    {
        $this->perJaringan = $perJaringan;
        $this->jurusanAktif = $jurusanAktif;
        $this->totalPendaftar = $totalPendaftar;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $rows = collect();
        
        // Add header info rows
        $rows->push(['Rekap Per Jaringan / Vendor - SPMB']);
        $rows->push(['Dicetak: ' . now()->format('d-m-Y H:i')]);
        $rows->push([]); // Empty row

        // Add data rows
        foreach ($this->perJaringan as $data) {
            $row = [$data['nama'], $data['total']];
            
            foreach ($this->jurusanAktif as $kode) {
                $row[] = $data['jurusan'][$kode] ?? 0;
            }
            
            $row[] = $data['lunas'];
            $row[] = $data['total'] - $data['lunas'];
            
            $rows->push($row);
        }

        // Add total row
        $rows->push([]);
        $totalRow = ['TOTAL', $this->totalPendaftar];
        for ($i = 0; $i < count($this->jurusanAktif) + 2; $i++) {
            $totalRow[] = '';
        }
        $rows->push($totalRow);

        return $rows;
    }

    /**
     * Define column headings (after info rows)
     */
    public function headings(): array
    {
        // This will be row 4 (after header info)
        $headers = ['Nama Jaringan', 'Total'];
        
        foreach ($this->jurusanAktif as $kode) {
            $headers[] = $kode;
        }
        
        $headers[] = 'Sudah Daftar Ulang';
        $headers[] = 'Belum Daftar Ulang';
        
        return $headers;
    }

    /**
     * Style the worksheet
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Title row
            1 => [
                'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => '0284c7']],
            ],
            // Date row
            2 => [
                'font' => ['size' => 10, 'color' => ['rgb' => '64748b']],
            ],
            // Header row (row 4)
            4 => [
                'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '0284c7']
                ],
            ],
        ];
    }

    /**
     * Sheet title
     */
    public function title(): string
    {
        return 'Rekap Jaringan';
    }
}
