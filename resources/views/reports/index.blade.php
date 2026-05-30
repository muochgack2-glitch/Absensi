@extends('layouts.admin')

@section('title', 'Laporan & Ekspor - SPMB')

@push('styles')
<style>
    .dashboard-content {
        animation: zoomFadeIn 0.35s ease-out;
    }

    @keyframes zoomFadeIn {
        from {
            opacity: 0;
            transform: scale(0.97);
        }
        to {
            opacity: 1;
            transform: scale(1);
        }
    }
</style>
@endpush

@section('content')
<div class="dashboard-content">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-3">
        <div>
            <h2 class="mb-2">
                <i class="fas fa-chart-pie me-2" style="color:var(--primary);"></i>
                Laporan & Ekspor
            </h2>
            <p class="text-muted mb-0">Rekap data pendaftar, statistik jaringan, dan ekspor ke Excel/PDF</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <x-button variant="success" icon="fas fa-file-excel" href="{{ route('report.export.excel', request()->query()) }}">
                Export Excel
            </x-button>
            <x-button variant="info" icon="fas fa-network-wired" href="{{ route('report.export.jaringan') }}">
                Rekap Jaringan
            </x-button>
            <x-button variant="danger" icon="fas fa-file-pdf" href="{{ route('report.export.pdf', request()->query()) }}">
                Export PDF
            </x-button>
        </div>
    </div>

    <!-- Filter Bar -->
    <x-section-card title="Filter Data" icon="fas fa-filter" class="mb-4">
        <form method="GET" action="{{ route('report.index') }}" class="row g-3">
            <div class="col-md-4">
                <x-form-group label="Gelombang" name="gelombang">
                    <x-select name="gelombang" onchange="this.form.submit()">
                        <option value="all" {{ $gelombang === 'all' ? 'selected' : '' }}>Semua Gelombang</option>
                        @foreach ($gelombangOptions as $g)
                            <option value="{{ $g }}" {{ $gelombang == $g ? 'selected' : '' }}>Gelombang {{ $g }}</option>
                        @endforeach
                    </x-select>
                </x-form-group>
            </div>

            <div class="col-md-4">
                <x-form-group label="Jurusan" name="jurusan_id">
                    <x-select name="jurusan_id" onchange="this.form.submit()">
                        <option value="all" {{ $jurusanId === 'all' ? 'selected' : '' }}>Semua Jurusan</option>
                        @foreach(($jurusanAktif ?? collect()) as $j)
                            <option value="{{ $j->id }}" {{ (string) $jurusanId === (string) $j->id ? 'selected' : '' }}>{{ $j->kode }}</option>
                        @endforeach
                    </x-select>
                </x-form-group>
            </div>

            <div class="col-md-4 d-flex align-items-end">
                @if ($gelombang !== 'all' || $jurusanId !== 'all')
                    <x-button variant="secondary" outline="true" icon="fas fa-times" href="{{ route('report.index') }}">
                        Reset Filter
                    </x-button>
                @endif
            </div>
        </form>
    </x-section-card>

    <!-- Summary Stats -->
    <div class="row g-4 mb-4">
        <div class="col-md-3 col-sm-6">
            <x-stat-card 
                icon="fas fa-users" 
                label="Total Pendaftar" 
                value="{{ $totalPendaftar }}"
                color="blue"
                description="Semua data"
            />
        </div>
        <div class="col-md-3 col-sm-6">
            <x-stat-card 
                icon="fas fa-check-circle" 
                label="Sudah Daftar Ulang" 
                value="{{ $totalLunas }}"
                color="green"
                :trend="$totalPendaftar > 0 ? round($totalLunas/$totalPendaftar*100) . '%' : '0%'"
                description="dari total"
            />
        </div>
        <div class="col-md-3 col-sm-6">
            <x-stat-card 
                icon="fas fa-hourglass-half" 
                label="Belum Daftar Ulang" 
                value="{{ $totalBelumBayar }}"
                color="red"
                description="Perlu tindak lanjut"
            />
        </div>
        <div class="col-md-3 col-sm-6">
            <x-stat-card 
                icon="fas fa-clipboard-check" 
                label="Pendaftaran Selesai" 
                value="{{ $totalSelesai }}"
                color="purple"
                description="Terverifikasi"
            />
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-4 mb-4">
        <!-- Jurusan chart -->
        <div class="col-md-5">
            <x-section-card title="Distribusi per Jurusan" icon="fas fa-chart-bar">
                <canvas id="chartJurusan" height="200"></canvas>
            </x-section-card>
        </div>

        <!-- Gelombang chart -->
        <div class="col-md-7">
            <x-section-card title="Pendaftar per Gelombang" icon="fas fa-chart-line">
                <canvas id="chartGelombang" height="200"></canvas>
            </x-section-card>
        </div>
    </div>

    <!-- Tables Row -->
    <div class="row g-4">
        <!-- Per Jurusan table -->
        <div class="col-md-5">
            <x-section-card title="Rekap per Jurusan" icon="fas fa-graduation-cap">
                <x-table size="sm">
                    <x-slot:header>
                        <tr>
                            <th>Jurusan</th>
                            <th class="text-center">Total</th>
                            <th class="text-center">Daftar Ulang</th>
                            <th>Progress</th>
                        </tr>
                    </x-slot:header>
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
                                <span class="badge bg-primary">{{ $j }}</span>
                            </td>
                            <td class="text-center fw-bold">{{ $data['total'] }}</td>
                            <td class="text-center text-success fw-bold">{{ $data['lunas'] }}</td>
                            <td style="min-width:80px;">
                                <small class="text-muted">{{ $pct }}%</small>
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar" style="width:{{ $pct }}%;background:{{ $colors[$j] }};"></div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </x-table>

                @if ($perUkuranKaos->count())
                    <hr class="my-4">
                    <h6 class="mb-3"><i class="fas fa-tshirt me-2"></i>Rekap Ukuran Kaos</h6>
                    <x-table size="sm">
                        <x-slot:header>
                            <tr><th>Ukuran</th><th class="text-center">Jumlah</th></tr>
                        </x-slot:header>
                        @foreach ($perUkuranKaos as $size => $count)
                            <tr>
                                <td><span class="badge bg-warning text-dark">{{ $size }}</span></td>
                                <td class="text-center fw-bold">{{ $count }}</td>
                            </tr>
                        @endforeach
                    </x-table>
                @endif
            </x-section-card>
        </div>

        <!-- Jaringan table -->
        <div class="col-md-7">
            <x-section-card title="Rekap per Jaringan / Vendor" icon="fas fa-network-wired" :badge="$perJaringan->count() . ' jaringan'">
                <x-table size="sm">
                    <x-slot:header>
                        <tr>
                            <th style="width:40px;">#</th>
                            <th>Nama Jaringan</th>
                            <th class="text-center">Total</th>
                            @foreach(($jurusanAktif ?? collect()) as $mj)
                                <th class="text-center">{{ $mj->kode }}</th>
                            @endforeach
                            <th class="text-center">Daftar Ulang</th>
                        </tr>
                    </x-slot:header>
                    @forelse ($perJaringan as $idx => $j)
                        @php
                            $rankBadge = match($idx) { 
                                0 => '<span class="badge bg-warning text-dark">🥇</span>', 
                                1 => '<span class="badge bg-secondary">🥈</span>', 
                                2 => '<span class="badge bg-info">🥉</span>', 
                                default => '<span class="badge bg-light text-dark">' . ($idx+1) . '</span>'
                            };
                            $lunasPct = $j['total'] > 0 ? round($j['lunas']/$j['total']*100) : 0;
                        @endphp
                        <tr>
                            <td>{!! $rankBadge !!}</td>
                            <td class="fw-semibold">{{ $j['nama'] }}</td>
                            <td class="text-center">
                                <strong>{{ $j['total'] }}</strong>
                                @if ($totalPendaftar > 0)
                                    <br><small class="text-muted">{{ round($j['total']/$totalPendaftar*100) }}%</small>
                                @endif
                            </td>
                            @foreach(($jurusanAktif ?? collect()) as $mj)
                                <td class="text-center">{{ $j['jurusan'][$mj->kode] ?? 0 }}</td>
                            @endforeach
                            <td class="text-center">
                                <span class="text-success fw-bold">{{ $j['lunas'] }}</span>
                                <br><small class="text-muted">{{ $lunasPct }}%</small>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted py-3">Belum ada data</td></tr>
                    @endforelse
                </x-table>
            </x-section-card>
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
