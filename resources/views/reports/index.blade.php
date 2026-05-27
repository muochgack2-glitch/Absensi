@extends('layouts.admin')

@section('title', 'Laporan & Ekspor - SPMB (Sistem Penerimaan Murid Baru)')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<style>
        :root {
            --accent: #22d3ee;
        }

        /* Page header */
        .page-header {
            background: linear-gradient(135deg, rgba(255,255,255,0.88), rgba(255,255,255,0.78));
            backdrop-filter: blur(10px);
            border: 1px solid rgba(99,102,241,0.14);
            border-radius: 18px;
            padding: 24px 28px;
            margin-bottom: 24px;
            box-shadow: 0 10px 30px rgba(15,23,42,0.08);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 16px;
        }
        .page-header h1 { font-size: 22px; font-weight: 800; color: #1e1b4b; margin: 0; }
        .page-header p { font-size: 13px; color: #64748b; margin: 2px 0 0; }

        /* Summary cards */
        .stat-card {
            background: linear-gradient(135deg, rgba(255,255,255,0.92), rgba(255,255,255,0.82));
            border: 1px solid rgba(148,163,184,0.18);
            border-radius: 16px;
            padding: 20px 22px;
            box-shadow: 0 8px 22px rgba(15,23,42,0.06);
            display: flex;
            align-items: center;
            gap: 16px;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .stat-card:hover { transform: translateY(-3px); box-shadow: 0 14px 26px rgba(15,23,42,0.12); }
        .stat-icon { width: 52px; height: 52px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 22px; flex-shrink: 0; }
        .stat-value { font-size: 28px; font-weight: 800; color: #1e1b4b; line-height: 1; }
        .stat-label { font-size: 12px; font-weight: 600; color: #64748b; margin-top: 4px; text-transform: uppercase; letter-spacing: 0.5px; }
        .stat-sub { font-size: 11px; color: #94a3b8; margin-top: 2px; }

        /* Filter bar */
        .filter-bar { background: rgba(255,255,255,0.9); border: 1px solid rgba(99,102,241,0.12); border-radius: 14px; padding: 16px 20px; margin-bottom: 20px; box-shadow: 0 6px 16px rgba(15,23,42,0.05); display: flex; align-items: center; gap: 12px; flex-wrap: wrap; }
        .filter-bar label { font-size: 13px; font-weight: 600; color: #374151; margin: 0; }
        .filter-bar select { border: 1.5px solid #e5e7eb; border-radius: 10px; padding: 8px 14px; font-size: 13px; font-family: 'Inter', sans-serif; color: #374151; min-width: 150px; }
        .filter-bar select:focus { border-color: var(--primary); outline: none; box-shadow: 0 0 0 3px rgba(99,102,241,0.1); }

        /* Export buttons */
        .export-group { display: flex; gap: 10px; flex-wrap: wrap; }
        .btn-export { display: inline-flex; align-items: center; gap: 8px; padding: 10px 20px; border-radius: 12px; font-size: 13px; font-weight: 700; font-family: 'Inter', sans-serif; border: none; cursor: pointer; text-decoration: none; transition: all 0.2s; }
        .btn-export:hover { transform: translateY(-2px); text-decoration: none; }
        .btn-excel { background: #d1fae5; color: #065f46; }
        .btn-excel:hover { background: #10b981; color: white; box-shadow: 0 6px 18px rgba(16,185,129,0.3); }
        .btn-jaringan { background: #e0e7ff; color: #3730a3; }
        .btn-jaringan:hover { background: var(--primary); color: white; box-shadow: 0 6px 18px rgba(99,102,241,0.3); }
        .btn-pdf { background: #fee2e2; color: #991b1b; }
        .btn-pdf:hover { background: #ef4444; color: white; box-shadow: 0 6px 18px rgba(239,68,68,0.3); }

        /* Section cards */
        .section-card { background: linear-gradient(135deg, rgba(255,255,255,0.92), rgba(255,255,255,0.84)); border: 1px solid rgba(148,163,184,0.14); border-radius: 16px; padding: 22px; box-shadow: 0 8px 22px rgba(15,23,42,0.05); margin-bottom: 20px; }
        .section-card .section-title { font-size: 15px; font-weight: 700; color: #1e1b4b; margin-bottom: 16px; display: flex; align-items: center; gap: 8px; }
        .section-card .section-title .icon { width: 30px; height: 30px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 14px; }

        /* Stats table */
        .stats-table { width: 100%; border-collapse: collapse; }
        .stats-table th { background: #f8fafc; padding: 10px 14px; text-align: left; font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid #f1f5f9; }
        .stats-table td { padding: 11px 14px; border-bottom: 1px solid #f8fafc; font-size: 13px; color: #1e293b; }
        .stats-table tr:last-child td { border-bottom: none; }
        .stats-table tr:hover td { background: #fafbff; }

        /* Progress bar */
        .prog-bar { height: 8px; background: #f1f5f9; border-radius: 50px; overflow: hidden; margin-top: 4px; }
        .prog-fill { height: 100%; border-radius: 50px; transition: width 0.6s ease; }

        /* Network table */
        .network-rank { width: 28px; height: 28px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 800; }
        .rank-1 { background: #fef3c7; color: #92400e; }
        .rank-2 { background: #f1f5f9; color: #475569; }
        .rank-3 { background: #fff7ed; color: #9a3412; }
        .rank-other { background: #f8fafc; color: #94a3b8; }

        /* Badge */
        .badge-pill { display: inline-block; padding: 3px 10px; border-radius: 50px; font-size: 11px; font-weight: 700; }
        .bp-blue { background: #e0e7ff; color: #3730a3; }
        .bp-green { background: #d1fae5; color: #065f46; }
        .bp-purple { background: #ede9fe; color: #5b21b6; }
</style>
@endpush

@section('content')
<!-- Page Header -->
            <div class="page-header">
                <div>
                    <h1><i class="fas fa-chart-pie" style="color:var(--primary);"></i> Laporan & Ekspor</h1>
                    <p>Rekap data pendaftar, statistik jaringan, dan ekspor ke Excel/PDF</p>
                </div>
                <div class="export-group">
                    <a href="{{ route('report.export.excel', request()->query()) }}" class="btn-export btn-excel">
                        <i class="fas fa-file-excel"></i> Export Excel
                    </a>
                    <a href="{{ route('report.export.jaringan') }}" class="btn-export btn-jaringan">
                        <i class="fas fa-network-wired"></i> Rekap Jaringan
                    </a>
                    <a href="{{ route('report.export.pdf', request()->query()) }}" class="btn-export btn-pdf" target="_blank">
                        <i class="fas fa-file-pdf"></i> Export PDF
                    </a>
                </div>
            </div>

            <!-- Filter Bar -->
            <form method="GET" action="{{ route('report.index') }}" class="filter-bar">
                <i class="fas fa-filter" style="color:var(--primary);"></i>
                <label>Gelombang:</label>
                <select name="gelombang" onchange="this.form.submit()">
                    <option value="all" {{ $gelombang === 'all' ? 'selected' : '' }}>Semua Gelombang</option>
                    @foreach ($gelombangOptions as $g)
                        <option value="{{ $g }}" {{ $gelombang == $g ? 'selected' : '' }}>Gelombang {{ $g }}</option>
                    @endforeach
                </select>

                <label>Jurusan:</label>
                <select name="jurusan_id" onchange="this.form.submit()">
                    <option value="all" {{ $jurusanId === 'all' ? 'selected' : '' }}>Semua Jurusan</option>
                    @foreach(($jurusanAktif ?? collect()) as $j)
                        <option value="{{ $j->id }}" {{ (string) $jurusanId === (string) $j->id ? 'selected' : '' }}>{{ $j->kode }}</option>
                    @endforeach
                </select>

                @if ($gelombang !== 'all' || $jurusanId !== 'all')
                    <a href="{{ route('report.index') }}" style="font-size:12px; color:#ef4444; text-decoration:none;">
                        <i class="fas fa-times"></i> Reset
                    </a>
                @endif
            </form>

            <!-- Summary Stats -->
            <div class="row g-3 mb-4">
                <div class="col-md-3 col-sm-6">
                    <div class="stat-card">
                        <div class="stat-icon" style="background:#eef2ff;">👥</div>
                        <div>
                            <div class="stat-value" id="totalPendaftar">{{ $totalPendaftar }}</div>
                            <div class="stat-label">Total Pendaftar</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="stat-card">
                        <div class="stat-icon" style="background:#d1fae5;">…</div>
                        <div>
                            <div class="stat-value" id="totalLunas" style="color:#059669;">{{ $totalLunas }}</div>
                            <div class="stat-label">Sudah Daftar Ulang</div>
                            <div class="stat-sub" id="pctLunas">{{ $totalPendaftar > 0 ? round($totalLunas/$totalPendaftar*100) : 0 }}% dari total</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="stat-card">
                        <div class="stat-icon" style="background:#fee2e2;">⏳</div>
                        <div>
                            <div class="stat-value" id="totalBelumBayar" style="color:#dc2626;">{{ $totalBelumBayar }}</div>
                            <div class="stat-label">Belum Daftar Ulang</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="stat-card">
                        <div class="stat-icon" style="background:#ede9fe;">…</div>
                        <div>
                            <div class="stat-value" id="totalSelesai" style="color:#7c3aed;">{{ $totalSelesai }}</div>
                            <div class="stat-label">Pendaftaran Selesai</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="row g-4 mb-4">
                <!-- Jurusan chart -->
                <div class="col-md-5">
                    <div class="section-card">
                        <div class="section-title">
                            <span class="icon" style="background:#eef2ff;color:var(--primary);">📊</span>
                            Distribusi per Jurusan
                        </div>
                        <canvas id="chartJurusan" height="200"></canvas>
                    </div>
                </div>

                <!-- Gelombang chart -->
                <div class="col-md-7">
                    <div class="section-card">
                        <div class="section-title">
                            <span class="icon" style="background:#d1fae5;color:#059669;">📈</span>
                            Pendaftar per Gelombang
                        </div>
                        <canvas id="chartGelombang" height="200"></canvas>
                    </div>
                </div>
            </div>

            <!-- Tables Row -->
            <div class="row g-4">
                <!-- Per Jurusan table -->
                <div class="col-md-5">
                    <div class="section-card">
                        <div class="section-title">
                            <span class="icon" style="background:#ede9fe;color:var(--secondary);">🎓</span>
                            Rekap per Jurusan
                        </div>
                        <table class="stats-table">
                            <thead>
                                <tr>
                                    <th>Jurusan</th>
                                    <th class="text-center">Total</th>
                                    <th class="text-center">Sudah Daftar Ulang</th>
                                    <th>Progress</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $jurusanCodes = collect($jurusanAktif ?? [])->pluck('kode')->values();
                                    $colors = collect($jurusanCodes)->mapWithKeys(function ($kode, $idx) {
                                        $palette = ['var(--primary)', 'var(--success)', 'var(--secondary)', '#0284c7', '#b45309', '#dc2626'];
                                        return [$kode => $palette[$idx % count($palette)]];
                                    })->all();
                                @endphp
                                @foreach ($jurusanCodes as $j)
                                    @php
                                        $data  = $perJurusan[$j] ?? ['total'=>0,'lunas'=>0,'selesai'=>0];
                                        $pct   = $data['total'] > 0 ? round($data['lunas']/$data['total']*100) : 0;
                                    @endphp
                                    <tr>
                                        <td>
                                            <span class="badge-pill" style="background:#eef2ff;color:#3730a3;">{{ $j }}</span>
                                        </td>
                                        <td class="text-center fw-bold">{{ $data['total'] }}</td>
                                        <td class="text-center" style="color:#059669;">{{ $data['lunas'] }}</td>
                                        <td style="min-width:80px;">
                                            <div style="font-size:10px;color:#94a3b8;">{{ $pct }}%</div>
                                            <div class="prog-bar">
                                                <div class="prog-fill" style="width:{{ $pct }}%;background:{{ $colors[$j] }};"></div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        @if ($perUkuranKaos->count())
                            <div class="section-title mt-4">
                                <span class="icon" style="background:#fef3c7;color:#92400e;">👕</span>
                                Rekap Ukuran Kaos
                            </div>
                            <table class="stats-table">
                                <thead>
                                    <tr><th>Ukuran</th><th class="text-center">Jumlah</th></tr>
                                </thead>
                                <tbody>
                                    @foreach ($perUkuranKaos as $size => $count)
                                        <tr>
                                            <td><span class="badge-pill" style="background:#fef3c7;color:#92400e;">{{ $size }}</span></td>
                                            <td class="text-center fw-bold">{{ $count }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>

                <!-- Jaringan table -->
                <div class="col-md-7">
                    <div class="section-card">
                        <div class="section-title">
                            <span class="icon" style="background:#e0f2fe;color:#0284c7;">🌐</span>
                            Rekap per Jaringan / Vendor
                            <span style="font-size:11px;font-weight:500;color:#94a3b8;margin-left:4px;">({{ $perJaringan->count() }} jaringan)</span>
                        </div>

                        <table class="stats-table">
                            <thead>
                                <tr>
                                    <th style="width:40px;">#</th>
                                    <th>Nama Jaringan</th>
                                    <th class="text-center">Total</th>
                                    @foreach(($jurusanAktif ?? collect()) as $mj)
                                        <th class="text-center">{{ $mj->kode }}</th>
                                    @endforeach
                                    <th class="text-center">Sudah Daftar Ulang</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($perJaringan as $idx => $j)
                                    @php
                                        $rankClass = match($idx) { 0 => 'rank-1', 1 => 'rank-2', 2 => 'rank-3', default => 'rank-other' };
                                        $rankSymbol = match($idx) { 0 => '🥇', 1 => '🥈', 2 => '🥉', default => $idx+1 };
                                        $lunasPct = $j['total'] > 0 ? round($j['lunas']/$j['total']*100) : 0;
                                    @endphp
                                    <tr>
                                        <td>
                                            <span class="network-rank {{ $rankClass }}">{{ $rankSymbol }}</span>
                                        </td>
                                        <td style="font-weight:600;">{{ $j['nama'] }}</td>
                                        <td class="text-center">
                                            <strong>{{ $j['total'] }}</strong>
                                            @if ($totalPendaftar > 0)
                                                <div style="font-size:10px;color:#94a3b8;">{{ round($j['total']/$totalPendaftar*100) }}%</div>
                                            @endif
                                        </td>
                                        @foreach(($jurusanAktif ?? collect()) as $mj)
                                            <td class="text-center">{{ $j['jurusan'][$mj->kode] ?? 0 }}</td>
                                        @endforeach
                                        <td class="text-center">
                                            <span style="color:#059669;font-weight:700;">{{ $j['lunas'] }}</span>
                                            <div style="font-size:10px;color:#94a3b8;">{{ $lunasPct }}%</div>
                                        </td>
                                    </tr>
                                @endforeach
                                @if ($perJaringan->isEmpty())
                                    <tr><td colspan="7" class="text-center text-muted py-3">Belum ada data</td></tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
        // --- Chart: Jurusan ---
        const jurusanData = @json($perJurusan);
        const jurusanLabels = Object.keys(jurusanData);
        const jurusanTotals = jurusanLabels.map(k => jurusanData[k].total);
        const jurusanColors = jurusanLabels.map((_, idx) => ['#6366f1', '#10b981', '#a855f7', '#0284c7', '#f59e0b', '#ef4444'][idx % 6]);

        new Chart(document.getElementById('chartJurusan'), {
            type: 'doughnut',
            data: {
                labels: jurusanLabels,
                datasets: [{
                    data: jurusanTotals,
                    backgroundColor: jurusanColors,
                    borderWidth: 3,
                    borderColor: '#ffffff',
                    hoverOffset: 6,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom', labels: { font: { family: 'Inter', size: 12 }, padding: 16 } },
                    tooltip: { callbacks: { label: ctx => ` ${ctx.label}: ${ctx.parsed} pendaftar` } }
                },
                cutout: '65%'
            }
        });

        // --- Chart: Gelombang ---
        const gelData = @json($perGelombang);
        const gelLabels = Object.keys(gelData).map(k => 'Gel. ' + k);
        const gelTotals = Object.values(gelData).map(v => v.total);
        const gelLunas  = Object.values(gelData).map(v => v.lunas);

        new Chart(document.getElementById('chartGelombang'), {
            type: 'bar',
            data: {
                labels: gelLabels,
                datasets: [
                    { label: 'Total', data: gelTotals, backgroundColor: 'rgba(99,102,241,0.15)', borderColor: '#6366f1', borderWidth: 2, borderRadius: 8 },
                    { label: 'Sudah Daftar Ulang', data: gelLunas,  backgroundColor: 'rgba(16,185,129,0.15)', borderColor: '#10b981', borderWidth: 2, borderRadius: 8 }
                ]
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'bottom', labels: { font: { family: 'Inter', size: 12 }, padding: 16 } } },
                scales: {
                    y: { beginAtZero: true, ticks: { precision: 0, font: { family: 'Inter' } }, grid: { color: '#f1f5f9' } },
                    x: { ticks: { font: { family: 'Inter' } }, grid: { display: false } }
                }
            }
        });
    </script>
@endpush


