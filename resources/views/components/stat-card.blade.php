{{-- 
    Statistic Card Component - Inspired by eRapor8
    
    Usage:
    <x-stat-card 
        icon="fas fa-users" 
        label="Total Users" 
        value="1,234" 
        color="blue"
        description="All time"
        trend="+12"
    />
--}}

@props([
    'icon' => 'fas fa-chart-line',
    'label' => '',
    'value' => '0',
    'description' => '',
    'color' => 'blue', // blue, green, yellow, red, purple, indigo
    'trend' => null,
    'trendUp' => null,
    'sparkline' => null,
    'class' => ''
])

@php
    $colorClasses = [
        'blue' => ['border' => '#3b82f6', 'bg' => 'linear-gradient(135deg, #3b82f6, #2563eb)'],
        'green' => ['border' => '#10b981', 'bg' => 'linear-gradient(135deg, #10b981, #059669)'],
        'yellow' => ['border' => '#f59e0b', 'bg' => 'linear-gradient(135deg, #f59e0b, #d97706)'],
        'red' => ['border' => '#ef4444', 'bg' => 'linear-gradient(135deg, #ef4444, #dc2626)'],
        'purple' => ['border' => '#a855f7', 'bg' => 'linear-gradient(135deg, #a855f7, #9333ea)'],
        'indigo' => ['border' => '#6366f1', 'bg' => 'linear-gradient(135deg, #6366f1, #4f46e5)'],
    ];
    
    $colorConfig = $colorClasses[$color] ?? $colorClasses['blue'];
@endphp

<div {{ $attributes->merge(['class' => 'stat-card-modern ' . $class]) }} style="border-left-color: {{ $colorConfig['border'] }};">
    <div class="stat-card-icon" style="background: {{ $colorConfig['bg'] }};">
        <i class="{{ $icon }}"></i>
    </div>
    
    <div class="stat-card-content">
        <div class="stat-card-label">{{ $label }}</div>
        <div class="stat-card-value">{{ $value }}</div>
        
        @if($description)
            <div class="stat-card-description">{{ $description }}</div>
        @endif
    </div>
    
    @if($trend || $sparkline)
        <div class="stat-card-meta">
            @if($trend)
                <span class="stat-trend-badge" style="color: {{ $trendUp ? '#10b981' : ($trendUp === false ? '#ef4444' : '#64748b') }};">
                    @if($trendUp === true)
                        <i class="fas fa-arrow-up"></i>
                    @elseif($trendUp === false)
                        <i class="fas fa-arrow-down"></i>
                    @endif
                    {{ $trend }}
                </span>
            @endif
            
            @if($sparkline)
                <div class="stat-sparkline">
                    {{ $sparkline }}
                </div>
            @endif
        </div>
    @endif
</div>

<style>
    .stat-card-modern {
        background: var(--bg-primary);
        border: 1px solid var(--border-light);
        border-left: 4px solid;
        border-radius: var(--radius-xl);
        padding: var(--space-6);
        transition: var(--transition-base);
        position: relative;
        overflow: hidden;
    }
    
    .stat-card-modern::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, currentColor, transparent);
        opacity: 0.1;
    }
    
    .stat-card-modern:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg);
    }
    
    .stat-card-icon {
        width: 48px;
        height: 48px;
        border-radius: var(--radius-lg);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        font-size: 20px;
        margin-bottom: var(--space-4);
    }
    
    .stat-card-content {
        margin-bottom: var(--space-3);
    }
    
    .stat-card-label {
        font-size: var(--text-xs);
        font-weight: var(--font-semibold);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--text-secondary);
        margin-bottom: var(--space-2);
    }
    
    .stat-card-value {
        font-size: 2.25rem;
        font-weight: var(--font-extrabold);
        color: var(--text-primary);
        line-height: 1;
        margin-bottom: var(--space-2);
        font-variant-numeric: tabular-nums;
    }
    
    .stat-card-description {
        font-size: var(--text-sm);
        color: var(--text-tertiary);
    }
    
    .stat-card-meta {
        display: flex;
        align-items: center;
        gap: var(--space-3);
        padding-top: var(--space-4);
        border-top: 1px solid var(--border-light);
        margin-top: var(--space-4);
    }
    
    .stat-trend-badge {
        font-size: var(--text-xs);
        font-weight: var(--font-semibold);
        display: inline-flex;
        align-items: center;
        gap: var(--space-1);
    }
    
    .stat-sparkline {
        margin-left: auto;
    }
</style>
