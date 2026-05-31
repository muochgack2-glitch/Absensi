@extends('layouts.admin')

@section('title', 'Kirim Pesan WhatsApp')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">📤 Kirim Pesan WhatsApp</h1>
            <p class="text-muted mb-0">Kirim pesan WhatsApp ke nomor tertentu</p>
        </div>
        <div>
            <a href="{{ route('whatsapp.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Send Form -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <ul class="nav nav-tabs card-header-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#manualTab" role="tab">
                                <i class="fas fa-keyboard me-2"></i>Tulis Manual
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#templateTab" role="tab">
                                <i class="fas fa-file-alt me-2"></i>Gunakan Template
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <!-- Manual Tab -->
                        <div class="tab-pane fade show active" id="manualTab" role="tabpanel">
                            <form id="manualForm">
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Nomor HP Tujuan <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="phone" name="phone" placeholder="081234567890" required>
                                    <small class="text-muted">Format: 08xxx, 628xxx, atau +628xxx</small>
                                </div>
                                <div class="mb-3">
                                    <label for="message" class="form-label">Pesan <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="message" name="message" rows="8" placeholder="Ketik pesan Anda di sini..." required></textarea>
                                    <small class="text-muted">Karakter: <span id="charCount">0</span></small>
                                </div>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary" id="sendBtn">
                                        <i class="fas fa-paper-plane me-2"></i>Kirim Pesan
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Template Tab -->
                        <div class="tab-pane fade" id="templateTab" role="tabpanel">
                            <form id="templateForm">
                                <div class="mb-3">
                                    <label for="phoneTemplate" class="form-label">Nomor HP Tujuan <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="phoneTemplate" name="phone" placeholder="081234567890" required>
                                    <small class="text-muted">Format: 08xxx, 628xxx, atau +628xxx</small>
                                </div>
                                <div class="mb-3">
                                    <label for="template" class="form-label">Pilih Template <span class="text-danger">*</span></label>
                                    <select class="form-select" id="template" name="template_id" required>
                                        <option value="">-- Pilih Template --</option>
                                        @foreach($templates as $template)
                                        <option value="{{ $template->id }}" data-message="{{ $template->message }}" data-variables="{{ json_encode($template->getAvailableVariables()) }}">
                                            {{ $template->label }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div id="variablesSection" style="display: none;">
                                    <div class="alert alert-info">
                                        <strong>Variabel Template:</strong>
                                        <p class="mb-0 small">Isi variabel di bawah untuk mengganti placeholder di template</p>
                                    </div>
                                    <div id="variableInputs"></div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Preview Pesan</label>
                                    <div class="border rounded p-3 bg-light" id="templatePreview" style="white-space: pre-wrap; min-height: 150px;">
                                        <span class="text-muted">Pilih template untuk melihat preview</span>
                                    </div>
                                </div>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary" id="sendTemplateBtn">
                                        <i class="fas fa-paper-plane me-2"></i>Kirim Pesan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Result Alert -->
            <div id="resultAlert" class="mt-3" style="display: none;"></div>
        </div>

        <!-- Info Sidebar -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                    <h6 class="card-title">
                        <i class="fas fa-info-circle me-2 text-primary"></i>Informasi
                    </h6>
                    <ul class="small mb-0 ps-3">
                        <li class="mb-2">Pastikan WhatsApp Gateway sudah terhubung</li>
                        <li class="mb-2">Nomor HP harus valid dan aktif WhatsApp</li>
                        <li class="mb-2">Pesan akan tercatat di log sistem</li>
                        <li class="mb-2">Gunakan template untuk pesan yang sering dikirim</li>
                    </ul>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="card-title">
                        <i class="fas fa-lightbulb me-2 text-warning"></i>Tips
                    </h6>
                    <ul class="small mb-0 ps-3">
                        <li class="mb-2">Gunakan bahasa yang sopan dan jelas</li>
                        <li class="mb-2">Sertakan informasi kontak jika diperlukan</li>
                        <li class="mb-2">Hindari mengirim spam atau pesan berlebihan</li>
                        <li class="mb-2">Cek preview sebelum mengirim</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Character counter
document.getElementById('message').addEventListener('input', function() {
    document.getElementById('charCount').textContent = this.value.length;
});

// Manual form submit
document.getElementById('manualForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const btn = document.getElementById('sendBtn');
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Mengirim...';
    
    const formData = {
        phone: document.getElementById('phone').value,
        message: document.getElementById('message').value,
        _token: '{{ csrf_token() }}'
    };
    
    fetch('{{ route("whatsapp.send.submit") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        showResult(data);
        if (data.success) {
            document.getElementById('manualForm').reset();
            document.getElementById('charCount').textContent = '0';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showResult({
            success: false,
            message: 'Terjadi kesalahan koneksi: ' + error.message
        });
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = originalText;
    });
});

// Template selection
document.getElementById('template').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const message = selectedOption.dataset.message;
    const variables = JSON.parse(selectedOption.dataset.variables || '[]');
    
    if (message) {
        document.getElementById('templatePreview').textContent = message;
        
        if (variables.length > 0) {
            document.getElementById('variablesSection').style.display = 'block';
            generateVariableInputs(variables);
        } else {
            document.getElementById('variablesSection').style.display = 'none';
        }
    } else {
        document.getElementById('templatePreview').innerHTML = '<span class="text-muted">Pilih template untuk melihat preview</span>';
        document.getElementById('variablesSection').style.display = 'none';
    }
});

function generateVariableInputs(variables) {
    const container = document.getElementById('variableInputs');
    container.innerHTML = '';
    
    variables.forEach(variable => {
        const div = document.createElement('div');
        div.className = 'mb-3';
        div.innerHTML = `
            <label class="form-label">{${variable}}</label>
            <input type="text" class="form-control variable-input" data-variable="${variable}" placeholder="Isi ${variable}">
        `;
        container.appendChild(div);
    });
    
    // Add event listeners to update preview
    document.querySelectorAll('.variable-input').forEach(input => {
        input.addEventListener('input', updateTemplatePreview);
    });
}

function updateTemplatePreview() {
    const selectedOption = document.getElementById('template').options[document.getElementById('template').selectedIndex];
    let message = selectedOption.dataset.message;
    
    document.querySelectorAll('.variable-input').forEach(input => {
        const variable = input.dataset.variable;
        const value = input.value || `{${variable}}`;
        message = message.replace(new RegExp(`\\{${variable}\\}`, 'g'), value);
    });
    
    document.getElementById('templatePreview').textContent = message;
}

// Template form submit
document.getElementById('templateForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const btn = document.getElementById('sendTemplateBtn');
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Mengirim...';
    
    const templateId = document.getElementById('template').value;
    const phone = document.getElementById('phoneTemplate').value;
    
    // Collect variable data
    const data = {};
    document.querySelectorAll('.variable-input').forEach(input => {
        data[input.dataset.variable] = input.value;
    });
    
    const formData = {
        phone: phone,
        template_id: templateId,
        data: data,
        _token: '{{ csrf_token() }}'
    };
    
    console.log('Sending template data:', formData);
    
    fetch('{{ route("whatsapp.send.template") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        showResult(data);
        if (data.success) {
            document.getElementById('templateForm').reset();
            document.getElementById('templatePreview').innerHTML = '<span class="text-muted">Pilih template untuk melihat preview</span>';
            document.getElementById('variablesSection').style.display = 'none';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showResult({
            success: false,
            message: 'Terjadi kesalahan koneksi: ' + error.message
        });
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = originalText;
    });
});

function showResult(data) {
    const alertDiv = document.getElementById('resultAlert');
    const alertClass = data.success ? 'alert-success' : 'alert-danger';
    const icon = data.success ? 'check-circle' : 'times-circle';
    
    let errorDetails = '';
    if (!data.success && data.errors) {
        errorDetails = '<ul class="mb-0 mt-2 small">';
        Object.keys(data.errors).forEach(key => {
            data.errors[key].forEach(error => {
                errorDetails += `<li>${error}</li>`;
            });
        });
        errorDetails += '</ul>';
    }
    
    alertDiv.className = `alert ${alertClass} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        <i class="fas fa-${icon} me-2"></i>
        <strong>${data.success ? 'Berhasil!' : 'Gagal!'}</strong> ${data.message}
        ${errorDetails}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    alertDiv.style.display = 'block';
    
    // Auto hide after 5 seconds for success, 10 seconds for error
    setTimeout(() => {
        alertDiv.style.display = 'none';
    }, data.success ? 5000 : 10000);
    
    // Scroll to alert
    alertDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}
</script>
@endpush
@endsection
