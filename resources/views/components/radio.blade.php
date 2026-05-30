{{-- 
    Modern Radio Component - Inspired by eRapor8
    
    Usage:
    <x-radio name="gender" value="male" label="Male" />
    <x-radio name="gender" value="female" label="Female" checked="true" />
--}}

@props([
    'name' => '',
    'label' => '',
    'value' => '',
    'checked' => false,
    'disabled' => false,
    'class' => ''
])

@php
    $isChecked = old($name) == $value || (!old($name) && $checked);
@endphp

<div {{ $attributes->merge(['class' => 'radio-modern ' . $class]) }}>
    <input 
        type="radio" 
        name="{{ $name }}" 
        id="{{ $name }}_{{ $value }}"
        value="{{ $value }}"
        {{ $isChecked ? 'checked' : '' }}
        {{ $disabled ? 'disabled' : '' }}
        class="radio-input"
    >
    <label for="{{ $name }}_{{ $value }}" class="radio-label">
        <span class="radio-circle">
            <span class="radio-dot"></span>
        </span>
        @if($label)
            <span class="radio-text">{{ $label }}</span>
        @endif
    </label>
</div>

<style>
    .radio-modern {
        display: inline-flex;
        align-items: flex-start;
        position: relative;
    }
    
    .radio-input {
        position: absolute;
        opacity: 0;
        cursor: pointer;
        height: 0;
        width: 0;
    }
    
    .radio-label {
        display: flex;
        align-items: center;
        gap: var(--space-3);
        cursor: pointer;
        user-select: none;
        margin: 0;
    }
    
    .radio-circle {
        position: relative;
        width: 20px;
        height: 20px;
        border: 2px solid var(--border-medium);
        border-radius: var(--radius-full);
        background: var(--bg-primary);
        transition: all var(--transition-fast);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    
    .radio-dot {
        width: 10px;
        height: 10px;
        border-radius: var(--radius-full);
        background: #ffffff;
        opacity: 0;
        transform: scale(0);
        transition: all var(--transition-fast);
    }
    
    .radio-input:checked ~ .radio-label .radio-circle {
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        border-color: var(--primary);
    }
    
    .radio-input:checked ~ .radio-label .radio-dot {
        opacity: 1;
        transform: scale(1);
    }
    
    .radio-input:focus ~ .radio-label .radio-circle {
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.2);
    }
    
    .radio-input:disabled ~ .radio-label {
        opacity: 0.6;
        cursor: not-allowed;
    }
    
    .radio-input:disabled ~ .radio-label .radio-circle {
        background: var(--gray-100);
    }
    
    .radio-text {
        font-size: var(--text-sm);
        color: var(--text-primary);
        line-height: 1.5;
    }
    
    .radio-label:hover .radio-circle {
        border-color: var(--primary);
    }
</style>
