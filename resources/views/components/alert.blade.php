{{-- 
    Modern Alert Component - Inspired by eRapor8
    
    Usage:
    <x-alert type="success" title="Success!">Operation completed successfully.</x-alert>
    <x-alert type="danger" dismissible="true">An error occurred.</x-alert>
--}}

@props([
    'type' => 'info', // info, success, warning, danger
    'title' => null,
    'icon' => null,
    'dismissible' => false,
    'class' => ''
])

@php
    $typeConfig = [
        'info' => [
            'bg' => 'var(--info-light)',
            'border' => 'var(--info)',
            'text' => 'var(--info-dark)',
            'icon' => 'fas fa-info-circle'
        ],
        'success' => [
            'bg' => 'var(--success-light)',
            'border' => 'var(--success)',
            'text' => 'var(--success-dark)',
            'icon' => 'fas fa-check-circle'
        ],
        'warning' => [
            'bg' => 'var(--warning-light)',
            'border' => 'var(--warning)',
            'text' => 'var(--warning-dark)',
            'icon' => 'fas fa-exclamation-triangle'
        ],
        'danger' => [
            'bg' => 'var(--danger-light)',
            'border' => 'var(--danger)',
            'text' => 'var(--danger-dark)',
            'icon' => 'fas fa-exclamation-circle'
        ],
    ];
    
    $config = $typeConfig[$type] ?? $typeConfig['info'];
    $displayIcon = $icon ?? $config['icon'];
@endphp

<div {{ $attributes->merge(['class' => 'alert-modern alert-' . $type . ' ' . $class]) }}
     style="background: {{ $config['bg'] }}; border-left-color: {{ $config['border'] }}; color: {{ $config['text'] }};"
     role="alert">
    
    @if($dismissible)
        <button type="button" class="alert-close" onclick="this.parentElement.remove()" aria-label="Close">
            <i class="fas fa-times"></i>
        </button>
    @endif
    
    <div class="alert-content">
        <div class="alert-icon" style="color: {{ $config['border'] }};">
            <i class="{{ $displayIcon }}"></i>
        </div>
        
        <div class="alert-body">
            @if($title)
                <div class="alert-title">{{ $title }}</div>
            @endif
            <div class="alert-message">{{ $slot }}</div>
        </div>
    </div>
</div>

<style>
    .alert-modern {
        position: relative;
        padding: var(--space-4);
        border-radius: var(--radius-lg);
        border-left: 4px solid;
        box-shadow: var(--shadow-sm);
        margin-bottom: var(--space-4);
        animation: slideInDown 0.3s ease-out;
    }
    
    @keyframes slideInDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .alert-content {
        display: flex;
        gap: var(--space-3);
        align-items: flex-start;
    }
    
    .alert-icon {
        font-size: var(--text-xl);
        flex-shrink: 0;
        margin-top: 2px;
    }
    
    .alert-body {
        flex: 1;
        min-width: 0;
    }
    
    .alert-title {
        font-size: var(--text-base);
        font-weight: var(--font-bold);
        margin-bottom: var(--space-1);
        line-height: 1.4;
    }
    
    .alert-message {
        font-size: var(--text-sm);
        line-height: 1.6;
    }
    
    .alert-message p:last-child {
        margin-bottom: 0;
    }
    
    .alert-close {
        position: absolute;
        top: var(--space-3);
        right: var(--space-3);
        background: none;
        border: none;
        color: currentColor;
        opacity: 0.5;
        cursor: pointer;
        padding: var(--space-1);
        border-radius: var(--radius-sm);
        transition: all var(--transition-fast);
        display: flex;
        align-items: center;
        justify-content: center;
        width: 24px;
        height: 24px;
    }
    
    .alert-close:hover {
        opacity: 1;
        background: rgba(0, 0, 0, 0.1);
    }
    
    /* Print: Hide alerts */
    @media print {
        .alert-modern {
            display: none !important;
        }
    }
</style>
