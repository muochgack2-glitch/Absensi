@extends('layouts.admin')

@section('title', 'Verifikasi Daftar Ulang - SPMB (Sistem Penerimaan Murid Baru)')

@push('styles')
<link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
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
        <div class="row g-3">
            <div class="col-md-3">
                <x-form-group label="Filter Status" name="filterStatus">
                    <x-select name="filterStatus" id="filterStatus">
                        <option value="">Semua Status</option>
                        <option value="Belum Daftar Ulang">Belum Daftar Ulang</option>
                        <option value="Diterima">Diterima</option>
                    </x-select>
                </x-form-group>
            </div>
            <div class="col-md-3">
                <x-form-group label="Filter Jurusan" name="filterJurusan">
                    <x-select name="filterJurusan" id="filterJurusan">
                        <option value="">Semua Jurusan</option>
                        @foreach(($jurusans ?? collect()) as $j)
                            <option value="{{ $j->kode }}">{{ $j->kode }}</option>
                        @endforeach
                    </x-select>
                </x-form-group>
            </div>
            <div class="col-md-4">
                <x-form-group label="Pencarian Cepat" name="filterKeyword">
                    <x-input 
                        name="filterKeyword" 
                        id="filterKeyword" 
                        icon="fas fa-search"
                        placeholder="Cari nama / no registrasi..."
                    />
                </x-form-group>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <x-button variant="secondary" outline="true" id="resetFilter" block="true">
                    <i class="fas fa-rotate-right"></i> Reset
                </x-button>
            </div>
        </div>
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
        {{ $pendaftars->links() }}
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script>
$(document).ready(function(){
    const table = $('#verifikasiTable').DataTable({
        language: {
            search: 'Cari:',
            lengthMenu: 'Tampilkan _MENU_ data',
            info: 'Menampilkan _START_ - _END_ dari _TOTAL_ data',
            paginate: { previous: 'Sebelumnya', next: 'Berikutnya' },
            zeroRecords: 'Tidak ada data yang ditemukan',
            emptyTable: 'Belum ada data pendaftar'
        },
        columnDefs:[{orderable:false,targets:-1}]
    });

    $('#filterStatus').on('change', function(){
        table.column(4).search(this.value).draw();
    });

    $('#filterJurusan').on('change', function(){
        table.column(2).search(this.value).draw();
    });

    $('#filterKeyword').on('keyup', function(){
        table.search(this.value).draw();
    });

    $('#resetFilter').on('click', function(){
        $('#filterStatus').val('');
        $('#filterJurusan').val('');
        $('#filterKeyword').val('');
        table.search('');
        table.column(4).search('');
        table.column(2).search('');
        table.draw();
    });

    // Rollback confirmation with Modal.confirm
    $(document).on('submit', '.rollback-form', function(e){
        e.preventDefault();
        e.stopImmediatePropagation();
        
        const $form = $(this);
        const form = this;
        const nama = $form.data('nama') || 'pendaftar ini';

        Modal.confirm(
            `Status daftar ulang untuk <strong>${nama}</strong> akan dikembalikan ke Belum Daftar Ulang.`,
            function() {
                // On confirm - use native submit (bypasses jQuery events)
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
    });

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
