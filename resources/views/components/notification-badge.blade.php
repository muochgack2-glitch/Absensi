{{-- 
    Notification Badge Component - Inspired by eRapor8
    
    Usage:
    <x-notification-badge count="5" />
    <x-notification-badge count="99" max="99" />
    <x-notification-badge dot="true" />
--}}

@props([
    'count' => 0,
    'max' => 99,
    'dot' => false,
    'type' => 'danger', // primary, success, warning, danger, info
    'class' => ''
])

@php
    $displayCount = $count > $max ? $max . '+' : $count;
    $show = $dot || $count > 0;
    
    $typeClasses = [
        'primary' => 'badge-primary',
        'success' => 'badge-success',
        'warning' => 'badge-warning',
        'danger' => 'badge-danger',
        'info' => 'badge-info',
    ];
    
    $typeClass = $typeClasses[$type] ?? 'badge-danger';
@endphp

@if($show)
    <span {{ $attributes->merge(['class' => 'notification-badge ' . $typeClass . ($dot ? ' badge-dot' : '') . ' ' . $class]) }}>
        @if(!$dot)
            {{ $displayCount }}
        @endif
    </span>
@endif

<style>
    .notification-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 20px;
        height: 20px;
        padding: 0 6px;
        font-size: 11px;
        font-weight: var(--font-bold);
        line-height: 1;
        color: #ffffff;
        border-radius: var(--radius-full);
        animation: badgePulse 2s infinite;
    }
    
    .notification-badge.badge-dot {
        min-width: 8px;
        width: 8px;
        height: 8px;
        padding: 0;
        border: 2px solid #ffffff;
    }
    
    @keyframes badgePulse {
        0%, 100% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.1);
        }
    }
    
    /* Badge Types */
    .badge-primary {
        background: linear-gradient(135deg, var(--primary), var(--secondary));
    }
    
    .badge-success {
        background: linear-gradient(135deg, var(--success), var(--success-dark));
    }
    
    .badge-warning {
        background: linear-gradient(135deg, var(--warning), var(--warning-dark));
    }
    
    .badge-danger {
        background: linear-gradient(135deg, var(--danger), var(--danger-dark));
    }
    
    .badge-info {
        background: linear-gradient(135deg, var(--info), var(--info-dark));
    }
</style>
