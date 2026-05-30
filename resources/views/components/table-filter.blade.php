{{-- 
    Table Filter Component - Inspired by eRapor8
    
    Usage:
    <x-table-filter>
        <select name="status" class="form-select">
            <option value="">All Status</option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
        </select>
        
        <select name="role" class="form-select">
            <option value="">All Roles</option>
            <option value="admin">Admin</option>
            <option value="user">User</option>
        </select>
    </x-table-filter>
--}}

@props([
    'route' => null,
    'class' => ''
])

<div {{ $attributes->merge(['class' => 'table-filter-modern ' . $class]) }}>
    <form method="GET" action="{{ $route ? route($route) : '' }}" class="filter-form">
        {{-- Preserve search query --}}
        @if(request('search'))
            <input type="hidden" name="search" value="{{ request('search') }}">
        @endif
        
        <div class="filter-label">
            <i class="fas fa-filter"></i>
            <span>Filter:</span>
        </div>
        
        <div class="filter-inputs">
            {{ $slot }}
        </div>
        
        <div class="filter-actions">
            <x-button variant="primary" type="submit" size="sm" icon="fas fa-check">
                Apply
            </x-button>
            
            @if(request()->except(['_token', 'search']))
                <x-button 
                    variant="secondary" 
                    outline="true" 
                    size="sm" 
                    href="{{ $route ? route($route, ['search' => request('search')]) : '' }}"
                    icon="fas fa-times"
                >
                    Clear
                </x-button>
            @endif
        </div>
    </form>
</div>

<style>
    .table-filter-modern {
        margin-bottom: var(--space-4);
        padding: var(--space-4);
        background: var(--bg-secondary);
        border-radius: var(--radius-lg);
        border: 1px solid var(--border-light);
    }
    
    .filter-form {
        display: flex;
        gap: var(--space-3);
        align-items: center;
        flex-wrap: wrap;
    }
    
    .filter-label {
        display: flex;
        align-items: center;
        gap: var(--space-2);
        font-size: var(--text-sm);
        font-weight: var(--font-semibold);
        color: var(--text-secondary);
        white-space: nowrap;
    }
    
    .filter-inputs {
        display: flex;
        gap: var(--space-3);
        flex: 1;
        flex-wrap: wrap;
    }
    
    .filter-inputs select,
    .filter-inputs input {
        min-width: 150px;
        padding: var(--space-2) var(--space-3);
        border: 1px solid var(--border-light);
        border-radius: var(--radius-md);
        font-size: var(--text-sm);
        background: var(--bg-primary);
        color: var(--text-primary);
        transition: all var(--transition-fast);
    }
    
    .filter-inputs select:focus,
    .filter-inputs input:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }
    
    .filter-actions {
        display: flex;
        gap: var(--space-2);
    }
    
    /* Mobile Responsive */
    @media (max-width: 768px) {
        .filter-form {
            flex-direction: column;
            align-items: stretch;
        }
        
        .filter-inputs {
            flex-direction: column;
        }
        
        .filter-inputs select,
        .filter-inputs input {
            width: 100%;
        }
        
        .filter-actions {
            justify-content: stretch;
        }
        
        .filter-actions .btn-modern {
            flex: 1;
        }
    }
    
    /* Print: Hide filters */
    @media print {
        .table-filter-modern {
            display: none !important;
        }
    }
</style>
