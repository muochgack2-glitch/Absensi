{{-- 
    Empty State Component - Inspired by eRapor8
    
    Usage:
    <x-empty-state icon="fas fa-inbox" message="No data available" />
    
    <x-empty-state icon="fas fa-search" message="No results found">
        <x-slot:action>
            <button class="btn btn-primary">Try Again</button>
        </x-slot:action>
    </x-empty-state>
--}}

@props([
    'icon' => 'fas fa-inbox',
    'message' => 'No data available',
    'description' => null,
    'action' => null,
    'size' => 'md', // sm, md, lg
    'class' => ''
])

@php
    $sizeClasses = [
        'sm' => ['padding' => 'py-4', 'icon' => 'text-3xl', 'message' => 'text-sm'],
        'md' => ['padding' => 'py-5', 'icon' => 'text-5xl', 'message' => 'text-base'],
        'lg' => ['padding' => 'py-6', 'icon' => 'text-6xl', 'message' => 'text-lg']
    ];
    
    $sizeConfig = $sizeClasses[$size] ?? $sizeClasses['md'];
@endphp

<div {{ $attributes->merge(['class' => 'empty-state-modern ' . $sizeConfig['padding'] . ' ' . $class]) }}>
    <div class="empty-state-icon {{ $sizeConfig['icon'] }}">
        <i class="{{ $icon }}"></i>
    </div>
    
    <div class="empty-state-message {{ $sizeConfig['message'] }}">
        {{ $message }}
    </div>
    
    @if($description)
        <div class="empty-state-description">
            {{ $description }}
        </div>
    @endif
    
    @if($action)
        <div class="empty-state-action">
            {{ $action }}
        </div>
    @endif
</div>

<style>
    .empty-state-modern {
        text-align: center;
        padding: var(--space-12) var(--space-6);
        color: var(--text-tertiary);
    }
    
    .empty-state-icon {
        margin-bottom: var(--space-4);
        opacity: 0.4;
        color: var(--text-muted);
    }
    
    .empty-state-message {
        font-weight: var(--font-semibold);
        color: var(--text-secondary);
        margin-bottom: var(--space-2);
    }
    
    .empty-state-description {
        font-size: var(--text-sm);
        color: var(--text-tertiary);
        max-width: 400px;
        margin: 0 auto var(--space-4);
        line-height: 1.6;
    }
    
    .empty-state-action {
        margin-top: var(--space-4);
    }
</style>
