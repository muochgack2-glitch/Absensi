<script>
    (function () {
        // Get theme from server (user preference from database)
        var serverTheme = '{{ auth()->check() ? (auth()->user()->theme_preference ?? 'dark') : 'dark' }}';
        
        // Check if user has manually changed theme in current session
        var savedTheme = localStorage.getItem('admin_theme');
        
        // Use localStorage if exists (user changed theme), otherwise use server preference
        var currentTheme = savedTheme || serverTheme;
        
        if (currentTheme === 'dark') {
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
    
    /* Fix text colors in card body for dark mode */
    .admin-dark .card-body {
        color: #e5e7eb !important;
    }
    .admin-dark .card-body p,
    .admin-dark .card-body span:not(.badge):not(.text-muted),
    .admin-dark .card-body strong,
    .admin-dark .card-body div {
        color: #e5e7eb !important;
    }
    .admin-dark .card-title {
        color: #f8fafc !important;
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
    /* Modal System - Light Mode */
    .modal-modern .modal-content,
    .modal-js-generated[data-theme="light"] .modal-js-content {
        background: #ffffff !important;
        color: #1e293b !important;
        border: 1px solid #e2e8f0 !important;
    }
    .modal-confirm-body {
        padding: 2rem;
    }
    .modal-icon-circle {
        width: 80px;
        height: 80px;
        margin: 0 auto 1.5rem;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .modal-icon-main {
        font-size: 40px;
    }
    .modal-confirm-title,
    .modal-js-generated[data-theme="light"] .modal-confirm-title {
        font-size: 1.25rem;
        font-weight: 700;
        margin-bottom: 1rem;
        color: #1e293b !important;
    }
    .modal-confirm-message,
    .modal-js-generated[data-theme="light"] .modal-confirm-message {
        font-size: 0.875rem;
        color: #64748b !important;
        line-height: 1.6;
        margin-bottom: 1.5rem;
    }
    .modal-btn-cancel,
    .modal-btn-confirm-warning,
    .modal-btn-confirm-danger,
    .modal-btn-confirm-success,
    .modal-btn-confirm-info,
    .modal-btn-ok,
    .modal-js-generated[data-theme="light"] .modal-btn-cancel {
        min-width: 100px;
        padding: 0.5rem 1rem;
        font-weight: 600;
        border-radius: 0.5rem;
    }
    .modal-js-generated[data-theme="light"] .modal-btn-cancel {
        background: #f3f4f6 !important;
        border: 1px solid #d1d5db !important;
        color: #374151 !important;
    }
    
    /* Dark Mode Styles */
    .admin-dark body { background-color: #0f172a !important; color: #e5e7eb; }
    
    /* CSS Variables for Dark Mode */
    .admin-dark {
        --bg-primary: #111827;
        --bg-secondary: #1e293b;
        --text-primary: #e5e7eb;
        --text-secondary: #cbd5e1;
        --border-light: #334155;
    }
    
    /* Force dark mode on elements using CSS variables */
    .admin-dark [style*="background: var(--bg-primary)"],
    .admin-dark [style*="background-color: var(--bg-primary)"] {
        background: #111827 !important;
        background-color: #111827 !important;
    }
    .admin-dark [style*="color: var(--text-primary)"] {
        color: #e5e7eb !important;
    }
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
    
    /* Bootstrap Modal Dark Mode - Extra specificity */
    .admin-dark .modal .modal-content {
        background: #111827 !important;
        color: #e5e7eb !important;
        border-color: #334155 !important;
    }
    .admin-dark .modal .modal-header {
        background: #111827 !important;
        color: #e5e7eb !important;
        border-bottom-color: #334155 !important;
    }
    .admin-dark .modal .modal-body {
        background: #111827 !important;
        color: #e5e7eb !important;
    }
    .admin-dark .modal .modal-footer {
        background: #111827 !important;
        color: #e5e7eb !important;
        border-top-color: #334155 !important;
    }
    .admin-dark .modal .modal-title {
        color: #f8fafc !important;
    }
    .admin-dark .modal .btn-close {
        filter: invert(1) grayscale(100%) brightness(200%);
    }
    /* Force Bootstrap modal-fade dark mode */
    .admin-dark .modal-dialog .modal-content {
        background-color: #111827 !important;
    }
    .admin-dark #detailModal .modal-content,
    .admin-dark #detailModal .modal-body,
    .admin-dark #detailModal .modal-header,
    .admin-dark #detailModal .modal-footer {
        background-color: #111827 !important;
        color: #e5e7eb !important;
    }
    
    /* Dropdown items dark mode */
    .admin-dark .dropdown-item {
        color: #e5e7eb !important;
    }
    .admin-dark .dropdown-item:hover,
    .admin-dark .dropdown-item:focus {
        background-color: #1e293b !important;
        color: #f8fafc !important;
    }
    
    /* Nav tabs dark mode */
    .admin-dark .nav-tabs .nav-link {
        color: #cbd5e1 !important;
    }
    .admin-dark .nav-tabs .nav-link.active {
        background: #111827 !important;
        color: #f8fafc !important;
        border-color: #334155 #334155 #111827 !important;
    }
    .admin-dark .nav-tabs .nav-link:hover {
        border-color: #334155 !important;
        color: #f8fafc !important;
    }
    
    /* Form check label dark mode */
    .admin-dark .form-check-label {
        color: #e5e7eb !important;
    }
    
    /* Code elements dark mode */
    .admin-dark code {
        background-color: #1e293b !important;
        color: #a5b4fc !important;
        padding: 0.2rem 0.4rem;
        border-radius: 0.25rem;
    }
    
    /* Alert light variant dark mode */
    .admin-dark .alert-light {
        background-color: #1e293b !important;
        border-color: #334155 !important;
        color: #cbd5e1 !important;
    }
    .admin-dark .text-muted { color: #94a3b8 !important; }
    .admin-dark .section-title,
    .admin-dark h1,
    .admin-dark h2,
    .admin-dark h3,
    .admin-dark h4,
    .admin-dark h5,
    .admin-dark h6 { color: #f8fafc !important; }
    .admin-dark .form-label { color: #e5e7eb !important; font-weight: 500; }
    .admin-dark .form-control,
    .admin-dark .form-select,
    .admin-dark textarea { background-color: #020617 !important; color: #f8fafc !important; border-color: #334155 !important; }
    .admin-dark .form-control::placeholder { color: #64748b; }
    .admin-dark .form-control:focus,
    .admin-dark .form-select:focus { border-color: #818cf8 !important; box-shadow: 0 0 0 .25rem rgba(99,102,241,.25) !important; }
    .admin-dark .table { --bs-table-bg: #111827 !important; --bs-table-color: #e5e7eb !important; --bs-table-border-color: #334155 !important; background-color: #111827 !important; }
    .admin-dark .table-striped > tbody > tr:nth-of-type(odd) > * { --bs-table-bg-type: #0f172a !important; background-color: #0f172a !important; }
    .admin-dark .table-hover > tbody > tr:hover > * { --bs-table-bg-state: #1e293b !important; background-color: #1e293b !important; }
    .admin-dark .table thead,
    .admin-dark .table tbody,
    .admin-dark .table tfoot {
        background-color: #111827 !important;
    }
    .admin-dark .table thead th,
    .admin-dark .table tbody td,
    .admin-dark .table tfoot td {
        background-color: #111827 !important;
        color: #e5e7eb !important;
        border-color: #334155 !important;
    }
    .admin-dark .table tbody tr {
        background-color: #111827 !important;
    }
    .admin-dark .table tbody tr:hover {
        background-color: #1e293b !important;
    }
    .admin-dark .table tbody tr:hover td {
        background-color: #1e293b !important;
    }
    /* Extra specificity for stubborn tables */
    .admin-dark .card-body .table,
    .admin-dark .card-body .table tbody,
    .admin-dark .card-body .table thead,
    .admin-dark .card-body .table tfoot {
        background-color: #111827 !important;
    }
    .admin-dark .card-body .table th,
    .admin-dark .card-body .table td {
        background-color: #111827 !important;
        color: #e5e7eb !important;
    }
    .admin-dark .table-responsive .table,
    .admin-dark .table-responsive .table tbody,
    .admin-dark .table-responsive .table thead,
    .admin-dark .table-responsive .table tfoot {
        background-color: #111827 !important;
    }
    .admin-dark .table-responsive .table th,
    .admin-dark .table-responsive .table td {
        background-color: #111827 !important;
        color: #e5e7eb !important;
    }
    /* Nuclear option - force all table elements to dark */
    .admin-dark table,
    .admin-dark table tbody,
    .admin-dark table thead,
    .admin-dark table tfoot,
    .admin-dark table tr {
        background-color: #111827 !important;
    }
    .admin-dark table th,
    .admin-dark table td {
        background-color: #111827 !important;
        color: #e5e7eb !important;
    }
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
    
    /* Button Secondary - Dark Mode */
    .admin-dark .btn-secondary {
        background-color: #334155 !important;
        border-color: #475569 !important;
        color: #e5e7eb !important;
    }
    .admin-dark .btn-secondary:hover {
        background-color: #475569 !important;
        border-color: #64748b !important;
    }
    .admin-dark .btn-secondary:focus {
        box-shadow: 0 0 0 0.25rem rgba(71, 85, 105, 0.5) !important;
    }
    
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
    
    /* Bootstrap Badges - Dark Mode */
    .admin-dark .badge.bg-primary {
        background-color: rgba(99, 102, 241, 0.25) !important;
        color: #a5b4fc !important;
    }
    .admin-dark .badge.bg-success {
        background-color: rgba(34, 197, 94, 0.25) !important;
        color: #86efac !important;
    }
    .admin-dark .badge.bg-danger {
        background-color: rgba(239, 68, 68, 0.25) !important;
        color: #fca5a5 !important;
    }
    .admin-dark .badge.bg-warning {
        background-color: rgba(251, 191, 36, 0.25) !important;
        color: #fde047 !important;
    }
    .admin-dark .badge.bg-info {
        background-color: rgba(59, 130, 246, 0.25) !important;
        color: #93c5fd !important;
    }
    .admin-dark .badge.bg-secondary {
        background-color: rgba(148, 163, 184, 0.25) !important;
        color: #cbd5e1 !important;
    }
    
    /* Success/Danger Subtle Badges */
    .admin-dark .bg-success-subtle {
        background-color: rgba(34, 197, 94, 0.15) !important;
    }
    .admin-dark .text-success {
        color: #4ade80 !important;
    }
    .admin-dark .border-success-subtle {
        border-color: rgba(34, 197, 94, 0.3) !important;
    }
    
    /* Modal System - Dark Mode Support */
    /* Dark theme classes */
    .theme-dark .modal-js-content.bg-dark-mode,
    .modal-js-generated.theme-dark .modal-js-content {
        background: #1e293b !important;
        color: #e5e7eb !important;
        border: 1px solid #334155 !important;
    }
    .theme-dark .text-light-title,
    .modal-js-generated.theme-dark .modal-confirm-title {
        color: #f8fafc !important;
    }
    .theme-dark .text-light-message,
    .modal-js-generated.theme-dark .modal-confirm-message {
        color: #cbd5e1 !important;
    }
    .theme-dark .btn-dark-cancel,
    .modal-js-generated.theme-dark .modal-btn-cancel {
        background: #334155 !important;
        border: 1px solid #475569 !important;
        color: #e5e7eb !important;
    }
    .theme-dark .btn-dark-cancel:hover,
    .modal-js-generated.theme-dark .modal-btn-cancel:hover {
        background: #475569 !important;
    }
    
    /* Light theme classes */
    .theme-light .modal-js-content.bg-light-mode,
    .modal-js-generated.theme-light .modal-js-content {
        background: #ffffff !important;
        color: #1e293b !important;
        border: 1px solid #e2e8f0 !important;
    }
    .theme-light .text-dark-title,
    .modal-js-generated.theme-light .modal-confirm-title {
        color: #1e293b !important;
    }
    .theme-light .text-dark-message,
    .modal-js-generated.theme-light .modal-confirm-message {
        color: #64748b !important;
    }
    .theme-light .btn-light-cancel,
    .modal-js-generated.theme-light .modal-btn-cancel {
        background: #f3f4f6 !important;
        border: 1px solid #d1d5db !important;
        color: #374151 !important;
    }
    
    /* Additional specificity for admin-dark class */
    .admin-dark .modal-modern .modal-content,
    .admin-dark .modal-js-generated[data-theme="dark"] .modal-js-content,
    .modal-js-generated[data-theme="dark"] .modal-js-content,
    .admin-dark .modal-modern .modal-js-content,
    .admin-dark .modal-js-generated .modal-js-content {
        background: #1e293b !important;
        color: #e5e7eb !important;
        border-color: #334155 !important;
        border: 1px solid #334155 !important;
    }
    .admin-dark .modal-modern .modal-title,
    .admin-dark .modal-modern .modal-confirm-title,
    .admin-dark .modal-js-generated .modal-confirm-title,
    .modal-js-generated[data-theme="dark"] .modal-confirm-title,
    .admin-dark .modal-modern .modal-js-content .modal-confirm-title,
    .admin-dark .modal-js-generated .modal-js-content .modal-confirm-title {
        color: #f8fafc !important;
    }
    .admin-dark .modal-modern .modal-message,
    .admin-dark .modal-modern .modal-confirm-message,
    .admin-dark .modal-js-generated .modal-confirm-message,
    .modal-js-generated[data-theme="dark"] .modal-confirm-message,
    .admin-dark .modal-modern .modal-js-content .modal-confirm-message,
    .admin-dark .modal-js-generated .modal-js-content .modal-confirm-message {
        color: #cbd5e1 !important;
    }
    .admin-dark .modal-modern .btn-secondary,
    .admin-dark .modal-modern .modal-btn-cancel,
    .admin-dark .modal-js-generated .modal-btn-cancel,
    .modal-js-generated[data-theme="dark"] .modal-btn-cancel,
    .admin-dark .modal-modern .modal-js-content .modal-btn-cancel,
    .admin-dark .modal-js-generated .modal-js-content .modal-btn-cancel {
        background: #334155 !important;
        border-color: #475569 !important;
        border: 1px solid #475569 !important;
        color: #e5e7eb !important;
    }
    .admin-dark .modal-modern .btn-secondary:hover,
    .admin-dark .modal-modern .modal-btn-cancel:hover,
    .admin-dark .modal-js-generated .modal-btn-cancel:hover,
    .modal-js-generated[data-theme="dark"] .modal-btn-cancel:hover,
    .admin-dark .modal-modern .modal-js-content .modal-btn-cancel:hover,
    .admin-dark .modal-js-generated .modal-js-content .modal-btn-cancel:hover {
        background: #475569 !important;
    }
    
    /* Pagination Dark Mode */
    .admin-dark .pagination {
        --bs-pagination-bg: #111827;
        --bs-pagination-border-color: #334155;
        --bs-pagination-hover-bg: #1e293b;
        --bs-pagination-hover-border-color: #475569;
        --bs-pagination-focus-bg: #1e293b;
        --bs-pagination-focus-color: #f8fafc;
        --bs-pagination-active-bg: #6366f1;
        --bs-pagination-active-border-color: #6366f1;
        --bs-pagination-disabled-bg: #111827;
        --bs-pagination-disabled-border-color: #334155;
    }
    .admin-dark .page-link {
        background-color: #111827 !important;
        border-color: #334155 !important;
        color: #cbd5e1 !important;
    }
    .admin-dark .page-link:hover {
        background-color: #1e293b !important;
        border-color: #475569 !important;
        color: #f8fafc !important;
    }
    .admin-dark .page-item.active .page-link {
        background-color: #6366f1 !important;
        border-color: #6366f1 !important;
        color: #ffffff !important;
    }
    .admin-dark .page-item.disabled .page-link {
        background-color: #111827 !important;
        border-color: #334155 !important;
        color: #64748b !important;
    }
    
    /* Card Footer Dark Mode */
    .admin-dark .card-footer {
        background-color: #111827 !important;
        border-top-color: #334155 !important;
        color: #e5e7eb !important;
    }
    
    /* Input Group Dark Mode */
    .admin-dark .input-group-text {
        background-color: #1e293b !important;
        border-color: #334155 !important;
        color: #cbd5e1 !important;
    }
    
    /* List Group Dark Mode */
    .admin-dark .list-group-item {
        background-color: #111827 !important;
        border-color: #334155 !important;
        color: #e5e7eb !important;
    }
    .admin-dark .list-group-item:hover {
        background-color: #1e293b !important;
    }
    
    /* Border utilities Dark Mode */
    .admin-dark .border {
        border-color: #334155 !important;
    }
    .admin-dark .border-top {
        border-top-color: #334155 !important;
    }
    .admin-dark .border-bottom {
        border-bottom-color: #334155 !important;
    }
    .admin-dark .border-start {
        border-left-color: #334155 !important;
    }
    .admin-dark .border-end {
        border-right-color: #334155 !important;
    }
    
    /* Btn Outline Dark Mode */
    .admin-dark .btn-outline-primary {
        color: #a5b4fc !important;
        border-color: #6366f1 !important;
        background-color: transparent !important;
    }
    .admin-dark .btn-outline-primary:hover {
        background-color: rgba(99, 102, 241, 0.2) !important;
        border-color: #818cf8 !important;
        color: #c7d2fe !important;
    }
    .admin-dark .btn-outline-primary.active,
    .admin-dark .btn-check:checked + .btn-outline-primary,
    .admin-dark input[type="radio"]:checked + .btn-outline-primary {
        background-color: #6366f1 !important;
        border-color: #6366f1 !important;
        color: #ffffff !important;
    }
    .admin-dark .btn-group .btn-check:checked + label {
        background-color: #6366f1 !important;
        border-color: #6366f1 !important;
        color: #ffffff !important;
    }
    .admin-dark .btn-outline-secondary {
        color: #cbd5e1 !important;
        border-color: #475569 !important;
    }
    .admin-dark .btn-outline-secondary:hover {
        background-color: #334155 !important;
        border-color: #475569 !important;
        color: #fff !important;
    }
    .admin-dark .btn-outline-danger {
        color: #f87171 !important;
        border-color: #ef4444 !important;
    }
    .admin-dark .btn-outline-danger:hover {
        background-color: #ef4444 !important;
        border-color: #ef4444 !important;
        color: #ffffff !important;
    }
    .admin-dark .btn-outline-success {
        color: #4ade80 !important;
        border-color: #22c55e !important;
    }
    .admin-dark .btn-outline-success:hover {
        background-color: #22c55e !important;
        border-color: #22c55e !important;
        color: #ffffff !important;
    }
    
    /* Small text Dark Mode */
    .admin-dark small,
    .admin-dark .small {
        color: #94a3b8;
    }
    
    /* Empty state Dark Mode */
    .admin-dark .empty-state {
        color: #64748b !important;
    }
    
    /* Override bg-white in dark mode */
    .admin-dark .bg-white {
        background-color: #111827 !important;
    }
    .admin-dark .card-header.bg-white {
        background-color: #111827 !important;
    }
    .admin-dark .card-footer.bg-white {
        background-color: #111827 !important;
    }
    .admin-dark .input-group-text.bg-white {
        background-color: #1e293b !important;
    }
    .admin-dark #qrCodeDisplay.bg-white {
        background-color: #ffffff !important; /* QR code needs white background */
    }
    
    /* Table Light Dark Mode Override */
    .admin-dark .table-light {
        --bs-table-bg: #1e293b !important;
        --bs-table-color: #cbd5e1 !important;
        --bs-table-border-color: #334155 !important;
    }
    .admin-dark .table-light th {
        background-color: #1e293b !important;
        color: #cbd5e1 !important;
        border-color: #334155 !important;
    }
    
    /* Table Responsive Container Dark Mode */
    .admin-dark .table-responsive {
        background-color: transparent !important;
    }
    
    /* Ensure all table cells have proper dark background */
    .admin-dark .table > :not(caption) > * > * {
        background-color: transparent !important;
        color: #e5e7eb !important;
    }
    .admin-dark .table tbody tr {
        background-color: transparent !important;
    }
    .admin-dark .table tbody tr:hover {
        background-color: #1e293b !important;
    }
    
    /* Fix Bootstrap default table backgrounds */
    .admin-dark .table > tbody {
        background-color: transparent !important;
    }
    .admin-dark .table > thead {
        background-color: transparent !important;
    }
    .admin-dark .table > tfoot {
        background-color: transparent !important;
    }
    
    /* Force dark background on card-body with tables */
    .admin-dark .card-body.p-0 {
        background-color: #111827 !important;
    }
    .admin-dark .card-body.p-0 .table-responsive {
        background-color: #111827 !important;
    }
    .admin-dark .card-body.p-0 .table {
        background-color: #111827 !important;
    }
    
    /* Fix table inside card with var(--bg-primary) */
    .admin-dark [style*="background: var(--bg-primary)"] .table,
    .admin-dark [style*="background: var(--bg-primary)"] .table thead,
    .admin-dark [style*="background: var(--bg-primary)"] .table tbody,
    .admin-dark [style*="background: var(--bg-primary)"] .table tfoot {
        background-color: #111827 !important;
    }
    .admin-dark [style*="background: var(--bg-primary)"] .table th,
    .admin-dark [style*="background: var(--bg-primary)"] .table td {
        background-color: #111827 !important;
        color: #e5e7eb !important;
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
                var theme = isDark ? 'dark' : 'light';
                
                // Save to localStorage
                localStorage.setItem('admin_theme', theme);
                
                // Save to database via AJAX
                fetch('{{ route("profile.update-theme") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ theme: theme })
                }).catch(function(error) {
                    console.error('Failed to save theme preference:', error);
                });
                
                applyLabel();
            });
        });

        applyLabel();
    });
</script>
