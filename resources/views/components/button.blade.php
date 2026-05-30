{{-- 
    Modern Button Component - Inspired by eRapor8
    
    Usage:
    <x-button>Click Me</x-button>
    <x-button variant="primary" size="lg">Large Button</x-button>
    <x-button variant="success" icon="fas fa-check">Save</x-button>
    <x-button variant="danger" outline="true">Delete</x-button>
--}}

@props([
    'variant' => 'primary', // primary, secondary, success, danger, warning, info, dark, light
    'size' => 'md', // sm, md, lg
    'outline' => false,
    'icon' => null,
    'iconRight' => null,
    'loading' => false,
    'disabled' => false,
    'block' => false,
    'rounded' => false,
    'type' => 'button',
    'href' => null,
    'class' => ''
])

@php
    $baseClass = 'btn-modern';
    
    // Variant classes
    if ($outline) {
        $variantClass = 'btn-outline-' . $variant;
    } else {
        $variantClass = 'btn-' . $variant;
    }
    
    // Size classes
    $sizeClasses = [
        'sm' => 'btn-sm',
        'md' => 'btn-md',
        'lg' => 'btn-lg'
    ];
    $sizeClass = $sizeClasses[$size] ?? 'btn-md';
    
    // Additional classes
    $additionalClasses = [];
    if ($block) $additionalClasses[] = 'btn-block';
    if ($rounded) $additionalClasses[] = 'btn-rounded';
    if ($loading) $additionalClasses[] = 'btn-loading';
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
        'disabled' => ($disabled || $loading) && !$href ? true : null
    ]);
@endphp

<{{ $tag }} {{ $attributes }}>
    @if($loading)
        <span class="btn-spinner">
            <i class="fas fa-circle-notch fa-spin"></i>
        </span>
    @endif
    
    @if($icon && !$loading)
        <i class="{{ $icon }} btn-icon-left"></i>
    @endif
    
    <span class="btn-text">{{ $slot }}</span>
    
    @if($iconRight && !$loading)
        <i class="{{ $iconRight }} btn-icon-right"></i>
    @endif
</{{ $tag }}>

<style>
    /* Base Button Styles */
    .btn-modern {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: var(--space-2);
        font-family: var(--font-sans);
        font-weight: var(--font-semibold);
        line-height: 1.5;
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
    
    .btn-modern:focus {
        outline: none;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.2);
    }
    
    .btn-modern:disabled,
    .btn-modern.disabled {
        opacity: 0.6;
        cursor: not-allowed;
        pointer-events: none;
    }
    
    /* Sizes */
    .btn-sm {
        padding: var(--space-2) var(--space-4);
        font-size: var(--text-sm);
        border-radius: var(--radius-md);
    }
    
    .btn-md {
        padding: var(--space-3) var(--space-6);
        font-size: var(--text-base);
        border-radius: var(--radius-lg);
    }
    
    .btn-lg {
        padding: var(--space-4) var(--space-8);
        font-size: var(--text-lg);
        border-radius: var(--radius-xl);
    }
    
    /* Block */
    .btn-block {
        display: flex;
        width: 100%;
    }
    
    /* Rounded */
    .btn-rounded {
        border-radius: var(--radius-full);
    }
    
    /* Loading State */
    .btn-loading .btn-text {
        opacity: 0.7;
    }
    
    .btn-spinner {
        display: inline-flex;
        align-items: center;
    }
    
    /* Icon Spacing */
    .btn-icon-left {
        margin-right: calc(var(--space-2) * -0.5);
    }
    
    .btn-icon-right {
        margin-left: calc(var(--space-2) * -0.5);
    }
    
    /* Primary Variant */
    .btn-primary {
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        color: #ffffff;
        border-color: transparent;
    }
    
    .btn-primary:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(102, 126, 234, 0.3);
    }
    
    .btn-primary:active:not(:disabled) {
        transform: translateY(0);
    }
    
    .btn-outline-primary {
        background: transparent;
        color: var(--primary);
        border-color: var(--primary);
    }
    
    .btn-outline-primary:hover:not(:disabled) {
        background: var(--primary);
        color: #ffffff;
    }
    
    /* Secondary Variant */
    .btn-secondary {
        background: var(--gray-100);
        color: var(--text-primary);
        border-color: var(--gray-200);
    }
    
    .btn-secondary:hover:not(:disabled) {
        background: var(--gray-200);
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }
    
    .btn-outline-secondary {
        background: transparent;
        color: var(--text-secondary);
        border-color: var(--gray-300);
    }
    
    .btn-outline-secondary:hover:not(:disabled) {
        background: var(--gray-100);
        color: var(--text-primary);
    }
    
    /* Success Variant */
    .btn-success {
        background: linear-gradient(135deg, var(--success), var(--success-dark));
        color: #ffffff;
        border-color: transparent;
    }
    
    .btn-success:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(16, 185, 129, 0.3);
    }
    
    .btn-outline-success {
        background: transparent;
        color: var(--success);
        border-color: var(--success);
    }
    
    .btn-outline-success:hover:not(:disabled) {
        background: var(--success);
        color: #ffffff;
    }
    
    /* Danger Variant */
    .btn-danger {
        background: linear-gradient(135deg, var(--danger), var(--danger-dark));
        color: #ffffff;
        border-color: transparent;
    }
    
    .btn-danger:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(239, 68, 68, 0.3);
    }
    
    .btn-outline-danger {
        background: transparent;
        color: var(--danger);
        border-color: var(--danger);
    }
    
    .btn-outline-danger:hover:not(:disabled) {
        background: var(--danger);
        color: #ffffff;
    }
    
    /* Warning Variant */
    .btn-warning {
        background: linear-gradient(135deg, var(--warning), var(--warning-dark));
        color: #ffffff;
        border-color: transparent;
    }
    
    .btn-warning:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(245, 158, 11, 0.3);
    }
    
    .btn-outline-warning {
        background: transparent;
        color: var(--warning-dark);
        border-color: var(--warning);
    }
    
    .btn-outline-warning:hover:not(:disabled) {
        background: var(--warning);
        color: #ffffff;
    }
    
    /* Info Variant */
    .btn-info {
        background: linear-gradient(135deg, var(--info), var(--info-dark));
        color: #ffffff;
        border-color: transparent;
    }
    
    .btn-info:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(59, 130, 246, 0.3);
    }
    
    .btn-outline-info {
        background: transparent;
        color: var(--info);
        border-color: var(--info);
    }
    
    .btn-outline-info:hover:not(:disabled) {
        background: var(--info);
        color: #ffffff;
    }
    
    /* Dark Variant */
    .btn-dark {
        background: var(--gray-800);
        color: #ffffff;
        border-color: var(--gray-800);
    }
    
    .btn-dark:hover:not(:disabled) {
        background: var(--gray-900);
        transform: translateY(-2px);
        box-shadow: var(--shadow-lg);
    }
    
    .btn-outline-dark {
        background: transparent;
        color: var(--gray-800);
        border-color: var(--gray-800);
    }
    
    .btn-outline-dark:hover:not(:disabled) {
        background: var(--gray-800);
        color: #ffffff;
    }
    
    /* Light Variant */
    .btn-light {
        background: #ffffff;
        color: var(--text-primary);
        border-color: var(--border-light);
    }
    
    .btn-light:hover:not(:disabled) {
        background: var(--gray-50);
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }
    
    .btn-outline-light {
        background: transparent;
        color: var(--text-secondary);
        border-color: var(--border-light);
    }
    
    .btn-outline-light:hover:not(:disabled) {
        background: #ffffff;
        color: var(--text-primary);
    }
</style>
