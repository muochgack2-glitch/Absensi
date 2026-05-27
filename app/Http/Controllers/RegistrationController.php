<?php

namespace App\Http\Controllers;

use App\Models\Pendaftar;
use App\Models\LogistikBayar;
use App\Models\Jurusan;
use App\Models\SettingSystem;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class RegistrationController extends Controller
{
    /**
     * Generate unique registration number
     */
    private function generateRegistrationNumber()
    {
        $tahun = Carbon::now()->year;
        $lastRegistration = Pendaftar::where('no_registrasi', 'like', 'SPMB-' . $tahun . '-%')
            ->orderByRaw('CAST(SUBSTRING(no_registrasi, -4) AS UNSIGNED) DESC')
            ->first();

        $nextNumber = 1;
        if ($lastRegistration) {
            $lastNumber = (int) substr($lastRegistration->no_registrasi, -4);
            $nextNumber = $lastNumber + 1;
        }

        return 'SPMB-' . $tahun . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Show the public registration form
     */
    public function showForm()
    {
        $jurusans = Jurusan::where('aktif', true)->orderBy('kode')->get();
        return view('registration.form', compact('jurusans'));
    }

    /**
     * Process the online registration
     */
    public function register(Request $request)
    {
        $request->validate([
            'nisn' => 'required|string|unique:pendaftar|min:10|max:10',
            'nama_lengkap' => 'required|string|max:100',
            'asal_sekolah' => 'required|string|max:100',
            'alamat' => 'required|string|max:255',
            'jurusan_id' => 'required|exists:jurusan,id',
            'nama_jaringan' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:100',
            'no_telepon' => 'nullable|string|max:20',
        ], [
            'nisn.required' => 'NISN harus diisi',
            'nisn.unique' => 'NISN ini sudah terdaftar',
            'nisn.min' => 'NISN harus 10 digit',
            'nisn.max' => 'NISN harus 10 digit',
            'nama_lengkap.required' => 'Nama lengkap harus diisi',
            'asal_sekolah.required' => 'Asal sekolah harus diisi',
            'alamat.required' => 'Alamat harus diisi',
            'jurusan_id.required' => 'Jurusan harus dipilih',
            'jurusan_id.exists' => 'Jurusan tidak valid',
        ]);

        try {
            $setting = SettingSystem::first();
            $noRegistrasi = $this->generateRegistrationNumber();

            // Create pendaftar
            $pendaftar = Pendaftar::create([
                'no_registrasi' => $noRegistrasi,
                'nisn' => $request->nisn,
                'nama_lengkap' => $request->nama_lengkap,
                'email' => $request->email,
                'no_telepon' => $request->no_telepon,
                'asal_sekolah' => $request->asal_sekolah,
                'alamat' => $request->alamat,
                'jurusan_id' => (int) $request->jurusan_id,
                'jurusan' => Jurusan::find($request->jurusan_id)?->kode ?? '-',
                'nama_jaringan' => $request->nama_jaringan,
                'gelombang' => $setting->gelombang_aktif,
                'tgl_daftar' => now(),
                'status_siswa' => 'Belum Daftar Ulang',
                'status_data' => 'awal',
            ]);

            // Create logistik entry
            LogistikBayar::create([
                'id_pendaftar' => $pendaftar->id_pendaftar,
                'status_bayar' => 'Belum',
                'status_kain' => 'Belum',
                'status_kaos' => 'Belum',
            ]);

            $tanggalDaftar = $pendaftar->tgl_daftar ?? $pendaftar->created_at ?? now();

            $jurusan = Jurusan::find((int) $request->jurusan_id);

            // Store in session for receipt display
            session([
                'registration_data' => [
                    'id' => $pendaftar->id_pendaftar,
                    'no_registrasi' => $noRegistrasi,
                    'nama_lengkap' => $pendaftar->nama_lengkap,
                    'nisn' => $pendaftar->nisn,
                    'jurusan' => $pendaftar->jurusan,
                    'jurusan_nama' => $jurusan?->nama,
                    'asal_sekolah' => $pendaftar->asal_sekolah,
                    'gelombang' => $pendaftar->gelombang,
                    'tgl_daftar' => $tanggalDaftar->format('d-m-Y H:i'),
                ]
            ]);

            return redirect()->route('registration.receipt')
                ->with('success', 'Pendaftaran berhasil!');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    /**
     * Show registration receipt
     */
    public function showReceipt()
    {
        $registrationData = session('registration_data');
        
        if (!$registrationData) {
            return redirect()->route('registration.form')
                ->withErrors(['error' => 'Silahkan daftar terlebih dahulu']);
        }

        return view('registration.receipt', compact('registrationData'));
    }

    /**
     * Print receipt as PDF
     */
    public function printReceipt()
    {
        $registrationData = session('registration_data');
        
        if (!$registrationData) {
            return redirect()->route('registration.form');
        }

        return view('registration.print-receipt', compact('registrationData'));
    }
}
