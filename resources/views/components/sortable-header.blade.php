{{-- 
    Sortable Table Header Component - Inspired by eRapor8
    
    Usage:
    <x-sortable-header 
        column="name" 
        :current="$sortColumn" 
        :direction="$sortDirection"
        route="users.index"
    >
        Name
    </x-sortable-header>
--}}

@props([
    'column' => '',
    'current' => '',
    'direction' => 'asc',
    'route' => null,
    'class' => ''
])

@php
    $isCurrent = $column === $current;
    $nextDirection = $isCurrent && $direction === 'asc' ? 'desc' : 'asc';
    
    $icon = 'fas fa-sort';
    if ($isCurrent) {
        $icon = $direction === 'asc' ? 'fas fa-sort-up' : 'fas fa-sort-down';
    }
    
    $url = $route ? route($route, array_merge(request()->query(), [
        'sort' => $column,
        'direction' => $nextDirection
    ])) : '#';
@endphp

<th {{ $attributes->merge(['class' => 'sortable-header ' . ($isCurrent ? 'active' : '') . ' ' . $class]) }}>
    <a href="{{ $url }}" class="sortable-link">
        <span class="sortable-text">{{ $slot }}</span>
        <i class="{{ $icon }} sortable-icon"></i>
    </a>
</th>

<style>
    .sortable-header {
        cursor: pointer;
        user-select: none;
    }
    
    .sortable-link {
        display: flex;
        align-items: center;
        gap: var(--space-2);
        color: inherit;
        text-decoration: none;
        transition: color var(--transition-fast);
    }
    
    .sortable-link:hover {
        color: var(--primary);
    }
    
    .sortable-header.active .sortable-link {
        color: var(--primary);
        font-weight: var(--font-extrabold);
    }
    
    .sortable-icon {
        font-size: 0.75em;
        opacity: 0.5;
        transition: opacity var(--transition-fast);
    }
    
    .sortable-header:hover .sortable-icon,
    .sortable-header.active .sortable-icon {
        opacity: 1;
    }
    
    /* Print: Remove sorting icons */
    @media print {
        .sortable-icon {
            display: none !important;
        }
        
        .sortable-link {
            pointer-events: none;
        }
    }
</style>
