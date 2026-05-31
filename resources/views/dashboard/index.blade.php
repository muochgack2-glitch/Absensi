@extends('layouts.admin')

@section('title', 'Dashboard - SPMB (Sistem Penerimaan Murid Baru)')

@push('styles')
<style>
    /* Modern Dashboard Styles */
    .dashboard-content {
        animation: fadeIn 0.3s ease-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
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
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .dashboard-header .subtitle {
        color: #64748b;
        font-size: 14px;
        margin-top: 8px;
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
        display: inline-flex;
        align-items: center;
        gap: 6px;
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

    /* Network Stats Item */
    .network-stat-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px 0;
        border-bottom: 1px solid #f1f5f9;
    }

    .network-stat-item:last-child {
        border-bottom: none;
    }

    .network-stat-item strong {
        color: #1e293b;
        font-size: 14px;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .dashboard-header h2 {
            font-size: 24px;
        }
    }
</style>
@endpush

@section('content')
<div class="dashboard-content">
    @if (Session::has('success'))
        <x-alert type="success" dismissible="true">
            {{ Session::get('success') }}
        </x-alert>
    @endif

    <!-- Dashboard Header -->
    <div class="dashboard-header">
        <h2>
            <i class="fas fa-chart-line text-primary"></i>
            Dashboard
        </h2>
        <div class="subtitle">
            <i class="fas fa-info-circle me-1"></i>
            Selamat datang di Sistem Penerimaan Murid Baru
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-6 col-lg-3">
            <x-stat-card 
                icon="fas fa-users" 
                label="Total Pendaftar" 
                value="0" 
                color="blue"
                description="Semua angkatan"
                id="totalPendaftar"
            >
                <x-slot:sparkline>
                    <svg class="sparkline" id="spark-totalPendaftar" viewBox="0 0 120 28" preserveAspectRatio="none" style="width: 80px; height: 24px;"></svg>
                </x-slot:sparkline>
            </x-stat-card>
        </div>
        <div class="col-md-6 col-lg-3">
            <x-stat-card 
                icon="fas fa-user-plus" 
                label="Pendaftar Baru" 
                value="0" 
                color="yellow"
                description="Hari ini"
                id="totalBaruHariIni"
            >
                <x-slot:sparkline>
                    <svg class="sparkline" id="spark-totalBaruHariIni" viewBox="0 0 120 28" preserveAspectRatio="none" style="width: 80px; height: 24px;"></svg>
                </x-slot:sparkline>
            </x-stat-card>
        </div>
        <div class="col-md-6 col-lg-3">
            <x-stat-card 
                icon="fas fa-clock" 
                label="Belum Daftar Ulang" 
                value="0" 
                color="red"
                description="Menunggu verifikasi"
                id="totalBelumBayar"
            >
                <x-slot:sparkline>
                    <svg class="sparkline" id="spark-totalBelumBayar" viewBox="0 0 120 28" preserveAspectRatio="none" style="width: 80px; height: 24px;"></svg>
                </x-slot:sparkline>
            </x-stat-card>
        </div>
        <div class="col-md-6 col-lg-3">
            <x-stat-card 
                icon="fas fa-check-circle" 
                label="Sudah Daftar Ulang" 
                value="0" 
                color="green"
                description="Terverifikasi"
                id="totalLunas"
            >
                <x-slot:sparkline>
                    <svg class="sparkline" id="spark-totalLunas" viewBox="0 0 120 28" preserveAspectRatio="none" style="width: 80px; height: 24px;"></svg>
                </x-slot:sparkline>
            </x-stat-card>
        </div>
    </div>

    <!-- Update Info -->
    <div class="update-info">
        <small>
            <i class="fas fa-sync-alt me-1"></i>
            <span id="lastUpdated">Update terakhir: -</span>
        </small>
    </div>

    <!-- Per Jurusan Stats -->
    <x-section-card title="Statistik Per Jurusan" icon="fas fa-layer-group" badge="LIVE" class="mb-4">
        <x-table>
            <x-slot:header>
                <tr>
                    <th>
                        <i class="fas fa-graduation-cap me-1 text-muted"></i>
                        Jurusan
                    </th>
                    <th class="text-center">
                        <i class="fas fa-users me-1 text-muted"></i>
                        Total
                    </th>
                    <th class="text-center">
                        <i class="fas fa-calendar-day me-1 text-muted"></i>
                        Hari Ini
                    </th>
                    <th class="text-center">
                        <i class="fas fa-hourglass-half me-1 text-muted"></i>
                        Belum
                    </th>
                    <th class="text-center">
                        <i class="fas fa-check-double me-1 text-muted"></i>
                        Sudah
                    </th>
                </tr>
            </x-slot:header>
            
            <tbody id="perJurusanStatsBody">
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">
                        <i class="fas fa-spinner fa-spin me-2"></i>
                        Memuat data...
                    </td>
                </tr>
            </tbody>
        </x-table>
    </x-section-card>

    <!-- Recent Activity & Network Stats -->
    <div class="row g-4">
        <div class="col-lg-8">
            <x-section-card title="Pendaftar Terbaru" icon="fas fa-list-ul">
                <x-slot:actions>
                    <x-button 
                        variant="primary" 
                        size="sm" 
                        icon="fas fa-plus" 
                        href="{{ route('pendaftar.create') }}"
                    >
                        Tambah Pendaftar
                    </x-button>
                </x-slot:actions>
                
                <x-table>
                    <x-slot:header>
                        <tr>
                            <th>
                                <i class="fas fa-hashtag me-1 text-muted"></i>
                                No. Registrasi
                            </th>
                            <th>
                                <i class="fas fa-user me-1 text-muted"></i>
                                Nama
                            </th>
                            <th>
                                <i class="fas fa-info-circle me-1 text-muted"></i>
                                Status
                            </th>
                            <th class="text-center">
                                <i class="fas fa-cog me-1 text-muted"></i>
                                Aksi
                            </th>
                        </tr>
                    </x-slot:header>
                    
                    @forelse ($recentPendaftars as $p)
                        @php
                            $status = optional($p->logistik)->status_bayar === 'Lunas'
                                ? 'Sudah Daftar Ulang'
                                : 'Belum Daftar Ulang';
                            $badgeClass = optional($p->logistik)->status_bayar === 'Lunas'
                                ? 'badge-success'
                                : 'badge-danger';
                            $badgeIcon = optional($p->logistik)->status_bayar === 'Lunas'
                                ? 'fa-check-circle'
                                : 'fa-clock';
                        @endphp
                        <tr>
                            <td>
                                <strong class="text-primary">{{ $p->no_registrasi }}</strong>
                            </td>
                            <td>{{ $p->nama_lengkap }}</td>
                            <td>
                                <span class="badge bg-{{ optional($p->logistik)->status_bayar === 'Lunas' ? 'success' : 'danger' }} bg-opacity-10 text-{{ optional($p->logistik)->status_bayar === 'Lunas' ? 'success' : 'danger' }}">
                                    <i class="fas {{ $badgeIcon }} me-1"></i>
                                    {{ $status }}
                                </span>
                            </td>
                            <td>
                                <x-table-actions align="center">
                                    <x-icon-button 
                                        icon="fas fa-eye" 
                                        variant="info" 
                                        size="sm" 
                                        tooltip="Lihat Detail"
                                        href="{{ route('pendaftar.daftar-ulang', $p->id_pendaftar) }}"
                                    />
                                </x-table-actions>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="p-0">
                                <x-empty-state 
                                    icon="fas fa-inbox" 
                                    message="Belum ada data pendaftar"
                                    description="Pendaftar baru akan muncul di sini"
                                />
                            </td>
                        </tr>
                    @endforelse
                </x-table>
            </x-section-card>
        </div>
        
        <div class="col-lg-4">
            <x-section-card title="Statistik Jaringan" icon="fas fa-chart-pie">
                @forelse ($perJaringanDashboard as $j)
                    <div class="network-stat-item">
                        <div>
                            <i class="fas fa-network-wired text-primary me-2"></i>
                            <strong>{{ $j->nama_jaringan_normalized }}</strong>
                        </div>
                        <span class="badge bg-primary bg-opacity-10 text-primary">
                            <i class="fas fa-users me-1"></i>
                            {{ $j->total }}
                        </span>
                    </div>
                @empty
                    <x-empty-state 
                        icon="fas fa-chart-pie" 
                        message="Belum ada data jaringan"
                        description="Data jaringan akan muncul di sini"
                        size="sm"
                    />
                @endforelse
            </x-section-card>
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
        // Find the stat-card-value element within the card
        const card = document.getElementById(elementId);
        if (!card) return;
        
        const valueEl = card.querySelector('.stat-card-value');
        if (!valueEl) return;
        
        animateCounter(valueEl, value);
    }

    function setTrendBadge(metricKey, deltaValue) {
        const card = document.getElementById(metricKey);
        if (!card) return;
        
        // Find or create trend badge in stat-meta
        let trendEl = card.querySelector('.trend-badge');
        if (!trendEl) {
            const metaEl = card.querySelector('.stat-meta');
            if (metaEl) {
                trendEl = document.createElement('span');
                trendEl.className = 'trend-badge';
                metaEl.insertBefore(trendEl, metaEl.firstChild);
            }
        }
        
        if (!trendEl) return;
        
        const val = Number(deltaValue) || 0;
        if (val === 0) {
            trendEl.textContent = '';
            trendEl.style.display = 'none';
            return;
        }
        const sign = val > 0 ? '+' : '';
        trendEl.textContent = `${sign}${val} vs kemarin`;
        trendEl.style.display = 'inline-block';
        trendEl.style.color = val > 0 ? '#10b981' : '#ef4444';
        trendEl.style.fontWeight = '600';
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

    function renderPerJurusanStats(rows = [], error = null) {
        const tbody = document.getElementById('perJurusanStatsBody');
        if (!tbody) return;

        if (error) {
            tbody.innerHTML = `<tr><td colspan="5" class="p-0"><div style="text-align: center; padding: 48px 24px; color: #ef4444;"><i class="fas fa-exclamation-triangle" style="font-size: 48px; margin-bottom: 16px; opacity: 0.5;"></i><p style="margin: 0; font-size: 14px; font-weight: 600;">Gagal memuat data</p><p style="margin: 8px 0 0; font-size: 12px; color: #94a3b8;">${error}</p></div></td></tr>`;
            return;
        }

        if (!rows.length) {
            tbody.innerHTML = `<tr><td colspan="5" class="p-0"><div style="text-align: center; padding: 48px 24px; color: #94a3b8;"><i class="fas fa-layer-group" style="font-size: 48px; margin-bottom: 16px; opacity: 0.5;"></i><p style="margin: 0; font-size: 14px;">Belum ada data jurusan</p></div></td></tr>`;
            return;
        }

        tbody.innerHTML = rows.map((r) => {
            const jur = r.jurusan ?? '-';
            return `
            <tr>
                <td><span class="jurusan-chip"><i class="fas fa-graduation-cap me-1"></i>${jur}</span></td>
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
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            
            const res = await fetch('{{ route('dashboard.stats') }}?gelombang=all&jurusan_id=all&_=' + Date.now(), {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken || ''
                },
                credentials: 'same-origin'
            });
            
            if (!res.ok) {
                const errorText = await res.text();
                console.error('Stats API HTTP Error:', res.status, res.statusText, errorText);
                
                // Check if redirected to login
                if (res.status === 401 || res.status === 419 || errorText.includes('login')) {
                    renderPerJurusanStats([], 'Sesi berakhir. Silakan refresh halaman.');
                    return;
                }
                
                renderPerJurusanStats([], `HTTP Error: ${res.status} ${res.statusText}`);
                return;
            }
            
            const contentType = res.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                console.error('Invalid content type:', contentType);
                renderPerJurusanStats([], 'Response bukan JSON. Cek server logs.');
                return;
            }
            
            const data = await res.json();
            
            // Check if response has error
            if (data.error) {
                console.error('Stats API Error:', data.error, data.message);
                renderPerJurusanStats([], data.message || data.error);
                return;
            }

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
            renderPerJurusanStats([], `Error: ${e.message}`);
        }
    }

    // Load stats on page load
    loadStats();
    
    // Auto-refresh every 30 seconds
    setInterval(loadStats, 30000);
</script>
@endpush
