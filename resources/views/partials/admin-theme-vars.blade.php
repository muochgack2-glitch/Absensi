@include('partials.theme-vars')
<style>
    .navbar {
        background: linear-gradient(135deg, var(--navbar-bg-start) 0%, var(--navbar-bg-end) 100%) !important;
        border-bottom: 1px solid var(--navbar-border) !important;
    }
    
    .navbar-title,
    .navbar-title-main,
    .navbar-title-sub,
    .school-name,
    .year-text,
    .separator {
        color: var(--navbar-text) !important;
    }
    
    /* Light theme mobile button */
    .admin-mobile-menu-btn {
        border-color: rgba(255, 255, 255, 0.5) !important;
        color: #ffffff !important;
        background: rgba(255, 255, 255, 0.15) !important;
    }
    
    .admin-mobile-menu-btn:hover {
        background: rgba(255, 255, 255, 0.25) !important;
        border-color: rgba(255, 255, 255, 0.8) !important;
    }
    
    /* Dark theme mobile button */
    .admin-dark .admin-mobile-menu-btn,
    [data-theme="dark"] .admin-mobile-menu-btn {
        border-color: var(--navbar-text) !important;
        color: var(--navbar-text) !important;
        background: transparent !important;
    }
    
    .admin-dark .admin-mobile-menu-btn:hover,
    [data-theme="dark"] .admin-mobile-menu-btn:hover {
        background: var(--bg-secondary) !important;
        border-color: var(--navbar-text) !important;
    }

    .sidebar .nav-link.active,
    .btn-primary {
        background-color: var(--theme-primary) !important;
        border-color: var(--theme-primary) !important;
    }

    .sidebar .nav-link:hover {
        border-left-color: var(--theme-primary) !important;
    }
</style>
