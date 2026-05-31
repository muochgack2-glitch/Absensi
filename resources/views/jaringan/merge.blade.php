@extends('layouts.admin')

@section('title', 'Merge Jaringan')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">🔀 Merge Jaringan - Mode Full</h1>
            <p class="text-muted mb-0">Gabungkan semua siswa dari satu jaringan ke jaringan lain</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('jaringan.merge-selective') }}" class="btn btn-outline-primary">
                <i class="fas fa-check-square me-2"></i>Mode Selective
            </a>
            <a href="{{ route('jaringan.history') }}" class="btn btn-outline-secondary">
                <i class="fas fa-history me-2"></i>History
            </a>
        </div>
    </div>

    <!-- Alert Messages -->
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

    <!-- Info Card -->
    <div class="alert alert-info mb-4">
        <div class="d-flex">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle fa-2x"></i>
            </div>
            <div class="flex-grow-1 ms-3">
                <h6 class="alert-heading mb-2">Cara Kerja Merge Jaringan:</h6>
                <ol class="mb-0 ps-3">
                    <li>Pilih jaringan <strong>SUMBER</strong> (FROM) - jaringan yang akan digabungkan</li>
                    <li>Pilih jaringan <strong>TUJUAN</strong> (TO) - jaringan hasil penggabungan</li>
                    <li>Klik <strong>Preview</strong> untuk melihat data yang akan terpengaruh</li>
                    <li>Konfirmasi dan <strong>Proses Merge</strong></li>
                    <li>Semua pendaftar dari jaringan sumber akan dipindahkan ke jaringan tujuan</li>
                </ol>
            </div>
        </div>
    </div>

    <!-- Merge Form -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent border-0 pt-4 pb-3">
            <h5 class="mb-0">Pilih Jaringan untuk Digabungkan</h5>
            <p class="text-muted small mb-0 mt-1">Pilih jaringan sumber (FROM) dan jaringan tujuan (TO)</p>
        </div>
        <div class="card-body">
            <form id="mergeForm">
                @csrf
                
                <div class="row g-4">
                    <!-- FROM: Jaringan Sumber -->
                    <div class="col-md-6">
                        <label class="form-label fw-medium">
                            <i class="fas fa-arrow-right text-danger me-2"></i>
                            Dari Jaringan (Sumber)
                        </label>
                        <select name="from_jaringan" id="fromJaringan" class="form-select" required>
                            <option value="">-- Pilih Jaringan Sumber --</option>
                            @foreach($jaringans as $jaringan)
                            <option value="{{ $jaringan->nama_jaringan }}" data-count="{{ $jaringan->total }}">
                                {{ $jaringan->nama_jaringan }} ({{ $jaringan->total }} pendaftar)
                            </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Jaringan ini akan digabungkan ke jaringan tujuan</small>
                    </div>
                    
                    <!-- TO: Jaringan Tujuan -->
                    <div class="col-md-6">
                        <label class="form-label fw-medium">
                            <i class="fas fa-arrow-left text-success me-2"></i>
                            Ke Jaringan (Tujuan)
                        </label>
                        <select name="to_jaringan" id="toJaringan" class="form-select" required>
                            <option value="">-- Pilih Jaringan Tujuan --</option>
                            @foreach($jaringans as $jaringan)
                            <option value="{{ $jaringan->nama_jaringan }}" data-count="{{ $jaringan->total }}">
                                {{ $jaringan->nama_jaringan }} ({{ $jaringan->total }} pendaftar)
                            </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Semua data akan digabungkan ke jaringan ini</small>
                    </div>
                </div>
                
                <!-- Visual Indicator -->
                <div class="text-center my-4" id="mergeIndicator" style="display: none;">
                    <div class="d-flex align-items-center justify-content-center gap-3 flex-wrap">
                        <div class="badge bg-danger fs-6 py-2 px-3" id="fromBadge"></div>
                        <i class="fas fa-arrow-right fa-2x text-primary"></i>
                        <div class="badge bg-success fs-6 py-2 px-3" id="toBadge"></div>
                    </div>
                    <p class="text-muted small mt-2 mb-0">
                        <span id="affectedInfo"></span>
                    </p>
                </div>
                
                <div class="d-flex gap-2 justify-content-end mt-4">
                    <button type="button" class="btn btn-secondary" onclick="resetForm()">
                        <i class="fas fa-redo me-2"></i>Reset
                    </button>
                    <button type="button" class="btn btn-primary" onclick="previewMerge()" id="previewBtn" disabled>
                        <i class="fas fa-eye me-2"></i>Preview Merge
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Auto-Detect Duplicates -->
    @if(count($suggestions) > 0)
    <div class="card border-0 shadow-sm mt-4">
        <div class="card-header bg-transparent border-0 pt-4 pb-3">
            <h5 class="mb-0">
                <i class="fas fa-lightbulb text-warning me-2"></i>
                Saran Jaringan yang Mirip
            </h5>
            <p class="text-muted small mb-0 mt-1">Sistem mendeteksi jaringan-jaringan berikut yang mungkin duplikat</p>
        </div>
        <div class="card-body">
            @foreach($suggestions as $index => $group)
            <div class="alert alert-warning mb-3">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="mb-2">Grup {{ $index + 1 }}:</h6>
                        <ul class="mb-0">
                            @foreach($group as $item)
                            <li>{{ $item->nama_jaringan }} <span class="badge bg-secondary">{{ $item->total }} pendaftar</span></li>
                            @endforeach
                        </ul>
                    </div>
                    <button type="button" class="btn btn-sm btn-warning" onclick="applySuggestion({{ json_encode($group) }})">
                        <i class="fas fa-magic me-1"></i>Gunakan
                    </button>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

<!-- Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title" id="previewModalLabel">
                    <i class="fas fa-eye text-primary me-2"></i>Preview Merge Jaringan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning mb-3">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Perhatian!</strong> Merge bersifat permanen dan akan mengubah data di database.
                </div>
                
                <div class="mb-3">
                    <h6 class="mb-2">Detail Merge:</h6>
                    <div class="bg-light p-3 rounded">
                        <div class="row mb-2">
                            <div class="col-4 text-muted">Dari (Sumber):</div>
                            <div class="col-8 fw-medium" id="previewFrom"></div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-4 text-muted">Ke (Tujuan):</div>
                            <div class="col-8 fw-medium" id="previewTo"></div>
                        </div>
                        <div class="row">
                            <div class="col-4 text-muted">Jumlah Data:</div>
                            <div class="col-8 fw-medium text-danger" id="previewCount"></div>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <h6 class="mb-2">Sample Pendaftar yang Terpengaruh:</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>No. Registrasi</th>
                                    <th>Nama Lengkap</th>
                                    <th>Jaringan Lama</th>
                                </tr>
                            </thead>
                            <tbody id="previewSamples">
                            </tbody>
                        </table>
                    </div>
                    <small class="text-muted">Menampilkan maksimal 5 data sample</small>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" onclick="processMerge()">
                    <i class="fas fa-check me-2"></i>Ya, Proses Merge
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
let previewData = null;

// Toast notification function
function showToast(message, type = 'info') {
    const toastId = 'toast_' + Date.now();
    const toastHtml = `
        <div class="toast align-items-center text-white bg-${type} border-0" role="alert" aria-live="assertive" aria-atomic="true" id="${toastId}">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'danger' ? 'exclamation-circle' : type === 'warning' ? 'exclamation-triangle' : 'info-circle'} me-2"></i>
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    `;
    
    let toastContainer = document.getElementById('toastContainer');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.id = 'toastContainer';
        toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
        toastContainer.style.zIndex = '9999';
        document.body.appendChild(toastContainer);
    }
    
    toastContainer.insertAdjacentHTML('beforeend', toastHtml);
    
    const toastElement = document.getElementById(toastId);
    const toast = new bootstrap.Toast(toastElement, { 
        autohide: true,
        delay: 3000 
    });
    
    toastElement.addEventListener('hidden.bs.toast', function() {
        toastElement.remove();
    });
    
    toast.show();
}

// Enable/disable preview button
document.getElementById('fromJaringan').addEventListener('change', checkForm);
document.getElementById('toJaringan').addEventListener('change', checkForm);

function checkForm() {
    const from = document.getElementById('fromJaringan').value;
    const to = document.getElementById('toJaringan').value;
    const previewBtn = document.getElementById('previewBtn');
    const indicator = document.getElementById('mergeIndicator');
    
    if (from && to) {
        if (from === to) {
            showToast('Tidak bisa menggabungkan jaringan ke dirinya sendiri!', 'danger');
            document.getElementById('toJaringan').value = '';
            previewBtn.disabled = true;
            indicator.style.display = 'none';
            return;
        }
        
        previewBtn.disabled = false;
        
        // Show visual indicator
        const fromOption = document.querySelector(`#fromJaringan option[value="${from}"]`);
        const toOption = document.querySelector(`#toJaringan option[value="${to}"]`);
        const fromCount = fromOption.dataset.count;
        
        document.getElementById('fromBadge').textContent = from;
        document.getElementById('toBadge').textContent = to;
        document.getElementById('affectedInfo').textContent = `${fromCount} pendaftar akan dipindahkan`;
        indicator.style.display = 'block';
    } else {
        previewBtn.disabled = true;
        indicator.style.display = 'none';
    }
}

function resetForm() {
    document.getElementById('mergeForm').reset();
    document.getElementById('previewBtn').disabled = true;
    document.getElementById('mergeIndicator').style.display = 'none';
    showToast('Form direset', 'info');
}

function previewMerge() {
    const from = document.getElementById('fromJaringan').value;
    const to = document.getElementById('toJaringan').value;
    
    if (!from || !to) {
        showToast('Pilih jaringan sumber dan tujuan terlebih dahulu!', 'danger');
        return;
    }
    
    // Show loading
    const previewBtn = document.getElementById('previewBtn');
    const originalText = previewBtn.innerHTML;
    previewBtn.disabled = true;
    previewBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Loading...';
    
    fetch('{{ route("jaringan.preview") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            from_jaringan: from,
            to_jaringan: to
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            showToast(data.error, 'danger');
            return;
        }
        
        previewData = data;
        
        // Fill preview modal
        document.getElementById('previewFrom').textContent = data.from_jaringan;
        document.getElementById('previewTo').textContent = data.to_jaringan;
        document.getElementById('previewCount').textContent = data.affected_count + ' pendaftar';
        
        // Fill samples table
        const samplesBody = document.getElementById('previewSamples');
        samplesBody.innerHTML = '';
        
        if (data.samples && data.samples.length > 0) {
            data.samples.forEach(sample => {
                const row = `
                    <tr>
                        <td>${sample.no_registrasi}</td>
                        <td>${sample.nama_lengkap}</td>
                        <td><span class="badge bg-secondary">${sample.nama_jaringan}</span></td>
                    </tr>
                `;
                samplesBody.innerHTML += row;
            });
        } else {
            samplesBody.innerHTML = '<tr><td colspan="3" class="text-center text-muted">Tidak ada data</td></tr>';
        }
        
        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('previewModal'));
        modal.show();
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Terjadi kesalahan saat memuat preview', 'danger');
    })
    .finally(() => {
        previewBtn.disabled = false;
        previewBtn.innerHTML = originalText;
    });
}

function processMerge() {
    if (!previewData) {
        showToast('Data preview tidak tersedia!', 'danger');
        return;
    }
    
    // Show loading
    const processBtn = event.target;
    const originalText = processBtn.innerHTML;
    processBtn.disabled = true;
    processBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';
    
    fetch('{{ route("jaringan.process-merge") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            from_jaringan: previewData.from_jaringan,
            to_jaringan: previewData.to_jaringan
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Close modal
            bootstrap.Modal.getInstance(document.getElementById('previewModal')).hide();
            
            // Show success toast
            showToast(data.message, 'success');
            
            // Reload page after 1 second
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            showToast(data.message || 'Gagal memproses merge', 'danger');
            processBtn.disabled = false;
            processBtn.innerHTML = originalText;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Terjadi kesalahan saat memproses merge', 'danger');
        processBtn.disabled = false;
        processBtn.innerHTML = originalText;
    });
}

function applySuggestion(group) {
    if (group.length < 2) return;
    
    // Set first as FROM, second as TO
    document.getElementById('fromJaringan').value = group[0].nama_jaringan;
    document.getElementById('toJaringan').value = group[1].nama_jaringan;
    
    checkForm();
    
    // Scroll to form
    document.getElementById('mergeForm').scrollIntoView({ behavior: 'smooth', block: 'center' });
    
    showToast('Saran diterapkan! Silakan preview merge', 'success');
}

// Auto-hide alerts after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(alert => {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });
});
</script>
@endpush
