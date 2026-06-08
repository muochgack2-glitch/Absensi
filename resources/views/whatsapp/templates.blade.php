@extends('layouts.admin')

@section('title', 'Template Pesan WhatsApp')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">📝 Template Pesan WhatsApp</h1>
            <p class="text-muted mb-0">Kelola template pesan untuk pengiriman otomatis</p>
        </div>
        <div>
            <a href="{{ route('whatsapp.templates.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Buat Template
            </a>
        </div>
    </div>

    <!-- Templates Grid -->
    <div class="row">
        @forelse($templates as $template)
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h5 class="card-title mb-1" style="color: var(--text-primary);">{{ $template->label }}</h5>
                            <span class="badge bg-{{ $template->type_color }}">{{ $template->type_label }}</span>
                            @if($template->auto_send)
                            <span class="badge bg-info">Auto Send</span>
                            @endif
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="status_{{ $template->id }}" {{ $template->is_active ? 'checked' : '' }} onchange="toggleStatus({{ $template->id }}, this.checked)">
                        </div>
                    </div>
                    
                    @if($template->description)
                    <p class="text-muted small mb-3">{{ $template->description }}</p>
                    @endif
                    
                    <div class="border rounded p-2 mb-3" style="max-height: 150px; overflow-y: auto; background: var(--bg-secondary); color: var(--text-primary);">
                        <small style="white-space: pre-wrap;">{{ Str::limit($template->message, 200) }}</small>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center text-muted small mb-3">
                        <span>
                            <i class="fas fa-chart-line me-1"></i>
                            Digunakan: {{ $template->usage_count }}x
                        </span>
                        @if($template->last_used_at)
                        <span>
                            <i class="fas fa-clock me-1"></i>
                            {{ $template->last_used_at->diffForHumans() }}
                        </span>
                        @endif
                    </div>
                    
                    <div class="btn-group w-100" role="group">
                        <button class="btn btn-sm btn-outline-primary" onclick="previewTemplate({{ $template->id }})" title="Preview">
                            <i class="fas fa-eye"></i>
                        </button>
                        <a href="{{ route('whatsapp.templates.edit', $template->id) }}" class="btn btn-sm btn-outline-secondary" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button class="btn btn-sm btn-outline-danger" onclick="deleteTemplate({{ $template->id }}, '{{ $template->label }}')" title="Hapus">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                    <p class="text-muted mb-3">Belum ada template pesan</p>
                    <a href="{{ route('whatsapp.templates.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Buat Template Pertama
                    </a>
                </div>
            </div>
        </div>
        @endforelse
    </div>
</div>

<!-- Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-eye me-2"></i>Preview Template
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="previewContent">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Form -->
<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

@push('scripts')
<style>
/* Message Preview Styling */
.message-preview {
    background-color: #f8f9fa;
    color: #212529;
    border-color: #dee2e6 !important;
}

/* Dark Mode Support */
.admin-dark .message-preview {
    background-color: #1e293b !important;
    color: #e5e7eb !important;
    border-color: #334155 !important;
}
</style>
<script>
function toggleStatus(templateId, isActive) {
    // In real implementation, you would make an AJAX call to update status
    console.log('Toggle template', templateId, 'to', isActive);
    
    // For now, just show a toast
    const message = isActive ? 'Template diaktifkan' : 'Template dinonaktifkan';
    showToast(message, 'success');
}

function previewTemplate(templateId) {
    const modal = new bootstrap.Modal(document.getElementById('previewModal'));
    modal.show();
    
    fetch(`{{ url('whatsapp/templates') }}/${templateId}/preview`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('previewContent').innerHTML = `
                    <div class="alert alert-info">
                        <strong>Preview dengan data sample:</strong>
                    </div>
                    <div class="border rounded p-3 message-preview" style="white-space: pre-wrap; font-family: monospace;">
                        ${data.preview}
                    </div>
                `;
            } else {
                document.getElementById('previewContent').innerHTML = `
                    <div class="alert alert-danger">
                        Gagal memuat preview
                    </div>
                `;
            }
        })
        .catch(error => {
            document.getElementById('previewContent').innerHTML = `
                <div class="alert alert-danger">
                    Error: ${error.message}
                </div>
            `;
        });
}

function deleteTemplate(templateId, templateName) {
    if (confirm(`Apakah Anda yakin ingin menghapus template "${templateName}"?`)) {
        const form = document.getElementById('deleteForm');
        form.action = `{{ url('whatsapp/templates') }}/${templateId}`;
        form.submit();
    }
}

function showToast(message, type = 'info') {
    // Simple toast notification
    const toast = document.createElement('div');
    toast.className = `alert alert-${type} position-fixed top-0 end-0 m-3`;
    toast.style.zIndex = '9999';
    toast.innerHTML = message;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.remove();
    }, 3000);
}
</script>
@endpush
@endsection
