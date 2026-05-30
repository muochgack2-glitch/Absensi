{{-- 
    Form Group Component - Inspired by eRapor8
    
    Usage:
    <x-form-group label="Email" name="email" required="true">
        <input type="email" name="email" class="form-control" />
    </x-form-group>
--}}

@props([
    'label' => '',
    'name' => '',
    'required' => false,
    'error' => null,
    'help' => null,
    'class' => ''
])

@php
    $errorMessage = $error ?? ($errors->has($name) ? $errors->first($name) : null);
    $hasError = !is_null($errorMessage);
@endphp

<div {{ $attributes->merge(['class' => 'form-group-modern ' . $class]) }}>
    @if($label)
        <label for="{{ $name }}" class="form-label-modern">
            {{ $label }}
            @if($required)
                <span class="text-danger">*</span>
            @endif
        </label>
    @endif
    
    <div class="form-input-wrapper {{ $hasError ? 'has-error' : '' }}">
        {{ $slot }}
    </div>
    
    @if($hasError)
        <div class="form-error-message">
            <i class="fas fa-exclamation-circle"></i>
            {{ $errorMessage }}
        </div>
    @endif
    
    @if($help && !$hasError)
        <div class="form-help-text">
            <i class="fas fa-info-circle"></i>
            {{ $help }}
        </div>
    @endif
</div>

<style>
    .form-group-modern {
        margin-bottom: var(--space-5);
    }
    
    .form-label-modern {
        display: block;
        font-size: var(--text-sm);
        font-weight: var(--font-semibold);
        color: var(--text-primary);
        margin-bottom: var(--space-2);
        line-height: 1.5;
    }
    
    .form-input-wrapper {
        position: relative;
    }
    
    .form-input-wrapper.has-error input,
    .form-input-wrapper.has-error select,
    .form-input-wrapper.has-error textarea {
        border-color: var(--danger) !important;
    }
    
    .form-input-wrapper.has-error input:focus,
    .form-input-wrapper.has-error select:focus,
    .form-input-wrapper.has-error textarea:focus {
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1) !important;
    }
    
    .form-error-message {
        display: flex;
        align-items: center;
        gap: var(--space-2);
        margin-top: var(--space-2);
        font-size: var(--text-sm);
        color: var(--danger);
        font-weight: var(--font-medium);
    }
    
    .form-help-text {
        display: flex;
        align-items: center;
        gap: var(--space-2);
        margin-top: var(--space-2);
        font-size: var(--text-xs);
        color: var(--text-tertiary);
    }
</style>
