{{-- 
    Info Card Component - Inspired by eRapor8
    
    Usage:
    <x-info-card icon="fas fa-info-circle" title="Information" type="info">
        Your content here
    </x-info-card>
--}}

@props([
    'icon' => null,
    'title' => '',
    'type' => 'default', // default, info, success, warning, danger
    'dismissible' => false,
    'class' => ''
])

@php
    $typeClasses = [
        'default' => ['bg' => 'var(--gray-50)', 'border' => 'var(--gray-300)', 'icon' => 'var(--gray-600)', 'text' => 'var(--text-primary)'],
        'info' => ['bg' => 'var(--info-light)', 'border' => 'var(--info)', 'icon' => 'var(--info-dark)', 'text' => 'var(--info-dark)'],
        'success' => ['bg' => 'var(--success-light)', 'border' => 'var(--success)', 'icon' => 'var(--success-dark)', 'text' => 'var(--success-dark)'],
        'warning' => ['bg' => 'var(--warning-light)', 'border' => 'var(--warning)', 'icon' => 'var(--warning-dark)', 'text' => 'var(--warning-dark)'],
        'danger' => ['bg' => 'var(--danger-light)', 'border' => 'var(--danger)', 'icon' => 'var(--danger-dark)', 'text' => 'var(--danger-dark)'],
    ];
    
    $typeConfig = $typeClasses[$type] ?? $typeClasses['default'];
    
    $defaultIcons = [
        'info' => 'fas fa-info-circle',
        'success' => 'fas fa-check-circle',
        'warning' => 'fas fa-exclamation-triangle',
        'danger' => 'fas fa-exclamation-circle',
        'default' => 'fas fa-lightbulb'
    ];
    
    $displayIcon = $icon ?? $defaultIcons[$type];
@endphp

<div {{ $attributes->merge(['class' => 'info-card-modern ' . $class]) }} 
     style="background: {{ $typeConfig['bg'] }}; border-left-color: {{ $typeConfig['border'] }};">
    
    @if($dismissible)
        <button type="button" class="info-card-close" onclick="this.parentElement.remove()">
            <i class="fas fa-times"></i>
        </button>
    @endif
    
    <div class="info-card-header">
        @if($displayIcon)
            <div class="info-card-icon" style="color: {{ $typeConfig['icon'] }};">
                <i class="{{ $displayIcon }}"></i>
            </div>
        @endif
        
        @if($title)
            <div class="info-card-title" style="color: {{ $typeConfig['text'] }};">
                {{ $title }}
            </div>
        @endif
    </div>
    
    <div class="info-card-body" style="color: {{ $typeConfig['text'] }};">
        {{ $slot }}
    </div>
</div>

<style>
    .info-card-modern {
        position: relative;
        padding: var(--space-4);
        border-radius: var(--radius-lg);
        border-left: 4px solid;
        box-shadow: var(--shadow-sm);
        transition: var(--transition-base);
    }
    
    .info-card-modern:hover {
        box-shadow: var(--shadow-md);
    }
    
    .info-card-close {
        position: absolute;
        top: var(--space-3);
        right: var(--space-3);
        background: none;
        border: none;
        color: currentColor;
        opacity: 0.5;
        cursor: pointer;
        padding: var(--space-1);
        transition: var(--transition-fast);
    }
    
    .info-card-close:hover {
        opacity: 1;
    }
    
    .info-card-header {
        display: flex;
        align-items: center;
        gap: var(--space-3);
        margin-bottom: var(--space-2);
    }
    
    .info-card-icon {
        font-size: var(--text-xl);
        flex-shrink: 0;
    }
    
    .info-card-title {
        font-size: var(--text-base);
        font-weight: var(--font-bold);
        line-height: 1.4;
    }
    
    .info-card-body {
        font-size: var(--text-sm);
        line-height: 1.6;
        padding-left: calc(var(--text-xl) + var(--space-3));
    }
    
    .info-card-body p:last-child {
        margin-bottom: 0;
    }
</style>
