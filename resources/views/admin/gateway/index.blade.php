@extends('layouts.app')

@section('content')
<div class="container-fluid" x-data="gatewayManager()">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">🌐 WhatsApp Gateway Management</h1>
        <button class="btn btn-primary btn-sm" @click="refreshAll()">
            <i class="fas fa-sync-alt"></i> Refresh All
        </button>
    </div>

    <div class="row">
        @foreach($statuses as $key => $gateway)
        <div class="col-md-6 mb-4">
            <div class="card {{ $gateway['online'] ? 'border-success' : 'border-danger' }}">
                <div class="card-header bg-{{ $gateway['online'] ? 'success' : 'danger' }} bg-opacity-10">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            📱 {{ $gateway['info']['name'] }}
                        </h5>
                        <span class="badge bg-{{ $gateway['online'] ? 'success' : 'danger' }}">
                            {{ $gateway['online'] ? '● Online' : '○ Offline' }}
                        </span>
                    </div>
                    <small class="text-muted">{{ $gateway['info']['purpose'] }}</small>
                </div>
                <div class="card-body">
                    @if($gateway['online'] && $gateway['status'])
                        <div class="row g-3">
                            <div class="col-6">
                                <strong>Port:</strong>
                                <span class="text-muted">{{ parse_url($gateway['info']['url'], PHP_URL_PORT) }}</span>
                            </div>
                            <div class="col-6">
                                <strong>Status:</strong>
                                <span class="badge bg-{{ $gateway['status']['status'] === 'connected' ? 'success' : 'warning' }}">
                                    {{ ucfirst($gateway['status']['status']) }}
                                </span>
                            </div>
                            
                            @if($gateway['health'])
                            <div class="col-6">
                                <strong>Uptime:</strong>
                                <span class="text-muted">{{ gmdate('H:i:s', $gateway['health']['uptime']) }}</span>
                            </div>
                            <div class="col-6">
                                <strong>Memory:</strong>
                                <span class="text-muted">
                                    {{ $gateway['health']['memory']['heapUsed'] }} MB / 
                                    {{ $gateway['health']['memory']['heapTotal'] }} MB
                                    ({{ $gateway['health']['memory']['percentage'] }}%)
                                </span>
                            </div>
                            <div class="col-6">
                                <strong>CPU:</strong>
                                <span class="text-muted">
                                    User: {{ $gateway['health']['cpu']['user'] }}ms, 
                                    System: {{ $gateway['health']['cpu']['system'] }}ms
                                </span>
                            </div>
                            <div class="col-6">
                                <strong>QR Available:</strong>
                                <span class="badge bg-{{ $gateway['status']['qrAvailable'] ? 'info' : 'secondary' }}">
                                    {{ $gateway['status']['qrAvailable'] ? 'Yes' : 'No' }}
                                </span>
                            </div>
                            @endif
                        </div>
                    @else
                        <div class="alert alert-danger mb-0">
                            <i class="fas fa-exclamation-circle"></i>
                            Gateway offline atau tidak dapat dijangkau
                            @if(isset($gateway['error']))
                            <br><small class="text-muted">{{ $gateway['error'] }}</small>
                            @endif
                        </div>
                    @endif
                </div>
                <div class="card-footer">
                    <div class="btn-group btn-group-sm" role="group">
                        <button class="btn btn-primary" @click="viewQR('{{ $key }}')">
                            <i class="fas fa-qrcode"></i> QR Code
                        </button>
                        <button class="btn btn-warning" @click="restart('{{ $key }}')">
                            <i class="fas fa-sync"></i> Restart
                        </button>
                        <button class="btn btn-danger" @click="logout('{{ $key }}')">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </button>
                        <button class="btn btn-info" @click="viewLogs('{{ $key }}')">
                            <i class="fas fa-file-alt"></i> Logs
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Failover Settings --}}
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">⚙️ Failover Settings</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="failoverEnabled" 
                               {{ $failoverSettings['enabled'] ? 'checked' : '' }} disabled>
                        <label class="form-check-label" for="failoverEnabled">
                            <strong>Auto Failover Enabled</strong>
                        </label>
                    </div>
                    <small class="text-muted">
                        {{ $failoverSettings['enabled'] ? 'Primary akan auto-switch ke backup jika offline' : 'Failover disabled' }}
                    </small>
                </div>
                <div class="col-md-4">
                    <strong>Health Check Timeout:</strong>
                    <span class="text-muted">{{ $failoverSettings['timeout'] }} seconds</span>
                </div>
                <div class="col-md-4">
                    <strong>Current Active:</strong>
                    <span class="badge bg-primary">Primary (Port 3000)</span>
                </div>
            </div>
        </div>
    </div>

    {{-- QR Modal --}}
    <div class="modal fade" id="qrModal" tabindex="-1" aria-labelledby="qrModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="qrModalLabel">Scan QR Code</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <div x-show="loadingQR">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Loading QR code...</p>
                    </div>
                    <div x-show="!loadingQR && qrCode">
                        <img :src="qrCode" class="img-fluid" style="max-width: 400px;" />
                        <p class="mt-3 text-muted">Scan dengan WhatsApp di HP Anda</p>
                    </div>
                    <div x-show="!loadingQR && !qrCode" class="alert alert-warning" role="alert">
                        <p x-text="qrError"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Logs Modal --}}
    <div class="modal fade" id="logsModal" tabindex="-1" aria-labelledby="logsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="logsModalLabel">Gateway Logs</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <pre x-text="logs" style="max-height: 500px; overflow-y: auto; background: #1e1e1e; color: #d4d4d4; padding: 15px; border-radius: 5px; font-size: 12px;"></pre>
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

        viewQR(gateway) {
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
                        this.qrError = data.message;
                    }
                })
                .catch(err => {
                    this.loadingQR = false;
                    this.qrError = 'Failed to load QR code: ' + err.message;
                });
        },

        restart(gateway) {
            if (!confirm('Restart gateway? Koneksi akan terputus sementara (5-10 detik).')) return;

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
                    alert(data.message);
                    setTimeout(() => location.reload(), 8000);
                })
                .catch(err => {
                    alert('Failed to restart: ' + err.message);
                });
        },

        logout(gateway) {
            if (!confirm('Logout dari WhatsApp? Anda perlu scan QR code lagi.')) return;

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
                    alert(data.message);
                    setTimeout(() => location.reload(), 5000);
                })
                .catch(err => {
                    alert('Failed to logout: ' + err.message);
                });
        },

        viewLogs(gateway) {
            this.logs = 'Loading logs...';
            
            const modal = new bootstrap.Modal(document.getElementById('logsModal'));
            modal.show();

            fetch(`/admin/gateway/${gateway}/logs`)
                .then(res => res.json())
                .then(data => {
                    this.logs = data.success ? data.logs : data.message;
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
