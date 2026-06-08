@extends('layouts.admin')

@section('title', 'Data Pendaftar Terhapus - SPMB (Sistem Penerimaan Murid Baru)')

@push('styles')
<style>
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

    #trashedTable thead th {
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: var(--text-secondary);
        white-space: nowrap;
    }
    #trashedTable tbody td {
        vertical-align: middle;
        font-size: 13px;
    }
    #trashedTable tbody tr:hover {
        background: var(--bg-secondary);
    }
    .reg-code {
        font-weight: 800;
        color: var(--primary);
        font-variant-numeric: tabular-nums;
    }
    .deleted-info {
        font-size: 12px;
        color: var(--text-secondary);
        line-height: 1.4;
    }
    .deleted-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 6px 12px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
        background-color: #fee2e2;
        color: #b91c1c;
    }
    
    /* Dark Mode Support */
    .admin-dark .deleted-badge {
        background-color: rgba(239, 68, 68, 0.2) !important;
        color: #fca5a5 !important;
    }
</style>
@endpush

@section('content')
<div class="dashboard-content">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-3">
        <div>
            <h2 class="mb-2">Data Pendaftar Terhapus</h2>
            <p class="text-muted mb-0">Kelola dan pulihkan data pendaftar yang telah dihapus.</p>
        </div>
        <div class="d-flex gap-2">
            <x-button variant="secondary" icon="fas fa-arrow-left" href="{{ route('pendaftar.index') }}">
                Kembali ke Data Pendaftar
            </x-button>
        </div>
    </div>

    @if (Session::has('success'))
        <x-alert type="success" dismissible="true">
            {{ Session::get('success') }}
        </x-alert>
    @endif

    @if (Session::has('error'))
        <x-alert type="danger" dismissible="true">
            {{ Session::get('error') }}
        </x-alert>
    @endif

    <!-- Filter Section -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('pendaftar.trashed') }}" id="filterForm">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small text-muted">
                            <i class="fas fa-search me-1"></i> Cari
                        </label>
                        <input type="text" name="search" class="form-control" placeholder="Nama, No. Reg, NISN..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                    </div>
                    <div class="col-md-4 d-flex align-items-end justify-content-end gap-2">
                        @if(request()->has('search'))
                        <a href="{{ route('pendaftar.trashed') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-1"></i> Reset Filter
                        </a>
                        @endif
                        <span class="text-muted small align-self-center">
                            {{ $pendaftars->total() }} data ditemukan
                        </span>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table -->
    <x-section-card title="Daftar Pendaftar Terhapus" icon="fas fa-trash-restore">
        <x-slot:actions>
            <small class="text-muted">Data yang dihapus dapat dipulihkan kembali</small>
        </x-slot:actions>
        <div class="table-responsive">
            <table class="table table-hover" id="trashedTable">
                <thead class="table-light">
                    <tr>
                        <th style="width: 80px;">No. Reg</th>
                        <th>Nama Lengkap</th>
                        <th>Jurusan</th>
                        <th>Gelombang</th>
                        <th style="width: 200px;">Info Penghapusan</th>
                        <th style="width: 150px;">Alasan</th>
                        <th style="width: 120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pendaftars as $p)
                        <tr>
                            <td><span class="reg-code">{{ $p->no_registrasi }}</span></td>
                            <td>
                                <div class="fw-semibold" style="color: var(--text-primary);">{{ $p->nama_lengkap }}</div>
                                <div class="small text-muted">NISN: {{ $p->nisn }}</div>
                            </td>
                            <td>{{ $p->jurusan }}</td>
                            <td>{{ $p->gelombang }}</td>
                            <td>
                                <div class="deleted-info">
                                    <div class="mb-1">
                                        <i class="fas fa-calendar text-danger me-1"></i>
                                        {{ $p->deleted_at->format('d M Y, H:i') }}
                                    </div>
                                    @if($p->deletedBy)
                                    <div>
                                        <i class="fas fa-user text-danger me-1"></i>
                                        {{ $p->deletedBy->username }}
                                    </div>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <small class="text-muted">
                                    {{ $p->deleted_reason ?? '-' }}
                                </small>
                            </td>
                            <td>
                                <form method="POST" 
                                      action="{{ route('pendaftar.restore', $p->id_pendaftar) }}" 
                                      class="d-inline restore-form" 
                                      data-nama="{{ $p->nama_lengkap }}"
                                      data-noreg="{{ $p->no_registrasi }}">
                                    @csrf
                                    <x-button 
                                        variant="success" 
                                        size="sm"
                                        icon="fas fa-undo"
                                        type="submit"
                                    >
                                        Pulihkan
                                    </x-button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted empty-state">
                                <i class="fas fa-inbox" style="font-size: 24px;"></i>
                                <p class="mt-2 mb-0">Tidak ada data terhapus</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-section-card>

    <!-- Pagination -->
    <div class="mt-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="text-muted small">
                    Menampilkan {{ $pendaftars->firstItem() ?? 0 }} - {{ $pendaftars->lastItem() ?? 0 }} dari {{ $pendaftars->total() }} data
                </div>
                <div class="d-flex align-items-center gap-2">
                    <label class="text-muted small mb-0">Tampilkan:</label>
                    <select class="form-select form-select-sm" style="width: auto;" onchange="changePerPage(this.value)">
                        <option value="10" {{ request('per_page', 20) == 10 ? 'selected' : '' }}>10</option>
                        <option value="20" {{ request('per_page', 20) == 20 ? 'selected' : '' }}>20</option>
                        <option value="50" {{ request('per_page', 20) == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page', 20) == 100 ? 'selected' : '' }}>100</option>
                    </select>
                </div>
            </div>
            <nav aria-label="Page navigation">
                <ul class="pagination mb-0">
                    {{-- Previous Page Link --}}
                    @if ($pendaftars->onFirstPage())
                        <li class="page-item disabled">
                            <span class="page-link"><i class="fas fa-chevron-left"></i></span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $pendaftars->previousPageUrl() }}" rel="prev">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        </li>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($pendaftars->links()->elements[0] as $page => $url)
                        @if ($page == $pendaftars->currentPage())
                            <li class="page-item active">
                                <span class="page-link">{{ $page }}</span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($pendaftars->hasMorePages())
                        <li class="page-item">
                            <a class="page-link" href="{{ $pendaftars->nextPageUrl() }}" rel="next">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        </li>
                    @else
                        <li class="page-item disabled">
                            <span class="page-link"><i class="fas fa-chevron-right"></i></span>
                        </li>
                    @endif
                </ul>
            </nav>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Change per page
function changePerPage(value) {
    const url = new URL(window.location.href);
    url.searchParams.set('per_page', value);
    url.searchParams.delete('page'); // Reset to page 1
    window.location.href = url.toString();
}

document.addEventListener('DOMContentLoaded', function(){
    // Restore confirmation
    document.addEventListener('submit', function(e){
        if (e.target.classList.contains('restore-form')) {
            e.preventDefault();
            e.stopImmediatePropagation();
            
            const form = e.target;
            const nama = form.dataset.nama || 'pendaftar ini';
            const noReg = form.dataset.noreg || '';

            Modal.confirm(
                `Pulihkan data pendaftar:<br><br><strong>${nama}</strong><br>No. Registrasi: <strong>${noReg}</strong><br><br><small class="text-muted">Data akan kembali ke daftar pendaftar aktif.</small>`,
                function() {
                    // On confirm - use native submit (bypasses event listeners)
                    console.log('Submitting restore form');
                    HTMLFormElement.prototype.submit.call(form);
                },
                {
                    title: 'Pulihkan Data?',
                    confirmText: 'Ya, Pulihkan',
                    cancelText: 'Batal',
                    type: 'success'
                }
            );
            
            return false;
        }
    }, true);

    @if (Session::has('success'))
    Modal.alert(
        '{{ addslashes(Session::get('success')) }}',
        'Berhasil',
        'success'
    );
    @endif
});
</script>
@endpush
