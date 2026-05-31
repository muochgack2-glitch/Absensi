{{-- 
    Modern Table Component - Inspired by eRapor8
    
    Usage:
    <x-table>
        <x-slot:header>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Actions</th>
            </tr>
        </x-slot:header>
        
        <tr>
            <td>John Doe</td>
            <td>john@example.com</td>
            <td>Actions here</td>
        </tr>
    </x-table>
--}}

@props([
    'striped' => true,
    'hover' => true,
    'bordered' => false,
    'responsive' => true,
    'size' => 'md', // sm, md, lg
    'class' => ''
])

@php
    $baseClass = 'table-modern';
    
    $classes = [$baseClass];
    
    if ($striped) $classes[] = 'table-striped';
    if ($hover) $classes[] = 'table-hover';
    if ($bordered) $classes[] = 'table-bordered';
    
    $sizeClasses = [
        'sm' => 'table-sm',
        'md' => 'table-md',
        'lg' => 'table-lg'
    ];
    $classes[] = $sizeClasses[$size] ?? 'table-md';
    
    $classes[] = $class;
    $classString = implode(' ', $classes);
@endphp

@if($responsive)
    <div class="table-responsive">
        <table {{ $attributes->merge(['class' => $classString]) }}>
            @if(isset($header))
                <thead>
                    {{ $header }}
                </thead>
            @endif
            <tbody>
                {{ $slot }}
            </tbody>
        </table>
    </div>
@else
    <table {{ $attributes->merge(['class' => $classString]) }}>
        @if(isset($header))
            <thead>
                {{ $header }}
            </thead>
        @endif
        <tbody>
            {{ $slot }}
        </tbody>
    </table>
@endif

<style>
    /* Modern Table Base Styles */
    .table-modern {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        margin-bottom: 0;
    }
    
    /* Table Header */
    .table-modern thead th {
        background: var(--bg-secondary);
        color: var(--text-secondary);
        font-size: var(--text-xs);
        font-weight: var(--font-bold);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: var(--space-3) var(--space-4);
        border-bottom: 2px solid var(--border-light);
        white-space: nowrap;
        vertical-align: middle;
    }
    
    /* Table Body */
    .table-modern tbody td {
        padding: var(--space-4);
        border-bottom: 1px solid var(--border-light);
        color: var(--text-primary);
        vertical-align: middle;
        font-size: var(--text-sm);
    }
    
    .table-modern tbody tr:last-child td {
        border-bottom: none;
    }
    
    /* Hover Effect */
    .table-hover tbody tr {
        transition: background-color var(--transition-fast);
    }
    
    .table-hover tbody tr:hover {
        background-color: var(--bg-secondary);
    }
    
    /* Striped */
    .table-striped tbody tr:nth-of-type(odd) {
        background-color: rgba(0, 0, 0, 0.02);
    }
    
    /* Bordered */
    .table-bordered {
        border: 1px solid var(--border-light);
        border-radius: var(--radius-lg);
        overflow: hidden;
    }
    
    .table-bordered thead th,
    .table-bordered tbody td {
        border-right: 1px solid var(--border-light);
    }
    
    .table-bordered thead th:last-child,
    .table-bordered tbody td:last-child {
        border-right: none;
    }
    
    /* Sizes */
    .table-sm thead th,
    .table-sm tbody td {
        padding: var(--space-2) var(--space-3);
        font-size: var(--text-xs);
    }
    
    .table-md thead th,
    .table-md tbody td {
        padding: var(--space-3) var(--space-4);
        font-size: var(--text-sm);
    }
    
    .table-lg thead th,
    .table-lg tbody td {
        padding: var(--space-4) var(--space-5);
        font-size: var(--text-base);
    }
    
    /* Responsive */
    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    
    /* Print Styles */
    @media print {
        .table-modern {
            border-collapse: collapse !important;
        }
        
        .table-modern thead th {
            background: transparent !important;
            color: #000 !important;
            border: 1px solid #000 !important;
        }
        
        .table-modern tbody td {
            border: 1px solid #000 !important;
        }
        
        .table-hover tbody tr:hover {
            background: transparent !important;
        }
        
        .table-striped tbody tr:nth-of-type(odd) {
            background: transparent !important;
        }
    }
</style>
