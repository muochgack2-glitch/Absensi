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
    .admin-dark .alert-success { background: #052e1a; border-color: #166534; color: #bbf7d0; }
    .admin-dark .alert-danger { background: #450a0a; border-color: #991b1b; color: #fecaca; }
    .admin-dark .btn-outline-secondary { color: #cbd5e1; border-color: #475569; }
    .admin-dark .btn-outline-secondary:hover { background: #334155; color: #fff; }
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
