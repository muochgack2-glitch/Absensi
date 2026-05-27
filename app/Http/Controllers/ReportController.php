<?php

namespace App\Http\Controllers;

use App\Models\Jurusan;
use App\Models\Pendaftar;
use App\Models\LogistikBayar;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $gelombang  = $request->get('gelombang', 'all');
        $jurusanId  = $request->get('jurusan_id', 'all');

        $query = Pendaftar::with('logistik');
        if ($gelombang !== 'all') $query->where('gelombang', $gelombang);
        if ($jurusanId !== 'all') $query->where('jurusan_id', $jurusanId);
        $pendaftars = $query->get();

        $totalPendaftar  = $pendaftars->count();
        $totalLunas      = $pendaftars->filter(fn($p) => optional($p->logistik)->status_bayar === 'Lunas')->count();
        $totalBelumBayar = $totalPendaftar - $totalLunas;
        $totalSelesai    = $pendaftars->filter(fn($p) => optional($p->logistik)->status_kaos === 'Sudah')->count();

        $jurusanAktif = Jurusan::where('aktif', true)->orderBy('kode')->get();

        $perJurusan = [];
        foreach ($jurusanAktif as $j) {
            $group = $pendaftars->where('jurusan', $j->kode);
            $perJurusan[$j->kode] = [
                'total'  => $group->count(),
                'lunas'  => $group->filter(fn($p) => optional($p->logistik)->status_bayar === 'Lunas')->count(),
                'selesai'=> $group->filter(fn($p) => optional($p->logistik)->status_kaos === 'Sudah')->count(),
            ];
        }

        $perGelombang = $pendaftars->groupBy('gelombang')->map(fn($g) => [
            'total' => $g->count(),
            'lunas' => $g->filter(fn($p) => optional($p->logistik)->status_bayar === 'Lunas')->count(),
        ])->sortKeys();

        $perJaringan = $pendaftars
            ->groupBy(fn($p) => $p->nama_jaringan ?: '(Langsung)')
            ->map(function ($group, $nama) use ($jurusanAktif) {
                $jurusanCounts = [];
                foreach ($jurusanAktif as $j) {
                    $jurusanCounts[$j->kode] = $group->where('jurusan', $j->kode)->count();
                }
                return [
                    'nama'    => $nama,
                    'total'   => $group->count(),
                    'lunas'   => $group->filter(fn($p) => optional($p->logistik)->status_bayar === 'Lunas')->count(),
                    'jurusan' => $jurusanCounts,
                ];
            })
            ->sortByDesc('total')
            ->values();

        $perUkuranKaos = $pendaftars
            ->filter(fn($p) => optional($p->logistik)->ukuran_kaos)
            ->groupBy(fn($p) => $p->logistik->ukuran_kaos)
            ->map->count()
            ->sortKeys();

        $gelombangOptions = Pendaftar::select('gelombang')->distinct()->orderBy('gelombang')->pluck('gelombang');

        return view('reports.index', compact(
            'pendaftars', 'totalPendaftar', 'totalLunas', 'totalBelumBayar', 'totalSelesai',
            'perJurusan', 'perGelombang', 'perJaringan', 'perUkuranKaos',
            'gelombangOptions', 'gelombang', 'jurusanId', 'jurusanAktif'
        ));
    }

    public function stats(Request $request)
    {
        try {
            $gelombang = $request->get('gelombang', 'all');
            $jurusanId = $request->get('jurusan_id', 'all');

            $query = Pendaftar::with('logistik');
            if ($gelombang !== 'all') $query->where('gelombang', $gelombang);
            if ($jurusanId !== 'all') $query->where('jurusan_id', $jurusanId);
            $pendaftars = $query->get();

            $totalPendaftar   = $pendaftars->count();
            $totalLunas       = $pendaftars->filter(fn($p) => optional($p->logistik)->status_bayar === 'Lunas')->count();
            $totalBelumBayar  = $totalPendaftar - $totalLunas;
            
            // More reliable way to check if registered today
            $today = \Carbon\Carbon::now()->startOfDay();
            $tomorrow = \Carbon\Carbon::now()->addDay()->startOfDay();
            $totalBaruHariIni = $pendaftars->filter(function($p) use ($today, $tomorrow) {
                $regDate = $p->tgl_daftar ?? $p->created_at;
                if (!$regDate) return false;
                return $regDate->gte($today) && $regDate->lt($tomorrow);
            })->count();
            
            \Log::debug('Dashboard stats query', [
                'total_pendaftar' => $totalPendaftar,
                'total_baru_hari_ini' => $totalBaruHariIni,
                'today_start' => $today->toDateTimeString(),
                'tomorrow_start' => $tomorrow->toDateTimeString(),
                'sample_data' => $pendaftars->take(3)->map(fn($p) => [
                    'id' => $p->id_pendaftar,
                    'tgl_daftar' => $p->tgl_daftar?->toDateTimeString(),
                    'created_at' => $p->created_at?->toDateTimeString(),
                ])->toArray(),
            ]);
            
            $pctLunas         = $totalPendaftar > 0 ? round($totalLunas / $totalPendaftar * 100) : 0;

            $today = \Carbon\Carbon::now()->startOfDay();
            $tomorrow = \Carbon\Carbon::now()->addDay()->startOfDay();
            
            $perJurusanStats = $pendaftars->groupBy('jurusan')->map(function ($items, $jurusan) use ($today, $tomorrow) {
                $lunas = $items->filter(fn($p) => optional($p->logistik)->status_bayar === 'Lunas')->count();
                $baruHariIni = $items->filter(function($p) use ($today, $tomorrow) {
                    $regDate = $p->tgl_daftar ?? $p->created_at;
                    if (!$regDate) return false;
                    return $regDate->gte($today) && $regDate->lt($tomorrow);
                })->count();
                return [
                    'jurusan'          => $jurusan,
                    'totalPendaftar'   => $items->count(),
                    'totalBaruHariIni' => $baruHariIni,
                    'totalBelumBayar'  => $items->count() - $lunas,
                    'totalLunas'       => $lunas,
                ];
            })->sortKeys()->values();

            return response()->json([
                'totalPendaftar'   => $totalPendaftar,
                'totalLunas'       => $totalLunas,
                'totalBelumBayar'  => $totalBelumBayar,
                'totalBaruHariIni' => $totalBaruHariIni,
                'pctLunas'         => $pctLunas,
                'perJurusanStats'  => $perJurusanStats,
                'updatedAt'        => now()->format('H:i:s'),
            ], 200, [
                'Content-Type' => 'application/json; charset=utf-8',
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
            ]);
        } catch (\Exception $e) {
            \Log::error('Stats endpoint error: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json([
                'error' => 'Gagal mengambil data statistik',
                'message' => $e->getMessage(),
                'debug_class' => class_basename($e),
                'totalPendaftar'   => 0,
                'totalLunas'       => 0,
                'totalBelumBayar'  => 0,
                'totalBaruHariIni' => 0,
                'pctLunas'         => 0,
                'perJurusanStats'  => [],
                'updatedAt'        => now()->format('H:i:s'),
            ], 200, [
                'Content-Type' => 'application/json; charset=utf-8',
            ]);
        }
    }

    public function exportExcel(Request $request)
    {
        $gelombang = $request->get('gelombang', 'all');
        $jurusanId = $request->get('jurusan_id', 'all');

        $query = Pendaftar::with('logistik');
        if ($gelombang !== 'all') $query->where('gelombang', $gelombang);
        if ($jurusanId !== 'all') $query->where('jurusan_id', $jurusanId);
        $pendaftars = $query->orderBy('no_registrasi')->get();

        $filename = 'data-pendaftar-spmb-' . now()->format('Ymd-His') . '.csv';
        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma'              => 'no-cache',
        ];

        $callback = function () use ($pendaftars) {
            $h = fopen('php://output', 'w');
            fprintf($h, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($h, ['No. Registrasi','NISN','Nama Lengkap','Asal Sekolah','Jurusan','Alamat','Nama Jaringan','Gelombang','Tanggal Daftar','Status Daftar Ulang','Ukuran Kaos','Status Kain','Status Kaos']);
            foreach ($pendaftars as $p) {
                fputcsv($h, [
                    $p->no_registrasi, $p->nisn, $p->nama_lengkap, $p->asal_sekolah,
                    $p->jurusan, $p->alamat, $p->nama_jaringan ?? '-',
                    'Gelombang ' . $p->gelombang,
                    $p->tgl_daftar->format('d-m-Y H:i'),
                    optional($p->logistik)->status_bayar === 'Lunas' ? 'Sudah Daftar Ulang' : 'Belum Daftar Ulang',
                    optional($p->logistik)->ukuran_kaos ?? '-',
                    optional($p->logistik)->status_kain ?? '-',
                    optional($p->logistik)->status_kaos ?? '-',
                ]);
            }
            fclose($h);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportJaringanExcel(Request $request)
    {
        $pendaftars   = Pendaftar::with('logistik')->get();
        $jurusanAktif = Jurusan::where('aktif', true)->orderBy('kode')->pluck('kode');

        $perJaringan = $pendaftars
            ->groupBy(fn($p) => $p->nama_jaringan ?: '(Langsung)')
            ->map(function ($group, $nama) use ($jurusanAktif) {
                $row = [$nama, $group->count()];
                foreach ($jurusanAktif as $kode) {
                    $row[] = $group->where('jurusan', $kode)->count();
                }
                $lunas = $group->filter(fn($p) => optional($p->logistik)->status_bayar === 'Lunas')->count();
                $row[] = $lunas;
                $row[] = $group->count() - $lunas;
                return $row;
            })
            ->sortByDesc(fn($r) => $r[1])
            ->values();

        $filename = 'rekap-jaringan-spmb-' . now()->format('Ymd-His') . '.csv';
        $headers  = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($perJaringan, $pendaftars, $jurusanAktif) {
            $h = fopen('php://output', 'w');
            fprintf($h, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($h, ['Rekap Per Jaringan / Vendor - SPMB']);
            fputcsv($h, ['Dicetak: ' . now()->format('d-m-Y H:i')]);
            fputcsv($h, []);

            $csvHeader = ['Nama Jaringan', 'Total'];
            foreach ($jurusanAktif as $kode) { $csvHeader[] = $kode; }
            $csvHeader[] = 'Sudah Daftar Ulang';
            $csvHeader[] = 'Belum Daftar Ulang';
            fputcsv($h, $csvHeader);

            foreach ($perJaringan as $row) { fputcsv($h, $row); }
            fputcsv($h, []);
            fputcsv($h, ['TOTAL', $pendaftars->count()]);
            fclose($h);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportPdf(Request $request)
    {
        $gelombang = $request->get('gelombang', 'all');
        $jurusanId = $request->get('jurusan_id', 'all');

        $query = Pendaftar::with('logistik');
        if ($gelombang !== 'all') $query->where('gelombang', $gelombang);
        if ($jurusanId !== 'all') $query->where('jurusan_id', $jurusanId);
        $pendaftars = $query->orderBy('no_registrasi')->get();

        $jurusanAktif = Jurusan::where('aktif', true)->orderBy('kode')->get();

        $perJurusan = [];
        foreach ($jurusanAktif as $j) {
            $group = $pendaftars->where('jurusan', $j->kode);
            $perJurusan[$j->kode] = [
                'total' => $group->count(),
                'lunas' => $group->filter(fn($p) => optional($p->logistik)->status_bayar === 'Lunas')->count(),
            ];
        }

        $perGelombang = $pendaftars->groupBy('gelombang')->map->count()->sortKeys();
        $totalLunas   = $pendaftars->filter(fn($p) => optional($p->logistik)->status_bayar === 'Lunas')->count();

        $perJaringan = $pendaftars
            ->groupBy(fn($p) => $p->nama_jaringan ?: '(Langsung)')
            ->map(function ($group, $nama) use ($jurusanAktif) {
                $jurusanCounts = [];
                foreach ($jurusanAktif as $j) {
                    $jurusanCounts[$j->kode] = $group->where('jurusan', $j->kode)->count();
                }
                return [
                    'nama'    => $nama,
                    'total'   => $group->count(),
                    'lunas'   => $group->filter(fn($p) => optional($p->logistik)->status_bayar === 'Lunas')->count(),
                    'jurusan' => $jurusanCounts,
                ];
            })
            ->sortByDesc('total')
            ->values();

        $jurusan = $jurusanId !== 'all' ? (Jurusan::find($jurusanId)?->kode ?? 'all') : 'all';

        return view('reports.pdf', compact(
            'pendaftars', 'perJurusan', 'perGelombang', 'totalLunas',
            'perJaringan', 'gelombang', 'jurusan', 'jurusanAktif'
        ));
    }
}
