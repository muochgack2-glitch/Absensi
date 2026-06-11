@extends('layouts.admin')

@section('title', 'Broadcast WhatsApp')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">📢 Broadcast WhatsApp</h1>
            <p class="text-muted mb-0">Kirim pesan ke banyak penerima sekaligus</p>
        </div>
        <div>
            <a href="{{ route('whatsapp.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
        </div>
    </div>

    <!-- Tab Navigation (Task 7.1) -->
    <ul class="nav nav-tabs mb-4" id="broadcastTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="spmb-tab" data-bs-toggle="tab" data-bs-target="#spmb-broadcast" type="button" role="tab">
                <i class="fas fa-users me-2"></i>Data SPMB
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="external-tab" data-bs-toggle="tab" data-bs-target="#external-broadcast" type="button" role="tab">
                <i class="fas fa-external-link-alt me-2"></i>Data Eksternal
            </button>
        </li>
    </ul>

    <div class="tab-content" id="broadcastTabContent">
        <!-- TAB 1: DATA SPMB (Existing broadcast form) -->
        <div class="tab-pane fade show active" id="spmb-broadcast" role="tabpanel">
            <div class="row">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <form id="broadcastForm">
                        @csrf
                        
                        <div class="mb-4">
                            <label class="form-label">Pilih Penerima <span class="text-danger">*</span></label>
                            
                            <div class="btn-group w-100 mb-3" role="group">
                                <input type="radio" class="btn-check" name="recipientType" id="allPendaftar" value="all" checked>
                                <label class="btn btn-outline-primary" for="allPendaftar">
                                    <i class="fas fa-users me-2"></i>Semua Pendaftar
                                </label>
                                
                                <input type="radio" class="btn-check" name="recipientType" id="selectPendaftar" value="select">
                                <label class="btn btn-outline-primary" for="selectPendaftar">
                                    <i class="fas fa-user-check me-2"></i>Pilih Manual
                                </label>
                                
                                <input type="radio" class="btn-check" name="recipientType" id="customNumbers" value="custom">
                                <label class="btn btn-outline-primary" for="customNumbers">
                                    <i class="fas fa-keyboard me-2"></i>Input Manual
                                </label>
                            </div>
                            
                            <!-- Select Pendaftar -->
                            <div id="selectPendaftarSection" style="display: none;">
                                <div class="border rounded p-3" style="max-height: 300px; overflow-y: auto; background: var(--bg-secondary);">
                                    @foreach($pendaftars as $pendaftar)
                                    <div class="form-check">
                                        <input class="form-check-input pendaftar-checkbox" type="checkbox" value="{{ $pendaftar->primary_phone ?? $pendaftar->no_hp_wali }}" id="pendaftar{{ $pendaftar->id_pendaftar }}">
                                        <label class="form-check-label" for="pendaftar{{ $pendaftar->id_pendaftar }}" style="color: var(--text-primary);">
                                            <strong>{{ $pendaftar->nama_lengkap }}</strong> - {{ substr($pendaftar->primary_phone ?? $pendaftar->no_hp_wali, 0, 4) }}****{{ substr($pendaftar->primary_phone ?? $pendaftar->no_hp_wali, -3) }}
                                            <br><small class="text-muted">{{ $pendaftar->jurusan }} - HP {{ $pendaftar->phone_type ?? 'Wali' }}</small>
                                        </label>
                                    </div>
                                    @endforeach
                                </div>
                                <div class="mt-2">
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="selectAll()">Pilih Semua</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="deselectAll()">Batal Pilih</button>
                                    <span class="ms-2 text-muted">Terpilih: <strong id="selectedCount">0</strong></span>
                                </div>
                            </div>
                            
                            <!-- Custom Numbers -->
                            <div id="customNumbersSection" style="display: none;">
                                <textarea class="form-control" id="customNumbersInput" rows="5" placeholder="Masukkan nomor HP, satu nomor per baris&#10;Contoh:&#10;081234567890&#10;082345678901&#10;083456789012"></textarea>
                                <small class="text-muted">Masukkan nomor HP, satu nomor per baris</small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Pesan <span class="text-danger">*</span></label>
                            <div class="mb-2">
                                <select class="form-select" id="templateSelect">
                                    <option value="">-- Pilih Template (Opsional) --</option>
                                    @foreach($templates as $template)
                                    <option value="{{ $template->id }}" data-message="{{ $template->message }}">
                                        {{ $template->label }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <textarea class="form-control" id="broadcastMessage" rows="8" placeholder="Ketik pesan broadcast Anda di sini..." required></textarea>
                            <small class="text-muted">Karakter: <span id="charCount">0</span></small>
                        </div>

                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Perhatian:</strong> Broadcast akan mengirim pesan ke semua penerima yang dipilih. Pastikan pesan sudah benar sebelum mengirim.
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg" id="sendBtn">
                                <i class="fas fa-paper-plane me-2"></i>Kirim Broadcast
                            </button>
                        </div>
                    </form>

                    <!-- Result -->
                    <div id="resultSection" class="mt-4" style="display: none;">
                        <div class="alert alert-info">
                            <h6 class="alert-heading">
                                <i class="fas fa-info-circle me-2"></i>Status Pengiriman
                            </h6>
                            <div id="resultContent"></div>
                        </div>
                    </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                    <h6 class="card-title">
                        <i class="fas fa-chart-bar me-2 text-primary"></i>Ringkasan
                    </h6>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Total Pendaftar:</span>
                        <strong>{{ $pendaftars->count() }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Penerima Terpilih:</span>
                        <strong id="recipientCount">{{ $pendaftars->count() }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Estimasi Waktu:</span>
                        <strong id="estimatedTime">~{{ ceil($pendaftars->count() / 60) }} menit</strong>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="card-title">
                        <i class="fas fa-info-circle me-2 text-info"></i>Informasi
                    </h6>
                    <ul class="small mb-0 ps-3">
                        <li class="mb-2">Pesan dikirim satu per satu dengan delay 1 detik</li>
                        <li class="mb-2">Maksimal 20 pesan per menit (rate limit)</li>
                        <li class="mb-2">Semua pengiriman akan tercatat di log</li>
                        <li class="mb-2">Pastikan WhatsApp Gateway terhubung</li>
                    </ul>
                </div>
            </div>

            <div class="card border-0 shadow-sm mt-3">
                <div class="card-body">
                    <h6 class="card-title">
                        <i class="fas fa-code me-2 text-success"></i>Variabel Template
                    </h6>
                    <p class="small text-muted mb-2">Gunakan variabel ini di pesan Anda:</p>
                    <div class="small">
                        <code>{nama}</code> - Nama pendaftar<br>
                        <code>{no_registrasi}</code> - Nomor registrasi<br>
                        <code>{jurusan}</code> - Jurusan pilihan<br>
                        <code>{nisn}</code> - NISN<br>
                        <code>{asal_sekolah}</code> - Asal sekolah<br>
                        <code>{sekolah}</code> - Nama sekolah<br>
                        <code>{tanggal}</code> - Tanggal hari ini<br>
                        <code>{tahun}</code> - Tahun sekarang<br>
                        <code>{portal_url}</code> - URL portal
                    </div>
                    <div class="alert alert-light mt-2 mb-0">
                        <small><strong>Contoh:</strong><br>
                        Hai {nama}, pendaftaran Anda di {jurusan} dengan nomor {no_registrasi} telah diterima.</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    <!-- TAB 2: DATA EKSTERNAL (Tasks 7.2-7.8) -->
    <div class="tab-pane fade" id="external-broadcast" role="tabpanel">
            <div class="row">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <form id="externalBroadcastForm">
                                @csrf
                                
                                <!-- Batch Name (Task 7.5) -->
                                <div class="mb-4">
                                    <label class="form-label">Nama Batch <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="batchName" placeholder="Misal: Alumni 2024, Broadcast Umum Januari" required>
                                    <small class="text-muted">Nama batch harus unik dalam 30 hari terakhir</small>
                                    <div class="invalid-feedback" id="batchNameError"></div>
                                </div>

                                <!-- Data Source Selection (Task 7.2) -->
                                <div class="mb-4">
                                    <label class="form-label">Sumber Data <span class="text-danger">*</span></label>
                                    <div class="btn-group w-100" role="group">
                                        <input type="radio" class="btn-check" name="sourceType" id="sourceCSV" value="csv" checked>
                                        <label class="btn btn-outline-primary" for="sourceCSV">
                                            <i class="fas fa-file-csv me-2"></i>Upload CSV
                                        </label>
                                        
                                        <input type="radio" class="btn-check" name="sourceType" id="sourceManual" value="manual">
                                        <label class="btn btn-outline-primary" for="sourceManual">
                                            <i class="fas fa-keyboard me-2"></i>Input Manual
                                        </label>
                                    </div>
                                </div>

                                <!-- CSV Upload Section (Task 7.3) -->
                                <div id="csvSection" class="mb-4">
                                    <label class="form-label">Upload File CSV <span class="text-danger">*</span></label>
                                    <input type="file" class="form-control" id="csvFile" accept=".csv,.txt">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Format: <code>name,phone,notes</code> (header wajib). Max 2MB.
                                    </small>
                                    <div class="alert alert-light mt-2">
                                        <strong>Contoh format CSV:</strong><br>
                                        <code>
                                            name,phone,notes<br>
                                            Budi Santoso,081234567890,Alumni 2023<br>
                                            Siti Aminah,082345678901,Orang Tua Siswa
                                        </code>
                                    </div>
                                </div>

                                <!-- Manual Input Section (Task 7.4) -->
                                <div id="manualSection" class="mb-4" style="display: none;">
                                    <label class="form-label">Input Manual <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="manualInput" rows="8" placeholder="Format: phone|name|notes (satu per baris)&#10;atau hanya nomor telepon saja&#10;&#10;Contoh:&#10;081234567890|Budi Santoso|Alumni 2023&#10;082345678901|Siti Aminah|Orang Tua&#10;083456789012"></textarea>
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Format: <code>phone|name|notes</code> atau hanya <code>phone</code>. Max 500 entries.
                                    </small>
                                </div>

                                <div class="d-grid mb-3">
                                    <button type="button" class="btn btn-primary" id="parseBtn" onclick="parseRecipients()">
                                        <i class="fas fa-search me-2"></i>Parse & Preview Recipients
                                    </button>
                                </div>

                                <!-- Recipient Preview Section (Task 7.6) -->
                                <div id="previewSection" style="display: none;">
                                    <hr>
                                    <h6 class="mb-3">
                                        <i class="fas fa-eye me-2 text-info"></i>Preview Recipients
                                    </h6>
                                    
                                    <div class="alert alert-info">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <strong>Total:</strong> <span id="previewTotalCount">0</span> recipients
                                            </div>
                                            <div>
                                                <strong>Terpilih:</strong> <span id="previewSelectedCount" class="text-success">0</span>
                                            </div>
                                            <div>
                                                <strong>Duplikat SPMB:</strong> <span id="previewDuplicateCount">0</span>
                                                <span class="badge bg-warning text-dark ms-1">🔄</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="selectAllPreview" checked onchange="toggleAllPreviewRecipients(this)">
                                            <label class="form-check-label" for="selectAllPreview">
                                                <strong>Pilih Semua</strong>
                                            </label>
                                        </div>
                                    </div>

                                    <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                                        <table class="table table-sm table-hover">
                                            <thead class="sticky-top bg-light">
                                                <tr>
                                                    <th width="50">
                                                        <input type="checkbox" class="form-check-input" id="selectAllHeader" checked onchange="toggleAllPreviewRecipients(this)">
                                                    </th>
                                                    <th>Nama</th>
                                                    <th>Nomor HP</th>
                                                    <th>Notes</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody id="previewTableBody">
                                            </tbody>
                                        </table>
                                    </div>

                                    <input type="hidden" id="parsedBatchId">

                                    <!-- Message Template and Text (Task 7.7) -->
                                    <hr class="my-4">
                                    <h6 class="mb-3">
                                        <i class="fas fa-envelope me-2 text-success"></i>Komposisi Pesan
                                    </h6>

                                    <div class="mb-3">
                                        <label class="form-label">Pilih Template (Opsional)</label>
                                        <select class="form-select" id="externalTemplateSelect">
                                            <option value="">-- Pilih Template --</option>
                                            @foreach($templates as $template)
                                            <option value="{{ $template->id }}" data-message="{{ $template->message }}">
                                                {{ $template->label }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Pesan <span class="text-danger">*</span></label>
                                        <textarea class="form-control" id="externalMessage" rows="6" placeholder="Ketik pesan Anda di sini..." required></textarea>
                                        <small class="text-muted d-block mb-2">
                                            <strong>Variabel Dasar:</strong> <code>{nama}</code>, <code>{phone}</code>, <code>{notes}</code>
                                        </small>
                                        <details class="mb-2">
                                            <summary class="text-primary" style="cursor: pointer;">
                                                <small><i class="fas fa-info-circle me-1"></i>Variabel SPMB (hanya untuk data duplikat) - Klik untuk lihat</small>
                                            </summary>
                                            <div class="mt-2 p-3 border rounded variable-box">
                                                <small>
                                                    <div class="mb-2">
                                                        <strong class="variable-label">📋 Data Pribadi:</strong><br>
                                                        <code>{no_registrasi}</code>, <code>{nisn}</code>, <code>{nik}</code>, <code>{email}</code>, 
                                                        <code>{tempat_lahir}</code>, <code>{tanggal_lahir}</code>, <code>{jenis_kelamin}</code>, <code>{agama}</code>
                                                    </div>
                                                    
                                                    <div class="mb-2">
                                                        <strong class="variable-label">🏫 Data Sekolah:</strong><br>
                                                        <code>{asal_sekolah}</code>, <code>{tahun_lulus}</code>, <code>{jurusan}</code>
                                                    </div>
                                                    
                                                    <div class="mb-2">
                                                        <strong class="variable-label">👨‍👩‍👧 Data Keluarga:</strong><br>
                                                        <code>{nama_ayah}</code>, <code>{nama_ibu}</code>, <code>{no_hp_ortu}</code>, 
                                                        <code>{nama_wali}</code>, <code>{no_hp_wali}</code>
                                                    </div>
                                                    
                                                    <div class="mb-0">
                                                        <strong class="variable-label">📝 Data Pendaftaran:</strong><br>
                                                        <code>{nama_jaringan}</code>, <code>{gelombang}</code>, <code>{tgl_daftar}</code>, 
                                                        <code>{status_siswa}</code>, <code>{alamat}</code>
                                                    </div>
                                                </small>
                                            </div>
                                        </details>
                                        
                                        <style>
                                        .variable-box {
                                            background-color: rgba(0, 0, 0, 0.05);
                                        }
                                        [data-bs-theme="dark"] .variable-box {
                                            background-color: rgba(255, 255, 255, 0.05) !important;
                                        }
                                        .variable-label {
                                            color: #000;
                                        }
                                        [data-bs-theme="dark"] .variable-label {
                                            color: #fff !important;
                                        }
                                        </style>
                                        <div id="templateWarning" class="alert alert-warning mt-2" style="display: none;">
                                            <i class="fas fa-exclamation-triangle me-2"></i>
                                            <strong>Peringatan:</strong> Template ini menggunakan variabel SPMB yang tidak tersedia untuk data eksternal: 
                                            <span id="warningVariables"></span>
                                        </div>
                                    </div>

                                    <!-- Send Button (Task 7.8) -->
                                    <div class="d-grid">
                                        <button type="button" class="btn btn-success btn-lg" id="sendExternalBtn" onclick="sendExternalBroadcast()">
                                            <i class="fas fa-paper-plane me-2"></i>Kirim Broadcast
                                        </button>
                                    </div>
                                </div>

                                <!-- Result Section -->
                                <div id="externalResultSection" class="mt-4" style="display: none;">
                                    <div class="alert alert-success">
                                        <h6 class="alert-heading">
                                            <i class="fas fa-check-circle me-2"></i>Broadcast Terkirim
                                        </h6>
                                        <div id="externalResultContent"></div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-body">
                            <h6 class="card-title">
                                <i class="fas fa-info-circle me-2 text-info"></i>Informasi
                            </h6>
                            <ul class="small mb-0 ps-3">
                                <li class="mb-2">Upload CSV atau input manual untuk broadcast ke nomor eksternal</li>
                                <li class="mb-2">Sistem akan deteksi duplikat dengan database SPMB</li>
                                <li class="mb-2">Pesan tetap terkirim meskipun duplikat</li>
                                <li class="mb-2">Rate limit: 1 pesan per detik</li>
                            </ul>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <h6 class="card-title">
                                <i class="fas fa-code me-2 text-success"></i>Variabel Template
                            </h6>
                            <p class="small text-muted mb-2">Variabel yang tersedia:</p>
                            <div class="small">
                                <code>{nama}</code> - Nama recipient<br>
                                <code>{phone}</code> - Nomor HP
                            </div>
                            <div class="alert alert-light mt-3 mb-0">
                                <small><strong>Contoh:</strong><br>
                                Halo {nama}, ini adalah pesan broadcast untuk Anda.</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Recipient type toggle
document.querySelectorAll('input[name="recipientType"]').forEach(radio => {
    radio.addEventListener('change', function() {
        document.getElementById('selectPendaftarSection').style.display = 'none';
        document.getElementById('customNumbersSection').style.display = 'none';
        
        if (this.value === 'select') {
            document.getElementById('selectPendaftarSection').style.display = 'block';
            updateRecipientCount();
        } else if (this.value === 'custom') {
            document.getElementById('customNumbersSection').style.display = 'block';
            updateRecipientCount();
        } else {
            document.getElementById('recipientCount').textContent = {{ $pendaftars->count() }};
            document.getElementById('estimatedTime').textContent = '~' + Math.ceil({{ $pendaftars->count() }} / 60) + ' menit';
        }
    });
});

// Checkbox change
document.querySelectorAll('.pendaftar-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', updateRecipientCount);
});

// Custom numbers input
document.getElementById('customNumbersInput').addEventListener('input', updateRecipientCount);

// Template select
document.getElementById('templateSelect').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const message = selectedOption.dataset.message;
    if (message) {
        document.getElementById('broadcastMessage').value = message;
        updateCharCount();
    }
});

// Character counter
document.getElementById('broadcastMessage').addEventListener('input', updateCharCount);

function updateCharCount() {
    const count = document.getElementById('broadcastMessage').value.length;
    document.getElementById('charCount').textContent = count;
}

function selectAll() {
    document.querySelectorAll('.pendaftar-checkbox').forEach(checkbox => {
        checkbox.checked = true;
    });
    updateRecipientCount();
}

function deselectAll() {
    document.querySelectorAll('.pendaftar-checkbox').forEach(checkbox => {
        checkbox.checked = false;
    });
    updateRecipientCount();
}

function updateRecipientCount() {
    const type = document.querySelector('input[name="recipientType"]:checked').value;
    let count = 0;
    
    if (type === 'all') {
        count = {{ $pendaftars->count() }};
    } else if (type === 'select') {
        count = document.querySelectorAll('.pendaftar-checkbox:checked').length;
        document.getElementById('selectedCount').textContent = count;
    } else if (type === 'custom') {
        const numbers = document.getElementById('customNumbersInput').value.split('\n').filter(n => n.trim());
        count = numbers.length;
    }
    
    document.getElementById('recipientCount').textContent = count;
    document.getElementById('estimatedTime').textContent = '~' + Math.ceil(count / 60) + ' menit';
}

// Form submit
document.getElementById('broadcastForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const type = document.querySelector('input[name="recipientType"]:checked').value;
    const message = document.getElementById('broadcastMessage').value;
    let recipients = [];
    
    if (type === 'all') {
        // Kirim semua dengan data lengkap
        recipients = @json($recipientsData);
    } else if (type === 'select') {
        // Kirim yang dipilih dengan data lengkap
        recipients = Array.from(document.querySelectorAll('.pendaftar-checkbox:checked')).map(cb => {
            const pendaftarId = cb.id.replace('pendaftar', '');
            const label = document.querySelector(`label[for="${cb.id}"]`);
            const nama = label.querySelector('strong').textContent;
            const jurusan = label.querySelector('small').textContent.split(' - ')[0];
            return {
                phone: cb.value,
                id_pendaftar: parseInt(pendaftarId),
                nama: nama,
                jurusan: jurusan
            };
        });
    } else if (type === 'custom') {
        // Custom numbers - tanpa id_pendaftar (null)
        recipients = document.getElementById('customNumbersInput').value.split('\n')
            .filter(n => n.trim())
            .map(phone => ({
                phone: phone.trim(),
                id_pendaftar: null,
                nama: 'Custom',
                jurusan: '-'
            }));
    }
    
    if (recipients.length === 0) {
        alert('Pilih minimal 1 penerima');
        return;
    }
    
    if (!message.trim()) {
        alert('Pesan tidak boleh kosong');
        return;
    }
    
    if (!confirm(`Kirim broadcast ke ${recipients.length} penerima?`)) {
        return;
    }
    
    const btn = document.getElementById('sendBtn');
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Mengirim...';
    
    fetch('{{ route("whatsapp.broadcast.send") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            recipients: recipients,
            message: message
        })
    })
    .then(response => response.json())
    .then(data => {
        showResult(data);
    })
    .catch(error => {
        alert('Error: ' + error.message);
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = originalText;
    });
});

function showResult(data) {
    const resultSection = document.getElementById('resultSection');
    const resultContent = document.getElementById('resultContent');
    
    let html = `
        <div class="mb-3">
            <strong>Total:</strong> ${data.total}<br>
            <strong>Berhasil:</strong> <span class="text-success">${data.success_count}</span><br>
            <strong>Gagal:</strong> <span class="text-danger">${data.failed_count}</span>
        </div>
    `;
    
    if (data.results && data.results.length > 0) {
        html += '<div class="table-responsive"><table class="table table-sm"><thead><tr><th>Nomor</th><th>Status</th></tr></thead><tbody>';
        data.results.forEach(result => {
            const statusClass = result.success ? 'success' : 'danger';
            const statusText = result.success ? 'Berhasil' : 'Gagal';
            html += `<tr><td>${result.phone}</td><td><span class="badge bg-${statusClass}">${statusText}</span></td></tr>`;
        });
        html += '</tbody></table></div>';
    }
    
    resultContent.innerHTML = html;
    resultSection.style.display = 'block';
    resultSection.scrollIntoView({ behavior: 'smooth' });
}

// ===== EXTERNAL BROADCAST JAVASCRIPT (Tasks 9.1-9.5) =====

// Task 9.1: Source type toggle
document.querySelectorAll('input[name="sourceType"]').forEach(radio => {
    radio.addEventListener('change', function() {
        if (this.value === 'csv') {
            document.getElementById('csvSection').style.display = 'block';
            document.getElementById('manualSection').style.display = 'none';
        } else {
            document.getElementById('csvSection').style.display = 'none';
            document.getElementById('manualSection').style.display = 'block';
        }
    });
});

// Task 9.4: Template variable warning
document.getElementById('externalTemplateSelect').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const message = selectedOption.dataset.message;
    if (message) {
        document.getElementById('externalMessage').value = message;
        
        // Check for SPMB-specific variables
        const spmbVariables = ['{no_registrasi}', '{no_pendaftaran}', '{jurusan}', '{nisn}', '{asal_sekolah}'];
        const foundVariables = spmbVariables.filter(v => message.includes(v));
        
        if (foundVariables.length > 0) {
            document.getElementById('warningVariables').textContent = foundVariables.join(', ');
            document.getElementById('templateWarning').style.display = 'block';
        } else {
            document.getElementById('templateWarning').style.display = 'none';
        }
    }
});

// Task 9.2: Parse recipients AJAX call
function parseRecipients() {
    const batchName = document.getElementById('batchName').value.trim();
    const sourceType = document.querySelector('input[name="sourceType"]:checked').value;
    
    if (!batchName) {
        alert('Nama batch harus diisi');
        document.getElementById('batchName').focus();
        return;
    }
    
    const formData = new FormData();
    formData.append('batch_name', batchName);
    formData.append('source_type', sourceType);
    
    if (sourceType === 'csv') {
        const fileInput = document.getElementById('csvFile');
        if (!fileInput.files.length) {
            alert('Pilih file CSV terlebih dahulu');
            return;
        }
        formData.append('csv_file', fileInput.files[0]);
    } else {
        const manualInput = document.getElementById('manualInput').value.trim();
        if (!manualInput) {
            alert('Input manual tidak boleh kosong');
            return;
        }
        formData.append('manual_input', manualInput);
    }
    
    const btn = document.getElementById('parseBtn');
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Parsing...';
    
    fetch('{{ route("whatsapp.broadcast.external.parse") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            displayPreview(data.data);
        } else {
            alert('Error: ' + data.message);
            if (data.message.includes('sudah digunakan')) {
                document.getElementById('batchName').classList.add('is-invalid');
                document.getElementById('batchNameError').textContent = data.message;
            }
        }
    })
    .catch(error => {
        alert('Error: ' + error.message);
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = originalText;
    });
}

// Task 9.3: Display recipient preview
function displayPreview(data) {
    document.getElementById('parsedBatchId').value = data.batch_id;
    document.getElementById('previewTotalCount').textContent = data.total_count;
    document.getElementById('previewDuplicateCount').textContent = data.duplicates_count;
    document.getElementById('previewSelectedCount').textContent = data.total_count;
    
    const tbody = document.getElementById('previewTableBody');
    tbody.innerHTML = '';
    
    data.preview.forEach((recipient, index) => {
        const row = document.createElement('tr');
        
        const duplicateBadge = recipient.is_duplicate_spmb 
            ? '<span class="badge bg-warning text-dark ms-1" title="Duplikat dengan database SPMB">🔄</span>'
            : '';
        
        row.innerHTML = `
            <td>
                <input type="checkbox" class="form-check-input preview-recipient-checkbox" 
                       value="${index}" 
                       data-phone="${recipient.phone_normalized}"
                       data-name="${recipient.name}"
                       checked 
                       onchange="updatePreviewSelectedCount()">
            </td>
            <td>${recipient.name}</td>
            <td>${recipient.phone}${duplicateBadge}</td>
            <td><small class="text-muted">${recipient.notes || '-'}</small></td>
            <td><span class="badge bg-success">Valid</span></td>
        `;
        
        tbody.appendChild(row);
    });
    
    // Store full data for later use
    window.previewData = data;
    
    document.getElementById('previewSection').style.display = 'block';
    document.getElementById('previewSection').scrollIntoView({ behavior: 'smooth' });
}

// Toggle all preview recipients
function toggleAllPreviewRecipients(checkbox) {
    const checkboxes = document.querySelectorAll('.preview-recipient-checkbox');
    checkboxes.forEach(cb => {
        cb.checked = checkbox.checked;
    });
    document.getElementById('selectAllPreview').checked = checkbox.checked;
    document.getElementById('selectAllHeader').checked = checkbox.checked;
    updatePreviewSelectedCount();
}

// Update selected count
function updatePreviewSelectedCount() {
    const checkboxes = document.querySelectorAll('.preview-recipient-checkbox:checked');
    document.getElementById('previewSelectedCount').textContent = checkboxes.length;
    
    // Update "select all" checkbox state
    const allCheckboxes = document.querySelectorAll('.preview-recipient-checkbox');
    const allChecked = allCheckboxes.length > 0 && checkboxes.length === allCheckboxes.length;
    document.getElementById('selectAllPreview').checked = allChecked;
    document.getElementById('selectAllHeader').checked = allChecked;
}

// Task 9.5: Send external broadcast
function sendExternalBroadcast() {
    const batchId = document.getElementById('parsedBatchId').value;
    const message = document.getElementById('externalMessage').value.trim();
    const templateId = document.getElementById('externalTemplateSelect').value;
    
    if (!batchId) {
        alert('Parse recipients terlebih dahulu');
        return;
    }
    
    if (!message) {
        alert('Pesan tidak boleh kosong');
        document.getElementById('externalMessage').focus();
        return;
    }
    
    // Get checked recipients only
    const checkedCheckboxes = document.querySelectorAll('.preview-recipient-checkbox:checked');
    const selectedCount = checkedCheckboxes.length;
    
    if (selectedCount === 0) {
        alert('Pilih minimal 1 recipient untuk dikirim');
        return;
    }
    
    const totalCount = document.getElementById('previewTotalCount').textContent;
    if (!confirm(`Kirim broadcast ke ${selectedCount} dari ${totalCount} recipients?`)) {
        return;
    }
    
    const btn = document.getElementById('sendExternalBtn');
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Mengirim...';
    
    const payload = {
        batch_id: batchId,
        message: message
    };
    
    if (templateId) {
        payload.template_id = templateId;
    }
    
    fetch('{{ route("whatsapp.broadcast.external.send") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(payload)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showExternalResult(data);
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        alert('Error: ' + error.message);
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = originalText;
    });
}

function showExternalResult(data) {
    const resultSection = document.getElementById('externalResultSection');
    const resultContent = document.getElementById('externalResultContent');
    
    let html = `
        <p class="mb-2">${data.message}</p>
        <div class="mb-0">
            <strong>Total:</strong> ${data.data.total}<br>
            <strong>Berhasil:</strong> <span class="text-success">${data.data.success_count}</span><br>
            <strong>Gagal:</strong> <span class="text-danger">${data.data.failed_count}</span>
        </div>
    `;
    
    resultContent.innerHTML = html;
    resultSection.style.display = 'block';
    resultSection.scrollIntoView({ behavior: 'smooth' });
    
    // Reset form
    setTimeout(() => {
        document.getElementById('externalBroadcastForm').reset();
        document.getElementById('previewSection').style.display = 'none';
        document.getElementById('batchName').classList.remove('is-invalid');
    }, 3000);
}
</script>
@endpush
@endsection
