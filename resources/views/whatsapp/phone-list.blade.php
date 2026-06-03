@extends('layouts.admin')

@section('title', 'Rekap Nomor HP Pendaftar')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">📱 Rekap Nomor HP Pendaftar</h1>
            <p class="text-muted mb-0">Daftar nomor HP pendaftar untuk broadcast WhatsApp</p>
        </div>
        <div>
            <button class="btn btn-success" onclick="sendBroadcast()">
                <i class="fas fa-paper-plane me-2"></i>Kirim Broadcast
            </button>
            <button class="btn btn-primary" onclick="exportPhones()">
                <i class="fas fa-download me-2"></i>Export
            </button>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('whatsapp.phone-list') }}" id="filterForm">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Jurusan</label>
                        <select name="jurusan_id" class="form-select" onchange="this.form.submit()">
                            <option value="">Semua Jurusan</option>
                            @foreach($jurusans as $jurusan)
                            <option value="{{ $jurusan->id }}" {{ request('jurusan_id') == $jurusan->id ? 'selected' : '' }}>
                                {{ $jurusan->nama_jurusan }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Gelombang</label>
                        <select name="gelombang" class="form-select" onchange="this.form.submit()">
                            <option value="">Semua Gelombang</option>
                            @foreach($gelombangs as $gelombang)
                            <option value="{{ $gelombang }}" {{ request('gelombang') == $gelombang ? 'selected' : '' }}>
                                {{ $gelombang }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Status Siswa</label>
                        <select name="status_siswa" class="form-select" onchange="this.form.submit()">
                            <option value="">Semua Status</option>
                            <option value="Belum Daftar Ulang" {{ request('status_siswa') == 'Belum Daftar Ulang' ? 'selected' : '' }}>Belum Daftar Ulang</option>
                            <option value="Sudah Daftar Ulang" {{ request('status_siswa') == 'Sudah Daftar Ulang' ? 'selected' : '' }}>Sudah Daftar Ulang</option>
                            <option value="Diterima" {{ request('status_siswa') == 'Diterima' ? 'selected' : '' }}>Diterima</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tipe Nomor</label>
                        <select name="phone_type" class="form-select" onchange="this.form.submit()">
                            <option value="all" {{ request('phone_type', 'all') == 'all' ? 'selected' : '' }}>Semua (Prioritas)</option>
                            <option value="wali" {{ request('phone_type') == 'wali' ? 'selected' : '' }}>Wali Saja</option>
                            <option value="ortu" {{ request('phone_type') == 'ortu' ? 'selected' : '' }}>Orang Tua Saja</option>
                            <option value="siswa" {{ request('phone_type') == 'siswa' ? 'selected' : '' }}>Siswa Saja</option>
                        </select>
                    </div>
                </div>
                <div class="row g-3 mt-2">
                    <div class="col-md-6">
                        <label class="form-label">Cari Nama/NISN</label>
                        <input type="text" name="search" class="form-control" placeholder="Cari nama atau NISN..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-2"></i>Filter
                            </button>
                            <a href="{{ route('whatsapp.phone-list') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-redo me-2"></i>Reset
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="rounded-circle bg-primary bg-opacity-10 p-3">
                                <i class="fas fa-users fa-2x text-primary"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Total Pendaftar</h6>
                            <h3 class="mb-0">{{ $statistics['total_pendaftar'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="rounded-circle bg-success bg-opacity-10 p-3">
                                <i class="fas fa-phone fa-2x text-success"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Punya Nomor HP</h6>
                            <h3 class="mb-0">{{ $statistics['with_phone'] }}</h3>
                            <small class="text-success">{{ $statistics['phone_percentage'] }}%</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="rounded-circle bg-warning bg-opacity-10 p-3">
                                <i class="fas fa-phone-slash fa-2x text-warning"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Tanpa Nomor HP</h6>
                            <h3 class="mb-0">{{ $statistics['without_phone'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="rounded-circle bg-info bg-opacity-10 p-3">
                                <i class="fas fa-filter fa-2x text-info"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Hasil Filter</h6>
                            <h3 class="mb-0">{{ $pendaftars->total() }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Phone List Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-list me-2"></i>Daftar Nomor HP
                </h5>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="selectAll" onchange="toggleSelectAll(this)">
                    <label class="form-check-label" for="selectAll">
                        Pilih Semua (<span id="selectedCount">0</span>)
                    </label>
                </div>
            </div>
        </div>
        <div class="card-body p-0" style="background: var(--bg-primary);">
            @if($pendaftars->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="50">
                                <input type="checkbox" class="form-check-input" disabled>
                            </th>
                            <th>No. Pendaftaran</th>
                            <th>Nama Lengkap</th>
                            <th>NISN</th>
                            <th>Jurusan</th>
                            <th>Gelombang</th>
                            <th>Nomor HP</th>
                            <th>Tipe</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendaftars as $pendaftar)
                        @php
                            $phoneData = $pendaftar->phone_data;
                        @endphp
                        <tr>
                            <td>
                                @if($phoneData['phone'])
                                <input type="checkbox" class="form-check-input phone-checkbox" 
                                       value="{{ $phoneData['phone'] }}"
                                       data-id="{{ $pendaftar->id_pendaftar }}"
                                       data-name="{{ $pendaftar->nama_lengkap }}"
                                       data-no-reg="{{ $pendaftar->no_registrasi }}"
                                       data-jurusan="{{ $pendaftar->masterJurusan->nama_jurusan ?? $pendaftar->jurusan }}"
                                       onchange="updateSelectedCount()">
                                @else
                                <i class="fas fa-minus text-muted"></i>
                                @endif
                            </td>
                            <td>
                                <strong style="color: var(--text-primary);">{{ $pendaftar->no_registrasi }}</strong>
                            </td>
                            <td style="color: var(--text-primary);">{{ $pendaftar->nama_lengkap }}</td>
                            <td style="color: var(--text-primary);">{{ $pendaftar->nisn }}</td>
                            <td>
                                <span class="badge bg-primary">
                                    {{ $pendaftar->masterJurusan->nama_jurusan ?? $pendaftar->jurusan }}
                                </span>
                            </td>
                            <td>{{ $pendaftar->gelombang }}</td>
                            <td>
                                @if($phoneData['phone'])
                                    <a href="https://wa.me/{{ $phoneData['phone'] }}" target="_blank" class="text-decoration-none">
                                        <i class="fab fa-whatsapp text-success me-1"></i>
                                        {{ $phoneData['formatted'] }}
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($phoneData['type'])
                                    <span class="badge bg-secondary">{{ $phoneData['type'] }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $pendaftar->status_siswa == 'Diterima' ? 'success' : ($pendaftar->status_siswa == 'Sudah Daftar Ulang' ? 'info' : 'warning') }}">
                                    {{ $pendaftar->status_siswa }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="p-3">
                <x-custom-pagination :paginator="$pendaftars" :showPerPage="true" />
            </div>
            @else
            <div class="text-center py-5">
                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                <p class="text-muted">Tidak ada data pendaftar dengan filter yang dipilih</p>
                <a href="{{ route('whatsapp.phone-list') }}" class="btn btn-outline-primary">
                    <i class="fas fa-redo me-2"></i>Reset Filter
                </a>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Broadcast Modal -->
<div class="modal fade" id="broadcastModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-paper-plane me-2"></i>Kirim Broadcast WhatsApp
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Penerima:</strong> <span id="recipientCount">0</span> nomor HP terpilih
                </div>

                <div class="mb-3">
                    <label class="form-label">Pilih Template (Opsional)</label>
                    <select class="form-select" id="broadcastTemplate" onchange="loadTemplate()">
                        <option value="">-- Tulis Manual --</option>
                        @foreach($templates as $template)
                        <option value="{{ $template->id }}" 
                                data-message="{{ $template->message }}"
                                data-variables="{{ json_encode($template->getAvailableVariables()) }}">
                            {{ $template->label }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div id="templateVariables" style="display: none;">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Template ini menggunakan variabel. Variabel akan diganti otomatis untuk setiap penerima.
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Pesan <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="broadcastMessage" rows="8" placeholder="Ketik pesan Anda di sini..." required></textarea>
                    <small class="text-muted">Karakter: <span id="broadcastCharCount">0</span></small>
                </div>

                <div class="mb-3">
                    <label class="form-label">Preview Pesan</label>
                    <div class="border rounded p-3 bg-light" id="broadcastPreview" style="white-space: pre-wrap; min-height: 100px;">
                        <span class="text-muted">Preview akan muncul di sini...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success" onclick="submitBroadcast()">
                    <i class="fas fa-paper-plane me-2"></i>Kirim Broadcast
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let selectedPhones = [];

// Toggle select all
function toggleSelectAll(checkbox) {
    const checkboxes = document.querySelectorAll('.phone-checkbox');
    checkboxes.forEach(cb => {
        cb.checked = checkbox.checked;
    });
    updateSelectedCount();
}

// Update selected count
function updateSelectedCount() {
    const checkboxes = document.querySelectorAll('.phone-checkbox:checked');
    selectedPhones = Array.from(checkboxes).map(cb => ({
        phone: cb.value,
        id: cb.dataset.id,
        name: cb.dataset.name,
        no_reg: cb.dataset.noReg,
        jurusan: cb.dataset.jurusan
    }));
    
    document.getElementById('selectedCount').textContent = selectedPhones.length;
    document.getElementById('selectAll').checked = checkboxes.length > 0 && checkboxes.length === document.querySelectorAll('.phone-checkbox').length;
}

// Character counter
document.getElementById('broadcastMessage')?.addEventListener('input', function() {
    document.getElementById('broadcastCharCount').textContent = this.value.length;
    updatePreview();
});

// Load template
function loadTemplate() {
    const select = document.getElementById('broadcastTemplate');
    const option = select.options[select.selectedIndex];
    const message = option.dataset.message;
    const variables = JSON.parse(option.dataset.variables || '[]');
    
    if (message) {
        document.getElementById('broadcastMessage').value = message;
        document.getElementById('broadcastCharCount').textContent = message.length;
        
        if (variables.length > 0) {
            document.getElementById('templateVariables').style.display = 'block';
        } else {
            document.getElementById('templateVariables').style.display = 'none';
        }
        
        updatePreview();
    } else {
        document.getElementById('broadcastMessage').value = '';
        document.getElementById('broadcastCharCount').textContent = '0';
        document.getElementById('templateVariables').style.display = 'none';
        updatePreview();
    }
}

// Update preview
function updatePreview() {
    const message = document.getElementById('broadcastMessage').value;
    const preview = document.getElementById('broadcastPreview');
    
    if (message) {
        // Replace common variables with example
        let previewText = message
            .replace(/{nama}/g, '[Nama Pendaftar]')
            .replace(/{no_pendaftaran}/g, '[No. Pendaftaran]')
            .replace(/{jurusan}/g, '[Jurusan]')
            .replace(/{gelombang}/g, '[Gelombang]')
            .replace(/{portal_url}/g, '{{ url("/") }}')
            .replace(/{sekolah}/g, '{{ config("app.name") }}')
            .replace(/{tanggal}/g, '[Tanggal]');
        
        preview.textContent = previewText;
    } else {
        preview.innerHTML = '<span class="text-muted">Preview akan muncul di sini...</span>';
    }
}

// Send broadcast
function sendBroadcast() {
    if (selectedPhones.length === 0) {
        alert('Pilih minimal 1 nomor HP untuk broadcast');
        return;
    }
    
    document.getElementById('recipientCount').textContent = selectedPhones.length;
    const modal = new bootstrap.Modal(document.getElementById('broadcastModal'));
    modal.show();
}

// Submit broadcast
function submitBroadcast() {
    const message = document.getElementById('broadcastMessage').value;
    const templateId = document.getElementById('broadcastTemplate').value;
    
    if (!message) {
        alert('Pesan tidak boleh kosong');
        return;
    }
    
    if (selectedPhones.length === 0) {
        alert('Tidak ada nomor HP yang dipilih');
        return;
    }
    
    if (!confirm(`Kirim broadcast ke ${selectedPhones.length} nomor HP?`)) {
        return;
    }
    
    // Prepare data
    const data = {
        phones: selectedPhones,
        message: message,
        template_id: templateId || null,
        _token: '{{ csrf_token() }}'
    };
    
    // Show loading
    const btn = event.target;
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Mengirim...';
    
    // Send request
    fetch('{{ route("whatsapp.broadcast.send-bulk") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(`Broadcast berhasil!\n\nTerkirim: ${data.success_count}\nGagal: ${data.failed_count}`);
            bootstrap.Modal.getInstance(document.getElementById('broadcastModal')).hide();
            
            // Reset form
            document.getElementById('broadcastMessage').value = '';
            document.getElementById('broadcastTemplate').value = '';
            document.querySelectorAll('.phone-checkbox').forEach(cb => cb.checked = false);
            updateSelectedCount();
        } else {
            alert('Gagal mengirim broadcast: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan: ' + error.message);
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = originalText;
    });
}

// Export phones
function exportPhones() {
    const checkboxes = document.querySelectorAll('.phone-checkbox:checked');
    
    if (checkboxes.length === 0) {
        alert('Pilih minimal 1 nomor HP untuk export');
        return;
    }
    
    const phones = Array.from(checkboxes).map(cb => ({
        no_registrasi: cb.dataset.noReg,
        nama: cb.dataset.name,
        jurusan: cb.dataset.jurusan,
        phone: cb.value
    }));
    
    // Create CSV
    let csv = 'No. Pendaftaran,Nama,Jurusan,Nomor HP\n';
    phones.forEach(p => {
        csv += `"${p.no_registrasi}","${p.nama}","${p.jurusan}","${p.phone}"\n`;
    });
    
    // Download
    const blob = new Blob([csv], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'nomor-hp-pendaftar-' + new Date().toISOString().split('T')[0] + '.csv';
    a.click();
    window.URL.revokeObjectURL(url);
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    updateSelectedCount();
});
</script>
@endpush
@endsection
