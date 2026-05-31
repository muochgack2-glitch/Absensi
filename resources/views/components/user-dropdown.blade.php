{{--
    User Profile Dropdown Component
    Inspired by eRapor SMK design
--}}

@php
    $user = auth()->user();
    $userName = $user->name ?? 'User';
    $userEmail = $user->email ?? '';
    $userRole = $user->role ?? 'user';
    
    // Role display
    $roleDisplay = match($userRole) {
        'administrator' => ['label' => 'Administrator', 'icon' => 'fas fa-user-shield', 'color' => '#ef4444'],
        'admin_wa' => ['label' => 'Admin WhatsApp', 'icon' => 'fab fa-whatsapp', 'color' => '#10b981'],
        'panitia' => ['label' => 'Panitia', 'icon' => 'fas fa-user-tie', 'color' => '#3b82f6'],
        default => ['label' => 'User', 'icon' => 'fas fa-user', 'color' => '#64748b'],
    };
    
    // Get initials for avatar
    $initials = collect(explode(' ', $userName))
        ->map(fn($word) => strtoupper(substr($word, 0, 1)))
        ->take(2)
        ->join('');
@endphp

<div class="user-dropdown-wrapper">
    <!-- Dropdown Toggle Button -->
    <button class="user-dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
        <div class="user-avatar-container">
            <div class="user-avatar-circle">
                <span class="user-initials">{{ $initials }}</span>
            </div>
            <span class="user-status-indicator"></span>
        </div>
        <div class="user-info-text d-none d-md-block">
            <div class="user-name">{{ $userName }}</div>
            <div class="user-role-badge" style="color: {{ $roleDisplay['color'] }};">
                <i class="{{ $roleDisplay['icon'] }} me-1"></i>{{ $roleDisplay['label'] }}
            </div>
        </div>
        <i class="fas fa-chevron-down dropdown-arrow d-none d-md-inline"></i>
    </button>

    <!-- Dropdown Menu -->
    <ul class="dropdown-menu dropdown-menu-end user-dropdown-menu">
        <!-- User Info Header -->
        <li class="user-dropdown-header">
            <div class="user-avatar-large">
                <span class="user-initials-large">{{ $initials }}</span>
                <span class="user-status-indicator-large"></span>
            </div>
            <div class="user-details">
                <div class="user-name-large">{{ $userName }}</div>
                <div class="user-email">{{ $userEmail }}</div>
                <div class="user-role-badge-large" style="background: {{ $roleDisplay['color'] }}20; color: {{ $roleDisplay['color'] }};">
                    <i class="{{ $roleDisplay['icon'] }} me-1"></i>{{ $roleDisplay['label'] }}
                </div>
            </div>
        </li>

        <li><hr class="dropdown-divider"></li>

        <!-- Profile Menu Item -->
        <li>
            <a class="dropdown-item" href="{{ route('profile.index') ?? '#' }}">
                <i class="fas fa-user-circle me-2"></i>
                <span>Profile Saya</span>
            </a>
        </li>

        <li><hr class="dropdown-divider"></li>

        <!-- Logout -->
        <li>
            <form id="logoutFormNav" action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="button" class="dropdown-item text-danger" onclick="confirmLogoutNav(event)">
                    <i class="fas fa-sign-out-alt me-2"></i>
                    <span>Logout</span>
                </button>
            </form>
        </li>
    </ul>
</div>

<style>
    .user-dropdown-wrapper {
        position: relative;
    }

    .user-dropdown-toggle {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 8px 12px;
        background: var(--bg-secondary);
        border: 1px solid var(--border-light);
        border-radius: var(--radius-lg);
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .user-dropdown-toggle:hover {
        background: var(--bg-tertiary);
        border-color: var(--border-medium);
        transform: translateY(-1px);
    }

    .user-avatar-container {
        position: relative;
    }

    .user-avatar-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        font-size: 14px;
        box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
    }

    .user-status-indicator {
        position: absolute;
        bottom: 2px;
        right: 2px;
        width: 10px;
        height: 10px;
        background: #10b981;
        border: 2px solid var(--bg-primary);
        border-radius: 50%;
        animation: pulse-status 2s ease-in-out infinite;
    }

    @keyframes pulse-status {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.6; }
    }

    .user-info-text {
        text-align: left;
    }

    .user-name {
        font-size: 14px;
        font-weight: 600;
        color: var(--text-primary);
        line-height: 1.2;
        margin-bottom: 2px;
    }

    .user-role-badge {
        font-size: 11px;
        font-weight: 500;
        display: flex;
        align-items: center;
    }

    .dropdown-arrow {
        font-size: 10px;
        color: var(--text-tertiary);
        margin-left: 4px;
        transition: transform 0.2s ease;
    }

    .user-dropdown-toggle[aria-expanded="true"] .dropdown-arrow {
        transform: rotate(180deg);
    }

    /* Dropdown Menu */
    .user-dropdown-menu {
        min-width: 280px;
        padding: 0;
        border: 1px solid var(--border-light);
        border-radius: var(--radius-xl);
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
        margin-top: 8px;
        overflow: hidden;
    }

    .user-dropdown-header {
        padding: 20px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .user-avatar-large {
        position: relative;
        flex-shrink: 0;
    }

    .user-initials-large {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 700;
        font-size: 20px;
        border: 3px solid rgba(255, 255, 255, 0.3);
    }

    .user-status-indicator-large {
        position: absolute;
        bottom: 2px;
        right: 2px;
        width: 14px;
        height: 14px;
        background: #10b981;
        border: 3px solid white;
        border-radius: 50%;
    }

    .user-details {
        flex: 1;
        min-width: 0;
    }

    .user-name-large {
        font-size: 16px;
        font-weight: 700;
        margin-bottom: 4px;
        color: white;
    }

    .user-email {
        font-size: 12px;
        opacity: 0.9;
        margin-bottom: 8px;
        color: white;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .user-role-badge-large {
        display: inline-flex;
        align-items: center;
        padding: 4px 10px;
        border-radius: var(--radius-md);
        font-size: 11px;
        font-weight: 600;
    }

    .user-dropdown-menu .dropdown-item {
        padding: 12px 20px;
        font-size: 14px;
        color: var(--text-primary);
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
    }

    .user-dropdown-menu .dropdown-item:hover {
        background: var(--bg-secondary);
        padding-left: 24px;
    }

    .user-dropdown-menu .dropdown-item i {
        width: 20px;
        font-size: 14px;
    }

    .user-dropdown-menu .dropdown-divider {
        margin: 0;
        opacity: 0.1;
    }

    /* Mobile Responsive */
    @media (max-width: 768px) {
        .user-dropdown-toggle {
            padding: 8px;
        }

        .user-avatar-circle {
            width: 36px;
            height: 36px;
            font-size: 13px;
        }

        .user-dropdown-menu {
            min-width: 260px;
        }
    }
</style>

<script>
    function confirmLogoutNav(event) {
        event.preventDefault();
        
        if (typeof Modal !== 'undefined' && Modal.confirm) {
            Modal.confirm(
                'Apakah Anda yakin ingin keluar dari sistem?',
                function() {
                    document.getElementById('logoutFormNav').submit();
                },
                {
                    title: 'Konfirmasi Logout',
                    confirmText: 'Ya, Keluar',
                    cancelText: 'Batal',
                    type: 'warning'
                }
            );
        } else {
            // Fallback jika Modal belum loaded
            if (confirm('Apakah Anda yakin ingin keluar dari sistem?')) {
                document.getElementById('logoutFormNav').submit();
            }
        }
    }
</script>
