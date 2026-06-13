@extends('layouts.admin')

@section('title', 'Detail Tahun Pelajaran ' . $ta->tahun)

@section('content')
<div class="container-fluid">
    <!-- Back Button -->
    <div class="mb-3">
        <a href="{{ route('admin.tahun-ajaran.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0" style="color: var(--text-primary);">📅 Detail Tahun Pelajaran {{ $ta->tahun }}</h1>
            <p class="small mb-0" style="color: var(--text-secondary);">Statistik dan informasi lengkap</p>
        </div>
        <div>
            <span class="badge bg-{{ $ta->getStatusBadgeColor() }} fs-5">{{ $ta->getStatusLabel() }}</span>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm border-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 style="color: var(--text-secondary);" class="mb-1">Total Pendaftar</h6>
                            <h2 class="mb-0" style="color: var(--text-primary);">{{ number_format($stats['total_pendaftar']) }}</h2>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-3 rounded">
                            <i class="fas fa-users fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 style="color: var(--text-secondary);" class="mb-1">Diterima</h6>
                            <h2 class="mb-0 text-success">{{ number_format($stats['diterima']) }}</h2>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded">
                            <i class="fas fa-check-circle fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 style="color: var(--text-secondary);" class="mb-1">Belum Daftar Ulang</h6>
                            <h2 class="mb-0 text-warning">{{ number_format($stats['belum_daftar_ulang']) }}</h2>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-3 rounded">
                            <i class="fas fa-clock fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-info">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 style="color: var(--text-secondary);" class="mb-1">Counter</h6>
                            <h2 class="mb-0 text-info">{{ number_format($ta->reg_number_current) }}</h2>
                        </div>
                        <div class="bg-info bg-opacity-10 p-3 rounded">
                            <i class="fas fa-hashtag fa-2x text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Info Card -->
        <div class="col-lg-4">
            <div class="card shadow-sm mb-4" style="background: var(--bg-primary); border-color: var(--border-light);">
                <div class="card-header" style="background: var(--bg-secondary); border-bottom: 1px solid var(--border-light);">
                    <h5 class="mb-0" style="color: var(--text-primary);"><i class="fas fa-info-circle me-2"></i>Informasi</h5>
                </div>
                <div class="card-body" style="background: var(--bg-primary);">
                    <table class="table table-sm mb-0">
                        <tr>
                            <td style="color: var(--text-secondary); border-color: var(--border-light);">Status</td>
                            <td style="border-color: var(--border-light);"><span class="badge bg-{{ $ta->getStatusBadgeColor() }}">{{ $ta->getStatusLabel() }}</span></td>
                        </tr>
                        <tr>
                            <td style="color: var(--text-secondary); border-color: var(--border-light);">Format No. Reg</td>
                            <td style="border-color: var(--border-light);"><code style="color: var(--text-primary); background: var(--bg-tertiary); padding: 2px 6px; border-radius: 4px;">{{ $ta->reg_number_pattern }}</code></td>
                        </tr>
                        <tr>
                            <td style="color: var(--text-secondary); border-color: var(--border-light);">Next Number</td>
                            <td style="border-color: var(--border-light);"><strong style="color: var(--text-primary);">SPMB-{{ $ta->getYearNumber() }}-{{ str_pad($ta->reg_number_current + 1, 4, '0', STR_PAD_LEFT) }}</strong></td>
                        </tr>
                        <tr>
                            <td style="color: var(--text-secondary); border-color: var(--border-light);">Periode Mulai</td>
                            <td style="color: var(--text-primary); border-color: var(--border-light);">{{ $ta->started_at ? $ta->started_at->format('d M Y') : '-' }}</td>
                        </tr>
                        <tr>
                            <td style="color: var(--text-secondary); border-color: var(--border-light);">Periode Selesai</td>
                            <td style="color: var(--text-primary); border-color: var(--border-light);">{{ $ta->closed_at ? $ta->closed_at->format('d M Y') : '-' }}</td>
                        </tr>
                        <tr>
                            <td style="color: var(--text-secondary); border-color: var(--border-light);">Dibuat Oleh</td>
                            <td style="color: var(--text-primary); border-color: var(--border-light);">{{ $ta->creator->name ?? 'System' }}</td>
                        </tr>
                        <tr>
                            <td style="color: var(--text-secondary); border-color: var(--border-light);">Dibuat Pada</td>
                            <td style="color: var(--text-primary); border-color: var(--border-light);">{{ $ta->created_at->format('d M Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Actions Card -->
            <div class="card shadow-sm" style="background: var(--bg-primary); border-color: var(--border-light);">
                <div class="card-header" style="background: var(--bg-secondary); border-bottom: 1px solid var(--border-light);">
                    <h5 class="mb-0" style="color: var(--text-primary);"><i class="fas fa-cog me-2"></i>Aksi</h5>
                </div>
                <div class="card-body" style="background: var(--bg-primary);">
                    @if(!$ta->isActive())
                    <button class="btn btn-success w-100 mb-2" onclick="activateTahunAjaran({{ $ta->id }}, '{{ $ta->tahun }}')">
                        <i class="fas fa-check-circle"></i> Aktifkan Tahun Pelajaran
                    </button>
                    @endif

                    @if($ta->status === 'upcoming')
                    <button class="btn btn-secondary w-100 mb-2" onclick="archiveTahunAjaran({{ $ta->id }}, '{{ $ta->tahun }}')">
                        <i class="fas fa-archive"></i> Arsipkan
                    </button>
                    @endif

                    <button class="btn btn-primary w-100 mb-2" onclick="updateStatistics({{ $ta->id }})">
                        <i class="fas fa-sync"></i> Update Statistik
                    </button>

                    <a href="#pendaftar-list" class="btn btn-outline-primary w-100" onclick="smoothScroll(event, 'pendaftar-list')">
                        <i class="fas fa-list"></i> Lihat Pendaftar
                    </a>
                </div>
            </div>
        </div>

        <!-- Chart Card -->
        <div class="col-lg-8">
            <div class="card shadow-sm" style="background: var(--bg-primary); border-color: var(--border-light);">
                <div class="card-header" style="background: var(--bg-secondary); border-bottom: 1px solid var(--border-light);">
                    <h5 class="mb-0" style="color: var(--text-primary);"><i class="fas fa-chart-line me-2"></i>Grafik Pendaftar per Bulan</h5>
                </div>
                <div class="card-body" style="background: var(--bg-primary);">
                    <canvas id="chartPendaftar" style="height: 300px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Pendaftar List Section -->
    <div class="row mt-4" id="pendaftar-list">
        <div class="col-12">
            <div class="card shadow-sm" style="background: var(--bg-primary); border-color: var(--border-light);">
                <div class="card-header" style="background: var(--bg-secondary); border-bottom: 1px solid var(--border-light);">
                    <h5 class="mb-0" style="color: var(--text-primary);"><i class="fas fa-users me-2"></i>Daftar Pendaftar ({{ number_format($ta->total_pendaftar) }} siswa)</h5>
                </div>
                <div class="card-body" style="background: var(--bg-primary);">
                    @if($ta->pendaftars->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead style="background: var(--bg-secondary); border-bottom: 2px solid var(--border-light);">
                                <tr>
                                    <th style="color: var(--text-primary);">No. Reg</th>
                                    <th style="color: var(--text-primary);">Nama Lengkap</th>
                                    <th style="color: var(--text-primary);">NISN</th>
                                    <th style="color: var(--text-primary);">Jurusan</th>
                                    <th style="color: var(--text-primary);">Status</th>
                                    <th style="color: var(--text-primary);">Tgl Daftar</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($ta->pendaftars->take(50) as $pendaftar)
                                <tr style="border-color: var(--border-light);">
                                    <td style="border-color: var(--border-light);"><strong style="color: var(--text-primary);">{{ $pendaftar->no_registrasi }}</strong></td>
                                    <td style="color: var(--text-primary); border-color: var(--border-light);">{{ $pendaftar->nama_lengkap }}</td>
                                    <td style="color: var(--text-primary); border-color: var(--border-light);">{{ $pendaftar->nisn }}</td>
                                    <td style="color: var(--text-primary); border-color: var(--border-light);">{{ $pendaftar->jurusan }}</td>
                                    <td style="border-color: var(--border-light);">
                                        @php
                                            $statusColor = match($pendaftar->status_siswa) {
                                                'Diterima' => 'success',
                                                'Pending' => 'warning',
                                                'Ditolak' => 'danger',
                                                'Belum Daftar Ulang' => 'secondary',
                                                default => 'secondary'
                                            };
                                        @endphp
                                        <span class="badge bg-{{ $statusColor }}">{{ $pendaftar->status_siswa }}</span>
                                    </td>
                                    <td style="color: var(--text-primary); border-color: var(--border-light);">{{ $pendaftar->created_at->format('d M Y') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    @if($ta->pendaftars->count() > 50)
                    <div class="alert alert-info mt-3 mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Menampilkan 50 pendaftar pertama dari {{ number_format($ta->total_pendaftar) }} total pendaftar.
                        <a href="{{ route('pendaftar.index', ['tahun' => $ta->tahun]) }}" class="alert-link">
                            Lihat semua data pendaftar tahun {{ $ta->tahun }}
                        </a>
                    </div>
                    @endif
                    @else
                    <div class="text-center py-5" style="color: var(--text-secondary);">
                        <i class="fas fa-inbox fa-3x mb-3"></i>
                        <p class="mb-0">Belum ada pendaftar untuk tahun pelajaran ini</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Activate Confirmation -->
<div class="modal fade" id="activateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title"><i class="fas fa-exclamation-triangle me-2"></i>Konfirmasi Aktivasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Anda akan mengaktifkan tahun pelajaran <strong id="activateTahun"></strong>.</p>
                <div class="alert alert-warning">
                    <i class="fas fa-info-circle me-2"></i>
                    Tahun pelajaran yang sedang aktif akan diarsipkan secara otomatis.
                </div>
                <p class="mb-0">Lanjutkan?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="activateForm" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-warning">Ya, Aktifkan</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Archive Confirmation -->
<div class="modal fade" id="archiveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-secondary text-white">
                <h5 class="modal-title"><i class="fas fa-archive me-2"></i>Konfirmasi Arsip</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Anda akan mengarsipkan tahun pelajaran <strong id="archiveTahun"></strong>.</p>
                <p class="mb-0">Lanjutkan?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="archiveForm" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-secondary">Ya, Arsipkan</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// Smooth scroll function
function smoothScroll(event, targetId) {
    event.preventDefault();
    const element = document.getElementById(targetId);
    if (element) {
        element.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
        // Highlight effect
        element.classList.add('highlight-flash');
        setTimeout(() => element.classList.remove('highlight-flash'), 2000);
    }
}

// Chart data
const chartData = @json($pendaftarPerBulan);
const labels = chartData.map(item => {
    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Oct', 'Nov', 'Des'];
    return months[item.bulan - 1];
});
const data = chartData.map(item => item.total);

// Create chart
const ctx = document.getElementById('chartPendaftar');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: labels,
        datasets: [{
            label: 'Jumlah Pendaftar',
            data: data,
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.1)',
            tension: 0.3,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});

function activateTahunAjaran(id, tahun) {
    document.getElementById('activateTahun').textContent = tahun;
    document.getElementById('activateForm').action = `/admin/tahun-ajaran/${id}/activate`;
    new bootstrap.Modal(document.getElementById('activateModal')).show();
}

function archiveTahunAjaran(id, tahun) {
    document.getElementById('archiveTahun').textContent = tahun;
    document.getElementById('archiveForm').action = `/admin/tahun-ajaran/${id}/archive`;
    new bootstrap.Modal(document.getElementById('archiveModal')).show();
}

function updateStatistics(id) {
    fetch(`/admin/tahun-ajaran/${id}/update-statistics`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('✅ Statistik berhasil diperbarui!');
            window.location.reload();
        } else {
            alert('❌ Gagal memperbarui statistik: ' + data.message);
        }
    })
    .catch(error => {
        alert('❌ Terjadi kesalahan: ' + error.message);
    });
}
</script>

<style>
.highlight-flash {
    animation: highlightFade 2s ease-in-out;
}

@keyframes highlightFade {
    0% { background-color: rgba(255, 193, 7, 0.3); }
    100% { background-color: transparent; }
}
</style>
@endpush
