{{-- 
    Table Search Component - Inspired by eRapor8
    
    Usage:
    <x-table-search 
        placeholder="Search users..." 
        route="users.index"
        :value="request('search')"
    />
--}}

@props([
    'placeholder' => 'Search...',
    'route' => null,
    'value' => '',
    'name' => 'search',
    'class' => ''
])

<div {{ $attributes->merge(['class' => 'table-search-modern ' . $class]) }}>
    <form method="GET" action="{{ $route ? route($route) : '' }}" class="search-form">
        {{-- Preserve other query parameters --}}
        @foreach(request()->except($name) as $key => $val)
            <input type="hidden" name="{{ $key }}" value="{{ $val }}">
        @endforeach
        
        <div class="search-input-wrapper">
            <i class="fas fa-search search-icon"></i>
            <input 
                type="text" 
                name="{{ $name }}" 
                value="{{ $value }}" 
                placeholder="{{ $placeholder }}"
                class="search-input"
            >
            
            @if($value)
                <button type="button" class="search-clear" onclick="this.previousElementSibling.value=''; this.closest('form').submit();">
                    <i class="fas fa-times"></i>
                </button>
            @endif
        </div>
        
        <x-button variant="primary" type="submit" icon="fas fa-search">
            Search
        </x-button>
    </form>
</div>

<style>
    .table-search-modern {
        margin-bottom: var(--space-4);
    }
    
    .search-form {
        display: flex;
        gap: var(--space-3);
        align-items: center;
    }
    
    .search-input-wrapper {
        position: relative;
        flex: 1;
        max-width: 400px;
    }
    
    .search-icon {
        position: absolute;
        left: var(--space-4);
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-tertiary);
        font-size: var(--text-sm);
        pointer-events: none;
    }
    
    .search-input {
        width: 100%;
        padding: var(--space-3) var(--space-4);
        padding-left: calc(var(--space-4) * 2 + 16px);
        padding-right: calc(var(--space-4) * 2 + 16px);
        border: 2px solid var(--border-light);
        border-radius: var(--radius-lg);
        font-size: var(--text-sm);
        transition: all var(--transition-fast);
        background: var(--bg-primary);
        color: var(--text-primary);
    }
    
    .search-input:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }
    
    .search-input::placeholder {
        color: var(--text-muted);
    }
    
    .search-clear {
        position: absolute;
        right: var(--space-3);
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: var(--text-tertiary);
        cursor: pointer;
        padding: var(--space-2);
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: var(--radius-md);
        transition: all var(--transition-fast);
    }
    
    .search-clear:hover {
        color: var(--danger);
        background: var(--danger-light);
    }
    
    /* Mobile Responsive */
    @media (max-width: 768px) {
        .search-form {
            flex-direction: column;
            align-items: stretch;
        }
        
        .search-input-wrapper {
            max-width: none;
        }
    }
    
    /* Print: Hide search */
    @media print {
        .table-search-modern {
            display: none !important;
        }
    }
</style>
