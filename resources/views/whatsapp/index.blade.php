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
            <a href="{{ route('whatsapp.send') }}" class="btn btn-primary">
                <i class="fas fa-paper-plane me-2"></i>Kirim Pesan
            </a>
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
                            <button class="btn btn-sm btn-outline-primary" onclick="refreshStatus()">
                                <i class="fas fa-sync-alt me-1"></i>Refresh
                            </button>
                            <button class="btn btn-sm btn-outline-secondary" onclick="showQRCode()" id="qrButton" style="display: none;">
                                <i class="fas fa-qrcode me-1"></i>Lihat QR Code
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
                                <strong class="text-muted small">{{ config('app.wa_server_url', 'http://localhost:3000') }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
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
                <div class="card-header bg-white border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-history me-2"></i>Log Pesan Terbaru
                        </h5>
                        <a href="{{ route('whatsapp.logs') }}" class="btn btn-sm btn-outline-primary">
                            Lihat Semua <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
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
                                        <strong>{{ $log->phone }}</strong>
                                        @if($log->pendaftar)
                                        <br><small class="text-muted">{{ $log->pendaftar->nama_lengkap }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <div style="max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
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
    statusInterval = setInterval(refreshStatus, 5000);
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
    const qrButton = document.getElementById('qrButton');
    
    if (!data.success) {
        statusDiv.innerHTML = `
            <span class="badge bg-danger">
                <i class="fas fa-times-circle me-1"></i>Disconnected
            </span>
            <small class="text-muted d-block mt-1">${data.message || 'Server tidak dapat dijangkau'}</small>
        `;
        detailsDiv.style.display = 'none';
        qrButton.style.display = 'none';
        return;
    }
    
    const status = data.data.status;
    let badgeClass = 'secondary';
    let icon = 'circle';
    let text = 'Unknown';
    
    if (status === 'connected') {
        badgeClass = 'success';
        icon = 'check-circle';
        text = 'Connected';
        qrButton.style.display = 'none';
    } else if (status === 'qr') {
        badgeClass = 'warning';
        icon = 'qrcode';
        text = 'Waiting QR Scan';
        qrButton.style.display = 'inline-block';
    } else {
        badgeClass = 'danger';
        icon = 'times-circle';
        text = 'Disconnected';
        qrButton.style.display = 'none';
    }
    
    statusDiv.innerHTML = `
        <span class="badge bg-${badgeClass}">
            <i class="fas fa-${icon} me-1"></i>${text}
        </span>
    `;
    
    // Update details
    document.getElementById('qrAvailable').textContent = data.data.qrAvailable ? 'Yes' : 'No';
    document.getElementById('reconnectAttempts').textContent = data.data.reconnectAttempts || 0;
    document.getElementById('lastUpdate').textContent = new Date(data.data.timestamp).toLocaleTimeString('id-ID');
    
    detailsDiv.style.display = 'block';
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

// Clear interval when leaving page
window.addEventListener('beforeunload', function() {
    if (statusInterval) {
        clearInterval(statusInterval);
    }
});
</script>
@endpush
@endsection
