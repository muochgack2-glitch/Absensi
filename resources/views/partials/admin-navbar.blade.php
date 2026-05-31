<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid">
        @php
            $settings = \App\Models\SettingSystem::instance()->toSettingsArray();
            $namaSekolah = $settings['school_name'] ?? 'Sekolah';
            $tahunAjaran = $settings['academic_year'] ?? date('Y');
        @endphp
        
        <!-- Mobile Menu Button -->
        <button class="btn btn-sm btn-outline-light admin-mobile-menu-btn d-lg-none" type="button">
            <i class="fas fa-bars"></i> Menu
        </button>
        
        <!-- Navbar Title -->
        <div class="navbar-title">
            <div class="navbar-title-main">Sistem Penerimaan Murid Baru</div>
            <div class="navbar-title-sub">
                <span class="school-name">{{ $namaSekolah }}</span>
                <span class="separator">•</span>
                <span class="year-text">Tahun Pelajaran {{ $tahunAjaran }}</span>
            </div>
        </div>
        
        <div class="ms-auto d-flex align-items-center gap-3">
            <button type="button" class="admin-theme-toggle" data-admin-theme-toggle></button>
            <x-user-dropdown />
        </div>
    </div>
</nav>
