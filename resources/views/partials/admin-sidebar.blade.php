<div class="sidebar" style="width: 250px;">
    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                <i class="fas fa-home"></i> Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('pendaftar.index') || request()->routeIs('pendaftar.create') || request()->routeIs('pendaftar.edit') || request()->routeIs('pendaftar.show') ? 'active' : '' }}" href="{{ route('pendaftar.index') }}">
                <i class="fas fa-users"></i> Data Pendaftar
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('pendaftar.verification-index') || request()->routeIs('pendaftar.daftar-ulang') ? 'active' : '' }}" href="{{ route('pendaftar.verification-index') }}">
                <i class="fas fa-money-bill"></i> Verifikasi Daftar Ulang
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('report.*') ? 'active' : '' }}" href="{{ route('report.index') }}">
                <i class="fas fa-file-pdf"></i> Laporan & Cetak
            </a>
        </li>
        @if(auth()->check() && auth()->user()->isAdministrator())
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}" href="{{ route('settings.index') }}">
                <i class="fas fa-cog"></i> Pengaturan Sistem
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">
                <i class="fas fa-user-shield"></i> Manajemen User
            </a>
        </li>
        @endif
    </ul>
</div>
