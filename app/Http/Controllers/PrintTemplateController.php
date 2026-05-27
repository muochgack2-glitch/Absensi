<?php

namespace App\Http\Controllers;

use App\Models\Pendaftar;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PrintTemplateController extends Controller
{
    /**
     * Generate Bukti Registrasi PDF
     */
    public function buktiRegistrasi($id)
    {
        $pendaftar = Pendaftar::findOrFail($id);
        
        $pdf = Pdf::loadView('prints.bukti-registrasi', compact('pendaftar'))
            ->setPaper('A4')
            ->setOption('margin-top', 10)
            ->setOption('margin-bottom', 10)
            ->setOption('margin-left', 15)
            ->setOption('margin-right', 15);
        
        return $pdf->download('Bukti_Registrasi_' . $pendaftar->no_pendaftaran . '.pdf');
    }

    /**
     * Generate Formulir Lengkap PDF
     */
    public function formulirLengkap($id)
    {
        $pendaftar = Pendaftar::findOrFail($id);
        
        $pdf = Pdf::loadView('prints.formulir-lengkap', compact('pendaftar'))
            ->setPaper('A4')
            ->setOption('margin-top', 10)
            ->setOption('margin-bottom', 10)
            ->setOption('margin-left', 15)
            ->setOption('margin-right', 15);
        
        return $pdf->download('Formulir_Lengkap_' . $pendaftar->no_pendaftaran . '.pdf');
    }

    /**
     * Generate Bukti Ambil Barang PDF
     */
    public function buktiAmbilBarang($id)
    {
        $pendaftar = Pendaftar::findOrFail($id);
        
        $pdf = Pdf::loadView('prints.bukti-ambil-barang', compact('pendaftar'))
            ->setPaper('A4')
            ->setOption('margin-top', 10)
            ->setOption('margin-bottom', 10)
            ->setOption('margin-left', 15)
            ->setOption('margin-right', 15);
        
        return $pdf->download('Bukti_Ambil_Barang_' . $pendaftar->no_pendaftaran . '.pdf');
    }

    /**
     * View Bukti Registrasi (inline display)
     */
    public function viewBuktiRegistrasi($id)
    {
        $pendaftar = Pendaftar::findOrFail($id);
        
        $pdf = Pdf::loadView('prints.bukti-registrasi', compact('pendaftar'))
            ->setPaper('A4')
            ->setOption('margin-top', 10)
            ->setOption('margin-bottom', 10)
            ->setOption('margin-left', 15)
            ->setOption('margin-right', 15);
        
        return $pdf->stream('Bukti_Registrasi_' . $pendaftar->no_pendaftaran . '.pdf');
    }

    /**
     * View Formulir Lengkap (inline display)
     */
    public function viewFormulirLengkap($id)
    {
        $pendaftar = Pendaftar::findOrFail($id);
        
        $pdf = Pdf::loadView('prints.formulir-lengkap', compact('pendaftar'))
            ->setPaper('A4')
            ->setOption('margin-top', 10)
            ->setOption('margin-bottom', 10)
            ->setOption('margin-left', 15)
            ->setOption('margin-right', 15);
        
        return $pdf->stream('Formulir_Lengkap_' . $pendaftar->no_pendaftaran . '.pdf');
    }

    /**
     * View Bukti Ambil Barang (inline display)
     */
    public function viewBuktiAmbilBarang($id)
    {
        $pendaftar = Pendaftar::findOrFail($id);
        
        $pdf = Pdf::loadView('prints.bukti-ambil-barang', compact('pendaftar'))
            ->setPaper('A4')
            ->setOption('margin-top', 10)
            ->setOption('margin-bottom', 10)
            ->setOption('margin-left', 15)
            ->setOption('margin-right', 15);
        
        return $pdf->stream('Bukti_Ambil_Barang_' . $pendaftar->no_pendaftaran . '.pdf');
    }
}
