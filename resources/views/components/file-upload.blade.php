{{-- 
    Modern File Upload Component - Inspired by eRapor8
    
    Usage:
    <x-file-upload name="avatar" accept="image/*" />
    <x-file-upload name="document" label="Upload Document" help="Max 2MB" />
--}}

@props([
    'name' => '',
    'accept' => '',
    'multiple' => false,
    'disabled' => false,
    'preview' => true,
    'class' => ''
])

@php
    $hasError = $errors->has($name);
    $uniqueId = 'file_' . $name . '_' . uniqid();
@endphp

<div {{ $attributes->merge(['class' => 'file-upload-modern ' . $class]) }}>
    <input 
        type="file" 
        name="{{ $name }}{{ $multiple ? '[]' : '' }}" 
        id="{{ $uniqueId }}"
        accept="{{ $accept }}"
        {{ $multiple ? 'multiple' : '' }}
        {{ $disabled ? 'disabled' : '' }}
        class="file-input"
        onchange="handleFileSelect(this, '{{ $uniqueId }}')"
    >
    
    <label for="{{ $uniqueId }}" class="file-label {{ $hasError ? 'has-error' : '' }}">
        <div class="file-icon">
            <i class="fas fa-cloud-upload-alt"></i>
        </div>
        <div class="file-text">
            <span class="file-title">Click to upload or drag and drop</span>
            <span class="file-subtitle">
                @if($accept)
                    {{ str_replace(['image/*', 'application/pdf', '.doc,.docx'], ['Images', 'PDF', 'Documents'], $accept) }}
                @else
                    Any file type
                @endif
            </span>
        </div>
    </label>
    
    @if($preview)
        <div id="{{ $uniqueId }}_preview" class="file-preview" style="display: none;">
            <div class="file-preview-list"></div>
        </div>
    @endif
</div>

<style>
    .file-upload-modern {
        width: 100%;
    }
    
    .file-input {
        display: none;
    }
    
    .file-label {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: var(--space-3);
        padding: var(--space-8);
        border: 2px dashed var(--border-medium);
        border-radius: var(--radius-xl);
        background: var(--bg-secondary);
        cursor: pointer;
        transition: all var(--transition-fast);
        text-align: center;
    }
    
    .file-label:hover {
        border-color: var(--primary);
        background: var(--bg-primary);
    }
    
    .file-label.has-error {
        border-color: var(--danger);
    }
    
    .file-icon {
        width: 64px;
        height: 64px;
        border-radius: var(--radius-full);
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
    }
    
    .file-text {
        display: flex;
        flex-direction: column;
        gap: var(--space-1);
    }
    
    .file-title {
        font-size: var(--text-base);
        font-weight: var(--font-semibold);
        color: var(--text-primary);
    }
    
    .file-subtitle {
        font-size: var(--text-sm);
        color: var(--text-tertiary);
    }
    
    .file-preview {
        margin-top: var(--space-4);
    }
    
    .file-preview-list {
        display: flex;
        flex-direction: column;
        gap: var(--space-2);
    }
    
    .file-preview-item {
        display: flex;
        align-items: center;
        gap: var(--space-3);
        padding: var(--space-3);
        background: var(--bg-secondary);
        border-radius: var(--radius-lg);
        border: 1px solid var(--border-light);
    }
    
    .file-preview-icon {
        width: 40px;
        height: 40px;
        border-radius: var(--radius-md);
        background: var(--primary);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    
    .file-preview-info {
        flex: 1;
        min-width: 0;
    }
    
    .file-preview-name {
        font-size: var(--text-sm);
        font-weight: var(--font-semibold);
        color: var(--text-primary);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    .file-preview-size {
        font-size: var(--text-xs);
        color: var(--text-tertiary);
    }
    
    .file-preview-remove {
        background: none;
        border: none;
        color: var(--danger);
        cursor: pointer;
        padding: var(--space-2);
        border-radius: var(--radius-md);
        transition: all var(--transition-fast);
    }
    
    .file-preview-remove:hover {
        background: var(--danger-light);
    }
</style>

<script>
function handleFileSelect(input, id) {
    const previewContainer = document.getElementById(id + '_preview');
    const previewList = previewContainer?.querySelector('.file-preview-list');
    
    if (!previewList) return;
    
    previewList.innerHTML = '';
    
    if (input.files && input.files.length > 0) {
        previewContainer.style.display = 'block';
        
        Array.from(input.files).forEach((file, index) => {
            const item = document.createElement('div');
            item.className = 'file-preview-item';
            
            const icon = file.type.startsWith('image/') ? 'fa-image' : 
                        file.type === 'application/pdf' ? 'fa-file-pdf' : 
                        'fa-file';
            
            const size = (file.size / 1024).toFixed(2) + ' KB';
            
            item.innerHTML = `
                <div class="file-preview-icon">
                    <i class="fas ${icon}"></i>
                </div>
                <div class="file-preview-info">
                    <div class="file-preview-name">${file.name}</div>
                    <div class="file-preview-size">${size}</div>
                </div>
                <button type="button" class="file-preview-remove" onclick="removeFile('${id}', ${index})">
                    <i class="fas fa-times"></i>
                </button>
            `;
            
            previewList.appendChild(item);
        });
    } else {
        previewContainer.style.display = 'none';
    }
}

function removeFile(id, index) {
    const input = document.getElementById(id);
    const dt = new DataTransfer();
    
    Array.from(input.files).forEach((file, i) => {
        if (i !== index) dt.items.add(file);
    });
    
    input.files = dt.files;
    handleFileSelect(input, id);
}
</script>
