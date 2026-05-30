{{-- 
    Modern Textarea Component - Inspired by eRapor8
    
    Usage:
    <x-textarea name="description" rows="4" placeholder="Enter description" />
--}}

@props([
    'name' => '',
    'value' => '',
    'placeholder' => '',
    'rows' => 4,
    'disabled' => false,
    'readonly' => false,
    'size' => 'md', // sm, md, lg
    'class' => ''
])

@php
    $hasError = $errors->has($name);
    
    $sizeClasses = [
        'sm' => 'textarea-sm',
        'md' => 'textarea-md',
        'lg' => 'textarea-lg'
    ];
    
    $classes = 'textarea-modern ' . ($sizeClasses[$size] ?? 'textarea-md');
    if ($hasError) $classes .= ' is-invalid';
    $classes .= ' ' . $class;
@endphp

<textarea 
    name="{{ $name }}"
    id="{{ $name }}"
    rows="{{ $rows }}"
    placeholder="{{ $placeholder }}"
    {{ $disabled ? 'disabled' : '' }}
    {{ $readonly ? 'readonly' : '' }}
    {{ $attributes->merge(['class' => $classes]) }}
>{{ old($name, $value) }}</textarea>

<style>
    .textarea-modern {
        width: 100%;
        padding: var(--space-3) var(--space-4);
        font-size: var(--text-sm);
        font-weight: var(--font-normal);
        line-height: 1.6;
        color: var(--text-primary);
        background-color: var(--bg-primary);
        border: 2px solid var(--border-light);
        border-radius: var(--radius-lg);
        transition: all var(--transition-fast);
        font-family: var(--font-sans);
        resize: vertical;
    }
    
    .textarea-modern:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }
    
    .textarea-modern::placeholder {
        color: var(--text-muted);
    }
    
    .textarea-modern:disabled {
        background-color: var(--gray-100);
        cursor: not-allowed;
        opacity: 0.6;
    }
    
    .textarea-modern:readonly {
        background-color: var(--gray-50);
    }
    
    .textarea-modern.is-invalid {
        border-color: var(--danger);
    }
    
    .textarea-modern.is-invalid:focus {
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
    }
    
    /* Sizes */
    .textarea-sm {
        padding: var(--space-2) var(--space-3);
        font-size: var(--text-xs);
        border-radius: var(--radius-md);
    }
    
    .textarea-md {
        padding: var(--space-3) var(--space-4);
        font-size: var(--text-sm);
        border-radius: var(--radius-lg);
    }
    
    .textarea-lg {
        padding: var(--space-4) var(--space-5);
        font-size: var(--text-base);
        border-radius: var(--radius-xl);
    }
</style>
