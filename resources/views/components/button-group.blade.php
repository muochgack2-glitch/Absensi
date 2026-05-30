{{-- 
    Button Group Component - Inspired by eRapor8
    Group multiple buttons together
    
    Usage:
    <x-button-group>
        <x-button variant="primary">Left</x-button>
        <x-button variant="primary">Middle</x-button>
        <x-button variant="primary">Right</x-button>
    </x-button-group>
    
    <x-button-group vertical="true">
        <x-button variant="secondary">Top</x-button>
        <x-button variant="secondary">Bottom</x-button>
    </x-button-group>
--}}

@props([
    'vertical' => false,
    'size' => 'md', // sm, md, lg
    'class' => ''
])

@php
    $baseClass = 'btn-group-modern';
    $directionClass = $vertical ? 'btn-group-vertical' : 'btn-group-horizontal';
    
    $sizeClasses = [
        'sm' => 'btn-group-sm',
        'md' => 'btn-group-md',
        'lg' => 'btn-group-lg'
    ];
    $sizeClass = $sizeClasses[$size] ?? 'btn-group-md';
    
    $classes = implode(' ', [$baseClass, $directionClass, $sizeClass, $class]);
@endphp

<div {{ $attributes->merge(['class' => $classes]) }} role="group">
    {{ $slot }}
</div>

<style>
    /* Base Button Group */
    .btn-group-modern {
        display: inline-flex;
        position: relative;
        vertical-align: middle;
    }
    
    /* Horizontal Group */
    .btn-group-horizontal {
        flex-direction: row;
    }
    
    .btn-group-horizontal > .btn-modern:not(:first-child),
    .btn-group-horizontal > .icon-btn-modern:not(:first-child) {
        margin-left: -2px;
    }
    
    .btn-group-horizontal > .btn-modern:not(:last-child),
    .btn-group-horizontal > .icon-btn-modern:not(:last-child) {
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
    }
    
    .btn-group-horizontal > .btn-modern:not(:first-child),
    .btn-group-horizontal > .icon-btn-modern:not(:first-child) {
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;
    }
    
    /* Vertical Group */
    .btn-group-vertical {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .btn-group-vertical > .btn-modern,
    .btn-group-vertical > .icon-btn-modern {
        width: 100%;
    }
    
    .btn-group-vertical > .btn-modern:not(:first-child),
    .btn-group-vertical > .icon-btn-modern:not(:first-child) {
        margin-top: -2px;
    }
    
    .btn-group-vertical > .btn-modern:not(:last-child),
    .btn-group-vertical > .icon-btn-modern:not(:last-child) {
        border-bottom-left-radius: 0;
        border-bottom-right-radius: 0;
    }
    
    .btn-group-vertical > .btn-modern:not(:first-child),
    .btn-group-vertical > .icon-btn-modern:not(:first-child) {
        border-top-left-radius: 0;
        border-top-right-radius: 0;
    }
    
    /* Hover effect - bring to front */
    .btn-group-modern > .btn-modern:hover,
    .btn-group-modern > .icon-btn-modern:hover,
    .btn-group-modern > .btn-modern:focus,
    .btn-group-modern > .icon-btn-modern:focus {
        z-index: 1;
    }
</style>
