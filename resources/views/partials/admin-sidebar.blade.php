<div class="sidebar" id="adminSidebar">
    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}" data-bs-toggle="tooltip" data-bs-placement="right" title="Dashboard">
                <i class="fas fa-home"></i> <span class="nav-text">Dashboard</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('pendaftar.index') || request()->routeIs('pendaftar.create') || request()->routeIs('pendaftar.edit') || request()->routeIs('pendaftar.show') ? 'active' : '' }}" href="{{ route('pendaftar.index') }}" data-bs-toggle="tooltip" data-bs-placement="right" title="Data Pendaftar">
                <i class="fas fa-users"></i> <span class="nav-text">Data Pendaftar</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('pendaftar.verification-index') || request()->routeIs('pendaftar.daftar-ulang') ? 'active' : '' }}" href="{{ route('pendaftar.verification-index') }}" data-bs-toggle="tooltip" data-bs-placement="right" title="Verifikasi Daftar Ulang">
                <i class="fas fa-money-bill"></i> <span class="nav-text">Verifikasi Daftar Ulang</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('report.*') ? 'active' : '' }}" href="{{ route('report.index') }}" data-bs-toggle="tooltip" data-bs-placement="right" title="Laporan & Cetak">
                <i class="fas fa-file-pdf"></i> <span class="nav-text">Laporan & Cetak</span>
            </a>
        </li>
        @if(auth()->check() && auth()->user()->isAdministrator())
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}" href="{{ route('settings.index') }}" data-bs-toggle="tooltip" data-bs-placement="right" title="Pengaturan Sistem">
                <i class="fas fa-cog"></i> <span class="nav-text">Pengaturan Sistem</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}" data-bs-toggle="tooltip" data-bs-placement="right" title="Manajemen User">
                <i class="fas fa-user-shield"></i> <span class="nav-text">Manajemen User</span>
            </a>
        </li>
        @endif
        <li class="nav-item mt-auto">
            <form id="logoutForm" action="{{ route('logout') }}" method="POST">
                @csrf
                <a class="nav-link text-danger" href="#" onclick="confirmLogout(event)" data-bs-toggle="tooltip" data-bs-placement="right" title="Logout">
                    <i class="fas fa-sign-out-alt"></i> <span class="nav-text">Logout</span>
                </a>
            </form>
        </li>
    </ul>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmLogout(event) {
    event.preventDefault();
    
    Swal.fire({
        title: 'Konfirmasi Logout',
        text: 'Apakah Anda yakin ingin keluar dari sistem?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Keluar',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('logoutForm').submit();
        }
    });
}
</script>
