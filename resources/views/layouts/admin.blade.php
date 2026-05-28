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
    @stack('styles')
    @include('partials.admin-theme-vars')
    <style>
        html {
            overflow-y: scroll;
        }

        body {
            background-color: #f5f7fa !important;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif !important;
        }

        .navbar {
            min-height: 68px !important;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%) !important;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1) !important;
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

        .sidebar {
            width: 250px !important;
            background: #2c3e50 !important;
            min-height: 100vh !important;
            padding: 20px 0 !important;
            flex: 0 0 250px !important;
        }

        .sidebar .nav-link {
            color: #ecf0f1 !important;
            padding: 12px 20px !important;
            margin: 5px 0 !important;
            border-left: 3px solid transparent !important;
            border-radius: 0 !important;
            transition: all 0.3s !important;
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

        .main-content {
            width: 100%;
            max-width: 1280px;
            margin: 0 auto;
            padding: 32px 28px !important;
            min-width: 0;
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

        .admin-logout-btn {
            min-height: 34px !important;
            padding: 6px 12px !important;
            font-size: 14px !important;
            line-height: 1.4 !important;
            white-space: nowrap !important;
        }

        .admin-page-title,
        .main-content h1,
        .main-content h2 {
            color: #2c3e50;
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

            .navbar-collapse {
                width: 100% !important;
                padding-top: 12px !important;
            }

            .navbar-collapse .ms-auto {
                width: 100% !important;
                align-items: stretch !important;
                gap: 10px !important;
                flex-direction: column !important;
            }

            .admin-theme-toggle {
                width: 100% !important;
                justify-content: center !important;
                margin-right: 0 !important;
            }

            .user-info {
                justify-content: center !important;
                padding: 8px 0 !important;
            }

            .navbar form {
                margin-left: 0 !important;
            }

            .navbar form .btn {
                width: 100% !important;
            }

            .admin-shell {
                display: block !important;
            }

            .admin-shell > .sidebar {
                display: none !important;
            }

            .admin-mobile-menu-btn {
                min-height: 38px !important;
                white-space: nowrap !important;
            }

            .admin-sidebar-offcanvas {
                background: #2c3e50 !important;
                color: #fff !important;
                width: min(82vw, 320px) !important;
            }

            .admin-sidebar-offcanvas .offcanvas-header {
                border-bottom: 1px solid rgba(255,255,255,.12) !important;
            }

            .admin-sidebar-offcanvas .btn-close {
                filter: invert(1) grayscale(100%) brightness(200%);
            }

            .admin-sidebar-offcanvas .sidebar {
                display: block !important;
                width: 100% !important;
                min-height: auto !important;
                flex: none !important;
                padding: 0 !important;
                background: transparent !important;
            }

            .admin-sidebar-offcanvas .sidebar .nav {
                flex-direction: column !important;
                flex-wrap: nowrap !important;
                padding: 0 !important;
                gap: 0 !important;
            }

            .admin-sidebar-offcanvas .sidebar .nav-link {
                margin: 4px 0 !important;
                padding: 12px 14px !important;
                border-left: 3px solid transparent !important;
                border-bottom: 0 !important;
                border-radius: 10px !important;
                font-size: 14px !important;
            }

            .admin-sidebar-offcanvas .sidebar .nav-link.active {
                border-left-color: white !important;
            }

            .main-content {
                max-width: none;
                margin: 0;
                padding: 18px 12px !important;
            }
        }
    </style>
</head>
<body>
    @include('partials.admin-navbar')

    <div class="container-fluid admin-shell" style="display:flex;">
        @include('partials.admin-sidebar')

        <main class="main-content" style="flex:1;">
            @yield('content')
        </main>
    </div>

    <div class="offcanvas offcanvas-start admin-sidebar-offcanvas d-lg-none" tabindex="-1" id="adminSidebarOffcanvas" aria-labelledby="adminSidebarOffcanvasLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="adminSidebarOffcanvasLabel"><i class="fas fa-bars me-2"></i>Menu Admin</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Tutup"></button>
        </div>
        <div class="offcanvas-body">
            <div class="d-grid gap-2 mb-3">
                <button type="button" class="admin-theme-toggle" data-admin-theme-toggle></button>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-outline-light w-100"><i class="fas fa-sign-out-alt"></i> Logout</button>
                </form>
            </div>
            @include('partials.admin-sidebar')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
