{{-- 
    Modern Select Component - Inspired by eRapor8
    
    Usage:
    <x-select name="country" :options="$countries" />
    
    <x-select name="status">
        <option value="">Select Status</option>
        <option value="active">Active</option>
        <option value="inactive">Inactive</option>
    </x-select>
--}}

@props([
    'name' => '',
    'value' => '',
    'options' => [],
    'placeholder' => 'Select an option',
    'disabled' => false,
    'size' => 'md', // sm, md, lg
    'class' => ''
])

@php
    $hasError = $errors->has($name);
    
    $sizeClasses = [
        'sm' => 'select-sm',
        'md' => 'select-md',
        'lg' => 'select-lg'
    ];
    
    $classes = 'select-modern ' . ($sizeClasses[$size] ?? 'select-md');
    if ($hasError) $classes .= ' is-invalid';
    $classes .= ' ' . $class;
    
    $selectedValue = old($name, $value);
@endphp

<div class="select-wrapper-modern">
    <select 
        name="{{ $name }}"
        id="{{ $name }}"
        {{ $disabled ? 'disabled' : '' }}
        {{ $attributes->merge(['class' => $classes]) }}
    >
        @if($slot->isEmpty() && !empty($options))
            @if($placeholder)
                <option value="">{{ $placeholder }}</option>
            @endif
            
            @foreach($options as $optValue => $optLabel)
                <option value="{{ $optValue }}" {{ $selectedValue == $optValue ? 'selected' : '' }}>
                    {{ $optLabel }}
                </option>
            @endforeach
        @else
            {{ $slot }}
        @endif
    </select>
    
    <i class="fas fa-chevron-down select-icon"></i>
</div>

<style>
    .select-wrapper-modern {
        position: relative;
        display: inline-block;
        width: 100%;
    }
    
    .select-modern {
        width: 100%;
        padding: var(--space-3) var(--space-4);
        padding-right: calc(var(--space-4) * 2 + 16px);
        font-size: var(--text-sm);
        font-weight: var(--font-normal);
        line-height: 1.5;
        color: var(--text-primary);
        background-color: var(--bg-primary);
        border: 2px solid var(--border-light);
        border-radius: var(--radius-lg);
        transition: all var(--transition-fast);
        font-family: var(--font-sans);
        appearance: none;
        cursor: pointer;
    }
    
    .select-modern:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }
    
    .select-modern:disabled {
        background-color: var(--gray-100);
        cursor: not-allowed;
        opacity: 0.6;
    }
    
    .select-modern.is-invalid {
        border-color: var(--danger);
    }
    
    .select-modern.is-invalid:focus {
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
    }
    
    /* Sizes */
    .select-sm {
        padding: var(--space-2) var(--space-3);
        padding-right: calc(var(--space-3) * 2 + 16px);
        font-size: var(--text-xs);
        border-radius: var(--radius-md);
    }
    
    .select-md {
        padding: var(--space-3) var(--space-4);
        padding-right: calc(var(--space-4) * 2 + 16px);
        font-size: var(--text-sm);
        border-radius: var(--radius-lg);
    }
    
    .select-lg {
        padding: var(--space-4) var(--space-5);
        padding-right: calc(var(--space-5) * 2 + 16px);
        font-size: var(--text-base);
        border-radius: var(--radius-xl);
    }
    
    .select-icon {
        position: absolute;
        right: var(--space-4);
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-tertiary);
        font-size: 0.75em;
        pointer-events: none;
        transition: transform var(--transition-fast);
    }
    
    .select-modern:focus ~ .select-icon {
        color: var(--primary);
        transform: translateY(-50%) rotate(180deg);
    }
</style>
