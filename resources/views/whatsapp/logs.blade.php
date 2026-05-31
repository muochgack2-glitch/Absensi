@extends('layouts.admin')

@section('title', 'Log Pesan WhatsApp')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">📋 Log Pesan WhatsApp</h1>
            <p class="text-muted mb-0">Riwayat pengiriman pesan WhatsApp</p>
        </div>
        <div>
            <a href="{{ route('whatsapp.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('whatsapp.logs') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label small">Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">Semua Status</option>
                        <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Terkirim</option>
                        <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Gagal</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small">Tipe</label>
                    <select name="type" class="form-select form-select-sm">
                        <option value="">Semua Tipe</option>
                        <option value="manual" {{ request('type') == 'manual' ? 'selected' : '' }}>Manual</option>
                        <option value="auto_registration" {{ request('type') == 'auto_registration' ? 'selected' : '' }}>Auto Registrasi</option>
                        <option value="broadcast" {{ request('type') == 'broadcast' ? 'selected' : '' }}>Broadcast</option>
                        <option value="reminder" {{ request('type') == 'reminder' ? 'selected' : '' }}>Pengingat</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small">Tanggal</label>
                    <input type="date" name="date" class="form-control form-control-sm" value="{{ request('date') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small">Cari Nomor HP</label>
                    <div class="input-group input-group-sm">
                        <input type="text" name="search" class="form-control" placeholder="08xxx..." value="{{ request('search') }}">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i>
                        </button>
                        @if(request()->hasAny(['status', 'type', 'date', 'search']))
                        <a href="{{ route('whatsapp.logs') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times"></i>
                        </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Logs Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header border-bottom" style="background: var(--bg-primary);">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0" style="color: var(--text-primary);">
                    <i class="fas fa-list me-2"></i>Daftar Log Pesan
                </h5>
                <span class="badge bg-primary">{{ $logs->total() }} total</span>
            </div>
        </div>
        <div class="card-body p-0" style="background: var(--bg-primary);">
            @if($logs->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">#</th>
                            <th width="12%">Waktu</th>
                            <th width="15%">Nomor HP</th>
                            <th width="30%">Pesan</th>
                            <th width="10%">Tipe</th>
                            <th width="10%">Status</th>
                            <th width="10%">Pengirim</th>
                            <th width="8%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($logs as $log)
                        <tr>
                            <td>{{ $logs->firstItem() + $loop->index }}</td>
                            <td>
                                <small class="text-muted">
                                    {{ $log->created_at->format('d/m/Y') }}<br>
                                    {{ $log->created_at->format('H:i:s') }}
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
                                    {{ Str::limit($log->message, 80) }}
                                </div>
                                @if($log->template)
                                <small class="text-muted">
                                    <i class="fas fa-file-alt me-1"></i>{{ $log->template->label }}
                                </small>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ $log->type_label }}</span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $log->status_color }}">
                                    {{ $log->status_label }}
                                </span>
                                @if($log->status == 'sent' && $log->sent_at)
                                <br><small class="text-muted">{{ $log->sent_at->format('H:i:s') }}</small>
                                @endif
                            </td>
                            <td>
                                @if($log->sender)
                                <small>{{ $log->sender->name }}</small>
                                @else
                                <small class="text-muted">System</small>
                                @endif
                            </td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary" onclick="showDetail({{ $log->id }})" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="card-footer bg-white border-top">
                {{ $logs->links() }}
            </div>
            @else
            <div class="text-center py-5">
                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                <p class="text-muted">Tidak ada log pesan</p>
                @if(request()->hasAny(['status', 'type', 'date', 'search']))
                <a href="{{ route('whatsapp.logs') }}" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-times me-2"></i>Reset Filter
                </a>
                @endif
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Detail Modal -->
<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-info-circle me-2"></i>Detail Log Pesan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detailContent">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function showDetail(logId) {
    const modal = new bootstrap.Modal(document.getElementById('detailModal'));
    modal.show();
    
    // Fetch log detail
    const log = @json($logs->items());
    const logData = log.find(l => l.id === logId);
    
    if (logData) {
        const content = `
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label small text-muted">Nomor HP</label>
                    <div class="fw-bold">${logData.phone}</div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label small text-muted">Status</label>
                    <div><span class="badge bg-${logData.status_color}">${logData.status_label}</span></div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label small text-muted">Tipe</label>
                    <div><span class="badge bg-secondary">${logData.type_label}</span></div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label small text-muted">Waktu Dibuat</label>
                    <div>${new Date(logData.created_at).toLocaleString('id-ID')}</div>
                </div>
                ${logData.sent_at ? `
                <div class="col-md-6 mb-3">
                    <label class="form-label small text-muted">Waktu Terkirim</label>
                    <div>${new Date(logData.sent_at).toLocaleString('id-ID')}</div>
                </div>
                ` : ''}
                ${logData.pendaftar ? `
                <div class="col-md-6 mb-3">
                    <label class="form-label small text-muted">Pendaftar</label>
                    <div>${logData.pendaftar.nama_lengkap}</div>
                </div>
                ` : ''}
                ${logData.template ? `
                <div class="col-md-6 mb-3">
                    <label class="form-label small text-muted">Template</label>
                    <div>${logData.template.label}</div>
                </div>
                ` : ''}
                ${logData.sender ? `
                <div class="col-md-6 mb-3">
                    <label class="form-label small text-muted">Dikirim Oleh</label>
                    <div>${logData.sender.name}</div>
                </div>
                ` : ''}
                <div class="col-12 mb-3">
                    <label class="form-label small text-muted">Pesan</label>
                    <div class="border rounded p-3 bg-light" style="white-space: pre-wrap;">${logData.message}</div>
                </div>
                ${logData.error_message ? `
                <div class="col-12 mb-3">
                    <label class="form-label small text-muted">Error Message</label>
                    <div class="alert alert-danger mb-0">${logData.error_message}</div>
                </div>
                ` : ''}
            </div>
        `;
        
        document.getElementById('detailContent').innerHTML = content;
    }
}
</script>
@endpush
@endsection
