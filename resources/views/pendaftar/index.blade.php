@extends('layouts.admin')

@section('title', 'Data Pendaftar - SPMB (Sistem Penerimaan Murid Baru)')

@push('styles')
<link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
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
        .page-title {
            font-size: 28px;
            font-weight: 800;
            letter-spacing: -0.03em;
            color: #1e293b;
            margin: 0;
        }
        .page-subtitle {
            margin-top: 6px;
            color: #64748b;
            font-size: 14px;
        }
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
        .summary-card.is-neutral .summary-value { color: #334155; }
        .data-panel {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 18px;
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.05);
            overflow: hidden;
        }
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
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }
        .table-shell {
            padding: 18px;
        }
        #pendaftarTable thead th {
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: #64748b;
            white-space: nowrap;
        }
        #pendaftarTable tbody td {
            vertical-align: middle;
            font-size: 13px;
        }
        #pendaftarTable tbody tr:hover {
            background: #f8fafc;
        }
        .reg-code {
            font-weight: 800;
            color: #0f172a;
            font-variant-numeric: tabular-nums;
        }
        .jurusan-pill,
        .size-pill,
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
        .jurusan-pill { background: #e0e7ff; color: #3730a3; }
        .size-pill { background: #e0f2fe; color: #075985; }
        .status-red {
            background-color: #fee2e2;
            color: #b91c1c;
        }
        .status-green {
            background-color: #dcfce7;
            color: #166534;
        }
        .btn-sm {
            padding: 6px 12px;
            font-size: 12px;
            font-weight: 600;
            border-radius: 10px;
        }
        .empty-state {
            padding: 28px 16px;
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
@php
    $totalPendaftar = $pendaftars->count();
    $totalDiterima = $pendaftars->where('status_siswa', 'Diterima')->count();
    $totalBelumDaftarUlang = $pendaftars->where('status_siswa', '!=', 'Diterima')->count();
    $totalDataAwal = $pendaftars->where('status_data', 'awal')->count();
@endphp

<div class="page-header-card d-flex justify-content-between align-items-center gap-3 flex-wrap">
                <div>
                    <h2 class="page-title">Data Pendaftar</h2>
                    <p class="page-subtitle">Kelola daftar calon siswa, status pendaftaran, dan akses cepat ke dokumen penting.</p>
                </div>
                <a href="{{ route('pendaftar.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i> Tambah Pendaftar
                </a>
            </div>

            <div class="summary-grid">
                <div class="summary-card is-neutral">
                    <div class="summary-label">Total Pendaftar</div>
                    <div class="summary-value">{{ $totalPendaftar }}</div>
                    <div class="summary-subtext">Data pada halaman ini</div>
                </div>
                <div class="summary-card is-danger">
                    <div class="summary-label">Belum Daftar Ulang</div>
                    <div class="summary-value">{{ $totalBelumDaftarUlang }}</div>
                    <div class="summary-subtext">Perlu ditindaklanjuti</div>
                </div>
                <div class="summary-card is-success">
                    <div class="summary-label">Diterima</div>
                    <div class="summary-value">{{ $totalDiterima }}</div>
                    <div class="summary-subtext">Sudah diverifikasi</div>
                </div>
                <div class="summary-card is-info">
                    <div class="summary-label">Data Awal</div>
                    <div class="summary-value">{{ $totalDataAwal }}</div>
                    <div class="summary-subtext">Perlu biodata lengkap</div>
                </div>
            </div>

            @if (Session::has('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i> {{ Session::get('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="data-panel">
                <div class="data-panel-head">
                    <div>
                        <h3 class="data-panel-title">Daftar Pendaftar</h3>
                        <div class="data-panel-subtitle">Gunakan aksi cepat untuk edit biodata, cetak bukti, atau lanjut ke formulir lengkap.</div>
                    </div>
                </div>
                <div class="table-shell">
                <div class="table-responsive">
                    <table class="table table-hover" id="pendaftarTable">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 80px;">No. Registrasi</th>
                                <th>Nama Lengkap</th>
                                <th style="width: 100px;">Jurusan</th>
                                <th style="width: 100px;">Gelombang</th>
                                <th style="width: 120px;">Status</th>
                                <th style="width: 120px;">Keterangan</th>
                                <th style="width: 80px;">Ukuran Kaos</th>
                                <th style="width: 220px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($pendaftars as $p)
                                @php
                                    $logistik = $p->logistik;
                                    if ($p->status_siswa === 'Diterima') {
                                        $statusClass = 'status-green';
                                        $statusText = 'Diterima';
                                    } else {
                                        $statusClass = 'status-red';
                                        $statusText = 'Belum Daftar Ulang';
                                    }
                                @endphp
                                <tr>
                                    <td><span class="reg-code">{{ $p->no_registrasi }}</span></td>
                                    <td>
                                        <div class="fw-semibold text-dark">{{ $p->nama_lengkap }}</div>
                                        <div class="small text-muted">Status data: {{ ucfirst($p->status_data ?? 'awal') }}</div>
                                    </td>
                                    <td><span class="jurusan-pill">{{ $p->jurusan }}</span></td>
                                    <td>{{ $p->gelombang }}</td>
                                    <td>
                                        <span class="status-badge {{ $statusClass }}">
                                            {{ $statusText }}
                                        </span>
                                    </td>
                                    <td>
                                        @if ($logistik->status_bayar === 'Belum')
                                            <span class="text-muted">-</span>
                                        @else
                                            <span class="badge bg-success-subtle text-success border border-success-subtle" data-bs-toggle="tooltip" data-bs-placement="top" title="Kaos otomatis dipesankan setelah verifikasi daftar ulang.">Kaos dipesankan</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($logistik->ukuran_kaos)
                                            <span class="size-pill">{{ $logistik->ukuran_kaos }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2 flex-wrap">
                                            <a href="{{ route('pendaftar.edit', $p->id_pendaftar) }}" class="btn btn-sm btn-info" title="Edit Biodata">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <a href="{{ route('pendaftar.print.registrasi', $p->id_pendaftar) }}" target="_blank" class="btn btn-sm btn-primary" title="Cetak Bukti Registrasi">
                                                <i class="fas fa-id-card"></i> Bukti Daftar
                                            </a>
                                            <a href="{{ route('pendaftar.print.formulir', $p->id_pendaftar) }}" target="_blank" class="btn btn-sm btn-success" title="Cetak Formulir Lengkap">
                                                <i class="fas fa-file-lines"></i> Formulir
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted empty-state">
                                        <i class="fas fa-inbox" style="font-size: 24px;"></i>
                                        <p class="mt-2 mb-0">Belum ada data pendaftar</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                </div>
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $pendaftars->links() }}
            </div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
        $(document).ready(function () {
            $('#pendaftarTable').DataTable({
                language: {
                    search: 'Cari:',
                    lengthMenu: 'Tampilkan _MENU_ data',
                    info: 'Menampilkan _START_ - _END_ dari _TOTAL_ data',
                    paginate: { previous: 'Sebelumnya', next: 'Berikutnya' },
                    zeroRecords: 'Tidak ada data yang ditemukan',
                    emptyTable: 'Belum ada data pendaftar'
                },
                columnDefs: [{ orderable: false, targets: -1 }]
            });

            @if (Session::has('created_pendaftar_id'))
                Swal.fire({
                    icon: 'success',
                    title: 'Data sudah dibuat',
                    html: 'Pendaftar <strong>{{ addslashes(Session::get('created_pendaftar_name')) }}</strong><br>No. Registrasi: <strong>{{ Session::get('created_pendaftar_no') }}</strong><br><br>Apakah ingin melengkapi biodata sekarang?',
                    showCancelButton: true,
                    confirmButtonText: 'Edit Data Lengkap',
                    cancelButtonText: 'Tidak, nanti saja',
                    confirmButtonColor: 'var(--theme-primary)',
                    cancelButtonColor: '#64748b'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = '{{ route('pendaftar.edit', Session::get('created_pendaftar_id')) }}';
                    }
                });
            @elseif (Session::has('rollback_success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Rollback Berhasil',
                    html: '{!! addslashes(Session::get('success')) !!}',
                    confirmButtonText: 'OK',
                    confirmButtonColor: 'var(--theme-primary)'
                });
            @endif

            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
@endpush
