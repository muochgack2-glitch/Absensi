{{-- 
    Modern Checkbox Component - Inspired by eRapor8
    
    Usage:
    <x-checkbox name="agree" label="I agree to terms" />
    <x-checkbox name="subscribe" label="Subscribe to newsletter" checked="true" />
--}}

@props([
    'name' => '',
    'label' => '',
    'value' => '1',
    'checked' => false,
    'disabled' => false,
    'class' => ''
])

@php
    $isChecked = old($name, $checked);
@endphp

<div {{ $attributes->merge(['class' => 'checkbox-modern ' . $class]) }}>
    <input 
        type="checkbox" 
        name="{{ $name }}" 
        id="{{ $name }}"
        value="{{ $value }}"
        {{ $isChecked ? 'checked' : '' }}
        {{ $disabled ? 'disabled' : '' }}
        class="checkbox-input"
    >
    <label for="{{ $name }}" class="checkbox-label">
        <span class="checkbox-box">
            <i class="fas fa-check checkbox-icon"></i>
        </span>
        @if($label)
            <span class="checkbox-text">{{ $label }}</span>
        @endif
    </label>
</div>

<style>
    .checkbox-modern {
        display: inline-flex;
        align-items: flex-start;
        position: relative;
    }
    
    .checkbox-input {
        position: absolute;
        opacity: 0;
        cursor: pointer;
        height: 0;
        width: 0;
    }
    
    .checkbox-label {
        display: flex;
        align-items: center;
        gap: var(--space-3);
        cursor: pointer;
        user-select: none;
        margin: 0;
    }
    
    .checkbox-box {
        position: relative;
        width: 20px;
        height: 20px;
        border: 2px solid var(--border-medium);
        border-radius: var(--radius-sm);
        background: var(--bg-primary);
        transition: all var(--transition-fast);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    
    .checkbox-icon {
        font-size: 12px;
        color: #ffffff;
        opacity: 0;
        transform: scale(0);
        transition: all var(--transition-fast);
    }
    
    .checkbox-input:checked ~ .checkbox-label .checkbox-box {
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        border-color: var(--primary);
    }
    
    .checkbox-input:checked ~ .checkbox-label .checkbox-icon {
        opacity: 1;
        transform: scale(1);
    }
    
    .checkbox-input:focus ~ .checkbox-label .checkbox-box {
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.2);
    }
    
    .checkbox-input:disabled ~ .checkbox-label {
        opacity: 0.6;
        cursor: not-allowed;
    }
    
    .checkbox-input:disabled ~ .checkbox-label .checkbox-box {
        background: var(--gray-100);
    }
    
    .checkbox-text {
        font-size: var(--text-sm);
        color: var(--text-primary);
        line-height: 1.5;
    }
    
    .checkbox-label:hover .checkbox-box {
        border-color: var(--primary);
    }
</style>
