<script>
    (function () {
        var savedTheme = localStorage.getItem('admin_theme') || 'light';
        if (savedTheme === 'dark') {
            document.documentElement.classList.add('admin-dark');
        }
    })();
</script>
<style>
    .admin-theme-toggle {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border: 2px solid #cbd5e1;
        border-radius: 999px;
        background: #ffffff;
        color: #1e293b;
        padding: 8px 16px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: .2s ease;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }
    .admin-theme-toggle:hover { 
        background: #f8fafc; 
        border-color: var(--primary);
        color: var(--primary);
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
    }
    .admin-dark .admin-theme-toggle {
        border: 2px solid rgba(255,255,255,.3);
        background: rgba(255,255,255,.1);
        color: #ffffff;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
    }
    .admin-dark .admin-theme-toggle:hover { 
        background: rgba(255,255,255,.2); 
        border-color: rgba(255,255,255,.5);
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.4);
    }
    
    /* Light Mode Explicit Styles */
    .modal-content {
        background: #ffffff !important;
        color: #1e293b !important;
        border-color: #e2e8f0 !important;
    }
    .modal-header {
        background: #ffffff !important;
        border-bottom-color: #e2e8f0 !important;
    }
    .modal-title {
        color: #1e293b !important;
    }
    .modal-body {
        background: #ffffff !important;
        color: #1e293b !important;
    }
    .modal-footer {
        background: #ffffff !important;
        border-top-color: #e2e8f0 !important;
    }
    .form-label {
        color: #475569 !important;
    }
    .form-control,
    .form-select,
    textarea {
        background-color: #ffffff !important;
        color: #1e293b !important;
        border-color: #cbd5e1 !important;
    }
    .form-control:focus,
    .form-select:focus {
        border-color: var(--primary) !important;
        box-shadow: 0 0 0 0.25rem rgba(var(--primary-rgb), 0.25) !important;
    }
    .card {
        background: #ffffff !important;
        color: #1e293b !important;
        border-color: #e2e8f0 !important;
    }
    .card-header {
        background: #ffffff !important;
        color: #1e293b !important;
        border-bottom-color: #e2e8f0 !important;
    }
    .card-body {
        color: #1e293b !important;
    }
    
    /* Alert Styles - Light Mode */
    .alert-success {
        background-color: #d1fae5 !important;
        border-color: #6ee7b7 !important;
        color: #065f46 !important;
    }
    .alert-danger {
        background-color: #fee2e2 !important;
        border-color: #fca5a5 !important;
        color: #991b1b !important;
    }
    .alert-info {
        background-color: #dbeafe !important;
        border-color: #93c5fd !important;
        color: #1e40af !important;
    }
    .alert-warning {
        background-color: #fef3c7 !important;
        border-color: #fcd34d !important;
        color: #92400e !important;
    }
    .alert-light {
        background-color: #f8fafc !important;
        border-color: #e2e8f0 !important;
        color: #475569 !important;
    }
    .alert-heading {
        color: inherit !important;
    }
    
    /* Dark Mode Styles */
    .admin-dark body { background-color: #0f172a !important; color: #e5e7eb; }
    .admin-dark .navbar { 
        box-shadow: 0 2px 20px rgba(0,0,0,.35) !important;
        background: #111827 !important;
        border-bottom-color: #1e293b !important;
    }
    .admin-dark .navbar-brand,
    .admin-dark .navbar-title,
    .admin-dark .navbar-title-main,
    .admin-dark .user-info { color: #f8fafc !important; }
    .admin-dark .navbar-title-sub,
    .admin-dark .navbar-brand .brand-subtitle,
    .admin-dark .navbar-brand .brand-year { color: #cbd5e1 !important; }
    .admin-dark .navbar-title-sub .school-name,
    .admin-dark .navbar-title-sub .separator,
    .admin-dark .navbar-title-sub .year-text { color: #cbd5e1 !important; }
    .admin-dark .navbar-brand strong { color: #f8fafc !important; }
    .admin-dark .sidebar { background: #020617 !important; border-right-color: #1e293b !important; }
    .admin-dark .sidebar-brand { background: #020617 !important; border-bottom-color: #1e293b !important; }
    .admin-dark .sidebar-brand-text { color: #f8fafc !important; }
    .admin-dark .sidebar .nav-link { color: #cbd5e1 !important; }
    .admin-dark .sidebar .nav-link i { color: #94a3b8 !important; }
    .admin-dark .sidebar .nav-link:hover { background: rgba(148,163,184,.16) !important; }
    .admin-dark .sidebar .nav-link:hover i { color: #818cf8 !important; }
    .admin-dark .sidebar .nav-link.active { 
        background: linear-gradient(90deg, rgba(99, 102, 241, 0.2) 0%, rgba(99, 102, 241, 0.1) 100%) !important;
        color: #818cf8 !important;
    }
    .admin-dark .sidebar .nav-link.active i { color: #818cf8 !important; }
    .admin-dark .sidebar .submenu { background: #0f172a !important; }
    .admin-dark .sidebar .submenu-link { color: #94a3b8 !important; }
    .admin-dark .sidebar .submenu-link:hover { background: #1e293b !important; color: #818cf8 !important; }
    .admin-dark .sidebar .submenu-link.active { background: #1e293b !important; color: #818cf8 !important; }
    .admin-dark .main-content { background: #0f172a; }
    .admin-dark .card,
    .admin-dark .modal-content,
    .admin-dark .dropdown-menu { background: #111827 !important; color: #e5e7eb !important; border-color: #334155 !important; }
    .admin-dark .text-muted { color: #94a3b8 !important; }
    .admin-dark .section-title,
    .admin-dark h1,
    .admin-dark h2,
    .admin-dark h3,
    .admin-dark h4,
    .admin-dark h5,
    .admin-dark h6 { color: #f8fafc !important; }
    .admin-dark .form-label { color: #cbd5e1; }
    .admin-dark .form-control,
    .admin-dark .form-select,
    .admin-dark textarea { background-color: #020617 !important; color: #f8fafc !important; border-color: #334155 !important; }
    .admin-dark .form-control::placeholder { color: #64748b; }
    .admin-dark .form-control:focus,
    .admin-dark .form-select:focus { border-color: #818cf8 !important; box-shadow: 0 0 0 .25rem rgba(99,102,241,.25) !important; }
    .admin-dark .table { --bs-table-bg: #111827; --bs-table-color: #e5e7eb; --bs-table-border-color: #334155; }
    .admin-dark .table-striped > tbody > tr:nth-of-type(odd) > * { --bs-table-bg-type: #0f172a; }
    .admin-dark .table-hover > tbody > tr:hover > * { --bs-table-bg-state: #1e293b; }
    .admin-dark .nav-tabs { border-bottom-color: #334155; }
    .admin-dark .nav-tabs .nav-link { color: #cbd5e1; border-color: transparent; }
    .admin-dark .nav-tabs .nav-link.active { background: #111827; color: #fff; border-color: #334155 #334155 #111827; }
    .admin-dark .tab-content { border-color: #334155 !important; background: #111827; }
    
    /* Alert Styles - Dark Mode */
    .admin-dark .alert-success { 
        background: #052e1a !important; 
        border-color: #166534 !important; 
        color: #bbf7d0 !important; 
    }
    .admin-dark .alert-danger { 
        background: #450a0a !important; 
        border-color: #991b1b !important; 
        color: #fecaca !important; 
    }
    .admin-dark .alert-info { 
        background: #082f49 !important; 
        border-color: #075985 !important; 
        color: #bae6fd !important; 
    }
    .admin-dark .alert-warning { 
        background: #451a03 !important; 
        border-color: #92400e !important; 
        color: #fef3c7 !important; 
    }
    .admin-dark .alert-light { 
        background: #1e293b !important; 
        border-color: #334155 !important; 
        color: #cbd5e1 !important; 
    }
    .admin-dark .alert-heading {
        color: inherit !important;
    }
    
    .admin-dark .btn-outline-secondary { color: #cbd5e1; border-color: #475569; }
    .admin-dark .btn-outline-secondary:hover { background: #334155; color: #fff; }
    
    /* Background Opacity Classes - Dark Mode Support */
    .admin-dark .bg-primary.bg-opacity-10 { 
        background-color: rgba(99, 102, 241, 0.15) !important; 
    }
    .admin-dark .bg-success.bg-opacity-10 { 
        background-color: rgba(34, 197, 94, 0.15) !important; 
    }
    .admin-dark .bg-danger.bg-opacity-10 { 
        background-color: rgba(239, 68, 68, 0.15) !important; 
    }
    .admin-dark .bg-warning.bg-opacity-10 { 
        background-color: rgba(251, 191, 36, 0.15) !important; 
    }
    .admin-dark .bg-info.bg-opacity-10 { 
        background-color: rgba(59, 130, 246, 0.15) !important; 
    }
    .admin-dark .bg-secondary.bg-opacity-10 { 
        background-color: rgba(148, 163, 184, 0.15) !important; 
    }
    
    /* Text Color Adjustments for Dark Mode */
    .admin-dark .text-primary { color: #818cf8 !important; }
    .admin-dark .text-success { color: #4ade80 !important; }
    .admin-dark .text-danger { color: #f87171 !important; }
    .admin-dark .text-warning { color: #fbbf24 !important; }
    .admin-dark .text-info { color: #60a5fa !important; }
    .admin-dark .text-secondary { color: #94a3b8 !important; }
</style>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var buttons = document.querySelectorAll('[data-admin-theme-toggle]');
        var applyLabel = function () {
            var isDark = document.documentElement.classList.contains('admin-dark');
            buttons.forEach(function (button) {
                button.innerHTML = isDark ? '<i class="fas fa-sun"></i> Light' : '<i class="fas fa-moon"></i> Dark';
                button.setAttribute('aria-label', isDark ? 'Aktifkan light mode' : 'Aktifkan dark mode');
            });
        };

        buttons.forEach(function (button) {
            button.addEventListener('click', function () {
                var isDark = document.documentElement.classList.toggle('admin-dark');
                localStorage.setItem('admin_theme', isDark ? 'dark' : 'light');
                applyLabel();
            });
        });

        applyLabel();
    });
</script>
