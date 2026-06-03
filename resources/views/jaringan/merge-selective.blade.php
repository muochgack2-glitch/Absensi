@extends('layouts.admin')

@section('title', 'Merge Selective - Pilih Siswa')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">✅ Merge Selective - Pilih Siswa</h1>
            <p class="text-muted mb-0">Pilih siswa tertentu untuk digabungkan (bisa dari jaringan berbeda)</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('jaringan.merge') }}" class="btn btn-outline-primary">
                <i class="fas fa-layer-group me-2"></i>Mode Full
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
                <h6 class="alert-heading mb-2">Cara Kerja Merge Selective:</h6>
                <ol class="mb-0 ps-3">
                    <li>Gunakan <strong>Search & Filter</strong> untuk menemukan siswa</li>
                    <li><strong>Centang siswa</strong> yang ingin digabungkan (bisa dari jaringan berbeda)</li>
                    <li>Gunakan <strong>Bulk Actions</strong> untuk centang cepat</li>
                    <li>Pilih <strong>Jaringan Tujuan</strong></li>
                    <li>Klik <strong>Preview</strong> untuk melihat detail sebelum merge</li>
                </ol>
            </div>
        </div>
    </div>

    <!-- Selected Counter (Floating Button) -->
    <div id="selectedCounter" class="floating-counter" style="display: none;">
        <div class="floating-counter-content">
            <div class="floating-counter-badge">
                <span id="selectedCount">0</span>
            </div>
            <div class="floating-counter-text">siswa dipilih</div>
            <div class="floating-counter-actions mt-2">
                <button type="button" class="btn btn-sm btn-light w-100 mb-2" onclick="showMergeForm()" id="mergeBtn">
                    <i class="fas fa-arrow-right me-1"></i>Merge
                </button>
                <button type="button" class="btn btn-sm btn-outline-light w-100" onclick="clearSelection()">
                    <i class="fas fa-times me-1"></i>Clear
                </button>
            </div>
        </div>
    </div>

    <!-- Search & Filter -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('jaringan.merge-selective') }}" id="filterForm">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label small text-muted">Search</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                            <input type="text" class="form-control border-start-0 ps-0" name="search" placeholder="No. Registrasi, Nama, Jaringan..." value="{{ $search }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small text-muted">Filter Jaringan</label>
                        <select name="jaringan" class="form-select">
                            <option value="">Semua Jaringan</option>
                            @foreach($jaringans as $jar)
                            <option value="{{ $jar }}" {{ $jaringanFilter == $jar ? 'selected' : '' }}>{{ $jar }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small text-muted">Sort By</label>
                        <select name="sort" class="form-select">
                            <option value="no_asc" {{ $sort == 'no_asc' ? 'selected' : '' }}>No. Registrasi (A-Z)</option>
                            <option value="no_desc" {{ $sort == 'no_desc' ? 'selected' : '' }}>No. Registrasi (Z-A)</option>
                            <option value="name_asc" {{ $sort == 'name_asc' ? 'selected' : '' }}>Nama (A-Z)</option>
                            <option value="name_desc" {{ $sort == 'name_desc' ? 'selected' : '' }}>Nama (Z-A)</option>
                            <option value="jaringan_asc" {{ $sort == 'jaringan_asc' ? 'selected' : '' }}>Jaringan (A-Z)</option>
                            <option value="jaringan_desc" {{ $sort == 'jaringan_desc' ? 'selected' : '' }}>Jaringan (Z-A)</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-filter me-1"></i>Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Bulk Actions -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body py-3">
            <div class="d-flex flex-wrap gap-2 align-items-center">
                <span class="text-muted small me-2">Bulk Actions:</span>
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="selectAll()">
                    <i class="fas fa-check-double me-1"></i>Pilih Semua (Halaman Ini)
                </button>
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="selectByJaringan()">
                    <i class="fas fa-layer-group me-1"></i>Pilih Per Jaringan
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="clearSelection()">
                    <i class="fas fa-times me-1"></i>Clear Semua
                </button>
            </div>
        </div>
    </div>

    <!-- Pendaftar Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent border-0 pt-4 pb-3">
            <h5 class="mb-0">Daftar Siswa</h5>
            <p class="text-muted small mb-0 mt-1">Total: {{ $pendaftars->total() }} siswa</p>
        </div>
        <div class="card-body p-0">
            @if($pendaftars->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="50" class="px-4">
                                <input type="checkbox" class="form-check-input" id="selectAllCheckbox" onclick="toggleSelectAll(this)">
                            </th>
                            <th>No. Registrasi</th>
                            <th>Nama Lengkap</th>
                            <th>Jaringan</th>
                            <th>Jurusan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendaftars as $pendaftar)
                        <tr>
                            <td class="px-4">
                                <input type="checkbox" class="form-check-input pendaftar-checkbox" 
                                       value="{{ $pendaftar->id_pendaftar }}" 
                                       data-jaringan="{{ $pendaftar->nama_jaringan }}"
                                       onchange="updateSelection()">
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ $pendaftar->no_registrasi }}</span>
                            </td>
                            <td>
                                <span class="fw-medium">{{ $pendaftar->nama_lengkap }}</span>
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $pendaftar->nama_jaringan ?: '(Langsung)' }}</span>
                            </td>
                            <td>{{ $pendaftar->jurusan ?: '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($pendaftars->hasPages())
            <div class="card-footer bg-transparent border-top">
                <x-custom-pagination :paginator="$pendaftars" :showPerPage="true" />
            </div>
            @endif
            @else
            <div class="text-center py-5">
                <i class="fas fa-inbox text-muted mb-3" style="font-size: 4rem; opacity: 0.3;"></i>
                <h5 class="text-muted">Tidak ada siswa ditemukan</h5>
                <p class="text-muted mb-4">Coba ubah filter atau search</p>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Merge Form Modal -->
<div class="modal fade" id="mergeFormModal" tabindex="-1" aria-labelledby="mergeFormModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title" id="mergeFormModalLabel">
                    <i class="fas fa-arrow-right text-primary me-2"></i>Pilih Jaringan Tujuan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info mb-3">
                    <i class="fas fa-info-circle me-2"></i>
                    <span id="mergeInfoText"></span>
                </div>
                
                <div class="mb-3">
                    <label class="form-label fw-medium">Ke Jaringan (Tujuan)</label>
                    <select id="toJaringanSelect" class="form-select" required>
                        <option value="">-- Pilih Jaringan Tujuan --</option>
                        @foreach($jaringans as $jar)
                        <option value="{{ $jar }}">{{ $jar }}</option>
                        @endforeach
                    </select>
                    <small class="text-muted">Semua siswa terpilih akan digabungkan ke jaringan ini</small>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" onclick="previewMerge()">
                    <i class="fas fa-eye me-2"></i>Preview Merge
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title" id="previewModalLabel">
                    <i class="fas fa-eye text-primary me-2"></i>Preview Merge Selective
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
                            <div class="col-4 text-muted">Ke (Tujuan):</div>
                            <div class="col-8 fw-medium" id="previewTo"></div>
                        </div>
                        <div class="row">
                            <div class="col-4 text-muted">Total Siswa:</div>
                            <div class="col-8 fw-medium text-danger" id="previewCount"></div>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <h6 class="mb-2">Siswa yang Akan Di-merge (Grouped by Jaringan):</h6>
                    <div id="previewGrouped"></div>
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

<!-- Select By Jaringan Modal -->
<div class="modal fade" id="selectJaringanModal" tabindex="-1" aria-labelledby="selectJaringanModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title" id="selectJaringanModalLabel">
                    <i class="fas fa-layer-group text-primary me-2"></i>Pilih Per Jaringan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted">Pilih jaringan untuk mencentang semua siswa dari jaringan tersebut:</p>
                <div class="list-group" id="jaringanList">
                    @foreach($jaringans as $jar)
                    <button type="button" class="list-group-item list-group-item-action" onclick="selectJaringan('{{ $jar }}')">
                        {{ $jar }}
                    </button>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    .floating-counter {
        position: fixed;
        bottom: 30px;
        right: 30px;
        z-index: 1000;
        animation: bounceIn 0.5s ease-out;
    }
    
    .floating-counter-content {
        background: #4F46E5;
        border-radius: 20px;
        padding: 20px;
        box-shadow: 0 8px 30px rgba(79, 70, 229, 0.4);
        text-align: center;
        min-width: 160px;
        transition: all 0.3s ease;
    }
    
    .floating-counter-content:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 40px rgba(79, 70, 229, 0.5);
    }
    
    .floating-counter-badge {
        background: white;
        color: #4F46E5;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 10px;
        font-size: 24px;
        font-weight: bold;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }
    
    .floating-counter-text {
        color: white;
        font-size: 14px;
        font-weight: 500;
        margin-bottom: 5px;
    }
    
    .floating-counter-actions .btn-light {
        background: white;
        color: #4F46E5;
        border: none;
        font-weight: 600;
    }
    
    .floating-counter-actions .btn-light:hover {
        background: #f8f9fa;
        transform: scale(1.05);
    }
    
    .floating-counter-actions .btn-outline-light {
        border: 2px solid white;
        color: white;
        background: transparent;
    }
    
    .floating-counter-actions .btn-outline-light:hover {
        background: white;
        color: #4F46E5;
    }
    
    @keyframes bounceIn {
        0% {
            transform: scale(0) translateY(100px);
            opacity: 0;
        }
        50% {
            transform: scale(1.1);
        }
        100% {
            transform: scale(1) translateY(0);
            opacity: 1;
        }
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .floating-counter {
            bottom: 20px;
            right: 20px;
        }
        
        .floating-counter-content {
            min-width: 140px;
            padding: 15px;
        }
        
        .floating-counter-badge {
            width: 50px;
            height: 50px;
            font-size: 20px;
        }
    }
</style>
@endpush

@push('scripts')
<script>
let selectedIds = new Set();
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

// Confirmation modal function
function showConfirmModal(title, message, onConfirm, confirmText = 'Ya', confirmClass = 'btn-primary') {
    const modalHtml = `
        <div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title">
                            <i class="fas fa-exclamation-triangle text-warning me-2"></i>${title}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        ${message}
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn ${confirmClass}" id="confirmBtn">${confirmText}</button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Remove existing modal if any
    const existingModal = document.getElementById('confirmModal');
    if (existingModal) {
        existingModal.remove();
    }
    
    document.body.insertAdjacentHTML('beforeend', modalHtml);
    const modal = new bootstrap.Modal(document.getElementById('confirmModal'));
    
    document.getElementById('confirmBtn').addEventListener('click', function() {
        modal.hide();
        onConfirm();
    });
    
    modal.show();
    
    document.getElementById('confirmModal').addEventListener('hidden.bs.modal', function() {
        this.remove();
    });
}

function updateSelection() {
    selectedIds.clear();
    
    document.querySelectorAll('.pendaftar-checkbox:checked').forEach(checkbox => {
        selectedIds.add(parseInt(checkbox.value));
    });
    
    const count = selectedIds.size;
    document.getElementById('selectedCount').textContent = count;
    
    if (count > 0) {
        document.getElementById('selectedCounter').style.display = 'block';
    } else {
        document.getElementById('selectedCounter').style.display = 'none';
    }
}

function toggleSelectAll(checkbox) {
    document.querySelectorAll('.pendaftar-checkbox').forEach(cb => {
        cb.checked = checkbox.checked;
    });
    updateSelection();
}

function selectAll() {
    document.querySelectorAll('.pendaftar-checkbox').forEach(cb => {
        cb.checked = true;
    });
    document.getElementById('selectAllCheckbox').checked = true;
    updateSelection();
    showToast('Semua siswa di halaman ini dipilih', 'success');
}

function clearSelection() {
    showConfirmModal(
        'Konfirmasi Clear',
        '<p>Apakah Anda yakin ingin membersihkan semua pilihan?</p>',
        function() {
            document.querySelectorAll('.pendaftar-checkbox').forEach(cb => {
                cb.checked = false;
            });
            document.getElementById('selectAllCheckbox').checked = false;
            updateSelection();
            showToast('Pilihan dibersihkan', 'info');
        },
        'Ya, Clear',
        'btn-danger'
    );
}

// Confirmation modal function
function showConfirmModal(title, message, onConfirm, confirmText = 'Ya', confirmClass = 'btn-primary') {
    const modalHtml = `
        <div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title">
                            <i class="fas fa-question-circle text-primary me-2"></i>${title}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        ${message}
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn ${confirmClass}" id="confirmBtn">${confirmText}</button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Remove existing modal if any
    const existingModal = document.getElementById('confirmModal');
    if (existingModal) {
        existingModal.remove();
    }
    
    document.body.insertAdjacentHTML('beforeend', modalHtml);
    const modal = new bootstrap.Modal(document.getElementById('confirmModal'));
    
    document.getElementById('confirmBtn').addEventListener('click', function() {
        modal.hide();
        onConfirm();
    });
    
    modal.show();
    
    document.getElementById('confirmModal').addEventListener('hidden.bs.modal', function() {
        this.remove();
    });
}

function selectByJaringan() {
    const modal = new bootstrap.Modal(document.getElementById('selectJaringanModal'));
    modal.show();
}

function selectJaringan(jaringan) {
    let count = 0;
    document.querySelectorAll('.pendaftar-checkbox').forEach(cb => {
        if (cb.dataset.jaringan === jaringan) {
            cb.checked = true;
            count++;
        }
    });
    updateSelection();
    bootstrap.Modal.getInstance(document.getElementById('selectJaringanModal')).hide();
    showToast(`${count} siswa dari ${jaringan} dipilih`, 'success');
}

function showMergeForm() {
    console.log('showMergeForm called, selectedIds:', selectedIds.size);
    
    if (selectedIds.size === 0) {
        showToast('Pilih minimal 1 siswa terlebih dahulu!', 'danger');
        return;
    }
    
    try {
        // Get unique jaringan from selected students
        const selectedJaringans = new Set();
        document.querySelectorAll('.pendaftar-checkbox:checked').forEach(cb => {
            const jaringan = cb.dataset.jaringan;
            if (jaringan && jaringan !== '' && jaringan !== 'null' && jaringan !== 'undefined') {
                selectedJaringans.add(jaringan);
            }
        });
        
        console.log('Selected jaringans:', Array.from(selectedJaringans));
        
        // Update dropdown: hide jaringan yang sudah dipilih
        const toJaringanSelect = document.getElementById('toJaringanSelect');
        if (!toJaringanSelect) {
            console.error('toJaringanSelect not found!');
            showToast('Error: Dropdown tidak ditemukan', 'danger');
            return;
        }
        
        let availableCount = 0;
        
        Array.from(toJaringanSelect.options).forEach(option => {
            // Skip empty option (placeholder)
            if (!option.value || option.value === '') {
                option.style.display = 'block';
                option.disabled = false;
                return;
            }
            
            // Hide jaringan yang sudah dipilih
            if (selectedJaringans.has(option.value)) {
                option.style.display = 'none';
                option.disabled = true;
            } else {
                option.style.display = 'block';
                option.disabled = false;
                availableCount++;
            }
        });
        
        console.log('Available options:', availableCount);
        
        // Reset selection
        toJaringanSelect.value = '';
        
        // Check if there are available options
        if (availableCount === 0) {
            showConfirmModal(
                'Tidak Ada Jaringan Tujuan',
                '<div class="alert alert-warning mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Semua siswa terpilih dari jaringan yang sama. Tidak ada jaringan tujuan yang tersedia untuk merge.</div><p class="mt-3 mb-0">Silakan pilih siswa dari jaringan yang berbeda.</p>',
                function() {
                    // Do nothing, just close
                },
                'OK',
                'btn-primary'
            );
            return;
        }
        
        document.getElementById('mergeInfoText').textContent = `${selectedIds.size} siswa terpilih akan digabungkan`;
        
        const modalElement = document.getElementById('mergeFormModal');
        if (!modalElement) {
            console.error('mergeFormModal not found!');
            showToast('Error: Modal tidak ditemukan', 'danger');
            return;
        }
        
        const modal = new bootstrap.Modal(modalElement);
        modal.show();
        
        console.log('Modal shown successfully');
        
    } catch (error) {
        console.error('Error in showMergeForm:', error);
        showToast('Terjadi kesalahan: ' + error.message, 'danger');
    }
}

function previewMerge() {
    const toJaringan = document.getElementById('toJaringanSelect').value;
    
    if (!toJaringan) {
        showToast('Pilih jaringan tujuan terlebih dahulu!', 'danger');
        return;
    }
    
    if (selectedIds.size === 0) {
        showToast('Pilih minimal 1 siswa terlebih dahulu!', 'danger');
        return;
    }
    
    // Show loading
    const previewBtn = event.target;
    const originalText = previewBtn.innerHTML;
    previewBtn.disabled = true;
    previewBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Loading...';
    
    fetch('{{ route("jaringan.preview-selective") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            selected_ids: Array.from(selectedIds),
            to_jaringan: toJaringan
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
        document.getElementById('previewTo').textContent = data.to_jaringan;
        document.getElementById('previewCount').textContent = data.total_count + ' siswa';
        
        // Fill grouped data
        const groupedDiv = document.getElementById('previewGrouped');
        groupedDiv.innerHTML = '';
        
        data.grouped.forEach(group => {
            const groupHtml = `
                <div class="card mb-2">
                    <div class="card-body py-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="badge bg-info">${group.jaringan}</span>
                                <span class="text-muted ms-2">${group.count} siswa</span>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            groupedDiv.innerHTML += groupHtml;
        });
        
        // Close merge form modal
        bootstrap.Modal.getInstance(document.getElementById('mergeFormModal')).hide();
        
        // Show preview modal
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
    
    fetch('{{ route("jaringan.process-merge-selective") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            selected_ids: Array.from(selectedIds),
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
