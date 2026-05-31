@extends('layouts.admin')

@section('title', 'Riwayat Penggabungan Jaringan - SPMB')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">📜 Riwayat Penggabungan Jaringan</h1>
            <p class="text-muted mb-0">Tracking semua aktivitas penggabungan jaringan</p>
        </div>
        <div>
            <a href="{{ route('jaringan.merge') }}" class="btn btn-primary">
                <i class="fas fa-arrow-left me-2"></i>Kembali ke Merge
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Filter -->
    <div class="card border-0 shadow-sm mb-4" style="background: var(--bg-primary);">
        <div class="card-body">
            <form method="GET" action="{{ route('jaringan.history') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Cari Jaringan</label>
                    <input type="text" name="search" class="form-control" placeholder="Cari nama jaringan..." value="{{ $search ?? '' }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select name="filter" class="form-select">
                        <option value="all" {{ ($filter ?? 'all') == 'all' ? 'selected' : '' }}>Semua</option>
                        <option value="active" {{ ($filter ?? 'all') == 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="undone" {{ ($filter ?? 'all') == 'undone' ? 'selected' : '' }}>Di-undo</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Tipe</label>
                    <select name="type" class="form-select">
                        <option value="all" {{ ($typeFilter ?? 'all') == 'all' ? 'selected' : '' }}>Semua</option>
                        <option value="full" {{ ($typeFilter ?? 'all') == 'full' ? 'selected' : '' }}>Full</option>
                        <option value="selective" {{ ($typeFilter ?? 'all') == 'selective' ? 'selected' : '' }}>Selective</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Dari Tanggal</label>
                    <input type="date" name="date_from" class="form-control" value="{{ $dateFrom ?? '' }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Sampai Tanggal</label>
                    <input type="date" name="date_to" class="form-control" value="{{ $dateTo ?? '' }}">
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- History List -->
    <div class="card border-0 shadow-sm" style="background: var(--bg-primary);">
        <div class="card-header border-bottom" style="background: var(--bg-primary); border-color: var(--border-light) !important;">
            <h5 class="mb-0" style="color: var(--text-primary);">
                <i class="fas fa-history me-2"></i>Riwayat ({{ $histories->total() }} aktivitas)
            </h5>
        </div>
        <div class="card-body">
            @if($histories->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Waktu</th>
                                <th>Tipe</th>
                                <th>Dari</th>
                                <th>Ke</th>
                                <th class="text-center">Jumlah</th>
                                <th>Oleh</th>
                                <th>Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($histories as $history)
                            <tr>
                                <td>
                                    <small>{{ $history->created_at->format('d M Y') }}</small><br>
                                    <small class="text-muted">{{ $history->created_at->format('H:i') }}</small>
                                </td>
                                <td>
                                    @if($history->merge_type == 'full')
                                        <span class="badge bg-primary">Full</span>
                                    @else
                                        <span class="badge bg-info">Selective</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $history->from_jaringan }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-success">{{ $history->to_jaringan }}</span>
                                </td>
                                <td class="text-center">
                                    <strong>{{ $history->affected_count }}</strong>
                                </td>
                                <td>
                                    <small>{{ $history->merged_by_name }}</small><br>
                                    <small class="text-muted">{{ $history->merged_by_role }}</small>
                                </td>
                                <td>
                                    @if($history->is_undone)
                                        <span class="badge bg-warning">
                                            <i class="fas fa-undo me-1"></i>Di-undo
                                        </span>
                                        <br>
                                        <small class="text-muted">{{ $history->undone_at?->format('d M Y H:i') }}</small>
                                    @else
                                        <span class="badge bg-success">
                                            <i class="fas fa-check me-1"></i>Aktif
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if(!$history->is_undone)
                                        <button type="button" class="btn btn-sm btn-warning" onclick="undoMerge({{ $history->id }}, '{{ $history->from_jaringan }}', '{{ $history->to_jaringan }}', {{ $history->affected_count }})">
                                            <i class="fas fa-undo me-1"></i>Undo
                                        </button>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="mt-4">
                    {{ $histories->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Belum ada riwayat penggabungan</p>
                    <a href="{{ route('jaringan.merge') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Mulai Gabungkan Jaringan
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Undo Confirmation Modal -->
<div class="modal fade" id="undoModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-undo me-2"></i>Batalkan Penggabungan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="undoContent">
                <!-- Content will be filled by JavaScript -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="undoForm" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-undo me-2"></i>Ya, Batalkan
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function undoMerge(historyId, namaLama, namaBaru, jumlah) {
    const content = `
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Perhatian!</strong> Anda akan membatalkan penggabungan ini.
        </div>
        
        <p><strong>Detail:</strong></p>
        <ul>
            <li>Dari: <span class="badge bg-secondary">${namaLama}</span></li>
            <li>Ke: <span class="badge bg-success">${namaBaru}</span></li>
            <li>Jumlah pendaftar: <strong>${jumlah}</strong></li>
        </ul>
        
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            ${jumlah} pendaftar akan dikembalikan ke jaringan "${namaLama}"
        </div>
        
        <p class="text-muted small mb-0">
            <strong>Catatan:</strong> Undo ini hanya mempengaruhi pendaftar dari merge ini saja.
        </p>
    `;
    
    document.getElementById('undoContent').innerHTML = content;
    document.getElementById('undoForm').action = `/admin/jaringan/undo/${historyId}`;
    
    const modal = new bootstrap.Modal(document.getElementById('undoModal'));
    modal.show();
}
</script>
@endpush
@endsection
