{{-- 
    Table Actions Component - Inspired by eRapor8
    Action buttons for table rows
    
    Usage:
    <x-table-actions>
        <x-icon-button icon="fas fa-eye" variant="info" size="sm" tooltip="View" />
        <x-icon-button icon="fas fa-edit" variant="primary" size="sm" tooltip="Edit" />
        <x-icon-button icon="fas fa-trash" variant="danger" size="sm" tooltip="Delete" />
    </x-table-actions>
--}}

@props([
    'align' => 'start', // start, center, end
    'gap' => 'sm', // sm, md, lg
    'class' => ''
])

@php
    $alignClasses = [
        'start' => 'justify-content-start',
        'center' => 'justify-content-center',
        'end' => 'justify-content-end'
    ];
    
    $gapClasses = [
        'sm' => 'gap-1',
        'md' => 'gap-2',
        'lg' => 'gap-3'
    ];
    
    $classes = 'table-actions d-flex align-items-center ' . 
                ($alignClasses[$align] ?? 'justify-content-start') . ' ' .
                ($gapClasses[$gap] ?? 'gap-1') . ' ' .
                $class;
@endphp

<div {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</div>

<style>
    .table-actions {
        white-space: nowrap;
    }
    
    /* Print: Hide action buttons */
    @media print {
        .table-actions {
            display: none !important;
        }
    }
</style>
