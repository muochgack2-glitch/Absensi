{{-- 
    Toast Container Component - Inspired by eRapor8
    Place this in your layout file (admin.blade.php)
    
    Usage in layout:
    <x-toast-container position="top-right" />
--}}

@props([
    'position' => 'top-right', // top-right, top-left, bottom-right, bottom-left, top-center, bottom-center
    'class' => ''
])

@php
    $positionClasses = [
        'top-right' => 'toast-top-right',
        'top-left' => 'toast-top-left',
        'bottom-right' => 'toast-bottom-right',
        'bottom-left' => 'toast-bottom-left',
        'top-center' => 'toast-top-center',
        'bottom-center' => 'toast-bottom-center',
    ];
    
    $positionClass = $positionClasses[$position] ?? 'toast-top-right';
@endphp

<div id="toast-container" class="toast-container {{ $positionClass }} {{ $class }}"></div>

<style>
    .toast-container {
        position: fixed;
        z-index: 9999;
        pointer-events: none;
        display: flex;
        flex-direction: column;
        gap: var(--space-3);
        max-width: 400px;
        width: 100%;
        padding: var(--space-4);
    }
    
    .toast-top-right {
        top: 0;
        right: 0;
    }
    
    .toast-top-left {
        top: 0;
        left: 0;
    }
    
    .toast-bottom-right {
        bottom: 0;
        right: 0;
    }
    
    .toast-bottom-left {
        bottom: 0;
        left: 0;
    }
    
    .toast-top-center {
        top: 0;
        left: 50%;
        transform: translateX(-50%);
    }
    
    .toast-bottom-center {
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
    }
    
    .toast-item {
        pointer-events: auto;
        background: #ffffff;
        color: #1e293b;
        border-radius: var(--radius-xl);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        padding: var(--space-4);
        display: flex;
        align-items: flex-start;
        gap: var(--space-3);
        border-left: 4px solid;
        animation: toastSlideIn 0.3s ease-out;
        position: relative;
        overflow: hidden;
    }
    
    /* Dark Mode Support for Toast */
    .admin-dark .toast-item {
        background: #1e293b !important;
        color: #e5e7eb !important;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.5) !important;
    }
    
    .admin-dark .toast-title {
        color: #f8fafc !important;
    }
    
    .admin-dark .toast-message {
        color: #cbd5e1 !important;
    }
    
    .admin-dark .toast-close {
        color: #94a3b8 !important;
    }
    
    .admin-dark .toast-close:hover {
        background: rgba(148, 163, 184, 0.2) !important;
        color: #f8fafc !important;
    }
    
    .toast-item::before {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: currentColor;
        animation: toastProgress var(--toast-duration, 5000ms) linear;
    }
    
    @keyframes toastSlideIn {
        from {
            opacity: 0;
            transform: translateX(100%);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
    
    @keyframes toastProgress {
        from {
            width: 100%;
        }
        to {
            width: 0%;
        }
    }
    
    .toast-item.toast-removing {
        animation: toastSlideOut 0.3s ease-out forwards;
    }
    
    @keyframes toastSlideOut {
        to {
            opacity: 0;
            transform: translateX(100%);
        }
    }
    
    .toast-icon {
        width: 40px;
        height: 40px;
        border-radius: var(--radius-lg);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        color: #ffffff;
        flex-shrink: 0;
    }
    
    .toast-content {
        flex: 1;
        min-width: 0;
    }
    
    .toast-title {
        font-size: var(--text-sm);
        font-weight: var(--font-bold);
        color: var(--text-primary);
        margin-bottom: var(--space-1);
        line-height: 1.4;
    }
    
    .toast-message {
        font-size: var(--text-sm);
        color: var(--text-secondary);
        line-height: 1.5;
    }
    
    .toast-close {
        background: none;
        border: none;
        color: var(--text-tertiary);
        cursor: pointer;
        padding: var(--space-1);
        border-radius: var(--radius-sm);
        transition: all var(--transition-fast);
        display: flex;
        align-items: center;
        justify-content: center;
        width: 24px;
        height: 24px;
        flex-shrink: 0;
    }
    
    .toast-close:hover {
        background: var(--gray-100);
        color: var(--text-primary);
    }
    
    /* Toast Types */
    .toast-success {
        border-left-color: var(--success);
        color: var(--success);
    }
    
    .toast-success .toast-icon {
        background: linear-gradient(135deg, var(--success), var(--success-dark));
    }
    
    .toast-error {
        border-left-color: var(--danger);
        color: var(--danger);
    }
    
    .toast-error .toast-icon {
        background: linear-gradient(135deg, var(--danger), var(--danger-dark));
    }
    
    .toast-warning {
        border-left-color: var(--warning);
        color: var(--warning);
    }
    
    .toast-warning .toast-icon {
        background: linear-gradient(135deg, var(--warning), var(--warning-dark));
    }
    
    .toast-info {
        border-left-color: var(--info);
        color: var(--info);
    }
    
    .toast-info .toast-icon {
        background: linear-gradient(135deg, var(--info), var(--info-dark));
    }
    
    /* Mobile Responsive */
    @media (max-width: 768px) {
        .toast-container {
            max-width: calc(100% - var(--space-4) * 2);
        }
    }
    
    /* Print: Hide toasts */
    @media print {
        .toast-container {
            display: none !important;
        }
    }
</style>

<script>
// Toast Notification System
window.Toast = {
    show: function(type, title, message, duration = 5000) {
        const container = document.getElementById('toast-container');
        if (!container) return;
        
        const icons = {
            success: 'fas fa-check-circle',
            error: 'fas fa-times-circle',
            warning: 'fas fa-exclamation-triangle',
            info: 'fas fa-info-circle'
        };
        
        const toast = document.createElement('div');
        toast.className = `toast-item toast-${type}`;
        toast.style.setProperty('--toast-duration', `${duration}ms`);
        
        toast.innerHTML = `
            <div class="toast-icon">
                <i class="${icons[type] || icons.info}"></i>
            </div>
            <div class="toast-content">
                ${title ? `<div class="toast-title">${title}</div>` : ''}
                <div class="toast-message">${message}</div>
            </div>
            <button class="toast-close" onclick="Toast.remove(this.parentElement)">
                <i class="fas fa-times"></i>
            </button>
        `;
        
        container.appendChild(toast);
        
        // Auto remove after duration
        if (duration > 0) {
            setTimeout(() => {
                this.remove(toast);
            }, duration);
        }
        
        return toast;
    },
    
    remove: function(toast) {
        if (!toast) return;
        toast.classList.add('toast-removing');
        setTimeout(() => {
            toast.remove();
        }, 300);
    },
    
    success: function(message, title = 'Success!', duration = 5000) {
        return this.show('success', title, message, duration);
    },
    
    error: function(message, title = 'Error!', duration = 5000) {
        return this.show('error', title, message, duration);
    },
    
    warning: function(message, title = 'Warning!', duration = 5000) {
        return this.show('warning', title, message, duration);
    },
    
    info: function(message, title = 'Info', duration = 5000) {
        return this.show('info', title, message, duration);
    }
};

// Laravel Session Flash Messages
document.addEventListener('DOMContentLoaded', function() {
    @if(session('success'))
        Toast.success('{{ session('success') }}');
    @endif
    
    @if(session('error'))
        Toast.error('{{ session('error') }}');
    @endif
    
    @if(session('warning'))
        Toast.warning('{{ session('warning') }}');
    @endif
    
    @if(session('info'))
        Toast.info('{{ session('info') }}');
    @endif
});
</script>
