@extends('layouts.app')

@section('content')
@php
    $schoolName = $settings['school_name'] ?? 'SPMB SIPDB';
    $logo = !empty($settings['school_logo']) ? asset('storage/' . $settings['school_logo']) : null;
    $statusLabel = $registrationOpen ? 'Pendaftaran Dibuka' : 'Pendaftaran Ditutup';
    $statusClass = $registrationOpen ? 'is-open' : 'is-closed';
    $jurusanQuota = $jurusanQuota ?? [];
    $rawContact = $settings['school_phone'] ?: ($settings['school_contact'] ?? '');
    $whatsappNumber = preg_replace('/\D+/', '', $rawContact);
    if (str_starts_with($whatsappNumber, '0')) {
        $whatsappNumber = '62' . substr($whatsappNumber, 1);
    }
    $whatsappMessage = rawurlencode('Halo Panitia SPMB ' . $schoolName . ', saya ingin bertanya tentang pendaftaran.');
    $whatsappUrl = $whatsappNumber ? 'https://wa.me/' . $whatsappNumber . '?text=' . $whatsappMessage : null;
@endphp

<main class="landing-shell">
    <span class="orb orb-one"></span>
    <span class="orb orb-two"></span>
    <span class="orb orb-three"></span>

    <nav class="topbar" aria-label="Navigasi utama">
        <a class="brand" href="{{ route('home') }}" aria-label="Beranda {{ $schoolName }}">
            <span class="brand-mark">
                @if($logo)
                    <img src="{{ $logo }}" alt="Logo {{ $schoolName }}">
                @else
                    <i class="fas fa-graduation-cap" aria-hidden="true"></i>
                @endif
            </span>
            <span>
                <strong>{{ $schoolName }}</strong>
                <small>SPMB {{ $settings['academic_year'] ?? '' }}</small>
            </span>
        </a>
        <div class="nav-actions">
            <a href="#cek-status" id="navCekStatus">Cek Status</a>
            <a href="{{ route('login') }}" id="navLogin">Login</a>
        </div>
    </nav>

    <section class="hero-section" aria-labelledby="landing-title">
        <div class="hero-copy">
            <div class="eyebrow {{ $statusClass }}">
                <span></span> {{ $statusLabel }} • {{ $settings['active_wave'] ?? 'Gelombang Aktif' }}
            </div>
            <div class="hero-highlights" aria-label="Keunggulan layanan">
                <span><i class="fas fa-bolt" aria-hidden="true"></i> One Day Service</span>
                <span><i class="fas fa-shield-halved" aria-hidden="true"></i> Data Aman</span>
                <span><i class="fas fa-chart-line" aria-hidden="true"></i> Real-time</span>
            </div>
            <div class="cta-group hero-cta-top">
                <a href="{{ route('registration.form') }}" class="btn btn-primary" id="btnDaftarSekarang">
                    <i class="fas fa-pen-to-square" aria-hidden="true"></i> Daftar Sekarang
                </a>
                <a href="#cek-status" class="btn btn-secondary" id="btnScrollCekStatus">
                    <i class="fas fa-magnifying-glass" aria-hidden="true"></i> Cek Status
                </a>
            </div>
            <div class="requirements-card hero-requirements" id="syarat">
                <div class="section-kicker">Persyaratan</div>
                <h1 id="landing-title">Siapkan Berkas Sebelum Mendaftar</h1>
                <p class="section-lead">Agar proses One Day Service berjalan cepat, pastikan dokumen berikut sudah siap.</p>
                <ul class="requirements-list">
                    <li><i class="fas fa-id-card" aria-hidden="true"></i><span>NISN calon siswa</span></li>
                    <li><i class="fas fa-users" aria-hidden="true"></i><span>Fotokopi Kartu Keluarga</span></li>
                    <li><i class="fas fa-file-signature" aria-hidden="true"></i><span>Fotokopi Akta Kelahiran</span></li>
                    <li><i class="fas fa-school" aria-hidden="true"></i><span>Ijazah/SKL atau surat keterangan lulus</span></li>
                    <li><i class="fas fa-image" aria-hidden="true"></i><span>Pas foto terbaru</span></li>
                    <li><i class="fas fa-folder-open" aria-hidden="true"></i><span>Berkas pendukung lain jika diminta panitia</span></li>
                </ul>
                @if($whatsappUrl)
                    <a href="{{ $whatsappUrl }}" class="whatsapp-card-link" id="btnWhatsappPersyaratanHero" target="_blank" rel="noopener">
                        <i class="fab fa-whatsapp" aria-hidden="true"></i>
                        Tanya persyaratan ke panitia
                    </a>
                @endif
            </div>
        </div>

        <aside class="status-panel" id="cek-status" aria-labelledby="status-title">
            <div class="panel-header">
                <span class="panel-icon"><i class="fas fa-clipboard-check" aria-hidden="true"></i></span>
                <div>
                    <h2 id="status-title">Cek Status Pendaftaran</h2>
                    <p>Masukkan No. Registrasi atau NISN.</p>
                </div>
            </div>

            <form class="status-form" method="GET" action="{{ route('home') }}">
                <label for="cek">No. Registrasi / NISN</label>
                <div class="input-row">
                    <input id="cek" name="cek" type="text" value="{{ $keyword }}" placeholder="Contoh: REG2026001" autocomplete="off" required>
                    <button type="submit" id="btnCekStatus"><i class="fas fa-search" aria-hidden="true"></i> Cek</button>
                </div>
            </form>

            @if($keyword !== '')
                @if($statusData)
                    @php
                        $landingStatus = $statusData->status_siswa === 'Diterima' ? 'Diterima' : 'Belum Daftar Ulang';
                        $landingStatusClass = $statusData->status_siswa === 'Diterima' ? 'is-accepted' : 'is-unpaid';
                    @endphp
                    <div class="result-card {{ $landingStatusClass }}" role="status">
                        <div class="result-top">
                            <strong>{{ $statusData->nama_lengkap }}</strong>
                            <span class="status-pill {{ $landingStatusClass }}">{{ $landingStatus }}</span>
                        </div>
                        <dl>
                            <div><dt>No. Registrasi</dt><dd>{{ $statusData->no_registrasi }}</dd></div>
                            <div><dt>NISN</dt><dd>{{ $statusData->nisn }}</dd></div>
                            <div><dt>Jurusan</dt><dd>{{ $statusData->jurusan }}</dd></div>
                            <div><dt>Gelombang</dt><dd>{{ $statusData->gelombang }}</dd></div>
                        </dl>
                    </div>
                @else
                    <div class="result-card warning" role="alert">
                        <strong>Data belum ditemukan</strong>
                        <p>Pastikan No. Registrasi atau NISN sudah benar, lalu coba kembali.</p>
                    </div>
                @endif
            @endif

            <div class="registration-steps" aria-labelledby="steps-title">
                <div class="steps-heading">
                    <span><i class="fas fa-route" aria-hidden="true"></i></span>
                    <div>
                        <h3 id="steps-title">Langkah-langkah Pendaftaran</h3>
                        <p>Alur layanan ringkas sesuai pedoman One Day Service: cepat, jelas, dan selesai terpantau.</p>
                    </div>
                </div>
                <ol class="steps-list">
                    <li>
                        <strong>Daftar Online dari Rumah</strong>
                        <span>Isi formulir pendaftaran, pilih jurusan, lalu kirim data awal secara online tanpa antre.</span>
                    </li>
                    <li>
                        <strong>Dapatkan Nomor Registrasi</strong>
                        <span>Sistem menerbitkan bukti pendaftaran untuk dipakai saat verifikasi dan cek status.</span>
                    </li>
                    <li>
                        <strong>Datang untuk Verifikasi Cepat</strong>
                        <span>Bawa berkas persyaratan ke panitia agar data, dokumen, dan pilihan jurusan divalidasi.</span>
                    </li>
                    <li>
                        <strong>Status Selesai Hari Itu Juga</strong>
                        <span>Cek hasil pendaftaran secara berkala menggunakan No. Registrasi atau NISN.</span>
                    </li>
                </ol>
            </div>
        </aside>
    </section>

    <section class="stats-section" aria-label="Statistik Pendaftaran">
        <div class="stat-card">
            <h5><i class="fas fa-users" aria-hidden="true"></i> Total Pendaftar</h5>
            <strong>{{ number_format($stats['total_pendaftar'] ?? 0) }}</strong>
            <span>Semua pendaftar</span>
        </div>
        <div class="stat-card">
            <h5><i class="fas fa-user-plus" aria-hidden="true"></i> Pendaftar Baru</h5>
            <strong>{{ number_format($stats['baru_hari_ini'] ?? 0) }}</strong>
            <span>Hari ini</span>
        </div>
        <div class="stat-card">
            <h5><i class="fas fa-clock" aria-hidden="true"></i> Belum Daftar Ulang</h5>
            <strong>{{ number_format($stats['belum_daftar_ulang'] ?? 0) }}</strong>
            <span>Menunggu verifikasi</span>
        </div>
        <div class="stat-card">
            <h5><i class="fas fa-check-circle" aria-hidden="true"></i> Sudah Daftar Ulang</h5>
            <strong>{{ number_format($stats['diterima'] ?? 0) }}</strong>
            <span>Status diterima</span>
        </div>
    </section>

    <section class="info-grid" aria-label="Informasi sekolah dan kuota">
        <article class="school-card">
            <div class="section-kicker">Profil Sekolah</div>
            <h2>Informasi {{ $schoolName }}</h2>
            <ul class="school-list">
                <li><i class="fas fa-location-dot" aria-hidden="true"></i><span>{{ $settings['school_address'] ?: 'Alamat sekolah belum diatur' }}</span></li>
                <li><i class="fas fa-city" aria-hidden="true"></i><span>{{ $settings['school_city'] ?: 'Kota belum diatur' }}</span></li>
                <li><i class="fas fa-phone" aria-hidden="true"></i><span>{{ $settings['school_phone'] ?: ($settings['school_contact'] ?: 'Kontak belum diatur') }}</span></li>
                <li><i class="fas fa-envelope" aria-hidden="true"></i><span>{{ $settings['school_email'] ?: 'Email belum diatur' }}</span></li>
                <li><i class="fas fa-user-tie" aria-hidden="true"></i><span>Kepala Sekolah: {{ $settings['principal_name'] ?: '-' }}</span></li>
            </ul>
        </article>

        <article class="quota-card">
            <div class="section-kicker">Kuota Jurusan</div>
            <h2>Pilih Kompetensi Keahlian</h2>
            <div class="quota-list">
                @foreach($jurusanQuota as $name => $quota)
                    <div class="quota-item">
                        <span>{{ $name }}</span>
                        <strong>{{ number_format((int) $quota) }} kursi</strong>
                    </div>
                @endforeach
            </div>
            <p class="fee-note">Biaya pendaftaran: <strong>Rp {{ number_format((int) ($settings['registration_fee'] ?? 0), 0, ',', '.') }}</strong></p>
        </article>
    </section>

    <section class="support-grid" aria-label="Pertanyaan umum">
        <article class="faq-card">
            <div class="section-kicker">FAQ</div>
            <h2>Pertanyaan yang Sering Diajukan</h2>
            <div class="faq-list">
                <details open>
                    <summary>Apakah pendaftaran bisa lewat HP?</summary>
                    <p>Bisa. Formulir online dapat diakses melalui HP, tablet, maupun komputer selama terhubung internet.</p>
                </details>
                <details>
                    <summary>Bagaimana cara cek status pendaftaran?</summary>
                    <p>Gunakan No. Registrasi atau NISN pada form Cek Status Pendaftaran di halaman ini.</p>
                </details>
                <details>
                    <summary>Apakah bukti registrasi wajib disimpan?</summary>
                    <p>Wajib disimpan atau dicetak karena digunakan untuk verifikasi data dan pengecekan status.</p>
                </details>
                <details>
                    <summary>Kapan harus datang verifikasi?</summary>
                    <p>Datang sesuai jadwal atau arahan panitia dengan membawa berkas persyaratan lengkap.</p>
                </details>
                <details>
                    <summary>Jika data salah harus bagaimana?</summary>
                    <p>Segera hubungi panitia melalui kontak sekolah atau tombol WhatsApp agar data dapat dibantu pengecekannya.</p>
                </details>
            </div>
        </article>
    </section>

    @if($whatsappUrl)
        <a href="{{ $whatsappUrl }}" class="floating-whatsapp" id="floatingWhatsappPanitia" target="_blank" rel="noopener" aria-label="Hubungi Panitia Pendaftaran via WhatsApp">
            <i class="fab fa-whatsapp" aria-hidden="true"></i>
            <span>Chat Panitia</span>
        </a>
    @endif
</main>
@endsection
