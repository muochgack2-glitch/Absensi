@include('partials.theme-vars')
<style>
    .navbar {
        background: linear-gradient(135deg, var(--theme-primary) 0%, var(--theme-secondary) 100%) !important;
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
