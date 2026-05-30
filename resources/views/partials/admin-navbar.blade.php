<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid">
        @php
            $settings = \App\Models\SettingSystem::instance()->toSettingsArray();
        @endphp
        
        <!-- Sidebar Toggle Button (Desktop) -->
        <button class="sidebar-toggle me-3 d-none d-lg-flex" type="button" id="sidebarToggle">
            <i class="fas fa-bars"></i>
        </button>
        
        <!-- Mobile Menu Button -->
        <button class="btn btn-sm btn-outline-light admin-mobile-menu-btn d-lg-none" type="button">
            <i class="fas fa-bars"></i> Menu
        </button>
        
        <div class="ms-auto d-flex align-items-center gap-3">
            <button type="button" class="admin-theme-toggle" data-admin-theme-toggle></button>
            <div class="user-info">
                <div class="user-avatar"><i class="fas fa-user"></i></div>
                <div class="d-none d-md-block">
                    <small>Selamat datang,</small>
                    <div style="font-weight: 600;">{{ auth()->user()->name ?? 'User' }}</div>
                </div>
            </div>
        </div>
    </div>
</nav>
