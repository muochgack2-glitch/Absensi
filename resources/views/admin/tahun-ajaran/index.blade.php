@extends('layouts.admin')

@section('title', 'Manajemen Tahun Pelajaran')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0" style="color: var(--text-primary);">📅 Manajemen Tahun Pelajaran</h1>
            <p class="small mb-0" style="color: var(--text-secondary);">Kelola tahun pelajaran dan nomor registrasi otomatis</p>
        </div>
        <div>
            <a href="{{ route('admin.tahun-ajaran.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Buat Tahun Pelajaran Baru
            </a>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>
        <strong>Berhasil!</strong> {{ session('success') }}
        @if(session('backup_path'))
        <br><small style="color: var(--text-secondary);">Backup disimpan di: {{ session('backup_path') }}</small>
        @endif
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>
        <strong>Gagal!</strong> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Active Year Info -->
    @if($activeTA)
    <div class="alert alert-info" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none;">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-1" style="color: white;"><i class="fas fa-calendar-check me-2"></i>Tahun Pelajaran Aktif</h5>
                <h3 class="mb-0" style="color: white; font-weight: bold;">{{ $activeTA->tahun }}</h3>
                <small style="color: white; opacity: 0.9;">Nomor registrasi berikutnya: SPMB-{{ $activeTA->getYearNumber() }}-{{ str_pad($activeTA->reg_number_current + 1, 4, '0', STR_PAD_LEFT) }}</small>
            </div>
            <div class="text-end">
                <h6 style="color: white; opacity: 0.9;" class="mb-1">Total Pendaftar</h6>
                <h2 style="color: white; font-weight: bold;" class="mb-0">{{ number_format($activeTA->total_pendaftar) }}</h2>
            </div>
        </div>
    </div>
    @endif

    <!-- Tahun Ajaran List -->
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Daftar Tahun Pelajaran</h5>
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="syncAllStatistics()">
                    <i class="fas fa-sync"></i> Sinkronkan Semua Statistik
                </button>
            </div>
        </div>
        <div class="card-body p-0">
            @if($tahunAjarans->isEmpty())
            <div class="text-center py-5">
                <i class="fas fa-calendar-times fa-3x mb-3" style="color: var(--text-secondary);"></i>
                <p style="color: var(--text-secondary);">Belum ada tahun pelajaran</p>
                <a href="{{ route('admin.tahun-ajaran.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Buat Tahun Pelajaran Pertama
                </a>
            </div>
            @else
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead style="background: var(--bg-secondary); border-bottom: 2px solid var(--border-color);">
                        <tr>
                            <th style="color: var(--text-primary);">Tahun Pelajaran</th>
                            <th>Status</th>
                            <th>Counter</th>
                            <th>Total Pendaftar</th>
                            <th>Diterima</th>
                            <th>Belum Daftar Ulang</th>
                            <th>Periode</th>
                            <th>Dibuat Oleh</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tahunAjarans as $ta)
                        <tr class="{{ $ta->isActive() ? 'table-success' : '' }}">
                            <td>
                                <strong>{{ $ta->tahun }}</strong>
                                @if($ta->isActive())
                                <span class="badge bg-success ms-2">Aktif</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $ta->getStatusBadgeColor() }}">
                                    {{ $ta->getStatusLabel() }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-primary">
                                    {{ $ta->reg_number_current }}
                                </span>
                                <small style="color: var(--text-secondary);" class="d-block">
                                    Next: SPMB-{{ $ta->getYearNumber() }}-{{ str_pad($ta->reg_number_current + 1, 4, '0', STR_PAD_LEFT) }}
                                </small>
                            </td>
                            <td><strong>{{ number_format($ta->total_pendaftar) }}</strong></td>
                            <td><span class="text-success">{{ number_format($ta->total_diterima) }}</span></td>
                            <td><span class="text-warning">{{ number_format($ta->total_belum_daftar_ulang ?? 0) }}</span></td>
                            <td>
                                <small style="color: var(--text-secondary);">
                                    {{ $ta->started_at ? $ta->started_at->format('d M Y') : '-' }}
                                    @if($ta->closed_at)
                                    <br>s/d {{ $ta->closed_at->format('d M Y') }}
                                    @endif
                                </small>
                            </td>
                            <td>
                                <small style="color: var(--text-secondary);">
                                    {{ $ta->creator->name ?? 'System' }}
                                    <br>{{ $ta->created_at->format('d M Y') }}
                                </small>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.tahun-ajaran.show', $ta->id) }}" 
                                       class="btn btn-outline-info" 
                                       title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    @if(!$ta->isActive())
                                    <button type="button" 
                                            class="btn btn-outline-success" 
                                            onclick="activateTahunAjaran({{ $ta->id }}, '{{ $ta->tahun }}')"
                                            title="Aktifkan">
                                        <i class="fas fa-check-circle"></i>
                                    </button>
                                    @endif
                                    
                                    @if($ta->status === 'upcoming')
                                    <button type="button" 
                                            class="btn btn-outline-secondary" 
                                            onclick="archiveTahunAjaran({{ $ta->id }}, '{{ $ta->tahun }}')"
                                            title="Arsipkan">
                                        <i class="fas fa-archive"></i>
                                    </button>
                                    @endif
                                    
                                    <button type="button" 
                                            class="btn btn-outline-primary" 
                                            onclick="updateStatistics({{ $ta->id }})"
                                            title="Update Statistik">
                                        <i class="fas fa-sync"></i>
                                    </button>
                                    
                                    @if(!$ta->isActive())
                                    <button type="button" 
                                            class="btn btn-outline-danger" 
                                            onclick="deleteTahunAjaran({{ $ta->id }}, '{{ $ta->tahun }}', {{ $ta->total_pendaftar }})"
                                            title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal: Activate Confirmation -->
<div class="modal fade" id="activateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title"><i class="fas fa-exclamation-triangle me-2"></i>Konfirmasi Aktivasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Anda akan mengaktifkan tahun pelajaran <strong id="activateTahun"></strong>.</p>
                <div class="alert alert-warning">
                    <i class="fas fa-info-circle me-2"></i>
                    Tahun pelajaran yang sedang aktif akan diarsipkan secara otomatis.
                </div>
                <p class="mb-0">Lanjutkan?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="activateForm" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-warning">Ya, Aktifkan</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Archive Confirmation -->
<div class="modal fade" id="archiveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-secondary text-white">
                <h5 class="modal-title"><i class="fas fa-archive me-2"></i>Konfirmasi Arsip</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Anda akan mengarsipkan tahun pelajaran <strong id="archiveTahun"></strong>.</p>
                <p class="mb-0">Lanjutkan?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="archiveForm" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-secondary">Ya, Arsipkan</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Delete Confirmation -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fas fa-trash me-2"></i>Konfirmasi Hapus</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>PERHATIAN!</strong> Tindakan ini tidak dapat dibatalkan.
                </div>
                <p>Anda akan <strong>menghapus permanen</strong> tahun pelajaran <strong id="deleteTahun"></strong>.</p>
                <div id="deleteWarning" class="alert alert-warning" style="display: none;">
                    <i class="fas fa-users me-2"></i>
                    Tahun ini memiliki <strong id="deletePendaftarCount"></strong> pendaftar yang akan ikut terhapus.
                </div>
                <p class="mb-0 text-danger"><strong>Apakah Anda yakin ingin melanjutkan?</strong></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="deleteForm" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-1"></i> Ya, Hapus Permanen
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function activateTahunAjaran(id, tahun) {
    document.getElementById('activateTahun').textContent = tahun;
    document.getElementById('activateForm').action = `/admin/tahun-ajaran/${id}/activate`;
    new bootstrap.Modal(document.getElementById('activateModal')).show();
}

function archiveTahunAjaran(id, tahun) {
    document.getElementById('archiveTahun').textContent = tahun;
    document.getElementById('archiveForm').action = `/admin/tahun-ajaran/${id}/archive`;
    new bootstrap.Modal(document.getElementById('archiveModal')).show();
}

function deleteTahunAjaran(id, tahun, totalPendaftar) {
    document.getElementById('deleteTahun').textContent = tahun;
    document.getElementById('deleteForm').action = `/admin/tahun-ajaran/${id}`;
    
    const warningDiv = document.getElementById('deleteWarning');
    const countSpan = document.getElementById('deletePendaftarCount');
    
    if (totalPendaftar > 0) {
        countSpan.textContent = totalPendaftar;
        warningDiv.style.display = 'block';
    } else {
        warningDiv.style.display = 'none';
    }
    
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

function updateStatistics(id) {
    fetch(`/admin/tahun-ajaran/${id}/update-statistics`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('✅ Statistik berhasil diperbarui!');
            window.location.reload();
        } else {
            alert('❌ Gagal memperbarui statistik: ' + data.message);
        }
    })
    .catch(error => {
        alert('❌ Terjadi kesalahan: ' + error.message);
    });
}

function syncAllStatistics() {
    if (!confirm('Sinkronkan statistik untuk semua tahun ajaran?')) return;
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route('admin.tahun-ajaran.sync-all') }}';
    
    const csrf = document.createElement('input');
    csrf.type = 'hidden';
    csrf.name = '_token';
    csrf.value = '{{ csrf_token() }}';
    form.appendChild(csrf);
    
    document.body.appendChild(form);
    form.submit();
}
</script>
@endpush
