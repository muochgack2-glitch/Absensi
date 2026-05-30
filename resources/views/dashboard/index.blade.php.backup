@extends('layouts.admin')

@section('title', 'Dashboard - SPMB (Sistem Penerimaan Murid Baru)')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    /* Modern Dashboard Styles - Inspired by eRapor8 */
    body { 
        font-family: 'Inter', sans-serif !important;
        background-color: #f8fafc !important;
    }

    /* Page Transitions */
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

    /* Modern Card Styling */
    .card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        background: #ffffff;
        overflow: hidden;
    }

    .card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.08);
    }

    /* Dashboard Header */
    .dashboard-header {
        margin-bottom: 32px;
    }

    .dashboard-header h2 {
        font-size: 28px;
        font-weight: 700;
        color: #1e293b;
        margin: 0;
    }

    .dashboard-header .subtitle {
        color: #64748b;
        font-size: 14px;
        margin-top: 4px;
    }

    /* Stat Cards */
    .stat-card {
        position: relative;
        padding: 24px;
        height: 100%;
        display: flex;
        flex-direction: column;
        border-left: 4px solid;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, currentColor, transparent);
        opacity: 0.1;
    }

    .stat-card.blue { border-left-color: #3b82f6; }
    .stat-card.yellow { border-left-color: #f59e0b; }
    .stat-card.red { border-left-color: #ef4444; }
    .stat-card.green { border-left-color: #10b981; }

    .stat-card .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        margin-bottom: 16px;
        color: #ffffff;
    }

    .stat-card.blue .stat-icon { background: linear-gradient(135deg, #3b82f6, #2563eb); }
    .stat-card.yellow .stat-icon { background: linear-gradient(135deg, #f59e0b, #d97706); }
    .stat-card.red .stat-icon { background: linear-gradient(135deg, #ef4444, #dc2626); }
    .stat-card.green .stat-icon { background: linear-gradient(135deg, #10b981, #059669); }

    .stat-card .stat-label {
        font-size: 13px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #64748b;
        margin-bottom: 8px;
    }

    .stat-card .stat-value {
        font-size: 36px;
        font-weight: 800;
        color: #1e293b;
        line-height: 1;
        margin-bottom: 12px;
        font-variant-numeric: tabular-nums;
    }

    .stat-card .stat-description {
        font-size: 13px;
        color: #94a3b8;
        margin-top: auto;
    }

    .stat-card .stat-meta {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-top: 16px;
        padding-top: 16px;
        border-top: 1px solid #f1f5f9;
    }

    .trend-badge {
        font-size: 12px;
        font-weight: 600;
        padding: 4px 8px;
        border-radius: 6px;
        background: #f1f5f9;
    }

    .sparkline {
        width: 80px;
        height: 24px;
        margin-left: auto;
    }

    /* Section Cards */
    .section-card {
        padding: 28px;
    }

    .section-card .section-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 24px;
    }

    .section-card .section-title {
        font-size: 18px;
        font-weight: 700;
        color: #1e293b;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .section-card .section-title i {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: #ffffff;
        font-size: 14px;
    }

    .live-badge {
        font-size: 11px;
        font-weight: 700;
        padding: 6px 12px;
        border-radius: 999px;
        background: linear-gradient(135deg, #10b981, #059669);
        color: #ffffff;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .live-badge::before {
        content: '';
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #ffffff;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }

    /* Modern Table */
    .modern-table {
        margin: 0;
    }

    .modern-table thead th {
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #64748b;
        background: #f8fafc;
        border-bottom: 2px solid #e2e8f0;
        padding: 12px 16px;
    }

    .modern-table tbody td {
        padding: 14px 16px;
        color: #475569;
        border-bottom: 1px solid #f1f5f9;
    }

    .modern-table tbody tr {
        transition: background-color 0.2s;
    }

    .modern-table tbody tr:hover {
        background-color: #f8fafc;
    }

    /* Badges */
    .badge-modern {
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
        letter-spacing: 0.3px;
    }

    .badge-modern.badge-success {
        background: #dcfce7;
        color: #166534;
    }

    .badge-modern.badge-danger {
        background: #fee2e2;
        color: #991b1b;
    }

    .badge-modern.badge-primary {
        background: #dbeafe;
        color: #1e40af;
    }

    /* Metric Badges */
    .metric-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 44px;
        padding: 6px 12px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 13px;
    }

    .metric-total { background: #dbeafe; color: #1e40af; }
    .metric-today { background: #fef3c7; color: #92400e; }
    .metric-belum { background: #fee2e2; color: #991b1b; }
    .metric-sudah { background: #dcfce7; color: #166534; }

    .jurusan-chip {
        display: inline-block;
        padding: 6px 14px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
        background: #f1f5f9;
        color: #475569;
    }

    /* Update Info */
    .update-info {
        text-align: right;
        margin-bottom: 20px;
    }

    .update-info small {
        font-size: 12px;
        color: #94a3b8;
        font-weight: 500;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .dashboard-header h2 {
            font-size: 24px;
        }

        .stat-card .stat-value {
            font-size: 28px;
        }

        .section-card {
            padding: 20px;
        }
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 48px 24px;
        color: #94a3b8;
    }

    .empty-state i {
        font-size: 48px;
        margin-bottom: 16px;
        opacity: 0.5;
    }

    .empty-state p {
        margin: 0;
        font-size: 14px;
    }
</style>
@endpush

@section('content')
<div class="dashboard-content">
    @if (Session::has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius: 12px; border-left: 4px solid #10b981;">
            <i class="fas fa-check-circle me-2"></i>{{ Session::get('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Dashboard Header -->
    <div class="dashboard-header">
        <h2>Dashboard</h2>
        <div class="subtitle">Selamat datang di Sistem Penerimaan Murid Baru</div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-6 col-lg-3">
            <div class="card stat-card blue">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-label">Total Pendaftar</div>
                <div class="stat-value" id="totalPendaftar">0</div>
                <div class="stat-description">Semua angkatan</div>
                <div class="stat-meta">
                    <span class="trend-badge" id="delta-totalPendaftar"></span>
                    <svg class="sparkline" id="spark-totalPendaftar" viewBox="0 0 120 28" preserveAspectRatio="none"></svg>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card stat-card yellow">
                <div class="stat-icon">
                    <i class="fas fa-user-plus"></i>
                </div>
                <div class="stat-label">Pendaftar Baru</div>
                <div class="stat-value" id="totalBaruHariIni">0</div>
                <div class="stat-description">Hari ini</div>
                <div class="stat-meta">
                    <span class="trend-badge" id="delta-totalBaruHariIni"></span>
                    <svg class="sparkline" id="spark-totalBaruHariIni" viewBox="0 0 120 28" preserveAspectRatio="none"></svg>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card stat-card red">
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-label">Belum Daftar Ulang</div>
                <div class="stat-value" id="totalBelumBayar">0</div>
                <div class="stat-description">Menunggu verifikasi</div>
                <div class="stat-meta">
                    <span class="trend-badge" id="delta-totalBelumBayar"></span>
                    <svg class="sparkline" id="spark-totalBelumBayar" viewBox="0 0 120 28" preserveAspectRatio="none"></svg>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card stat-card green">
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-label">Sudah Daftar Ulang</div>
                <div class="stat-value" id="totalLunas">0</div>
                <div class="stat-description">Terverifikasi</div>
                <div class="stat-meta">
                    <span class="trend-badge" id="delta-totalLunas"></span>
                    <svg class="sparkline" id="spark-totalLunas" viewBox="0 0 120 28" preserveAspectRatio="none"></svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Update Info -->
    <div class="update-info">
        <small id="lastUpdated">Update terakhir: -</small>
    </div>

    <!-- Per Jurusan Stats -->
    <div class="card section-card mb-4">
        <div class="section-header">
            <h5 class="section-title">
                <i class="fas fa-layer-group"></i>
                Statistik Per Jurusan
            </h5>
            <span class="live-badge">LIVE</span>
        </div>
        <div class="table-responsive">
            <table class="table modern-table">
                <thead>
                    <tr>
                        <th>Jurusan</th>
                        <th class="text-center">Total</th>
                        <th class="text-center">Hari Ini</th>
                        <th class="text-center">Belum</th>
                        <th class="text-center">Sudah</th>
                    </tr>
                </thead>
                <tbody id="perJurusanStatsBody">
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">Memuat data...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Activity & Network Stats -->
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card section-card">
                <div class="section-header">
                    <h5 class="section-title">
                        <i class="fas fa-list"></i>
                        Pendaftar Terbaru
                    </h5>
                </div>
                <div class="table-responsive">
                    <table class="table modern-table">
                        <thead>
                            <tr>
                                <th>No. Registrasi</th>
                                <th>Nama</th>
                                <th>Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($recentPendaftars as $p)
                                @php
                                    $status = optional($p->logistik)->status_bayar === 'Lunas'
                                        ? 'Sudah Daftar Ulang'
                                        : 'Belum Daftar Ulang';
                                    $badgeClass = optional($p->logistik)->status_bayar === 'Lunas'
                                        ? 'badge-success'
                                        : 'badge-danger';
                                @endphp
                                <tr>
                                    <td><strong>{{ $p->no_registrasi }}</strong></td>
                                    <td>{{ $p->nama_lengkap }}</td>
                                    <td><span class="badge-modern {{ $badgeClass }}">{{ $status }}</span></td>
                                    <td class="text-center">
                                        <a href="{{ route('pendaftar.daftar-ulang', $p->id_pendaftar) }}" class="btn btn-sm btn-outline-primary" style="border-radius: 8px;">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4">
                                        <div class="empty-state">
                                            <i class="fas fa-inbox"></i>
                                            <p>Belum ada data pendaftar</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card section-card">
                <div class="section-header">
                    <h5 class="section-title">
                        <i class="fas fa-chart-pie"></i>
                        Statistik Jaringan
                    </h5>
                </div>
                @forelse ($perJaringanDashboard as $j)
                    <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                        <div>
                            <strong style="color: #1e293b;">{{ $j->nama_jaringan }}</strong>
                        </div>
                        <span class="badge-modern badge-primary">{{ $j->total }}</span>
                    </div>
                @empty
                    <div class="empty-state">
                        <i class="fas fa-chart-pie"></i>
                        <p>Belum ada data jaringan</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Real-time Stats with Modern Animations
    function animateCounter(element, targetValue, duration = 600) {
        const startValue = Number(element.dataset.value ?? element.textContent) || 0;
        const endValue = Number(targetValue) || 0;

        if (startValue === endValue) {
            element.textContent = endValue;
            element.dataset.value = endValue;
            return;
        }

        const startTime = performance.now();

        function step(currentTime) {
            const progress = Math.min((currentTime - startTime) / duration, 1);
            const eased = 1 - Math.pow(1 - progress, 3); // ease-out cubic
            const currentValue = Math.round(startValue + (endValue - startValue) * eased);
            element.textContent = currentValue;

            if (progress < 1) {
                requestAnimationFrame(step);
            } else {
                element.dataset.value = endValue;
            }
        }

        requestAnimationFrame(step);
    }

    function setCardValue(elementId, value) {
        const el = document.getElementById(elementId);
        if (!el) return;
        animateCounter(el, value);
    }

    function setTrendBadge(metricKey, deltaValue) {
        const el = document.getElementById(`delta-${metricKey}`);
        if (!el) return;
        const val = Number(deltaValue) || 0;
        if (val === 0) {
            el.textContent = '';
            el.style.display = 'none';
            return;
        }
        const sign = val > 0 ? '+' : '';
        el.textContent = `${sign}${val} vs kemarin`;
        el.style.display = 'inline-block';
        el.style.color = val > 0 ? '#10b981' : '#ef4444';
        el.style.fontWeight = '600';
    }

    function renderSparkline(metricKey, values) {
        const svg = document.getElementById(`spark-${metricKey}`);
        if (!svg || !Array.isArray(values) || values.length === 0) return;
        const w = 120, h = 28, pad = 2;
        const min = Math.min(...values), max = Math.max(...values);
        const range = max - min || 1;
        const step = (w - pad * 2) / Math.max(values.length - 1, 1);
        const points = values.map((v, i) => {
            const x = pad + i * step;
            const y = h - pad - ((v - min) / range) * (h - pad * 2);
            return `${x},${y}`;
        }).join(' ');
        svg.innerHTML = `<polyline points="${points}" fill="none" stroke="#3b82f6" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" opacity="0.8"></polyline>`;
    }

    function renderPerJurusanStats(rows = []) {
        const tbody = document.getElementById('perJurusanStatsBody');
        if (!tbody) return;

        if (!rows.length) {
            tbody.innerHTML = `<tr><td colspan="5"><div class="empty-state"><i class="fas fa-layer-group"></i><p>Belum ada data jurusan</p></div></td></tr>`;
            return;
        }

        tbody.innerHTML = rows.map((r) => {
            const jur = r.jurusan ?? '-';
            return `
            <tr>
                <td><span class="jurusan-chip">${jur}</span></td>
                <td class="text-center"><span class="metric-badge metric-total">${r.totalPendaftar ?? 0}</span></td>
                <td class="text-center"><span class="metric-badge metric-today">${r.totalBaruHariIni ?? 0}</span></td>
                <td class="text-center"><span class="metric-badge metric-belum">${r.totalBelumBayar ?? 0}</span></td>
                <td class="text-center"><span class="metric-badge metric-sudah">${r.totalLunas ?? 0}</span></td>
            </tr>
        `;
        }).join('');
    }

    async function loadStats() {
        try {
            const res = await fetch('{{ route('report.stats') }}', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            });
            
            if (!res.ok) {
                console.error('Stats API HTTP Error:', res.status);
                return;
            }
            
            const data = await res.json();

            setCardValue('totalPendaftar', data.totalPendaftar || 0);
            setCardValue('totalLunas', data.totalLunas || 0);
            setCardValue('totalBelumBayar', data.totalBelumBayar || 0);
            setCardValue('totalBaruHariIni', data.totalBaruHariIni || 0);

            setTrendBadge('totalPendaftar', data.deltaVsYesterday?.totalPendaftar);
            setTrendBadge('totalBaruHariIni', data.deltaVsYesterday?.totalBaruHariIni);
            setTrendBadge('totalBelumBayar', data.deltaVsYesterday?.totalBelumBayar);
            setTrendBadge('totalLunas', data.deltaVsYesterday?.totalLunas);

            renderSparkline('totalPendaftar', data.trend7d?.totalPendaftar || []);
            renderSparkline('totalBaruHariIni', data.trend7d?.totalBaruHariIni || []);
            renderSparkline('totalBelumBayar', data.trend7d?.totalBelumBayar || []);
            renderSparkline('totalLunas', data.trend7d?.totalLunas || []);

            renderPerJurusanStats(data.perJurusanStats || []);

            const last = document.getElementById('lastUpdated');
            if (last) last.textContent = `Update terakhir: ${data.updatedAt || '-'}`;
        } catch (e) {
            console.error('Failed to load stats:', e);
        }
    }

    // Load stats on page load
    loadStats();
    
    // Auto-refresh every 30 seconds
    setInterval(loadStats, 30000);
</script>
@endpush
