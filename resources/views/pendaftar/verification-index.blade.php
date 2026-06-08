@extends('layouts.admin')

@section('title', 'Verifikasi Daftar Ulang - SPMB (Sistem Penerimaan Murid Baru)')

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

    #verifikasiTable thead th {
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: var(--text-secondary);
        white-space: nowrap;
    }
    #verifikasiTable tbody td {
        vertical-align: middle;
        font-size: 13px;
    }
    #verifikasiTable tbody tr:hover {
        background: var(--bg-secondary);
    }
    .reg-code {
        font-weight: 800;
        color: var(--primary);
        font-variant-numeric: tabular-nums;
    }
    .status-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 6px 12px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
        line-height: 1.2;
    }
    .status-red {
        background-color: #fee2e2;
        color: #b91c1c;
    }
    .status-green {
        background-color: #dcfce7;
        color: #166534;
    }
    .keterangan-badge {
        padding: 5px 10px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 700;
        display: inline-block;
    }
    .ket-green {
        background-color: #e7f9ef;
        color: #1f9d55;
    }
    .ket-gray {
        background-color: #f1f5f9;
        color: #64748b;
    }
    .size-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 6px 10px;
        border-radius: 999px;
        background: #e0f2fe;
        color: #075985;
        font-size: 12px;
        font-weight: 700;
    }
    
    /* Dark Mode Support */
    .admin-dark .status-red {
        background-color: rgba(239, 68, 68, 0.2) !important;
        color: #fca5a5 !important;
    }
    .admin-dark .status-green {
        background-color: rgba(34, 197, 94, 0.2) !important;
        color: #86efac !important;
    }
    .admin-dark .ket-green {
        background-color: rgba(34, 197, 94, 0.15) !important;
        color: #4ade80 !important;
    }
    .admin-dark .ket-gray {
        background-color: #334155 !important;
        color: #94a3b8 !important;
    }
    .admin-dark .size-pill {
        background: rgba(56, 189, 248, 0.2) !important;
        color: #7dd3fc !important;
    }
    
    /* Fix pagination button size */
    .pagination {
        margin-bottom: 0;
    }
    .pagination .page-link {
        padding: 0.375rem 0.75rem;
        font-size: 0.875rem;
        line-height: 1.5;
        min-width: 38px;
        text-align: center;
    }
    .pagination .page-item:first-child .page-link,
    .pagination .page-item:last-child .page-link {
        font-size: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .pagination .page-item.active .page-link {
        z-index: 3;
        background-color: var(--primary);
        border-color: var(--primary);
    }
    .pagination .page-item.disabled .page-link {
        opacity: 0.5;
    }
</style>
@endpush

@section('content')
<div class="dashboard-content">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-3">
        <div>
            <h2 class="mb-2">Verifikasi Daftar Ulang</h2>
            <p class="text-muted mb-0">Kelola status pendaftaran selesai, keterangan kaos dipesankan, dan aksi verifikasi dengan cepat.</p>
        </div>
    </div>

    @if (Session::has('success'))
        <x-alert type="success" dismissible="true">
            {{ Session::get('success') }}
        </x-alert>
    @endif

    <!-- Filter Section -->
    <x-section-card title="Filter Data" icon="fas fa-filter" class="mb-4">
        <form method="GET" action="{{ route('pendaftar.verification-index') }}">
            <div class="row g-3">
                <div class="col-md-3">
                    <x-form-group label="Filter Status" name="status">
                        <x-select name="status" id="filterStatus">
                            <option value="">Semua Status</option>
                            <option value="Belum Daftar Ulang" {{ request('status') == 'Belum Daftar Ulang' ? 'selected' : '' }}>Belum Daftar Ulang</option>
                            <option value="Diterima" {{ request('status') == 'Diterima' ? 'selected' : '' }}>Diterima</option>
                        </x-select>
                    </x-form-group>
                </div>
                <div class="col-md-3">
                    <x-form-group label="Filter Jurusan" name="jurusan">
                        <x-select name="jurusan" id="filterJurusan">
                            <option value="">Semua Jurusan</option>
                            @foreach(($jurusans ?? collect()) as $j)
                                <option value="{{ $j->kode }}" {{ request('jurusan') == $j->kode ? 'selected' : '' }}>{{ $j->kode }}</option>
                            @endforeach
                        </x-select>
                    </x-form-group>
                </div>
                <div class="col-md-4">
                    <x-form-group label="Pencarian Cepat" name="search">
                        <x-input 
                            name="search" 
                            id="filterKeyword" 
                            icon="fas fa-search"
                            placeholder="Cari nama / no registrasi..."
                            value="{{ request('search') }}"
                        />
                    </x-form-group>
                </div>
                <div class="col-md-2 d-flex align-items-end gap-2">
                    <x-button variant="primary" type="submit" block="true">
                        <i class="fas fa-filter"></i> Filter
                    </x-button>
                    @if(request()->hasAny(['search', 'status', 'jurusan']))
                    <a href="{{ route('pendaftar.verification-index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i>
                    </a>
                    @endif
                </div>
            </div>
        </form>
    </x-section-card>

    <!-- Data Table -->
    <x-section-card title="Daftar Verifikasi" icon="fas fa-list-check">
        <x-slot:actions>
            <small class="text-muted">Gunakan tombol verifikasi untuk memproses daftar ulang dan cetak bukti</small>
        </x-slot:actions>

        <div class="table-responsive">
            <table class="table table-hover" id="verifikasiTable">
                <thead class="table-light">
                    <tr>
                        <th>No. Registrasi</th>
                        <th>Nama</th>
                        <th>Jurusan</th>
                        <th>Gelombang</th>
                        <th>Status</th>
                        <th>Keterangan</th>
                        <th>Ukuran Kaos</th>
                        <th>Aksi Verifikasi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pendaftars as $p)
                        @php
                            $logistik = $p->logistik;
                            if ($p->status_siswa === 'Diterima') {
                                $statusClass = 'status-green';
                                $statusText = 'Diterima';
                                $keterangan = 'Kaos dipesankan';
                                $keteranganClass = 'ket-green';
                            } else {
                                $statusClass = 'status-red';
                                $statusText = 'Belum Daftar Ulang';
                                $keterangan = '-';
                                $keteranganClass = 'ket-gray';
                            }
                        @endphp
                        <tr>
                            <td><span class="reg-code">{{ $p->no_registrasi }}</span></td>
                            <td>
                                <div class="fw-semibold" style="color: var(--text-primary);">{{ $p->nama_lengkap }}</div>
                            </td>
                            <td>{{ $p->masterJurusan?->kode ?? $p->jurusan }}</td>
                            <td>{{ $p->gelombang }}</td>
                            <td>
                                <span class="status-badge {{ $statusClass }}">{{ $statusText }}</span>
                            </td>
                            <td>
                                <span class="keterangan-badge {{ $keteranganClass }}" 
                                      data-bs-toggle="tooltip" 
                                      data-bs-placement="top" 
                                      title="Kaos otomatis dipesankan setelah verifikasi daftar ulang.">
                                    {{ $keterangan }}
                                </span>
                            </td>
                            <td>
                                @if($logistik->ukuran_kaos)
                                    <span class="size-pill">{{ $logistik->ukuran_kaos }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <x-table-actions align="start">
                                    @if ($logistik->status_bayar === 'Belum')
                                        <x-button 
                                            variant="success" 
                                            size="sm"
                                            icon="fas fa-check"
                                            href="{{ route('pendaftar.daftar-ulang', $p->id_pendaftar) }}"
                                        >
                                            Verifikasi
                                        </x-button>
                                    @else
                                        <form method="POST" 
                                              action="{{ route('pendaftar.cancel-daftar-ulang', $p->id_pendaftar) }}" 
                                              class="d-inline rollback-form" 
                                              data-nama="{{ $p->nama_lengkap }}">
                                            @csrf
                                            <x-button 
                                                variant="danger" 
                                                size="sm"
                                                outline="true"
                                                icon="fas fa-rotate-left"
                                                type="submit"
                                            >
                                                Batalkan
                                            </x-button>
                                        </form>
                                    @endif

                                    <x-button 
                                        variant="warning" 
                                        size="sm"
                                        icon="fas fa-print"
                                        href="{{ route('pendaftar.print.ambil-barang', $p->id_pendaftar) }}"
                                        target="_blank"
                                    >
                                        Cetak
                                    </x-button>
                                </x-table-actions>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-5">
                                <x-empty-state 
                                    icon="fas fa-inbox" 
                                    message="Belum ada data pendaftar"
                                    size="sm"
                                />
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
    // Rollback confirmation with Modal.confirm
    document.addEventListener('submit', function(e){
        if (e.target.classList.contains('rollback-form')) {
            e.preventDefault();
            e.stopImmediatePropagation();
            
            const form = e.target;
            const nama = form.dataset.nama || 'pendaftar ini';

            Modal.confirm(
                `Status daftar ulang untuk <strong>${nama}</strong> akan dikembalikan ke Belum Daftar Ulang.`,
                function() {
                    // On confirm - use native submit (bypasses event listeners)
                    console.log('Submitting form via native method');
                    HTMLFormElement.prototype.submit.call(form);
                },
                {
                    title: 'Batalkan verifikasi?',
                    confirmText: 'Ya, batalkan',
                    cancelText: 'Tidak',
                    type: 'warning'
                }
            );
            
            return false;
        }
    }, true);

    @if (Session::has('rollback_success'))
    Modal.alert(
        '{!! addslashes(Session::get('success')) !!}',
        'Rollback Berhasil',
        'success'
    );
    @endif

    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endpush
