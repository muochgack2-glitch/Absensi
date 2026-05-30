{{-- 
    Modern Input Component - Inspired by eRapor8
    
    Usage:
    <x-input name="email" type="email" placeholder="Enter email" />
    <x-input name="search" icon="fas fa-search" />
    <x-input name="password" type="password" iconRight="fas fa-eye" />
--}}

@props([
    'type' => 'text',
    'name' => '',
    'value' => '',
    'placeholder' => '',
    'icon' => null,
    'iconRight' => null,
    'disabled' => false,
    'readonly' => false,
    'size' => 'md', // sm, md, lg
    'class' => ''
])

@php
    $hasError = $errors->has($name);
    
    $sizeClasses = [
        'sm' => 'input-sm',
        'md' => 'input-md',
        'lg' => 'input-lg'
    ];
    
    $classes = 'input-modern ' . ($sizeClasses[$size] ?? 'input-md');
    if ($icon) $classes .= ' has-icon-left';
    if ($iconRight) $classes .= ' has-icon-right';
    if ($hasError) $classes .= ' is-invalid';
    $classes .= ' ' . $class;
@endphp

<div class="input-wrapper-modern">
    @if($icon)
        <i class="{{ $icon }} input-icon-left"></i>
    @endif
    
    <input 
        type="{{ $type }}"
        name="{{ $name }}"
        id="{{ $name }}"
        value="{{ old($name, $value) }}"
        placeholder="{{ $placeholder }}"
        {{ $disabled ? 'disabled' : '' }}
        {{ $readonly ? 'readonly' : '' }}
        {{ $attributes->merge(['class' => $classes]) }}
    >
    
    @if($iconRight)
        <i class="{{ $iconRight }} input-icon-right"></i>
    @endif
</div>

<style>
    .input-wrapper-modern {
        position: relative;
        display: flex;
        align-items: center;
    }
    
    .input-modern {
        width: 100%;
        padding: var(--space-3) var(--space-4);
        font-size: var(--text-sm);
        font-weight: var(--font-normal);
        line-height: 1.5;
        color: var(--text-primary);
        background-color: var(--bg-primary);
        border: 2px solid var(--border-light);
        border-radius: var(--radius-lg);
        transition: all var(--transition-fast);
        font-family: var(--font-sans);
    }
    
    .input-modern:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }
    
    .input-modern::placeholder {
        color: var(--text-muted);
    }
    
    .input-modern:disabled {
        background-color: var(--gray-100);
        cursor: not-allowed;
        opacity: 0.6;
    }
    
    .input-modern:readonly {
        background-color: var(--gray-50);
    }
    
    .input-modern.is-invalid {
        border-color: var(--danger);
    }
    
    .input-modern.is-invalid:focus {
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
    }
    
    /* Sizes */
    .input-sm {
        padding: var(--space-2) var(--space-3);
        font-size: var(--text-xs);
        border-radius: var(--radius-md);
    }
    
    .input-md {
        padding: var(--space-3) var(--space-4);
        font-size: var(--text-sm);
        border-radius: var(--radius-lg);
    }
    
    .input-lg {
        padding: var(--space-4) var(--space-5);
        font-size: var(--text-base);
        border-radius: var(--radius-xl);
    }
    
    /* With Icons */
    .input-modern.has-icon-left {
        padding-left: calc(var(--space-4) * 2 + 16px);
    }
    
    .input-modern.has-icon-right {
        padding-right: calc(var(--space-4) * 2 + 16px);
    }
    
    .input-icon-left,
    .input-icon-right {
        position: absolute;
        color: var(--text-tertiary);
        font-size: var(--text-sm);
        pointer-events: none;
    }
    
    .input-icon-left {
        left: var(--space-4);
    }
    
    .input-icon-right {
        right: var(--space-4);
    }
</style>
