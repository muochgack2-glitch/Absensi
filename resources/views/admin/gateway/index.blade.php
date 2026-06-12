@extends('layouts.app')

@section('content')
<div class="container-fluid" x-data="gatewayManager()">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body py-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="h3 mb-1 text-white">
                                <i class="fab fa-whatsapp me-2"></i>WhatsApp Gateway Management
                            </h1>
                            <p class="text-white-50 mb-0">Monitor dan kelola dual gateway dengan failover otomatis</p>
                        </div>
                        <button class="btn btn-light" @click="refreshAll()">
                            <i class="fas fa-sync-alt me-2"></i>Refresh All
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gateway Cards -->
    <div class="row">
        @foreach($statuses as $key => $gateway)
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <!-- Card Header -->
                <div class="card-header border-0 {{ $gateway['online'] ? 'bg-success' : 'bg-danger' }} text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0">
                                <i class="fas fa-server me-2"></i>{{ $gateway['info']['name'] }}
                            </h5>
                            <small class="opacity-75">{{ $gateway['info']['purpose'] }}</small>
                        </div>
                        <div class="text-end">
                            <h3 class="mb-0">
                                <i class="fas fa-{{ $gateway['online'] ? 'check-circle' : 'times-circle' }}"></i>
                            </h3>
                            <small>{{ $gateway['online'] ? 'Online' : 'Offline' }}</small>
                        </div>
                    </div>
                </div>

                <!-- Card Body -->
                <div class="card-body">
                    @if($gateway['online'] && $gateway['status'])
                        <!-- Connection Status -->
                        <div class="d-flex align-items-center justify-content-between mb-3 p-3 rounded" 
                             style="background: {{ $gateway['status']['status'] === 'connected' ? '#d4edda' : '#fff3cd' }};">
                            <div>
                                <h6 class="mb-0">Connection Status</h6>
                                <small class="text-muted">Port {{ parse_url($gateway['info']['url'], PHP_URL_PORT) }}</small>
                            </div>
                            <span class="badge {{ $gateway['status']['status'] === 'connected' ? 'bg-success' : 'bg-warning' }} fs-6">
                                <i class="fas fa-{{ $gateway['status']['status'] === 'connected' ? 'link' : 'qrcode' }} me-1"></i>
                                {{ ucfirst($gateway['status']['status']) }}
                            </span>
                        </div>

                        @if($gateway['health'])
                        <!-- Health Metrics -->
                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <div class="p-3 rounded bg-light">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas fa-clock text-primary me-2"></i>
                                        <small class="text-muted">Uptime</small>
                                    </div>
                                    <h6 class="mb-0">
                                        @php
                                            $uptime = $gateway['health']['uptime'];
                                            $days = floor($uptime / 86400);
                                            $hours = floor(($uptime % 86400) / 3600);
                                            $minutes = floor(($uptime % 3600) / 60);
                                        @endphp
                                        @if($days > 0)
                                            {{ $days }}d {{ $hours }}h
                                        @elseif($hours > 0)
                                            {{ $hours }}h {{ $minutes }}m
                                        @else
                                            {{ $minutes }}m
                                        @endif
                                    </h6>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 rounded bg-light">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas fa-memory text-info me-2"></i>
                                        <small class="text-muted">Memory</small>
                                    </div>
                                    <h6 class="mb-0">
                                        {{ $gateway['health']['memory']['percentage'] }}%
                                        <small class="text-muted">({{ $gateway['health']['memory']['heapUsed'] }}MB)</small>
                                    </h6>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 rounded bg-light">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas fa-microchip text-warning me-2"></i>
                                        <small class="text-muted">CPU Time</small>
                                    </div>
                                    <h6 class="mb-0">
                                        {{ $gateway['health']['cpu']['user'] + $gateway['health']['cpu']['system'] }}ms
                                    </h6>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 rounded bg-light">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas fa-qrcode text-success me-2"></i>
                                        <small class="text-muted">QR Status</small>
                                    </div>
                                    <h6 class="mb-0">
                                        {{ $gateway['status']['qrAvailable'] ? 'Available' : 'Not Needed' }}
                                    </h6>
                                </div>
                            </div>
                        </div>
                        @endif
                    @else
                        <div class="alert alert-danger border-0" role="alert">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-exclamation-triangle fa-2x me-3"></i>
                                <div>
                                    <h6 class="alert-heading mb-1">Gateway Offline</h6>
                                    <p class="mb-0">Gateway tidak dapat dijangkau atau sedang tidak aktif</p>
                                    @if(isset($gateway['error']))
                                    <small class="text-muted">{{ $gateway['error'] }}</small>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Card Footer -->
                <div class="card-footer bg-white border-0">
                    <div class="row g-2">
                        <div class="col-6 col-md-3">
                            <button class="btn btn-outline-primary w-100" @click="viewQR('{{ $key }}')">
                                <i class="fas fa-qrcode"></i>
                                <span class="d-none d-md-inline ms-1">QR Code</span>
                            </button>
                        </div>
                        <div class="col-6 col-md-3">
                            <button class="btn btn-outline-warning w-100" @click="restart('{{ $key }}')">
                                <i class="fas fa-sync"></i>
                                <span class="d-none d-md-inline ms-1">Restart</span>
                            </button>
                        </div>
                        <div class="col-6 col-md-3">
                            <button class="btn btn-outline-danger w-100" @click="logout('{{ $key }}')">
                                <i class="fas fa-sign-out-alt"></i>
                                <span class="d-none d-md-inline ms-1">Logout</span>
                            </button>
                        </div>
                        <div class="col-6 col-md-3">
                            <button class="btn btn-outline-info w-100" @click="viewLogs('{{ $key }}')">
                                <i class="fas fa-file-alt"></i>
                                <span class="d-none d-md-inline ms-1">Logs</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Failover Settings Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">
                        <i class="fas fa-exchange-alt text-primary me-2"></i>Failover Configuration
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-4">
                            <div class="d-flex align-items-center">
                                <div class="form-check form-switch me-3">
                                    <input class="form-check-input" type="checkbox" id="failoverEnabled" 
                                           {{ $failoverSettings['enabled'] ? 'checked' : '' }} disabled 
                                           style="width: 3em; height: 1.5em;">
                                </div>
                                <div>
                                    <h6 class="mb-0">Auto Failover</h6>
                                    <small class="text-muted">
                                        {{ $failoverSettings['enabled'] ? 'Aktif - Backup otomatis digunakan jika primary offline' : 'Tidak aktif' }}
                                    </small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center p-3 bg-light rounded">
                                <small class="text-muted d-block">Health Check Timeout</small>
                                <h5 class="mb-0">{{ $failoverSettings['timeout'] }} <small>detik</small></h5>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center p-3 bg-primary bg-opacity-10 rounded">
                                <small class="text-muted d-block">Active Gateway</small>
                                <h5 class="mb-0 text-primary">
                                    <i class="fas fa-server me-2"></i>Primary (Port 3000)
                                </h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-light">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1">
                                <i class="fas fa-info-circle text-info me-2"></i>Gateway Management Tips
                            </h6>
                            <small class="text-muted">
                                • Restart: Gunakan jika gateway hang atau memory tinggi (tidak perlu scan QR ulang)
                                • Logout: Untuk generate QR baru atau ganti nomor WhatsApp
                            </small>
                        </div>
                        <a href="{{ route('whatsapp.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-chart-line me-2"></i>Lihat Dashboard Pesan
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- QR Modal -->
    <div class="modal fade" id="qrModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-0" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <h5 class="modal-title text-white">
                        <i class="fas fa-qrcode me-2"></i>Scan QR Code WhatsApp
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 text-center">
                    <div x-show="loadingQR" class="py-5">
                        <div class="spinner-border text-primary mb-3" style="width: 3rem; height: 3rem;" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="text-muted">Loading QR code...</p>
                    </div>
                    <div x-show="!loadingQR && qrCode" style="display: none;">
                        <div class="bg-white p-4 rounded shadow-sm d-inline-block mb-3">
                            <img :src="qrCode" class="img-fluid" style="max-width: 300px;" />
                        </div>
                        <div class="alert alert-info border-0 text-start" role="alert">
                            <h6 class="alert-heading">
                                <i class="fas fa-mobile-alt me-2"></i>Cara Scan QR Code:
                            </h6>
                            <ol class="mb-0 ps-3 small">
                                <li>Buka WhatsApp di HP Anda</li>
                                <li>Tap menu <strong>(⋮)</strong> → <strong>"Perangkat Tertaut"</strong></li>
                                <li>Tap <strong>"Tautkan Perangkat"</strong></li>
                                <li>Arahkan kamera ke QR code di atas</li>
                            </ol>
                        </div>
                    </div>
                    <div x-show="!loadingQR && !qrCode" style="display: none;">
                        <div class="alert alert-warning border-0" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <span x-text="qrError"></span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Tutup
                    </button>
                    <button type="button" class="btn btn-primary" @click="viewQR(currentGateway)">
                        <i class="fas fa-sync-alt me-2"></i>Refresh QR
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Logs Modal -->
    <div class="modal fade" id="logsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-0" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <h5 class="modal-title text-white">
                        <i class="fas fa-file-alt me-2"></i>Gateway Logs
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0">
                    <pre x-text="logs" class="mb-0 p-4" style="max-height: 70vh; overflow-y: auto; background: #1e1e1e; color: #d4d4d4; font-size: 13px; font-family: 'Courier New', monospace; line-height: 1.6;"></pre>
                </div>
                <div class="modal-footer border-0 bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Tutup
                    </button>
                    <button type="button" class="btn btn-primary" @click="viewLogs(currentGateway)">
                        <i class="fas fa-sync-alt me-2"></i>Refresh Logs
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function gatewayManager() {
    return {
        loadingQR: false,
        qrCode: null,
        qrError: null,
        logs: '',
        currentGateway: null,

        viewQR(gateway) {
            this.currentGateway = gateway;
            this.loadingQR = true;
            this.qrCode = null;
            this.qrError = null;
            
            const modal = new bootstrap.Modal(document.getElementById('qrModal'));
            modal.show();

            fetch(`/admin/gateway/${gateway}/qr`)
                .then(res => res.json())
                .then(data => {
                    this.loadingQR = false;
                    if (data.success) {
                        this.qrCode = data.qr;
                    } else {
                        this.qrError = data.message || 'Gateway sedang connected atau QR tidak tersedia';
                    }
                })
                .catch(err => {
                    this.loadingQR = false;
                    this.qrError = 'Failed to load QR code: ' + err.message;
                });
        },

        restart(gateway) {
            if (!confirm('⚠️ Restart gateway ini?\n\nKoneksi akan terputus sementara (5-10 detik). Anda tidak perlu scan QR code ulang.')) return;

            // Show loading state
            const btn = event.target.closest('button');
            const originalHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Restarting...';

            fetch(`/admin/gateway/${gateway}/restart`, { 
                method: 'POST', 
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                }
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        alert('✅ ' + data.message + '\n\nHalaman akan reload dalam 8 detik...');
                        setTimeout(() => location.reload(), 8000);
                    } else {
                        alert('❌ ' + data.message);
                        btn.disabled = false;
                        btn.innerHTML = originalHtml;
                    }
                })
                .catch(err => {
                    alert('❌ Failed to restart: ' + err.message);
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                });
        },

        logout(gateway) {
            if (!confirm('⚠️ Logout dari WhatsApp?\n\nAnda perlu scan QR code lagi untuk reconnect.')) return;

            // Show loading state
            const btn = event.target.closest('button');
            const originalHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Logging out...';

            fetch(`/admin/gateway/${gateway}/logout`, { 
                method: 'POST', 
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                }
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        alert('✅ ' + data.message + '\n\nHalaman akan reload dalam 5 detik...');
                        setTimeout(() => location.reload(), 5000);
                    } else {
                        alert('❌ ' + data.message);
                        btn.disabled = false;
                        btn.innerHTML = originalHtml;
                    }
                })
                .catch(err => {
                    alert('❌ Failed to logout: ' + err.message);
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                });
        },

        viewLogs(gateway) {
            this.currentGateway = gateway;
            this.logs = 'Loading logs...';
            
            const modal = new bootstrap.Modal(document.getElementById('logsModal'));
            modal.show();

            fetch(`/admin/gateway/${gateway}/logs`)
                .then(res => res.json())
                .then(data => {
                    this.logs = data.success ? data.logs : ('Error: ' + data.message);
                })
                .catch(err => {
                    this.logs = 'Failed to load logs: ' + err.message;
                });
        },

        refreshAll() {
            location.reload();
        }
    }
}
</script>
@endsection
