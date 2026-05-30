@extends('layouts.admin')

@section('title', 'Data Pendaftar - SPMB (Sistem Penerimaan Murid Baru)')

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
</style>
@endpush

@section('content')
@php
    $totalPendaftar = $pendaftars->count();
    $totalDiterima = $pendaftars->where('status_siswa', 'Diterima')->count();
    $totalBelumDaftarUlang = $pendaftars->where('status_siswa', '!=', 'Diterima')->count();
    $totalDataAwal = $pendaftars->where('status_data', 'awal')->count();
@endphp

<div class="dashboard-content">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-3">
        <div>
            <h2 class="mb-2">Data Pendaftar</h2>
            <p class="text-muted mb-0">Kelola daftar calon siswa, status pendaftaran, dan akses cepat ke dokumen penting.</p>
        </div>
        <x-button variant="primary" icon="fas fa-plus" href="{{ route('pendaftar.create') }}">
            Tambah Pendaftar
        </x-button>
    </div>

    <!-- Summary Stats -->
    <div class="row g-4 mb-4">
        <div class="col-md-3 col-sm-6">
            <x-stat-card 
                icon="fas fa-users" 
                label="Total Pendaftar" 
                value="{{ $totalPendaftar }}"
                color="blue"
                description="Data pada halaman ini"
            />
        </div>
        <div class="col-md-3 col-sm-6">
            <x-stat-card 
                icon="fas fa-hourglass-half" 
                label="Belum Daftar Ulang" 
                value="{{ $totalBelumDaftarUlang }}"
                color="red"
                description="Perlu ditindaklanjuti"
            />
        </div>
        <div class="col-md-3 col-sm-6">
            <x-stat-card 
                icon="fas fa-check-circle" 
                label="Diterima" 
                value="{{ $totalDiterima }}"
                color="green"
                description="Sudah diverifikasi"
            />
        </div>
        <div class="col-md-3 col-sm-6">
            <x-stat-card 
                icon="fas fa-clipboard-list" 
                label="Data Awal" 
                value="{{ $totalDataAwal }}"
                color="yellow"
                description="Perlu biodata lengkap"
            />
        </div>
    </div>

    @if (Session::has('success'))
        <x-alert type="success" dismissible="true">
            {{ Session::get('success') }}
        </x-alert>
    @endif

    <!-- Data Table -->
    <x-section-card title="Daftar Pendaftar" icon="fas fa-list">
        <x-slot:actions>
            <small class="text-muted">Gunakan aksi cepat untuk edit biodata, cetak bukti, atau lanjut ke formulir lengkap</small>
        </x-slot:actions>
                <div class="table-responsive">
                    <!-- Bulk Actions Bar -->
                    <div id="bulkActionsBar" style="display: none; padding: 1rem; background: #f8f9fa; border-radius: 0.5rem; margin-bottom: 1rem; border: 2px solid #dee2e6;">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <i class="fas fa-check-square text-primary me-2"></i>
                                <strong><span id="selectedCount">0</span> pendaftar dipilih</strong>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-sm btn-danger" onclick="bulkDelete()">
                                    <i class="fas fa-trash me-1"></i> Hapus Terpilih
                                </button>
                                <button type="button" class="btn btn-sm btn-secondary" onclick="clearSelection()">
                                    <i class="fas fa-times me-1"></i> Batal
                                </button>
                            </div>
                        </div>
                    </div>

                    <table class="table table-hover" id="pendaftarTable">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 40px;">
                                    <input type="checkbox" id="selectAll" title="Pilih Semua">
                                </th>
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
                                    <td>
                                        <input type="checkbox" class="select-item" value="{{ $p->id_pendaftar }}" onchange="updateBulkActionsBar()">
                                    </td>
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
                                        <x-table-actions align="start">
                                            <x-icon-button 
                                                icon="fas fa-edit" 
                                                variant="info" 
                                                size="sm"
                                                href="{{ route('pendaftar.edit', $p->id_pendaftar) }}"
                                                tooltip="Edit Biodata"
                                            />
                                            <x-icon-button 
                                                icon="fas fa-id-card" 
                                                variant="primary" 
                                                size="sm"
                                                href="{{ route('pendaftar.print.registrasi', $p->id_pendaftar) }}"
                                                target="_blank"
                                                tooltip="Cetak Bukti Registrasi"
                                            />
                                            <x-icon-button 
                                                icon="fas fa-file-lines" 
                                                variant="success" 
                                                size="sm"
                                                href="{{ route('pendaftar.print.formulir', $p->id_pendaftar) }}"
                                                target="_blank"
                                                tooltip="Cetak Formulir Lengkap"
                                            />
                                        </x-table-actions>
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
                columnDefs: [
                    { orderable: false, targets: [0, -1] } // Disable sorting for checkbox and action columns
                ]
            });

            @if (Session::has('created_pendaftar_id'))
                Modal.confirm(
                    'Pendaftar <strong>{{ addslashes(Session::get('created_pendaftar_name')) }}</strong><br>No. Registrasi: <strong>{{ Session::get('created_pendaftar_no') }}</strong><br><br>Apakah ingin melengkapi biodata sekarang?',
                    function() {
                        window.location.href = '{{ route('pendaftar.edit', Session::get('created_pendaftar_id')) }}';
                    },
                    {
                        title: 'Data Sudah Dibuat',
                        confirmText: 'Edit Data Lengkap',
                        cancelText: 'Tidak, nanti saja',
                        type: 'success'
                    }
                );
            @elseif (Session::has('rollback_success'))
                Modal.alert(
                    '{!! addslashes(Session::get('success')) !!}',
                    'Rollback Berhasil',
                    'success'
                );
            @elseif (Session::has('success'))
                Modal.alert(
                    '{{ addslashes(Session::get('success')) }}',
                    'Berhasil',
                    'success'
                );
            @endif

            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });

        // Select All functionality
        document.getElementById('selectAll').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.select-item');
            checkboxes.forEach(cb => cb.checked = this.checked);
            updateBulkActionsBar();
        });

        // Update bulk actions bar
        function updateBulkActionsBar() {
            const selected = document.querySelectorAll('.select-item:checked');
            const count = selected.length;
            const selectAll = document.getElementById('selectAll');
            const totalCheckboxes = document.querySelectorAll('.select-item').length;
            
            // Update "Select All" checkbox state
            if (count === 0) {
                selectAll.checked = false;
                selectAll.indeterminate = false;
            } else if (count === totalCheckboxes) {
                selectAll.checked = true;
                selectAll.indeterminate = false;
            } else {
                selectAll.checked = false;
                selectAll.indeterminate = true;
            }
            
            // Show/hide bulk actions bar
            if (count > 0) {
                document.getElementById('bulkActionsBar').style.display = 'block';
                document.getElementById('selectedCount').textContent = count;
            } else {
                document.getElementById('bulkActionsBar').style.display = 'none';
            }
        }

        // Clear selection
        function clearSelection() {
            document.querySelectorAll('.select-item').forEach(cb => cb.checked = false);
            document.getElementById('selectAll').checked = false;
            updateBulkActionsBar();
        }

        // Bulk delete
        function bulkDelete() {
            const selected = Array.from(document.querySelectorAll('.select-item:checked'))
                                  .map(cb => cb.value);
            
            if (selected.length === 0) {
                Modal.alert('Tidak ada pendaftar yang dipilih', 'Peringatan', 'warning');
                return;
            }
            
            Modal.confirm(
                `Yakin ingin menghapus <strong>${selected.length} pendaftar</strong>?<br><small class="text-muted">Data yang dihapus tidak dapat dikembalikan.</small>`,
                function() {
                    // Show loading
                    const bulkActionsBar = document.getElementById('bulkActionsBar');
                    bulkActionsBar.innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin me-2"></i>Menghapus...</div>';
                    
                    // Send request to server
                    fetch('{{ route('pendaftar.bulk-delete') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ ids: selected })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Modal.alert(
                                `Berhasil menghapus ${data.count} pendaftar`,
                                'Sukses',
                                'success'
                            );
                            setTimeout(() => location.reload(), 1500);
                        } else {
                            Modal.alert(data.message || 'Terjadi kesalahan', 'Error', 'danger');
                            location.reload();
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Modal.alert('Terjadi kesalahan saat menghapus data', 'Error', 'danger');
                        location.reload();
                    });
                },
                {
                    title: 'Konfirmasi Hapus',
                    confirmText: 'Ya, Hapus',
                    cancelText: 'Batal',
                    type: 'danger'
                }
            );
        }
    </script>
@endpush
