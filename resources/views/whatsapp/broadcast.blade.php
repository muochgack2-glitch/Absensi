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
        recipients = @json($pendaftars->map(function($p) {
            return [
                'phone' => $p->primary_phone,
                'id_pendaftar' => $p->id_pendaftar,
                'nama' => $p->nama_lengkap,
                'jurusan' => $p->jurusan
            ];
        })->filter(fn($p) => !empty($p['phone']))->values());
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
</script>
@endpush
@endsection
