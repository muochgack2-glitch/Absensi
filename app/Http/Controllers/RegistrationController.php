<?php

namespace App\Http\Controllers;

use App\Models\Pendaftar;
use App\Models\LogistikBayar;
use App\Models\Jurusan;
use App\Models\SettingSystem;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class RegistrationController extends Controller
{
    protected WhatsAppService $whatsappService;

    public function __construct(WhatsAppService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
    }

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

            // Send WhatsApp notification if enabled and phone number available
            $this->sendWhatsAppNotification($pendaftar, $jurusan);

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

        $settings = SettingSystem::instance()->toSettingsArray();

        return view('registration.receipt', compact('registrationData', 'settings'));
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

        $settings = SettingSystem::instance()->toSettingsArray();

        return view('registration.print-receipt', compact('registrationData', 'settings'));
    }

    /**
     * Send WhatsApp notification to new registrant
     * 
     * @param Pendaftar $pendaftar
     * @param Jurusan|null $jurusan
     * @return void
     */
    private function sendWhatsAppNotification(Pendaftar $pendaftar, ?Jurusan $jurusan): void
    {
        try {
            // Check if auto-send is enabled
            if (!$this->whatsappService->isAutoSendEnabled()) {
                Log::info('WhatsApp auto-send is disabled', [
                    'pendaftar_id' => $pendaftar->id_pendaftar,
                ]);
                return;
            }

            // Check if WhatsApp is connected
            if (!$this->whatsappService->isConnected()) {
                Log::warning('WhatsApp not connected, skipping notification', [
                    'pendaftar_id' => $pendaftar->id_pendaftar,
                ]);
                return;
            }

            // Determine phone number to send to
            $phoneNumber = $this->getPhoneNumber($pendaftar);
            
            if (!$phoneNumber) {
                Log::info('No phone number available for WhatsApp notification', [
                    'pendaftar_id' => $pendaftar->id_pendaftar,
                ]);
                return;
            }

            // Prepare data for template
            $data = [
                'nama' => $pendaftar->nama_lengkap,
                'no_pendaftaran' => $pendaftar->no_registrasi,
                'jurusan' => $jurusan?->nama_jurusan ?? $pendaftar->jurusan ?? 'N/A',
                'gelombang' => $pendaftar->gelombang ?? 'N/A',
                'portal_url' => url('/'),
                'sekolah' => config('app.name', 'SMK PGRI Blora'),
                'tanggal' => $pendaftar->tgl_daftar ? $pendaftar->tgl_daftar->format('d-m-Y') : now()->format('d-m-Y'),
            ];

            // Send using template
            $result = $this->whatsappService->sendWithTemplate(
                $phoneNumber,
                'welcome_registration', // Use existing template name
                $data,
                [
                    'pendaftar_id' => $pendaftar->id_pendaftar,
                    'type' => 'auto_registration',
                    'sent_by' => null, // System auto-send
                ]
            );

            if ($result['success']) {
                Log::info('WhatsApp notification sent successfully', [
                    'pendaftar_id' => $pendaftar->id_pendaftar,
                    'phone' => $phoneNumber,
                    'log_id' => $result['log_id'] ?? null,
                ]);
            } else {
                Log::warning('WhatsApp notification failed', [
                    'pendaftar_id' => $pendaftar->id_pendaftar,
                    'phone' => $phoneNumber,
                    'error' => $result['message'] ?? 'Unknown error',
                ]);
            }

        } catch (\Exception $e) {
            // Don't fail registration if WhatsApp fails
            Log::error('WhatsApp notification exception', [
                'pendaftar_id' => $pendaftar->id_pendaftar,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    /**
     * Get phone number for WhatsApp notification
     * Priority: no_hp_wali > no_hp_ortu > no_telepon
     * 
     * @param Pendaftar $pendaftar
     * @return string|null
     */
    private function getPhoneNumber(Pendaftar $pendaftar): ?string
    {
        // Priority 1: Wali phone number
        if (!empty($pendaftar->no_hp_wali)) {
            return $pendaftar->no_hp_wali;
        }

        // Priority 2: Parent phone number
        if (!empty($pendaftar->no_hp_ortu)) {
            return $pendaftar->no_hp_ortu;
        }

        // Priority 3: Student phone number
        if (!empty($pendaftar->no_telepon)) {
            return $pendaftar->no_telepon;
        }

        return null;
    }
}
