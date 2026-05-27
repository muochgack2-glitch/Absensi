<?php

namespace App\Http\Controllers;

use App\Models\Jurusan;
use App\Models\Pendaftar;
use App\Models\SettingSystem;
use Illuminate\Http\Request;

class LandingController extends Controller
{
    /**
     * Display the landing page with dynamic statistics.
     */
    public function index(Request $request)
    {
        $settings = SettingSystem::instance()->toSettingsArray();

        $totalPendaftar = Pendaftar::count();
        $totalDiterima  = Pendaftar::where('status_siswa', 'Diterima')->count();
        $totalBelumDaftarUlang = Pendaftar::where('status_siswa', '!=', 'Diterima')->count();
        $totalDataAwal = Pendaftar::where('status_data', 'awal')->count();
        $totalBaruHariIni = Pendaftar::whereDate('created_at', today())->count();

        $stats = [
            'total_pendaftar' => $totalPendaftar,
            'belum_daftar_ulang' => $totalBelumDaftarUlang,
            'diterima' => $totalDiterima,
            'persen_diterima' => $totalPendaftar > 0 ? (int) round(($totalDiterima / $totalPendaftar) * 100) : 0,
            'data_awal' => $totalDataAwal,
            'baru_hari_ini' => $totalBaruHariIni,
        ];

        $jurusanAktif = Jurusan::where('aktif', true)->orderBy('kode')->get();
        $jurusanQuota = $jurusanAktif->mapWithKeys(fn($j) => [$j->kode => (int) $j->kuota])->all();

        $keyword    = trim((string) $request->query('cek', ''));
        $statusData = null;

        if ($keyword !== '') {
            $statusData = Pendaftar::with('logistik')
                ->where('no_registrasi', $keyword)
                ->orWhere('nisn', $keyword)
                ->first();
        }

        $registrationOpen = $settings['registration_status'] === 'open';
        $schoolName       = $settings['school_name'] ?: 'SPMB SIPDB';
        $title            = $schoolName . ' - Pendaftaran Online';
        $metaDescription  = 'Pendaftaran murid baru online ' . $schoolName
            . ' tahun ajaran ' . $settings['academic_year']
            . '. Cek status pendaftaran, lihat gelombang aktif, kuota jurusan, dan informasi sekolah secara real-time.';
        $metaImage = !empty($settings['school_logo'])
            ? asset('storage/' . $settings['school_logo'])
            : asset('images/og-image.svg');

        return view('landing.index', [
            'title'           => $title,
            'metaTitle'       => $title,
            'metaDescription' => $metaDescription,
            'metaKeywords'    => 'SPMB ' . $schoolName
                . ', pendaftaran murid baru ' . $settings['academic_year']
                . ', cek status pendaftaran, ' . ($settings['school_city'] ?: 'sekolah'),
            'metaAuthor'      => $schoolName,
            'metaSiteName'    => $schoolName,
            'metaImage'       => $metaImage,
            'settings'        => $settings,
            'stats'           => $stats,
            'keyword'         => $keyword,
            'statusData'      => $statusData,
            'registrationOpen' => $registrationOpen,
            'jurusanQuota' => $jurusanQuota,
        ]);
    }
}
