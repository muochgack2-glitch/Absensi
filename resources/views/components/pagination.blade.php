{{-- 
    Modern Pagination Component - Inspired by eRapor8
    
    Usage:
    <x-pagination :paginator="$users" />
    
    Or with custom info:
    <x-pagination :paginator="$users" showInfo="true" />
--}}

@props([
    'paginator' => null,
    'showInfo' => true,
    'size' => 'md', // sm, md, lg
    'class' => ''
])

@if($paginator && $paginator->hasPages())
    <div {{ $attributes->merge(['class' => 'pagination-modern ' . $class]) }}>
        @if($showInfo)
            <div class="pagination-info">
                Menampilkan {{ $paginator->firstItem() }} - {{ $paginator->lastItem() }} 
                dari {{ $paginator->total() }} data
            </div>
        @endif
        
        <nav class="pagination-nav">
            <x-button-group size="{{ $size }}">
                {{-- Previous Button --}}
                @if($paginator->onFirstPage())
                    <x-button variant="secondary" disabled="true" icon="fas fa-chevron-left">
                        Previous
                    </x-button>
                @else
                    <x-button variant="secondary" href="{{ $paginator->previousPageUrl() }}" icon="fas fa-chevron-left">
                        Previous
                    </x-button>
                @endif
                
                {{-- Page Numbers --}}
                @foreach($paginator->getUrlRange(1, $paginator->lastPage()) as $page => $url)
                    @if($page == $paginator->currentPage())
                        <x-button variant="primary">{{ $page }}</x-button>
                    @else
                        <x-button variant="secondary" href="{{ $url }}">{{ $page }}</x-button>
                    @endif
                @endforeach
                
                {{-- Next Button --}}
                @if($paginator->hasMorePages())
                    <x-button variant="secondary" href="{{ $paginator->nextPageUrl() }}" iconRight="fas fa-chevron-right">
                        Next
                    </x-button>
                @else
                    <x-button variant="secondary" disabled="true" iconRight="fas fa-chevron-right">
                        Next
                    </x-button>
                @endif
            </x-button-group>
        </nav>
    </div>
@endif

<style>
    .pagination-modern {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: var(--space-4);
        margin-top: var(--space-6);
        flex-wrap: wrap;
    }
    
    .pagination-info {
        font-size: var(--text-sm);
        color: var(--text-secondary);
        font-weight: var(--font-medium);
    }
    
    .pagination-nav {
        display: flex;
        justify-content: center;
        flex: 1;
    }
    
    /* Mobile Responsive */
    @media (max-width: 768px) {
        .pagination-modern {
            flex-direction: column;
            align-items: stretch;
        }
        
        .pagination-info {
            text-align: center;
        }
        
        .pagination-nav {
            justify-content: center;
        }
        
        .pagination-nav .btn-group-modern {
            flex-wrap: wrap;
            justify-content: center;
        }
    }
    
    /* Print: Hide pagination */
    @media print {
        .pagination-modern {
            display: none !important;
        }
    }
</style>
