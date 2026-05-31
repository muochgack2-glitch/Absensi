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

    <!-- Filter -->
    <div class="card border-0 shadow-sm mb-4" style="background: var(--bg-primary);">
        <div class="card-body">
            <form method="GET" action="{{ route('jaringan.history') }}" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Cari Jaringan</label>
                    <input type="text" name="search" class="form-control" placeholder="Cari nama jaringan..." value="{{ $search }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Dari Tanggal</label>
                    <input type="date" name="date_from" class="form-control" value="{{ $dateFrom }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Sampai Tanggal</label>
                    <input type="date" name="date_to" class="form-control" value="{{ $dateTo }}">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-2"></i>Filter
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
                @foreach($groupedHistories as $groupKey => $group)
                    @php
                        $first = $group->first();
                        $totalAffected = $group->sum('jumlah_pendaftar');
                    @endphp
                    <div class="card mb-3 border" style="background: var(--bg-secondary); border-color: var(--border-light) !important;">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h6 class="mb-1" style="color: var(--text-primary);">
                                        <i class="fas fa-clock text-primary me-2"></i>
                                        {{ $first->tanggal_merge->format('d M Y, H:i') }} WIB
                                    </h6>
                                    <p class="text-muted small mb-0">
                                        <i class="fas fa-user me-1"></i>
                                        Oleh: {{ $first->admin->name ?? 'Unknown' }}
                                    </p>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-success">{{ $group->count() }} jaringan digabung</span>
                                    <br>
                                    <small class="text-muted">{{ $totalAffected }} pendaftar terpengaruh</small>
                                </div>
                            </div>
                            
                            <div class="alert alert-info mb-3">
                                <strong>Menjadi:</strong> <span class="badge bg-primary">{{ $first->nama_baru }}</span>
                            </div>
                            
                            <div class="table-responsive">
                                <table class="table table-sm mb-0">
                                    <thead>
                                        <tr>
                                            <th>Dari</th>
                                            <th class="text-center">Jumlah</th>
                                            <th>Keterangan</th>
                                            <th class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($group as $history)
                                        <tr>
                                            <td>
                                                <span class="badge bg-secondary">{{ $history->nama_lama }}</span>
                                            </td>
                                            <td class="text-center">{{ $history->jumlah_pendaftar }}</td>
                                            <td>
                                                <small class="text-muted">{{ $history->keterangan ?: '-' }}</small>
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-sm btn-warning" onclick="undoMerge({{ $history->id }}, '{{ $history->nama_lama }}', '{{ $history->nama_baru }}', {{ $history->jumlah_pendaftar }})">
                                                    <i class="fas fa-undo me-1"></i>Undo
                                                </button>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endforeach
                
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
            <li>Dari: <span class="badge bg-secondary">${namaBaru}</span></li>
            <li>Kembali ke: <span class="badge bg-primary">${namaLama}</span></li>
            <li>Jumlah pendaftar: <strong>${jumlah}</strong></li>
        </ul>
        
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            ${jumlah} pendaftar akan dikembalikan ke jaringan "${namaLama}"
        </div>
        
        <p class="text-muted small mb-0">
            <strong>Catatan:</strong> Undo ini hanya mempengaruhi pendaftar dari merge ini saja. 
            Jika ada merge lain yang juga menggunakan "${namaBaru}", mereka tidak terpengaruh.
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
