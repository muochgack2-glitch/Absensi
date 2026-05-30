{{-- 
    Action Card Component - Inspired by eRapor8
    Interactive card with hover effects and click actions
    
    Usage:
    <x-action-card 
        icon="fas fa-plus" 
        title="Add New" 
        description="Create a new item"
        href="{{ route('item.create') }}"
        color="blue"
    />
--}}

@props([
    'icon' => 'fas fa-plus',
    'title' => '',
    'description' => '',
    'href' => '#',
    'color' => 'blue', // blue, green, purple, orange, red
    'class' => ''
])

@php
    $colorClasses = [
        'blue' => 'linear-gradient(135deg, #3b82f6, #2563eb)',
        'green' => 'linear-gradient(135deg, #10b981, #059669)',
        'purple' => 'linear-gradient(135deg, #a855f7, #9333ea)',
        'orange' => 'linear-gradient(135deg, #f97316, #ea580c)',
        'red' => 'linear-gradient(135deg, #ef4444, #dc2626)',
    ];
    
    $gradient = $colorClasses[$color] ?? $colorClasses['blue'];
@endphp

<a href="{{ $href }}" {{ $attributes->merge(['class' => 'action-card-modern ' . $class]) }}>
    <div class="action-card-icon" style="background: {{ $gradient }};">
        <i class="{{ $icon }}"></i>
    </div>
    
    <div class="action-card-content">
        <div class="action-card-title">{{ $title }}</div>
        
        @if($description)
            <div class="action-card-description">{{ $description }}</div>
        @endif
    </div>
    
    <div class="action-card-arrow">
        <i class="fas fa-arrow-right"></i>
    </div>
</a>

<style>
    .action-card-modern {
        display: flex;
        align-items: center;
        gap: var(--space-4);
        padding: var(--space-5);
        background: var(--bg-primary);
        border: 2px solid var(--border-light);
        border-radius: var(--radius-xl);
        text-decoration: none;
        color: inherit;
        transition: all var(--transition-base);
        position: relative;
        overflow: hidden;
    }
    
    .action-card-modern::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        opacity: 0;
        transition: var(--transition-base);
    }
    
    .action-card-modern:hover {
        border-color: var(--primary);
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg);
    }
    
    .action-card-modern:hover::before {
        opacity: 0.03;
    }
    
    .action-card-modern:hover .action-card-arrow {
        transform: translateX(4px);
        opacity: 1;
    }
    
    .action-card-icon {
        width: 56px;
        height: 56px;
        border-radius: var(--radius-lg);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        font-size: 24px;
        flex-shrink: 0;
        position: relative;
        z-index: 1;
    }
    
    .action-card-content {
        flex: 1;
        position: relative;
        z-index: 1;
    }
    
    .action-card-title {
        font-size: var(--text-lg);
        font-weight: var(--font-bold);
        color: var(--text-primary);
        margin-bottom: var(--space-1);
    }
    
    .action-card-description {
        font-size: var(--text-sm);
        color: var(--text-secondary);
        line-height: 1.5;
    }
    
    .action-card-arrow {
        color: var(--text-tertiary);
        font-size: var(--text-lg);
        transition: all var(--transition-base);
        opacity: 0.5;
        position: relative;
        z-index: 1;
    }
</style>
