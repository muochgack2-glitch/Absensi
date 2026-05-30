{{-- 
    Section Card Component - Inspired by eRapor8
    Card with header section for organizing content
    
    Usage:
    <x-section-card title="Recent Activity" icon="fas fa-list">
        <x-slot:actions>
            <button>Action</button>
        </x-slot:actions>
        
        Content here
    </x-section-card>
--}}

@props([
    'title' => '',
    'icon' => null,
    'badge' => null,
    'actions' => null,
    'padding' => 'md', // sm, md, lg
    'class' => ''
])

@php
    $paddingClasses = [
        'sm' => 'p-3',
        'md' => 'p-4',
        'lg' => 'p-5'
    ];
    
    $paddingClass = $paddingClasses[$padding] ?? 'p-4';
@endphp

<div {{ $attributes->merge(['class' => 'section-card-modern ' . $class]) }}>
    @if($title || $icon || $badge || $actions)
        <div class="section-card-header {{ $paddingClass }}">
            <div class="section-card-title-wrapper">
                @if($icon)
                    <div class="section-card-icon">
                        <i class="{{ $icon }}"></i>
                    </div>
                @endif
                
                @if($title)
                    <h5 class="section-card-title">{{ $title }}</h5>
                @endif
                
                @if($badge)
                    <span class="section-card-badge">{{ $badge }}</span>
                @endif
            </div>
            
            @if($actions)
                <div class="section-card-actions">
                    {{ $actions }}
                </div>
            @endif
        </div>
    @endif
    
    <div class="section-card-body {{ $paddingClass }}">
        {{ $slot }}
    </div>
</div>

<style>
    .section-card-modern {
        background: var(--bg-primary);
        border: 1px solid var(--border-light);
        border-radius: var(--radius-xl);
        box-shadow: var(--shadow-sm);
        transition: var(--transition-base);
        overflow: hidden;
    }
    
    .section-card-modern:hover {
        box-shadow: var(--shadow-md);
    }
    
    .section-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid var(--border-light);
        background: var(--bg-secondary);
    }
    
    .section-card-title-wrapper {
        display: flex;
        align-items: center;
        gap: var(--space-3);
        flex: 1;
    }
    
    .section-card-icon {
        width: 36px;
        height: 36px;
        border-radius: var(--radius-md);
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: var(--text-sm);
        flex-shrink: 0;
    }
    
    .section-card-title {
        font-size: var(--text-lg);
        font-weight: var(--font-bold);
        color: var(--text-primary);
        margin: 0;
        line-height: 1.4;
    }
    
    .section-card-badge {
        font-size: var(--text-xs);
        font-weight: var(--font-bold);
        padding: var(--space-1) var(--space-3);
        border-radius: var(--radius-full);
        background: linear-gradient(135deg, var(--success), var(--success-dark));
        color: #ffffff;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        display: inline-flex;
        align-items: center;
        gap: var(--space-1);
    }
    
    .section-card-badge::before {
        content: '';
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #ffffff;
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }
    
    .section-card-actions {
        display: flex;
        align-items: center;
        gap: var(--space-2);
    }
    
    .section-card-body {
        background: var(--bg-primary);
    }
</style>
