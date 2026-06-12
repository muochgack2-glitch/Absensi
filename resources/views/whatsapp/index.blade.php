@extends('layouts.admin')

@section('title', 'WhatsApp Gateway - Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">📱 WhatsApp Gateway</h1>
            <p class="text-muted mb-0">Monitor dan kelola pengiriman pesan WhatsApp</p>
        </div>
        <div>
            <a href="{{ route('gateway.index') }}" class="btn btn-outline-info me-2">
                <i class="fas fa-server me-2"></i>Gateway Management
            </a>
            <a href="{{ route('whatsapp.send') }}" class="btn btn-primary">
                <i class="fas fa-paper-plane me-2"></i>Kirim Pesan
            </a>
        </div>
    </div>

    <!-- Dual Gateway Info Banner -->
    <div class="alert alert-info border-0 shadow-sm mb-4" role="alert">
        <div class="d-flex align-items-center">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle fa-2x"></i>
            </div>
            <div class="flex-grow-1 ms-3">
                <h6 class="alert-heading mb-1">
                    <i class="fas fa-server me-2"></i>Dual Gateway Management
                </h6>
                <p class="mb-2 small">
                    Sistem sekarang menggunakan <strong>2 WhatsApp Gateway</strong> dengan failover otomatis:
                    <strong>Primary (Port 3000)</strong> dan <strong>Backup (Port 3001)</strong>.
                </p>
                <p class="mb-0">
                    <a href="{{ route('gateway.index') }}" class="btn btn-sm btn-info">
                        <i class="fas fa-arrow-right me-1"></i>Buka Gateway Management Dashboard
                    </a>
                    <small class="text-muted ms-3">
                        <i class="fas fa-lightbulb me-1"></i>Monitor kedua gateway, lihat QR code, restart, dan logout dari satu dashboard
                    </small>
                </p>
            </div>
        </div>
    </div>

    <!-- Auto-Healing Diagnostics Panel -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm border-start border-5 border-primary">
                <div class="card-header bg-gradient-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-tools me-2"></i>Auto-Healing Diagnostics
                        </h5>
                        <div>
                            <button class="btn btn-sm btn-light me-2" onclick="refreshDiagnostics()">
                                <i class="fas fa-sync-alt me-1"></i>Refresh
                            </button>
                            <button class="btn btn-sm btn-warning" onclick="runAutoFix()" id="autoFixBtn">
                                <i class="fas fa-magic me-1"></i>Auto-Fix Issues
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Diagnostics Status -->
                    <div id="diagnosticsStatus" class="mb-3">
                        <div class="text-center py-3">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2 mb-0 text-muted">Loading diagnostics...</p>
                        </div>
                    </div>

                    <!-- Issues Panel -->
                    <div id="issuesPanel" style="display: none;">
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div id="issuesList"></div>
                            </div>
                        </div>

                        <!-- Error Log Viewer (Collapsible) -->
                        <div class="mt-3">
                            <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#errorLogCollapse">
                                <i class="fas fa-file-alt me-1"></i>View Error Logs (Last 100 lines)
                            </button>
                            <div class="collapse mt-2" id="errorLogCollapse">
                                <div class="card card-body bg-dark text-light" style="max-height: 400px; overflow-y: auto; font-family: monospace; font-size: 12px;">
                                    <div id="errorLogContent" class="text-white">
                                        <div class="text-center py-3">
                                            <div class="spinner-border spinner-border-sm text-light" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                            <p class="mt-2 mb-0">Loading error logs...</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Fix History Table -->
                    <div id="fixHistoryPanel" class="mt-4" style="display: none;">
                        <h6 class="mb-3"><i class="fas fa-history me-2"></i>Fix History (Last 10)</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Timestamp</th>
                                        <th>User</th>
                                        <th>Issues Fixed</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody id="fixHistoryTable">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Connection Status Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <i class="fab fa-whatsapp fa-3x" style="color: #25D366;"></i>
                            </div>
                            <div>
                                <h5 class="mb-1">Status Koneksi WhatsApp</h5>
                                <div id="connectionStatus">
                                    <span class="badge bg-secondary">
                                        <i class="fas fa-spinner fa-spin me-1"></i>Checking...
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="text-end">
                            <button class="btn btn-sm btn-outline-primary me-2" onclick="refreshStatus()">
                                <i class="fas fa-sync-alt me-1"></i>Refresh
                            </button>
                            <button 
                                class="btn btn-sm btn-outline-warning me-2" 
                                onclick="restartServer()" 
                                id="restartBtn"
                                data-bs-toggle="tooltip" 
                                data-bs-placement="bottom" 
                                title="Restart Node.js server (gunakan jika server hang, lambat, atau memory leak). Tidak perlu scan QR ulang.">
                                <i class="fas fa-redo me-1"></i>Restart Server
                            </button>
                            <button 
                                class="btn btn-sm btn-outline-danger" 
                                onclick="resetConnection()" 
                                id="resetBtn"
                                data-bs-toggle="tooltip" 
                                data-bs-placement="bottom" 
                                title="Reset koneksi WhatsApp dan generate QR baru. Gunakan jika status Disconnected atau QR tidak muncul.">
                                <i class="fas fa-power-off me-1"></i>Reset & Reconnect
                            </button>
                        </div>
                    </div>
                    <div id="statusDetails" class="mt-3" style="display: none;">
                        <div class="row text-center">
                            <div class="col-md-3">
                                <small class="text-muted d-block">QR Available</small>
                                <strong id="qrAvailable">-</strong>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted d-block">Reconnect Attempts</small>
                                <strong id="reconnectAttempts">-</strong>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted d-block">Last Update</small>
                                <strong id="lastUpdate">-</strong>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted d-block">Server URL</small>
                                <strong class="text-muted small">{{ $activeServerUrl ?? config('app.wa_server_url', 'http://localhost:3000') }}</strong>
                                @if(isset($activeServerUrl) && strpos($activeServerUrl, '3001') !== false)
                                <br><span class="badge badge-sm bg-warning text-dark">Using Backup</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <!-- QR Code Section (Auto-show when status = 'qr') -->
                    <div id="qrSection" class="mt-4" style="display: none;">
                        <div class="alert alert-warning border-0">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h6 class="alert-heading mb-2">
                                        <i class="fas fa-qrcode me-2"></i>Scan QR Code untuk Menghubungkan WhatsApp
                                    </h6>
                                    <p class="mb-2 small">WhatsApp belum terhubung. Scan QR code di bawah dengan aplikasi WhatsApp Anda.</p>
                                    <ol class="mb-0 small ps-3">
                                        <li>Buka WhatsApp di HP</li>
                                        <li>Tap menu (⋮) → "Perangkat Tertaut"</li>
                                        <li>Tap "Tautkan Perangkat"</li>
                                        <li>Scan QR code di samping</li>
                                    </ol>
                                </div>
                                <div class="col-md-4 text-center">
                                    <div id="qrCodeDisplay" class="bg-white p-3 rounded">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <p class="mt-2 mb-0 small text-muted">Loading QR...</p>
                                    </div>
                                    <button class="btn btn-sm btn-outline-primary mt-2" onclick="refreshQRInline()">
                                        <i class="fas fa-sync-alt me-1"></i>Refresh QR
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <!-- Server Health Card (NEW!) -->
        <div class="col-md-12 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-header border-bottom" style="background: var(--bg-primary);">
                    <h6 class="mb-0" style="color: var(--text-primary);">
                        <i class="fas fa-heartbeat me-2"></i>Server Health
                    </h6>
                </div>
                <div class="card-body" style="background: var(--bg-primary);">
                    <div id="healthMetrics" class="row text-center">
                        <div class="col-md-3">
                            <small class="text-muted d-block">Uptime</small>
                            <strong id="serverUptime" style="color: var(--text-primary);">-</strong>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted d-block">Memory Usage</small>
                            <strong id="memoryUsage" style="color: var(--text-primary);">-</strong>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted d-block">Memory %</small>
                            <strong id="memoryPercent" style="color: var(--text-primary);">-</strong>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted d-block">Node Version</small>
                            <strong id="nodeVersion" style="color: var(--text-primary);">-</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="rounded-circle bg-success bg-opacity-10 p-3">
                                <i class="fas fa-check-circle fa-2x text-success"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Terkirim</h6>
                            <h3 class="mb-0">{{ $statistics['total_sent'] ?? 0 }}</h3>
                            <small class="text-success">
                                <i class="fas fa-arrow-up me-1"></i>{{ $statistics['sent_today'] ?? 0 }} hari ini
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="rounded-circle bg-danger bg-opacity-10 p-3">
                                <i class="fas fa-times-circle fa-2x text-danger"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Gagal</h6>
                            <h3 class="mb-0">{{ $statistics['total_failed'] ?? 0 }}</h3>
                            <small class="text-danger">
                                <i class="fas fa-arrow-down me-1"></i>{{ $statistics['failed_today'] ?? 0 }} hari ini
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="rounded-circle bg-warning bg-opacity-10 p-3">
                                <i class="fas fa-clock fa-2x text-warning"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Pending</h6>
                            <h3 class="mb-0">{{ $statistics['total_pending'] ?? 0 }}</h3>
                            <small class="text-muted">Menunggu</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="rounded-circle bg-primary bg-opacity-10 p-3">
                                <i class="fas fa-file-alt fa-2x text-primary"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Template</h6>
                            <h3 class="mb-0">{{ $statistics['active_templates'] ?? 0 }}</h3>
                            <small class="text-muted">dari {{ $statistics['total_templates'] ?? 0 }} total</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Logs -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header border-bottom" style="background: var(--bg-primary);">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0" style="color: var(--text-primary);">
                            <i class="fas fa-history me-2"></i>Log Pesan Terbaru
                        </h5>
                        <a href="{{ route('whatsapp.logs') }}" class="btn btn-sm btn-outline-primary">
                            Lihat Semua <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body p-0" style="background: var(--bg-primary);">
                    @if($recentLogs->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Waktu</th>
                                    <th>Nomor HP</th>
                                    <th>Pesan</th>
                                    <th>Tipe</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentLogs as $log)
                                <tr>
                                    <td>
                                        <small class="text-muted">
                                            {{ $log->created_at->format('d/m/Y H:i') }}
                                        </small>
                                    </td>
                                    <td>
                                        <strong style="color: var(--text-primary);">{{ $log->phone }}</strong>
                                        @if($log->pendaftar)
                                        <br><small class="text-muted">{{ $log->pendaftar->nama_lengkap }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <div style="max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; color: var(--text-primary);">
                                            {{ Str::limit($log->message, 50) }}
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $log->type_label }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $log->status_color }}">
                                            {{ $log->status_label }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-5">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Belum ada log pesan</p>
                        <a href="{{ route('whatsapp.send') }}" class="btn btn-primary">
                            <i class="fas fa-paper-plane me-2"></i>Kirim Pesan Pertama
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- QR Code Modal -->
<div class="modal fade" id="qrModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-qrcode me-2"></i>Scan QR Code WhatsApp
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center" id="qrModalBody">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-3">Loading QR Code...</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" onclick="refreshQR()">
                    <i class="fas fa-sync-alt me-1"></i>Refresh QR
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Auto refresh status every 5 seconds
let statusInterval;

document.addEventListener('DOMContentLoaded', function() {
    refreshStatus();
    loadHealthMetrics(); // Load health metrics on page load
    loadDiagnostics(); // Load diagnostics on page load
    statusInterval = setInterval(refreshStatus, 5000);
    
    // Refresh health metrics every 30 seconds
    setInterval(loadHealthMetrics, 30000);
    
    // Refresh diagnostics every 60 seconds
    setInterval(loadDiagnostics, 60000);
    
    // Initialize Bootstrap tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Load error logs when collapse is shown
    const errorLogCollapse = document.getElementById('errorLogCollapse');
    if (errorLogCollapse) {
        errorLogCollapse.addEventListener('show.bs.collapse', function () {
            loadErrorLogs();
        });
    }
});

function refreshStatus() {
    fetch('{{ route("whatsapp.status") }}')
        .then(response => response.json())
        .then(data => {
            updateStatusUI(data);
        })
        .catch(error => {
            console.error('Error fetching status:', error);
            document.getElementById('connectionStatus').innerHTML = `
                <span class="badge bg-danger">
                    <i class="fas fa-times-circle me-1"></i>Connection Error
                </span>
            `;
        });
}

function updateStatusUI(data) {
    const statusDiv = document.getElementById('connectionStatus');
    const detailsDiv = document.getElementById('statusDetails');
    const qrSection = document.getElementById('qrSection');
    
    if (!data.success) {
        statusDiv.innerHTML = `
            <span class="badge bg-danger">
                <i class="fas fa-times-circle me-1"></i>Disconnected
            </span>
            <small class="text-muted d-block mt-1">${data.message || 'Server tidak dapat dijangkau'}</small>
        `;
        detailsDiv.style.display = 'none';
        qrSection.style.display = 'none';
        return;
    }
    
    const status = data.data.status;
    const reconnectAttempts = data.data.reconnectAttempts || 0;
    let badgeClass = 'secondary';
    let icon = 'circle';
    let text = 'Unknown';
    let extraInfo = '';
    
    if (status === 'connected') {
        badgeClass = 'success';
        icon = 'check-circle';
        text = 'Connected';
        qrSection.style.display = 'none';
    } else if (status === 'qr') {
        badgeClass = 'warning';
        icon = 'qrcode';
        text = 'Waiting QR Scan';
        qrSection.style.display = 'block';
        // Auto-load QR code
        refreshQRInline();
    } else if (status === 'disconnected' && reconnectAttempts > 0) {
        // Show reconnecting state
        badgeClass = 'info';
        icon = 'spinner fa-spin';
        text = 'Reconnecting...';
        extraInfo = `<small class="text-muted d-block mt-1">Attempt ${reconnectAttempts}/5</small>`;
        qrSection.style.display = 'none';
    } else {
        badgeClass = 'danger';
        icon = 'times-circle';
        text = 'Disconnected';
        qrSection.style.display = 'none';
    }
    
    statusDiv.innerHTML = `
        <span class="badge bg-${badgeClass}">
            <i class="fas fa-${icon} me-1"></i>${text}
        </span>
        ${extraInfo}
    `;
    
    // Update details
    document.getElementById('qrAvailable').textContent = data.data.qrAvailable ? 'Yes' : 'No';
    document.getElementById('reconnectAttempts').textContent = reconnectAttempts;
    document.getElementById('lastUpdate').textContent = new Date(data.data.timestamp).toLocaleTimeString('id-ID');
    
    detailsDiv.style.display = 'block';
}

// Load health metrics
function loadHealthMetrics() {
    fetch('{{ route("whatsapp.health") }}')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data) {
                const health = data.data;
                
                // Format uptime
                const uptimeSeconds = health.uptime;
                const days = Math.floor(uptimeSeconds / 86400);
                const hours = Math.floor((uptimeSeconds % 86400) / 3600);
                const minutes = Math.floor((uptimeSeconds % 3600) / 60);
                const uptimeStr = days > 0 
                    ? `${days}d ${hours}h ${minutes}m`
                    : hours > 0
                    ? `${hours}h ${minutes}m`
                    : `${minutes}m`;
                
                document.getElementById('serverUptime').textContent = uptimeStr;
                document.getElementById('memoryUsage').textContent = `${health.memory.heapUsed} / ${health.memory.heapTotal} MB`;
                document.getElementById('memoryPercent').textContent = `${health.memory.percentage}%`;
                document.getElementById('nodeVersion').textContent = health.node.version;
                
                // Color code memory percentage
                const memoryPercentEl = document.getElementById('memoryPercent');
                if (health.memory.percentage > 90) {
                    memoryPercentEl.style.color = '#dc3545'; // Danger
                } else if (health.memory.percentage > 75) {
                    memoryPercentEl.style.color = '#ffc107'; // Warning
                } else {
                    memoryPercentEl.style.color = 'var(--text-primary)'; // Normal
                }
            }
        })
        .catch(error => {
            console.error('Failed to load health metrics:', error);
        });
}

function refreshQRInline() {
    const qrDisplay = document.getElementById('qrCodeDisplay');
    qrDisplay.innerHTML = `
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <p class="mt-2 mb-0 small text-muted">Loading QR...</p>
    `;
    
    fetch('{{ route("whatsapp.qr") }}')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data.qr) {
                qrDisplay.innerHTML = `
                    <img src="${data.data.qr}" alt="QR Code" class="img-fluid" style="max-width: 250px;">
                    <p class="mt-2 mb-0 small text-muted">Scan dengan WhatsApp</p>
                `;
            } else {
                qrDisplay.innerHTML = `
                    <div class="alert alert-warning mb-0 small">
                        <i class="fas fa-exclamation-triangle me-1"></i>
                        ${data.data?.message || data.message || 'QR tidak tersedia'}
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error fetching QR:', error);
            qrDisplay.innerHTML = `
                <div class="alert alert-danger mb-0 small">
                    <i class="fas fa-times-circle me-1"></i>
                    Gagal memuat QR
                </div>
            `;
        });
}

function showQRCode() {
    const modal = new bootstrap.Modal(document.getElementById('qrModal'));
    modal.show();
    refreshQR();
}

function refreshQR() {
    const modalBody = document.getElementById('qrModalBody');
    modalBody.innerHTML = `
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <p class="mt-3">Loading QR Code...</p>
    `;
    
    fetch('{{ route("whatsapp.qr") }}')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data.qr) {
                modalBody.innerHTML = `
                    <img src="${data.data.qr}" alt="QR Code" class="img-fluid" style="max-width: 300px;">
                    <p class="mt-3 text-muted">${data.data.message || 'Scan QR code dengan WhatsApp Anda'}</p>
                    <div class="alert alert-info mt-3 text-start">
                        <strong>Cara Scan:</strong>
                        <ol class="mb-0 mt-2">
                            <li>Buka WhatsApp di HP</li>
                            <li>Tap menu (⋮) di kanan atas</li>
                            <li>Pilih "Perangkat Tertaut"</li>
                            <li>Tap "Tautkan Perangkat"</li>
                            <li>Scan QR code di atas</li>
                        </ol>
                    </div>
                `;
            } else {
                modalBody.innerHTML = `
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        ${data.data?.message || data.message || 'QR code tidak tersedia'}
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error fetching QR:', error);
            modalBody.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-times-circle me-2"></i>
                    Gagal memuat QR code
                </div>
            `;
        });
}

// Reset connection (logout and force new QR)
function resetConnection() {
    if (!confirm('Yakin ingin reset koneksi WhatsApp? Anda perlu scan QR code ulang.')) {
        return;
    }
    
    const resetBtn = document.getElementById('resetBtn');
    const originalHtml = resetBtn.innerHTML;
    resetBtn.disabled = true;
    resetBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Resetting...';
    
    fetch('{{ route("whatsapp.logout") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            showAlert('success', 'Koneksi berhasil direset. Generating QR code baru...');
            
            // Wait 3 seconds then refresh status to show QR
            setTimeout(() => {
                refreshStatus();
                resetBtn.disabled = false;
                resetBtn.innerHTML = originalHtml;
            }, 3000);
        } else {
            showAlert('error', data.message || 'Gagal reset koneksi');
            resetBtn.disabled = false;
            resetBtn.innerHTML = originalHtml;
        }
    })
    .catch(error => {
        console.error('Error resetting connection:', error);
        showAlert('error', 'Gagal reset koneksi: ' + error.message);
        resetBtn.disabled = false;
        resetBtn.innerHTML = originalHtml;
    });
}

// Restart server (PM2 restart via process.exit)
function restartServer() {
    if (!confirm('Yakin ingin restart server? Koneksi akan terputus sebentar (5-10 detik). Session WhatsApp tetap tersimpan, tidak perlu scan QR ulang.')) {
        return;
    }
    
    const restartBtn = document.getElementById('restartBtn');
    const originalHtml = restartBtn.innerHTML;
    restartBtn.disabled = true;
    restartBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Restarting...';
    
    fetch('{{ route("whatsapp.restart") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            showAlert('success', 'Server restarting... Status akan update otomatis dalam 10 detik.');
            
            // Wait 10 seconds then refresh status
            setTimeout(() => {
                refreshStatus();
                restartBtn.disabled = false;
                restartBtn.innerHTML = originalHtml;
                showAlert('info', 'Server restart selesai. Checking koneksi...');
            }, 10000);
        } else {
            showAlert('error', data.message || 'Gagal restart server');
            restartBtn.disabled = false;
            restartBtn.innerHTML = originalHtml;
        }
    })
    .catch(error => {
        console.error('Error restarting server:', error);
        showAlert('error', 'Gagal restart server: ' + error.message);
        restartBtn.disabled = false;
        restartBtn.innerHTML = originalHtml;
    });
}

// Show alert helper
function showAlert(type, message) {
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const icon = type === 'success' ? 'check-circle' : 'times-circle';
    
    const alertHtml = `
        <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
            <i class="fas fa-${icon} me-2"></i>${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    // Insert at top of page
    const container = document.querySelector('.container-fluid');
    const tempDiv = document.createElement('div');
    tempDiv.innerHTML = alertHtml;
    container.insertBefore(tempDiv.firstElementChild, container.firstChild);
    
    // Auto dismiss after 5 seconds
    setTimeout(() => {
        const alert = container.querySelector('.alert');
        if (alert) {
            alert.remove();
        }
    }, 5000);
}

// Clear interval when leaving page
window.addEventListener('beforeunload', function() {
    if (statusInterval) {
        clearInterval(statusInterval);
    }
});

// ===== AUTO-HEALING DIAGNOSTICS FUNCTIONS =====

// Load diagnostics
function loadDiagnostics() {
    fetch('{{ route("whatsapp.diagnostics") }}')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateDiagnosticsUI(data.data);
            } else {
                showDiagnosticsError(data.message);
            }
        })
        .catch(error => {
            console.error('Failed to load diagnostics:', error);
            showDiagnosticsError('Failed to load diagnostics: ' + error.message);
        });
}

// Refresh diagnostics
function refreshDiagnostics() {
    document.getElementById('diagnosticsStatus').innerHTML = `
        <div class="text-center py-3">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2 mb-0 text-muted">Refreshing diagnostics...</p>
        </div>
    `;
    loadDiagnostics();
}

// Update diagnostics UI
function updateDiagnosticsUI(data) {
    const issues = data.issues || [];
    const fixHistory = data.fix_history || [];
    const diagnosticsStatus = document.getElementById('diagnosticsStatus');
    const issuesPanel = document.getElementById('issuesPanel');
    const fixHistoryPanel = document.getElementById('fixHistoryPanel');

    if (issues.length === 0) {
        // No issues found - show success state
        diagnosticsStatus.innerHTML = `
            <div class="alert alert-success border-0 mb-0">
                <div class="d-flex align-items-center">
                    <i class="fas fa-check-circle fa-2x me-3"></i>
                    <div>
                        <h6 class="mb-1">All Systems Healthy</h6>
                        <small class="text-muted">No issues detected. Server is running normally.</small>
                    </div>
                </div>
            </div>
        `;
        issuesPanel.style.display = 'none';
    } else {
        // Issues found - show them
        diagnosticsStatus.style.display = 'none';
        issuesPanel.style.display = 'block';
        
        let issuesHtml = '';
        issues.forEach(issue => {
            const badgeClass = issue.type === 'error' ? 'danger' : 'warning';
            const icon = issue.type === 'error' ? 'times-circle' : 'exclamation-triangle';
            
            issuesHtml += `
                <div class="alert alert-${badgeClass} border-0 mb-2">
                    <div class="d-flex align-items-start">
                        <i class="fas fa-${icon} fa-lg me-3 mt-1"></i>
                        <div class="flex-grow-1">
                            <h6 class="mb-1">${issue.title}</h6>
                            <p class="mb-1 small">${issue.description}</p>
                            ${issue.auto_fixable 
                                ? '<span class="badge bg-success"><i class="fas fa-magic me-1"></i>Auto-fixable</span>' 
                                : '<span class="badge bg-secondary"><i class="fas fa-ban me-1"></i>Manual fix required</span>'
                            }
                        </div>
                    </div>
                </div>
            `;
        });
        
        document.getElementById('issuesList').innerHTML = issuesHtml;
    }

    // Update fix history
    if (fixHistory.length > 0) {
        fixHistoryPanel.style.display = 'block';
        let historyHtml = '';
        
        fixHistory.reverse().forEach(fix => {
            const timestamp = new Date(fix.timestamp).toLocaleString('id-ID');
            const fixedCount = fix.fixed_issues?.length || 0;
            const failedCount = fix.failed_issues?.length || 0;
            
            historyHtml += `
                <tr>
                    <td class="text-nowrap"><small>${timestamp}</small></td>
                    <td><small>${fix.user_name || 'Unknown'}</small></td>
                    <td>
                        <small>
                            ${fix.fixed_issues?.map(i => i.title).join(', ') || 'None'}
                        </small>
                    </td>
                    <td>
                        ${fixedCount > 0 
                            ? `<span class="badge bg-success">${fixedCount} fixed</span>` 
                            : '<span class="badge bg-secondary">No fixes</span>'
                        }
                        ${failedCount > 0 ? `<span class="badge bg-danger ms-1">${failedCount} failed</span>` : ''}
                    </td>
                </tr>
            `;
        });
        
        document.getElementById('fixHistoryTable').innerHTML = historyHtml;
    } else {
        fixHistoryPanel.style.display = 'none';
    }
}

// Show diagnostics error
function showDiagnosticsError(message) {
    document.getElementById('diagnosticsStatus').innerHTML = `
        <div class="alert alert-danger border-0 mb-0">
            <i class="fas fa-exclamation-triangle me-2"></i>
            ${message}
        </div>
    `;
    document.getElementById('issuesPanel').style.display = 'none';
}

// Run auto-fix
function runAutoFix() {
    if (!confirm('Run auto-fix to automatically resolve detected issues? This may restart the server.')) {
        return;
    }

    const autoFixBtn = document.getElementById('autoFixBtn');
    const originalHtml = autoFixBtn.innerHTML;
    autoFixBtn.disabled = true;
    autoFixBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Fixing...';

    fetch('{{ route("whatsapp.auto-fix") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', data.message);
            
            // Reload diagnostics after 3 seconds
            setTimeout(() => {
                loadDiagnostics();
                autoFixBtn.disabled = false;
                autoFixBtn.innerHTML = originalHtml;
            }, 3000);
        } else {
            showAlert('error', data.message || 'Auto-fix failed');
            autoFixBtn.disabled = false;
            autoFixBtn.innerHTML = originalHtml;
        }
    })
    .catch(error => {
        console.error('Auto-fix error:', error);
        showAlert('error', 'Auto-fix failed: ' + error.message);
        autoFixBtn.disabled = false;
        autoFixBtn.innerHTML = originalHtml;
    });
}

// Load error logs
function loadErrorLogs() {
    const errorLogContent = document.getElementById('errorLogContent');
    errorLogContent.innerHTML = `
        <div class="text-center py-3">
            <div class="spinner-border spinner-border-sm text-light" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2 mb-0">Loading error logs...</p>
        </div>
    `;

    fetch('{{ route("whatsapp.error-logs") }}')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const logs = data.data.logs || 'No error logs found';
                errorLogContent.innerHTML = `<pre class="mb-0 text-light" style="white-space: pre-wrap;">${escapeHtml(logs)}</pre>`;
            } else {
                errorLogContent.innerHTML = `<div class="alert alert-danger mb-0">${data.message}</div>`;
            }
        })
        .catch(error => {
            console.error('Failed to load error logs:', error);
            errorLogContent.innerHTML = `<div class="alert alert-danger mb-0">Failed to load error logs: ${error.message}</div>`;
        });
}

// Escape HTML helper
function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, m => map[m]);
}
</script>
@endpush
@endsection
