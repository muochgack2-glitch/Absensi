@extends('layouts.admin')

@section('title', 'Dashboard - SPMB (Sistem Penerimaan Murid Baru)')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
        body { font-family: 'Inter', sans-serif !important; }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }
        .stat-card {
            border-top: 4px solid;
            padding: 20px;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .stat-card.red {
            border-top-color: #e74c3c;
        }
        .stat-card.yellow {
            border-top-color: #f39c12;
        }
        .stat-card.green {
            border-top-color: #27ae60;
        }
        .stat-card h5 {
            color: #7f8c8d;
            font-size: 13px;
            text-transform: uppercase;
            font-weight: 700;
            line-height: 1.35;
            min-height: 38px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .stat-card h5 i {
            width: 24px;
            height: 24px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            color: #fff;
            flex-shrink: 0;
        }
        .stat-card.red h5 i {
            background: #e74c3c;
        }
        .stat-card.yellow h5 i {
            background: #f39c12;
        }
        .stat-card.green h5 i {
            background: #27ae60;
        }
        .stat-card[style*="#3498db"] h5 i {
            background: #3498db;
        }
        .stat-card .number {
            font-size: 32px;
            font-weight: 700;
            color: #2c3e50;
            margin: 8px 0 10px;
            font-variant-numeric: tabular-nums;
            letter-spacing: 0.5px;
        }
        .stat-card small {
            margin-top: auto;
            display: block;
        }
        @media (max-width: 768px) {
            .sidebar {
                position: absolute;
                width: 100%;
                z-index: 100;
            }
            .main-content {
                padding-left: 0;
            }
        }
        .per-jurusan-card {
            border: 1px solid rgba(99, 102, 241, 0.12);
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
        }
        .per-jurusan-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            padding: 14px 16px;
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.12), rgba(16, 185, 129, 0.1));
            border-bottom: 1px solid rgba(99, 102, 241, 0.12);
        }
        .per-jurusan-head h5 {
            margin: 0;
            font-size: 15px;
            font-weight: 700;
            color: #1f2937;
        }
        .live-pill {
            font-size: 11px;
            font-weight: 700;
            color: #4338ca;
            background: rgba(99, 102, 241, 0.14);
            padding: 5px 10px;
            border-radius: 999px;
        }
        .jurusan-chip {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.2px;
            color: #0f172a;
            background: #e2e8f0;
        }
        .metric-badge {
            display: inline-block;
            min-width: 38px;
            padding: 3px 10px;
            border-radius: 999px;
            font-weight: 700;
            font-size: 12px;
            text-align: center;
        }
        .metric-total { background: #dbeafe; color: #1d4ed8; }
        .metric-today { background: #fef3c7; color: #b45309; }
        .metric-belum { background: #fee2e2; color: #b91c1c; }
        .metric-sudah { background: #dcfce7; color: #15803d; }
        .per-jurusan-table thead th {
            font-size: 12px;
            text-transform: uppercase;
            color: #64748b;
            border-bottom-width: 1px;
        }
        .per-jurusan-table tbody tr:hover {
            background: rgba(99, 102, 241, 0.05);
        }
</style>
@endpush

@section('content')
@if (Session::has('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ Session::get('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <h2 class="mb-4">Dashboard</h2>

            <!-- Statistics -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card stat-card" style="border-top-color: #3498db;">
                        <h5><i class="fas fa-users"></i> Total Pendaftar</h5>
                        <div class="number" id="totalPendaftar">0</div>
                        <small class="text-muted">Semua angkatan</small>
                        <div class="stat-meta"><span class="trend-badge" id="delta-totalPendaftar">±0 vs kemarin</span><svg class="sparkline" id="spark-totalPendaftar" viewBox="0 0 120 28" preserveAspectRatio="none"></svg></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stat-card yellow">
                        <h5><i class="fas fa-user-plus"></i> Pendaftar Baru (Hari Ini)</h5>
                        <div class="number" id="totalBaruHariIni">0</div>
                        <small class="text-muted">Pendaftaran masuk hari ini</small>
                        <div class="stat-meta"><span class="trend-badge" id="delta-totalBaruHariIni">±0 vs kemarin</span><svg class="sparkline" id="spark-totalBaruHariIni" viewBox="0 0 120 28" preserveAspectRatio="none"></svg></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stat-card red">
                        <h5><i class="fas fa-clock"></i> Belum Daftar Ulang</h5>
                        <div class="number" id="totalBelumBayar">0</div>
                        <small class="text-muted">Menunggu verifikasi</small>
                        <div class="stat-meta"><span class="trend-badge" id="delta-totalBelumBayar">±0 vs kemarin</span><svg class="sparkline" id="spark-totalBelumBayar" viewBox="0 0 120 28" preserveAspectRatio="none"></svg></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stat-card green">
                        <h5><i class="fas fa-check-circle"></i> Sudah Daftar Ulang</h5>
                        <div class="number" id="totalLunas">0</div>
                        <small class="text-muted">Kain diterima</small>
                        <div class="stat-meta"><span class="trend-badge" id="delta-totalLunas">±0 vs kemarin</span><svg class="sparkline" id="spark-totalLunas" viewBox="0 0 120 28" preserveAspectRatio="none"></svg></div>
                    </div>
                </div>
        </div>
        <div class="text-end mb-3"><small class="text-muted" id="lastUpdated">Update terakhir: -</small></div>
        <script>
            // Fetch real-time stats and update dashboard
            function animateCounter(element, targetValue, duration = 550) {
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
                const sign = val > 0 ? '+' : '';
                el.textContent = `${sign}${val} vs kemarin`;
                el.style.color = val > 0 ? '#16a34a' : (val < 0 ? '#dc2626' : '#64748b');
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
                svg.innerHTML = `<polyline points="${points}" fill="none" stroke="#6366f1" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></polyline>`;
            }

            function renderPerJurusanStats(rows = []) {
                const tbody = document.getElementById('perJurusanStatsBody');
                if (!tbody) return;

                if (!rows.length) {
                    tbody.innerHTML = `<tr><td colspan="5" class="text-center text-muted py-3">Belum ada data jurusan</td></tr>`;
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
                    // Use absolute path to avoid routing issues
                    const statsUrl = '/laporan/stats';
                    console.log('Loading stats from:', statsUrl);
                    
                    const res = await fetch(statsUrl, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        credentials: 'same-origin'  // Include cookies for session
                    });
                    
                    // Debug: log response status
                    console.log('Stats API Response Status:', res.status);
                    console.log('Stats API Response Headers:', {
                        'Content-Type': res.headers.get('Content-Type'),
                        'X-Status': res.status
                    });
                    
                    if (!res.ok) {
                        const errorText = await res.text();
                        console.error('Stats API HTTP Error:', res.status);
                        console.error('Response body:', errorText.substring(0, 500));
                        showErrorMessage(`HTTP ${res.status} - Periksa logs untuk detail`);
                        return;
                    }
                    
                    const data = await res.json();
                    console.log('Stats data received:', data);
                    
                    // Check for error in response
                    if (data.error) {
                        console.error('Stats API Error Response:', data.error, data.message);
                        showErrorMessage('Error: ' + data.error);
                        // Still show the data (0 values) so UI doesn't break
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
                    console.error('Failed to load stats - Exception:', e);
                    console.error('Error Stack:', e.stack);
                    console.error('Error type:', e.constructor.name);
                    showErrorMessage('Network error: ' + e.message);
                    
                    // Load fallback data from page if available
                    loadFallbackData();
                }
            }

            function loadFallbackData() {
                // This will be populated if backend renders initial data
                // For now, show a helpful message
                const fallbackDiv = document.getElementById('lastUpdated');
                if (fallbackDiv && !fallbackDiv.textContent.includes('Update terakhir:')) {
                    console.log('Trying fallback data load...');
                }
            }

            function showErrorMessage(message) {
                const last = document.getElementById('lastUpdated');
                if (last) last.textContent = message;
                console.warn('Displaying error to user:', message);
            }

            loadStats();
            // Optional: refresh every 30 seconds
            setInterval(loadStats, 30000);
        </script>

        <div class="card p-0 mb-4 per-jurusan-card">
            <div class="per-jurusan-head">
                <h5><i class="fas fa-layer-group"></i> Statistik Per Jurusan</h5>
                <span class="live-pill">Live 30s</span>
            </div>
            <div class="p-3">
                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle mb-0 per-jurusan-table">
                        <thead class="table-light">
                            <tr>
                                <th>Jurusan</th>
                                <th class="text-center">Total Pendaftar</th>
                                <th class="text-center">Pendaftar Baru (Hari Ini)</th>
                                <th class="text-center">Belum Daftar Ulang</th>
                                <th class="text-center">Sudah Daftar Ulang</th>
                            </tr>
                        </thead>
                        <tbody id="perJurusanStatsBody">
                            <tr>
                                <td colspan="5" class="text-center text-muted py-3">Memuat data...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

            <!-- Recent Activity -->
            <div class="row">
                <div class="col-lg-8">
                    <div class="card p-4">
                        <h5 class="mb-4">
                            <i class="fas fa-list"></i> Pendaftar Terbaru
                        </h5>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th>No. Registrasi</th>
                                        <th>Nama</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($recentPendaftars as $p)
                                        @php
                                            $status = optional($p->logistik)->status_bayar === 'Lunas'
                                                ? 'Sudah Daftar Ulang'
                                                : 'Belum Daftar Ulang';
                                            $badgeClass = optional($p->logistik)->status_bayar === 'Lunas'
                                                ? 'bg-success'
                                                : 'bg-danger';
                                        @endphp
                                        <tr>
                                            <td>{{ $p->no_registrasi }}</td>
                                            <td>{{ $p->nama_lengkap }}</td>
                                            <td><span class="badge {{ $badgeClass }}">{{ $status }}</span></td>
                                            <td>
                                                <a href="{{ route('pendaftar.daftar-ulang', $p->id_pendaftar) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted py-4">
                                                <i class="fas fa-inbox"></i> Belum ada data
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card p-4">
                        <h5 class="mb-4">
                            <i class="fas fa-chart-pie"></i> Statistik Jaringan
                        </h5>
                        @forelse ($perJaringanDashboard as $j)
                            <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                <div>
                                    <strong>{{ $j->nama_jaringan }}</strong>
                                </div>
                                <span class="badge bg-primary rounded-pill">{{ $j->total }}</span>
                            </div>
                        @empty
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-chart-pie" style="font-size: 40px;"></i>
                                <p class="mt-3">Belum ada data</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
@endsection

@push('scripts')
<script>
            // Fetch real-time stats and update dashboard
            function animateCounter(element, targetValue, duration = 550) {
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
                const sign = val > 0 ? '+' : '';
                el.textContent = `${sign}${val} vs kemarin`;
                el.style.color = val > 0 ? '#16a34a' : (val < 0 ? '#dc2626' : '#64748b');
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
                svg.innerHTML = `<polyline points="${points}" fill="none" stroke="#6366f1" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></polyline>`;
            }

            function renderPerJurusanStats(rows = []) {
                const tbody = document.getElementById('perJurusanStatsBody');
                if (!tbody) return;

                if (!rows.length) {
                    tbody.innerHTML = `<tr><td colspan="5" class="text-center text-muted py-3">Belum ada data jurusan</td></tr>`;
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
                    const res = await fetch('{{ route('report.stats') }}');
                    if (!res.ok) return;
                    const data = await res.json();

                    setCardValue('totalPendaftar', data.totalPendaftar);
                    setCardValue('totalLunas', data.totalLunas);
                    setCardValue('totalBelumBayar', data.totalBelumBayar);
                    setCardValue('totalBaruHariIni', data.totalBaruHariIni);

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
                    console.error('Failed to load stats', e);
                }
            }

            loadStats();
            // Optional: refresh every 30 seconds
            setInterval(loadStats, 30000);
        </script>
@endpush
