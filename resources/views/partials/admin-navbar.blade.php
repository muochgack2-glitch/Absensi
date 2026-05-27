<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ route('dashboard') }}">
            <i class="fas fa-graduation-cap"></i> SPMB (Sistem Penerimaan Murid Baru)
        </a>
        <button class="btn btn-sm btn-outline-light admin-mobile-menu-btn d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#adminSidebarOffcanvas" aria-controls="adminSidebarOffcanvas">
            <i class="fas fa-bars"></i> Menu
        </button>
        <div class="navbar-collapse d-none d-lg-flex" id="navbarNav">
            <div class="ms-auto d-flex align-items-center">
                <button type="button" class="admin-theme-toggle me-3" data-admin-theme-toggle></button>
                <div class="user-info">
                    <div class="user-avatar"><i class="fas fa-user"></i></div>
                    <div>
                        <small>Selamat datang,</small>
                        <div style="font-weight: 600;">{{ Session::get('admin_name') }}</div>
                    </div>
                </div>
                <form action="{{ route('logout') }}" method="POST" style="margin-left: 15px;">
                    @csrf
                    <button type="submit" class="btn btn-outline-light admin-logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</button>
                </form>
            </div>
        </div>
    </div>
</nav>
