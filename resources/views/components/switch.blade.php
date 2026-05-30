{{-- 
    Modern Switch Component - Inspired by eRapor8
    
    Usage:
    <x-switch name="notifications" label="Enable Notifications" />
    <x-switch name="dark_mode" label="Dark Mode" checked="true" />
--}}

@props([
    'name' => '',
    'label' => '',
    'value' => '1',
    'checked' => false,
    'disabled' => false,
    'size' => 'md', // sm, md, lg
    'class' => ''
])

@php
    $isChecked = old($name, $checked);
    
    $sizeClasses = [
        'sm' => 'switch-sm',
        'md' => 'switch-md',
        'lg' => 'switch-lg'
    ];
    
    $sizeClass = $sizeClasses[$size] ?? 'switch-md';
@endphp

<div {{ $attributes->merge(['class' => 'switch-modern ' . $sizeClass . ' ' . $class]) }}>
    <input 
        type="checkbox" 
        name="{{ $name }}" 
        id="{{ $name }}"
        value="{{ $value }}"
        {{ $isChecked ? 'checked' : '' }}
        {{ $disabled ? 'disabled' : '' }}
        class="switch-input"
    >
    <label for="{{ $name }}" class="switch-label">
        <span class="switch-slider">
            <span class="switch-handle"></span>
        </span>
        @if($label)
            <span class="switch-text">{{ $label }}</span>
        @endif
    </label>
</div>

<style>
    .switch-modern {
        display: inline-flex;
        align-items: center;
        position: relative;
    }
    
    .switch-input {
        position: absolute;
        opacity: 0;
        cursor: pointer;
        height: 0;
        width: 0;
    }
    
    .switch-label {
        display: flex;
        align-items: center;
        gap: var(--space-3);
        cursor: pointer;
        user-select: none;
        margin: 0;
    }
    
    .switch-slider {
        position: relative;
        display: inline-block;
        width: 44px;
        height: 24px;
        background: var(--gray-300);
        border-radius: var(--radius-full);
        transition: all var(--transition-fast);
        flex-shrink: 0;
    }
    
    .switch-handle {
        position: absolute;
        top: 2px;
        left: 2px;
        width: 20px;
        height: 20px;
        background: #ffffff;
        border-radius: var(--radius-full);
        transition: all var(--transition-fast);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }
    
    .switch-input:checked ~ .switch-label .switch-slider {
        background: linear-gradient(135deg, var(--primary), var(--secondary));
    }
    
    .switch-input:checked ~ .switch-label .switch-handle {
        transform: translateX(20px);
    }
    
    .switch-input:focus ~ .switch-label .switch-slider {
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.2);
    }
    
    .switch-input:disabled ~ .switch-label {
        opacity: 0.6;
        cursor: not-allowed;
    }
    
    .switch-input:disabled ~ .switch-label .switch-slider {
        background: var(--gray-200);
    }
    
    .switch-text {
        font-size: var(--text-sm);
        color: var(--text-primary);
        font-weight: var(--font-medium);
    }
    
    /* Sizes */
    .switch-sm .switch-slider {
        width: 36px;
        height: 20px;
    }
    
    .switch-sm .switch-handle {
        width: 16px;
        height: 16px;
    }
    
    .switch-sm .switch-input:checked ~ .switch-label .switch-handle {
        transform: translateX(16px);
    }
    
    .switch-md .switch-slider {
        width: 44px;
        height: 24px;
    }
    
    .switch-md .switch-handle {
        width: 20px;
        height: 20px;
    }
    
    .switch-md .switch-input:checked ~ .switch-label .switch-handle {
        transform: translateX(20px);
    }
    
    .switch-lg .switch-slider {
        width: 52px;
        height: 28px;
    }
    
    .switch-lg .switch-handle {
        width: 24px;
        height: 24px;
    }
    
    .switch-lg .switch-input:checked ~ .switch-label .switch-handle {
        transform: translateX(24px);
    }
</style>
