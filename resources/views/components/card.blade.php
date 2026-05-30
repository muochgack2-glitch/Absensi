{{-- 
    Modern Card Component - Inspired by eRapor8
    
    Usage:
    <x-card>Content here</x-card>
    <x-card hover="true" shadow="lg">Hover effect</x-card>
    <x-card padding="sm">Small padding</x-card>
--}}

@props([
    'hover' => false,
    'shadow' => 'sm', // sm, md, lg, xl
    'padding' => 'md', // sm, md, lg, xl, none
    'border' => false,
    'borderColor' => null,
    'class' => ''
])

@php
    $paddingClasses = [
        'none' => '',
        'sm' => 'p-3',
        'md' => 'p-4',
        'lg' => 'p-5',
        'xl' => 'p-6'
    ];
    
    $shadowClasses = [
        'none' => '',
        'sm' => 'shadow-sm',
        'md' => 'shadow-md',
        'lg' => 'shadow-lg',
        'xl' => 'shadow-xl'
    ];
    
    $classes = 'card-modern ' . ($paddingClasses[$padding] ?? 'p-4') . ' ' . ($shadowClasses[$shadow] ?? 'shadow-sm');
    
    if ($hover) {
        $classes .= ' card-hover';
    }
    
    if ($border && $borderColor) {
        $classes .= ' border-start border-4';
    }
    
    $classes .= ' ' . $class;
@endphp

<div {{ $attributes->merge(['class' => $classes]) }} @if($border && $borderColor) style="border-left-color: {{ $borderColor }} !important;" @endif>
    {{ $slot }}
</div>

<style>
    .card-modern {
        background: var(--bg-primary);
        border: 1px solid var(--border-light);
        border-radius: var(--radius-xl);
        transition: var(--transition-base);
    }
    
    .card-hover:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg) !important;
    }
</style>
