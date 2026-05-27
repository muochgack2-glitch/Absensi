@extends('layouts.admin')

@section('title', 'Verifikasi Daftar Ulang - SPMB (Sistem Penerimaan Murid Baru)')

@push('styles')
<link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<style>
        body { font-family: 'Inter', sans-serif !important; }
        .page-header-card {
            background: linear-gradient(135deg, #ffffff, #f8fbff);
            border: 1px solid #e2e8f0;
            border-radius: 18px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
            padding: 22px 24px;
            margin-bottom: 20px;
        }
        .page-title { font-size: 28px; font-weight: 800; margin: 0; color: #1e293b; letter-spacing: -0.03em; }
        .page-subtitle { font-size: 14px; color: #64748b; margin-top: 6px; }
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 14px;
            margin-bottom: 20px;
        }
        .summary-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 18px;
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.05);
        }
        .summary-label {
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .05em;
            color: #64748b;
        }
        .summary-value {
            margin-top: 10px;
            font-size: 30px;
            font-weight: 800;
            line-height: 1;
            color: #0f172a;
        }
        .summary-subtext {
            margin-top: 8px;
            font-size: 12px;
            color: #94a3b8;
        }
        .summary-card.is-danger .summary-value { color: #b91c1c; }
        .summary-card.is-success .summary-value { color: #166534; }
        .summary-card.is-info .summary-value { color: #1d4ed8; }
        .summary-card.is-warning .summary-value { color: #b45309; }
        .filter-wrap,
        .data-panel {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 18px;
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.05);
        }
        .filter-wrap {
            padding: 16px 18px;
            margin-bottom: 16px;
        }
        .filter-wrap .form-select, .filter-wrap .form-control {
            font-size: 14px;
            border-radius: 10px;
            border: 1px solid #cbd5e1;
        }
        .filter-wrap .form-select:focus, .filter-wrap .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 .25rem rgba(102,126,234,.15);
        }
        .filter-wrap .filter-label { font-size: 13px; font-weight: 600; color: #64748b; margin-bottom: 6px; display: block; }
        .data-panel-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 16px 18px;
            border-bottom: 1px solid #e2e8f0;
            background: linear-gradient(135deg, #f8fafc, #f1f5f9);
        }
        .data-panel-title {
            font-size: 15px;
            font-weight: 700;
            color: #0f172a;
            margin: 0;
        }
        .data-panel-subtitle {
            font-size: 12px;
            color: #64748b;
            margin-top: 2px;
        }
        .table-shell { padding: 18px; }
        .table thead th {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: #64748b;
            border-bottom-width: 1px;
            white-space: nowrap;
        }
        .table tbody td {
            vertical-align: middle;
            font-size: 13px;
        }
        .table tbody tr:hover { background: #f8fafc; }
        .reg-code { font-weight: 800; color: #0f172a; font-variant-numeric: tabular-nums; }
        .status-badge { padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 700; }
        .status-red { background-color: #fee2e2; color: #b91c1c; }
        .status-green { background-color: #dcfce7; color: #166534; }
        .keterangan-badge { padding: 5px 10px; border-radius: 999px; font-size: 11px; font-weight: 700; display: inline-block; }
        .ket-green { background-color: #e7f9ef; color: #1f9d55; }
        .ket-gray { background-color: #f1f5f9; color: #64748b; }
        .size-pill { display: inline-flex; align-items: center; justify-content: center; padding: 6px 10px; border-radius: 999px; background: #e0f2fe; color: #075985; font-size: 12px; font-weight: 700; }
        .btn-sm {
            padding: 6px 12px;
            font-size: 12px;
            font-weight: 600;
            border-radius: 10px;
        }
        @media (max-width: 992px) {
            .summary-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }
        @media (max-width: 576px) {
            .page-header-card { padding: 18px; }
            .page-title { font-size: 23px; }
            .summary-grid { grid-template-columns: 1fr; }
            .table-shell { padding: 12px; }
        }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="page-title">Verifikasi Daftar Ulang</h2>
                <p class="page-subtitle">Kelola status pendaftaran selesai, keterangan kaos dipesankan, dan aksi verifikasi dengan cepat.</p>
            </div>
        </div>

        @if (Session::has('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">{{ Session::get('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif

        <div class="filter-wrap">
            <div class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="filter-label" for="filterStatus">Filter Status</label>
                    <select id="filterStatus" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="Belum Daftar Ulang">Belum Daftar Ulang</option>
                        <option value="Diterima">Diterima</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="filter-label" for="filterJurusan">Filter Jurusan</label>
                    <select id="filterJurusan" class="form-select">
                        <option value="">Semua Jurusan</option>
                        @foreach(($jurusans ?? collect()) as $j)
                            <option value="{{ $j->kode }}">{{ $j->kode }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="filter-label" for="filterKeyword">Pencarian Cepat</label>
                    <input type="text" id="filterKeyword" class="form-control" placeholder="Cari nama / no registrasi...">
                </div>
                <div class="col-md-2 d-grid">
                    <button type="button" id="resetFilter" class="btn btn-outline-secondary">Reset Filter</button>
                </div>
            </div>
        </div>

        <div class="card p-4">
            <div class="table-responsive">
                <table class="table table-hover" id="verifikasiTable">
                    <thead class="table-light">
                        <tr>
                            <th>No. Registrasi</th><th>Nama</th><th>Jurusan</th><th>Gelombang</th><th>Status</th><th>Keterangan</th><th>Ukuran Kaos</th><th>Aksi Verifikasi</th>
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
                                <td><strong>{{ $p->no_registrasi }}</strong></td>
                                <td>{{ $p->nama_lengkap }}</td>
                                <td>{{ $p->masterJurusan?->kode ?? $p->jurusan }}</td>
                                <td>{{ $p->gelombang }}</td>
                                <td><span class="status-badge {{ $statusClass }}">{{ $statusText }}</span></td>
                                <td><span class="keterangan-badge {{ $keteranganClass }}" data-bs-toggle="tooltip" data-bs-placement="top" title="Kaos otomatis dipesankan setelah verifikasi daftar ulang.">{{ $keterangan }}</span></td>
                                <td>{{ $logistik->ukuran_kaos ?? '-' }}</td>
                                <td>
                                    <div class="d-flex gap-2 flex-wrap">
                                        @if ($logistik->status_bayar === 'Belum')
                                            <a href="{{ route('pendaftar.daftar-ulang', $p->id_pendaftar) }}" class="btn btn-sm btn-success">
                                                <i class="fas fa-check"></i> Verifikasi
                                            </a>
                                        @else
                                            <form method="POST" action="{{ route('pendaftar.cancel-daftar-ulang', $p->id_pendaftar) }}" class="d-inline rollback-form" data-nama="{{ $p->nama_lengkap }}">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="fas fa-rotate-left"></i> Batalkan Verifikasi
                                                </button>
                                            </form>
                                        @endif

                                        <a href="{{ route('pendaftar.print.ambil-barang', $p->id_pendaftar) }}" target="_blank" class="btn btn-sm btn-warning">
                                            <i class="fas fa-print"></i> Cetak Bukti
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center text-muted py-4">Belum ada data pendaftar</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-4">{{ $pendaftars->links() }}</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function(){
    const table = $('#verifikasiTable').DataTable({
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

    $(document).on('submit', '.rollback-form', function(e){
        e.preventDefault();
        const form = this;
        const nama = $(form).data('nama') || 'pendaftar ini';

        Swal.fire({
            icon: 'warning',
            title: 'Batalkan verifikasi?',
            html: `Status daftar ulang untuk <strong>${nama}</strong> akan dikembalikan ke Belum Daftar Ulang.`,
            showCancelButton: true,
            confirmButtonText: 'Ya, batalkan',
            cancelButtonText: 'Tidak',
            confirmButtonColor: '#dc2626'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });

    @if (Session::has('rollback_success'))
    Swal.fire({icon:'success',title:'Rollback Berhasil',html:'{!! addslashes(Session::get('success')) !!}',confirmButtonColor:'var(--theme-primary)'});
    @endif

    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endpush
