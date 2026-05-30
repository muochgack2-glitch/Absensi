{{-- 
    Icon Button Component - Inspired by eRapor8
    Button with only icon, no text
    
    Usage:
    <x-icon-button icon="fas fa-edit" variant="primary" />
    <x-icon-button icon="fas fa-trash" variant="danger" size="sm" />
    <x-icon-button icon="fas fa-plus" variant="success" rounded="true" />
--}}

@props([
    'icon' => 'fas fa-circle',
    'variant' => 'primary', // primary, secondary, success, danger, warning, info, dark, light
    'size' => 'md', // sm, md, lg
    'outline' => false,
    'rounded' => false,
    'loading' => false,
    'disabled' => false,
    'type' => 'button',
    'href' => null,
    'tooltip' => null,
    'class' => ''
])

@php
    $baseClass = 'icon-btn-modern';
    
    // Variant classes
    if ($outline) {
        $variantClass = 'icon-btn-outline-' . $variant;
    } else {
        $variantClass = 'icon-btn-' . $variant;
    }
    
    // Size classes
    $sizeClasses = [
        'sm' => 'icon-btn-sm',
        'md' => 'icon-btn-md',
        'lg' => 'icon-btn-lg'
    ];
    $sizeClass = $sizeClasses[$size] ?? 'icon-btn-md';
    
    // Additional classes
    $additionalClasses = [];
    if ($rounded) $additionalClasses[] = 'icon-btn-rounded';
    if ($loading) $additionalClasses[] = 'icon-btn-loading';
    if ($disabled || $loading) $additionalClasses[] = 'disabled';
    
    $classes = implode(' ', array_merge(
        [$baseClass, $variantClass, $sizeClass],
        $additionalClasses,
        [$class]
    ));
    
    $tag = $href ? 'a' : 'button';
    $attributes = $attributes->merge([
        'class' => $classes,
        'type' => $href ? null : $type,
        'href' => $href,
        'disabled' => ($disabled || $loading) && !$href ? true : null,
        'title' => $tooltip,
        'data-bs-toggle' => $tooltip ? 'tooltip' : null
    ]);
@endphp

<{{ $tag }} {{ $attributes }}>
    @if($loading)
        <i class="fas fa-circle-notch fa-spin"></i>
    @else
        <i class="{{ $icon }}"></i>
    @endif
</{{ $tag }}>

<style>
    /* Base Icon Button Styles */
    .icon-btn-modern {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-family: var(--font-sans);
        font-weight: var(--font-semibold);
        text-align: center;
        text-decoration: none;
        white-space: nowrap;
        vertical-align: middle;
        user-select: none;
        border: 2px solid transparent;
        transition: all var(--transition-fast);
        cursor: pointer;
        position: relative;
    }
    
    .icon-btn-modern:focus {
        outline: none;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.2);
    }
    
    .icon-btn-modern:disabled,
    .icon-btn-modern.disabled {
        opacity: 0.6;
        cursor: not-allowed;
        pointer-events: none;
    }
    
    /* Sizes */
    .icon-btn-sm {
        width: 32px;
        height: 32px;
        font-size: var(--text-sm);
        border-radius: var(--radius-md);
    }
    
    .icon-btn-md {
        width: 40px;
        height: 40px;
        font-size: var(--text-base);
        border-radius: var(--radius-lg);
    }
    
    .icon-btn-lg {
        width: 48px;
        height: 48px;
        font-size: var(--text-lg);
        border-radius: var(--radius-xl);
    }
    
    /* Rounded */
    .icon-btn-rounded {
        border-radius: var(--radius-full) !important;
    }
    
    /* Primary Variant */
    .icon-btn-primary {
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        color: #ffffff;
        border-color: transparent;
    }
    
    .icon-btn-primary:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(102, 126, 234, 0.3);
    }
    
    .icon-btn-outline-primary {
        background: transparent;
        color: var(--primary);
        border-color: var(--primary);
    }
    
    .icon-btn-outline-primary:hover:not(:disabled) {
        background: var(--primary);
        color: #ffffff;
    }
    
    /* Secondary Variant */
    .icon-btn-secondary {
        background: var(--gray-100);
        color: var(--text-primary);
        border-color: var(--gray-200);
    }
    
    .icon-btn-secondary:hover:not(:disabled) {
        background: var(--gray-200);
        transform: translateY(-2px);
    }
    
    .icon-btn-outline-secondary {
        background: transparent;
        color: var(--text-secondary);
        border-color: var(--gray-300);
    }
    
    .icon-btn-outline-secondary:hover:not(:disabled) {
        background: var(--gray-100);
    }
    
    /* Success Variant */
    .icon-btn-success {
        background: linear-gradient(135deg, var(--success), var(--success-dark));
        color: #ffffff;
        border-color: transparent;
    }
    
    .icon-btn-success:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(16, 185, 129, 0.3);
    }
    
    .icon-btn-outline-success {
        background: transparent;
        color: var(--success);
        border-color: var(--success);
    }
    
    .icon-btn-outline-success:hover:not(:disabled) {
        background: var(--success);
        color: #ffffff;
    }
    
    /* Danger Variant */
    .icon-btn-danger {
        background: linear-gradient(135deg, var(--danger), var(--danger-dark));
        color: #ffffff;
        border-color: transparent;
    }
    
    .icon-btn-danger:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(239, 68, 68, 0.3);
    }
    
    .icon-btn-outline-danger {
        background: transparent;
        color: var(--danger);
        border-color: var(--danger);
    }
    
    .icon-btn-outline-danger:hover:not(:disabled) {
        background: var(--danger);
        color: #ffffff;
    }
    
    /* Warning Variant */
    .icon-btn-warning {
        background: linear-gradient(135deg, var(--warning), var(--warning-dark));
        color: #ffffff;
        border-color: transparent;
    }
    
    .icon-btn-warning:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(245, 158, 11, 0.3);
    }
    
    .icon-btn-outline-warning {
        background: transparent;
        color: var(--warning-dark);
        border-color: var(--warning);
    }
    
    .icon-btn-outline-warning:hover:not(:disabled) {
        background: var(--warning);
        color: #ffffff;
    }
    
    /* Info Variant */
    .icon-btn-info {
        background: linear-gradient(135deg, var(--info), var(--info-dark));
        color: #ffffff;
        border-color: transparent;
    }
    
    .icon-btn-info:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(59, 130, 246, 0.3);
    }
    
    .icon-btn-outline-info {
        background: transparent;
        color: var(--info);
        border-color: var(--info);
    }
    
    .icon-btn-outline-info:hover:not(:disabled) {
        background: var(--info);
        color: #ffffff;
    }
    
    /* Dark Variant */
    .icon-btn-dark {
        background: var(--gray-800);
        color: #ffffff;
        border-color: var(--gray-800);
    }
    
    .icon-btn-dark:hover:not(:disabled) {
        background: var(--gray-900);
        transform: translateY(-2px);
    }
    
    .icon-btn-outline-dark {
        background: transparent;
        color: var(--gray-800);
        border-color: var(--gray-800);
    }
    
    .icon-btn-outline-dark:hover:not(:disabled) {
        background: var(--gray-800);
        color: #ffffff;
    }
    
    /* Light Variant */
    .icon-btn-light {
        background: #ffffff;
        color: var(--text-primary);
        border-color: var(--border-light);
    }
    
    .icon-btn-light:hover:not(:disabled) {
        background: var(--gray-50);
        transform: translateY(-2px);
    }
    
    .icon-btn-outline-light {
        background: transparent;
        color: var(--text-secondary);
        border-color: var(--border-light);
    }
    
    .icon-btn-outline-light:hover:not(:disabled) {
        background: #ffffff;
    }
</style>
