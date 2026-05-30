<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin SPMB')</title>
    @include('partials.favicon')
    @include('partials.admin-theme')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('css/modern-utilities.css') }}" rel="stylesheet">
    @stack('styles')
    @include('partials.admin-theme-vars')
    <style>
        html {
            overflow-y: scroll;
        }

        body {
            background-color: #f5f7fa !important;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif !important;
            margin: 0;
            padding: 0;
        }

        /* Modern Layout - eRapor8 Style */
        .admin-layout {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Styling */
        .sidebar {
            width: 250px !important;
            background: #2c3e50 !important;
            min-height: 100vh !important;
            padding: 0 !important;
            flex: 0 0 250px !important;
            transition: all 0.3s ease !important;
            overflow: hidden !important;
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            z-index: 1000;
        }

        .sidebar.collapsed {
            width: 70px !important;
            flex: 0 0 70px !important;
        }

        .sidebar.collapsed .nav-text {
            opacity: 0 !important;
            width: 0 !important;
            display: inline-block !important;
            overflow: hidden !important;
        }

        .sidebar.collapsed .nav-link {
            justify-content: center !important;
            padding: 12px 10px !important;
        }

        /* Sidebar Brand */
        .sidebar-brand {
            padding: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            gap: 12px;
            min-height: 80px;
            transition: all 0.3s ease;
        }

        .sidebar.collapsed .sidebar-brand {
            padding: 20px 10px;
            justify-content: center;
        }

        .sidebar-brand-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-size: 20px;
            flex-shrink: 0;
        }

        .sidebar-brand-text {
            color: #ffffff;
            font-weight: 700;
            font-size: 16px;
            line-height: 1.2;
            transition: opacity 0.3s ease;
        }

        .sidebar.collapsed .sidebar-brand-text {
            opacity: 0;
            width: 0;
            overflow: hidden;
        }

        /* Sidebar Navigation */
        .sidebar .nav {
            padding: 20px 0;
        }

        .sidebar .nav-link {
            color: #ecf0f1 !important;
            padding: 12px 20px !important;
            margin: 5px 0 !important;
            border-left: 3px solid transparent !important;
            border-radius: 0 !important;
            transition: all 0.3s !important;
            display: flex !important;
            align-items: center !important;
            gap: 12px !important;
            white-space: nowrap !important;
        }

        .sidebar .nav-link i {
            min-width: 20px !important;
            text-align: center !important;
        }

        .sidebar .nav-text {
            transition: opacity 0.3s ease, width 0.3s ease !important;
        }

        .sidebar .nav-link:hover {
            background-color: rgba(255,255,255,0.1) !important;
            border-left-color: var(--primary) !important;
        }

        .sidebar .nav-link.active {
            background: var(--primary) !important;
            border-left-color: white !important;
            box-shadow: none !important;
        }

        /* Main Content Area */
        .main-wrapper {
            flex: 1;
            margin-left: 250px;
            transition: margin-left 0.3s ease;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .sidebar.collapsed + .main-wrapper {
            margin-left: 70px;
        }

        /* Navbar in Content Area */
        .navbar {
            min-height: 68px !important;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%) !important;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1) !important;
            position: sticky;
            top: 0;
            z-index: 999;
        }

        .navbar > .container-fluid {
            min-height: 68px !important;
            padding-left: 24px !important;
            padding-right: 24px !important;
        }

        .navbar-brand {
            display: inline-flex !important;
            align-items: center !important;
            gap: 12px !important;
            min-height: 40px !important;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif !important;
            font-weight: 700 !important;
            font-size: 18px !important;
            line-height: 1.2 !important;
            white-space: normal !important;
            max-width: 340px !important;
        }

        .navbar-brand .brand-mark {
            width: 42px;
            height: 42px;
            min-width: 42px;
            border-radius: 14px;
            display: inline-grid;
            place-items: center;
            background: rgba(255,255,255,0.15);
            border: 1px solid rgba(255,255,255,0.18);
        }

        .navbar-brand .brand-mark img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            border-radius: 12px;
        }

        .navbar-brand .brand-text {
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 2px;
            color: #f8fafc;
        }

        .navbar-brand .brand-subtitle {
            font-size: 0.65rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            opacity: 0.85;
            margin: 0;
        }

        .navbar-brand strong {
            font-size: 1rem;
            font-weight: 800;
            line-height: 1.1;
            display: block;
            color: #ffffff;
        }

        .navbar-brand .brand-year {
            font-size: 0.75rem;
            opacity: 0.82;
            margin: 0;
        }

        /* Sidebar Toggle Button */
        .sidebar-toggle {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: #ffffff;
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
        }

        .sidebar-toggle:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        /* Content Area */
        .main-content {
            width: 100%;
            max-width: 1280px;
            margin: 0 auto;
            padding: 32px 28px !important;
            min-width: 0;
            flex: 1;
        }

        .user-info {
            display: flex !important;
            align-items: center !important;
            gap: 10px !important;
            color: white !important;
            min-height: 40px !important;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif !important;
        }

        .user-info small,
        .user-info div {
            line-height: 1.2 !important;
        }

        .user-avatar {
            width: 40px !important;
            height: 40px !important;
            min-width: 40px !important;
            background: rgba(255,255,255,0.3) !important;
            border-radius: 50% !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }

        .admin-theme-toggle {
            min-height: 34px !important;
            line-height: 1 !important;
            white-space: nowrap !important;
        }

        .admin-page-title,
        .main-content h1,
        .main-content h2 {
            color: #2c3e50;
        }

        /* Mobile Responsive */
        @media (max-width: 991px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.mobile-show {
                transform: translateX(0);
            }

            .main-wrapper {
                margin-left: 0 !important;
            }

            .admin-mobile-menu-btn {
                display: inline-flex !important;
            }
        }

        @media (max-width: 768px) {
            .navbar > .container-fluid {
                align-items: flex-start !important;
            }

            .navbar-brand {
                max-width: calc(100vw - 74px) !important;
                font-size: 15px !important;
                white-space: normal !important;
                line-height: 1.25 !important;
            }

            .main-content {
                max-width: none;
                margin: 0;
                padding: 18px 12px !important;
            }
        }

        /* Mobile Overlay */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }

        .sidebar-overlay.show {
            display: block;
        }

        @media (min-width: 992px) {
            .admin-mobile-menu-btn {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    @include('partials.admin-sidebar')

    <!-- Sidebar Overlay for Mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Main Wrapper -->
    <div class="main-wrapper">
        <!-- Navbar -->
        @include('partials.admin-navbar')

        <!-- Main Content -->
        <main class="main-content">
            @yield('content')
        </main>
    </div>

    <!-- Toast Container -->
    <x-toast-container position="top-right" />

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/modal.js') }}?v={{ time() }}"></script>
    
    <script>
    // Sidebar Toggle Functionality
    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.getElementById('adminSidebar');
        const toggleBtn = document.getElementById('sidebarToggle');
        const mainWrapper = document.querySelector('.main-wrapper');
        const mobileMenuBtn = document.querySelector('.admin-mobile-menu-btn');
        const overlay = document.getElementById('sidebarOverlay');
        
        // Load saved state from localStorage
        const sidebarCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
        if (sidebarCollapsed && window.innerWidth >= 992) {
            sidebar.classList.add('collapsed');
            if (mainWrapper) {
                mainWrapper.style.marginLeft = '70px';
            }
        }
        
        // Initialize Bootstrap tooltips for collapsed state
        let tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        let tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl, {
                trigger: sidebar.classList.contains('collapsed') ? 'hover' : 'manual'
            });
        });
        
        // Toggle sidebar on button click (Desktop)
        if (toggleBtn) {
            toggleBtn.addEventListener('click', function() {
                sidebar.classList.toggle('collapsed');
                const isCollapsed = sidebar.classList.contains('collapsed');
                
                // Update main wrapper margin
                if (mainWrapper) {
                    mainWrapper.style.marginLeft = isCollapsed ? '70px' : '250px';
                }
                
                // Save state to localStorage
                localStorage.setItem('sidebarCollapsed', isCollapsed);
                
                // Update tooltips
                tooltipList.forEach(function(tooltip) {
                    if (isCollapsed) {
                        tooltip.enable();
                    } else {
                        tooltip.disable();
                    }
                });
                
                // Update toggle button icon
                const icon = toggleBtn.querySelector('i');
                if (isCollapsed) {
                    icon.classList.remove('fa-bars');
                    icon.classList.add('fa-angles-right');
                } else {
                    icon.classList.remove('fa-angles-right');
                    icon.classList.add('fa-bars');
                }
            });
            
            // Set initial icon state
            const icon = toggleBtn.querySelector('i');
            if (sidebarCollapsed) {
                icon.classList.remove('fa-bars');
                icon.classList.add('fa-angles-right');
            }
        }

        // Mobile menu toggle
        if (mobileMenuBtn) {
            mobileMenuBtn.addEventListener('click', function() {
                sidebar.classList.add('mobile-show');
                overlay.classList.add('show');
            });
        }

        // Close sidebar on overlay click
        if (overlay) {
            overlay.addEventListener('click', function() {
                sidebar.classList.remove('mobile-show');
                overlay.classList.remove('show');
            });
        }
        
        // Disable tooltips initially if sidebar is expanded
        if (!sidebarCollapsed) {
            tooltipList.forEach(function(tooltip) {
                tooltip.disable();
            });
        }
    });
    </script>
    
    <!-- Form Validation -->
    <script src="{{ asset('js/form-validation.js?v=' . time()) }}"></script>
    
    <!-- Auto-save Draft -->
    <script src="{{ asset('js/auto-save-draft.js?v=' . time()) }}"></script>
    
    @stack('scripts')
</body>
</html>
