@extends('layouts.app')

@section('content')
@php
    $schoolName = $settings['school_name'] ?? 'SPMB SIPDB';
    $logo = !empty($settings['school_logo']) ? asset('storage/' . $settings['school_logo']) : null;
    $statusLabel = $registrationOpen ? 'Pendaftaran Dibuka' : 'Pendaftaran Ditutup';
    $statusClass = $registrationOpen ? 'is-open' : 'is-closed';
    $jurusanQuota = $jurusanQuota ?? [];
    $rawContact = $settings['school_contact'] ?: ($settings['school_phone'] ?? '');
    $whatsappNumber = preg_replace('/\D+/', '', $rawContact);
    if ($whatsappNumber !== '' && str_starts_with($whatsappNumber, '0')) {
        $whatsappNumber = '62' . substr($whatsappNumber, 1);
    }
    $whatsappMessage = rawurlencode('Halo Pak ilham, Panitia SPMB ' . $schoolName . ', saya ingin bertanya tentang pendaftaran.');
    $whatsappUrl = $whatsappNumber ? 'https://wa.me/' . $whatsappNumber . '?text=' . $whatsappMessage : null;
    $websiteUrl = $settings['school_website'] ?? null;
    $instagramUrl = $settings['instagram_url'] ?? null;
    $youtubeUrl = $settings['school_youtube'] ?? null;
    $tiktokUrl = $settings['tiktok_url'] ?? null;
@endphp

<main class="landing-shell">
    <style>
        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
        }
        .nav-actions {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }
        .social-links {
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .social-link {
            width: 36px;
            height: 36px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            background: rgba(255,255,255,0.12);
            color: #f8fafc;
            text-decoration: none;
            transition: transform 0.2s ease, background 0.2s ease, color 0.2s ease;
            font-size: 16px;
        }
        .social-link:hover {
            transform: translateY(-1px);
            background: rgba(255,255,255,0.24);
        }
        .social-link[data-brand="globe"] { color: #f8fafc; }
        .social-link[data-brand="instagram"] { color: #e1306c; }
        .social-link[data-brand="youtube"] { color: #ff0000; }
        .social-link[data-brand="tiktok"] { color: #ffffff; }
        .social-link[data-brand="whatsapp"] { color: #25d366; }
        .social-link[data-brand="instagram"]:hover { background: rgba(225,48,108,0.18); }
        .social-link[data-brand="youtube"]:hover { background: rgba(255,0,0,0.18); }
        .social-link[data-brand="tiktok"]:hover { background: rgba(255,255,255,0.18); color: #111; }
        .social-link[data-brand="whatsapp"]:hover { background: rgba(37,211,102,0.18); }
        .social-link[data-brand="globe"]:hover { background: rgba(248,250,252,0.18); }
        .brand small {
            display: block;
            color: rgba(248,250,252,0.8);
            line-height: 1.3;
        }
        .brand-subtitle {
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-size: 0.75rem;
            margin-top: 2px;
            color: rgba(248,250,252,0.7);
        }
        @media (max-width: 780px) {
            .social-links { margin-top: 8px; }
        }
    </style>

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
                <small class="brand-subtitle">SISTEM PENERIMAAN MURID BARU</small>
                <strong>{{ $schoolName }}</strong>
                <small class="brand-year">{{ $settings['academic_year'] ?? '' }}</small>
            </span>
        </a>
        <div class="nav-actions">
            <a href="#cek-status" id="navCekStatus">Cek Status</a>
            <a href="{{ route('login') }}" id="navLogin">Login</a>
            <div class="social-links">
                @if($websiteUrl)
                    <a href="{{ $websiteUrl }}" target="_blank" rel="noopener" class="social-link" data-brand="globe" title="Website Sekolah" aria-label="Website Sekolah">
                        <i class="fas fa-globe"></i>
                    </a>
                @endif
                @if($instagramUrl)
                    <a href="{{ $instagramUrl }}" target="_blank" rel="noopener" class="social-link" data-brand="instagram" title="Instagram Sekolah" aria-label="Instagram Sekolah">
                        <i class="fab fa-instagram"></i>
                    </a>
                @endif
                @if($youtubeUrl)
                    <a href="{{ $youtubeUrl }}" target="_blank" rel="noopener" class="social-link" data-brand="youtube" title="YouTube Sekolah" aria-label="YouTube Sekolah">
                        <i class="fab fa-youtube"></i>
                    </a>
                @endif
                @if($tiktokUrl)
                    <a href="{{ $tiktokUrl }}" target="_blank" rel="noopener" class="social-link" data-brand="tiktok" title="TikTok Sekolah" aria-label="TikTok Sekolah">
                        <i class="fab fa-tiktok"></i>
                    </a>
                @endif
                @if($whatsappUrl)
                    <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener" class="social-link" data-brand="whatsapp" title="WhatsApp Panitia" aria-label="WhatsApp Panitia">
                        <i class="fab fa-whatsapp"></i>
                    </a>
                @endif
            </div>
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
                        <strong>Daftar Online dari manapun</strong>
                        <span>Isi formulir pendaftaran, pilih jurusan, lalu kirim data awal secara online tanpa antre.</span>
                    </li>
                    <li>
                        <strong>Dapatkan Nomor Registrasi</strong>
                        <span>Sistem menerbitkan bukti pendaftaran untuk dipakai saat verifikasi dan cek status.</span>
                    </li>
                    <li>
                        <strong>Datang untuk Verifikasi / Daftar Ulang </strong>
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

<script>
// Scroll Reveal Animation
document.addEventListener('DOMContentLoaded', function() {
    // Add scroll-reveal class to elements
    const revealElements = document.querySelectorAll('.stats-section, .info-grid > *, .support-grid > *, .requirements-card, .registration-steps');
    
    revealElements.forEach(el => {
        el.classList.add('scroll-reveal');
    });
    
    // Intersection Observer for scroll reveal
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('revealed');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);
    
    revealElements.forEach(el => {
        observer.observe(el);
    });
    
    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if (href !== '#' && href !== '') {
                e.preventDefault();
                const target = document.querySelector(href);
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            }
        });
    });
    
    // Counter animation for stats
    const animateCounter = (element, target, duration = 2000) => {
        const start = 0;
        const increment = target / (duration / 16);
        let current = start;
        
        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                element.textContent = target.toLocaleString('id-ID');
                clearInterval(timer);
            } else {
                element.textContent = Math.floor(current).toLocaleString('id-ID');
            }
        }, 16);
    };
    
    // Observe stats cards for counter animation
    const statsObserver = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const strong = entry.target.querySelector('strong');
                if (strong) {
                    const value = parseInt(strong.textContent.replace(/\D/g, ''));
                    if (!isNaN(value)) {
                        animateCounter(strong, value);
                    }
                }
                statsObserver.unobserve(entry.target);
            }
        });
    }, { threshold: 0.5 });
    
    document.querySelectorAll('.stat-card').forEach(card => {
        statsObserver.observe(card);
    });
    
    // Add loading state to form submission
    const statusForm = document.querySelector('.status-form');
    if (statusForm) {
        statusForm.addEventListener('submit', function() {
            const button = this.querySelector('button[type="submit"]');
            if (button) {
                button.classList.add('loading');
                button.disabled = true;
            }
        });
    }
    
    // Parallax effect for hero section (disabled to prevent overlap)
    /*
    let ticking = false;
    window.addEventListener('scroll', function() {
        if (!ticking) {
            window.requestAnimationFrame(function() {
                const scrolled = window.pageYOffset;
                const heroSection = document.querySelector('.hero-section');
                if (heroSection && scrolled < window.innerHeight) {
                    heroSection.style.transform = 'translateY(' + (scrolled * 0.5) + 'px)';
                }
                ticking = false;
            });
            ticking = true;
        }
    });
    */
});
</script>

@endsection
