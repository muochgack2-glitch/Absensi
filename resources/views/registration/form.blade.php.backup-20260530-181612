<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran - SPMB (Sistem Penerimaan Murid Baru)</title>
    @include('partials.favicon')
    @php
        $sys = \App\Models\SettingSystem::instance()->toSettingsArray();
        $schoolName = $sys['school_name'] ?: 'SPMB';
        $schoolLogo = !empty($sys['school_logo']) ? asset('storage/' . $sys['school_logo']) : null;
        $schoolPhone = $sys['school_phone'] ?: ($sys['school_contact'] ?: '(021) 1234-5678');
        $schoolEmail = $sys['school_email'] ?: 'info@spmb.sch.id';
        $footerText = $sys['print_footer_text'] ?: 'SPMB (Sistem Penerimaan Murid Baru)';
    @endphp
    @include('partials.theme-vars')
    <meta name="description" content="Formulir pendaftaran online SPMB (Sistem Penerimaan Murid Baru). Daftar sekarang dengan mudah dan cepat.">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: var(--theme-primary, #6366f1);
            --primary-light: color-mix(in srgb, var(--theme-primary, #6366f1) 75%, white);
            --primary-dark: color-mix(in srgb, var(--theme-primary, #6366f1) 85%, black);
            --secondary: var(--theme-secondary, #a855f7);
            --accent: #06b6d4;
            --success: #10b981;
            --surface: rgba(255, 255, 255, 0.08);
            --surface-hover: rgba(255, 255, 255, 0.12);
            --glass: rgba(255, 255, 255, 0.95);
            --glass-border: rgba(255, 255, 255, 0.3);
            --text-primary: #1e1b4b;
            --text-secondary: #6b7280;
            --text-muted: #9ca3af;
            --danger: #ef4444;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, var(--theme-primary) 0%, var(--theme-secondary) 100%);
            padding: 20px;
            position: relative;
            overflow-x: hidden;
        }

        /* Floating orbs */
        body::before, body::after {
            display: none;
        }

        .page-wrapper {
            position: relative;
            z-index: 1;
            max-width: 720px;
            margin: 0 auto;
        }

        /* Back to home link */
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 20px;
            transition: all 0.3s;
            padding: 8px 16px;
            border-radius: 50px;
            background: rgba(255,255,255,0.08);
            backdrop-filter: blur(10px);
        }
        .back-link:hover {
            color: white;
            background: rgba(255,255,255,0.15);
            transform: translateX(-4px);
        }

        /* Main card */
        .reg-card {
            background: var(--glass);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            box-shadow: 0 25px 80px rgba(0,0,0,0.25), 0 0 0 1px rgba(255,255,255,0.1);
            overflow: hidden;
            animation: slideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Header */
        .reg-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 50%, var(--accent) 100%);
            background-size: 200% 200%;
            animation: headerGlow 8s ease infinite;
            color: white;
            padding: 48px 40px 40px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        @keyframes headerGlow {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }

        .reg-header::before {
            content: '';
            position: absolute;
            inset: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }

        .header-icon {
            width: 72px; height: 72px;
            background: rgba(255,255,255,0.2);
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            margin-bottom: 16px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.25);
            animation: iconPulse 3s ease-in-out infinite;
            overflow: hidden;
        }

        .header-icon img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        @keyframes iconPulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        .reg-header h1 {
            font-size: 28px;
            font-weight: 800;
            letter-spacing: -0.5px;
            margin-bottom: 8px;
            position: relative;
        }

        .reg-header .subtitle {
            font-size: 15px;
            opacity: 0.9;
            font-weight: 400;
        }

        .badge-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(255,255,255,0.2);
            backdrop-filter: blur(10px);
            padding: 6px 16px;
            border-radius: 50px;
            font-size: 12px;
            font-weight: 600;
            margin-top: 14px;
            border: 1px solid rgba(255,255,255,0.2);
        }

        .badge-pill .dot {
            width: 6px; height: 6px;
            background: #34d399;
            border-radius: 50%;
            animation: blink 2s ease-in-out infinite;
        }

        @keyframes blink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.3; }
        }

        /* Body */
        .reg-body {
            padding: 40px;
        }

        /* Alert */
        .alert-errors {
            background: linear-gradient(135deg, #fef2f2, #fee2e2);
            border: 1px solid #fca5a5;
            border-radius: 16px;
            padding: 16px 20px;
            margin-bottom: 24px;
            animation: shake 0.5s ease;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        .alert-errors .alert-title {
            font-weight: 700;
            color: #dc2626;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 8px;
        }

        .alert-errors ul {
            margin: 0;
            padding-left: 20px;
            font-size: 13px;
            color: #991b1b;
        }

        /* Info box */
        .info-box {
            background: linear-gradient(135deg, #eef2ff, #e0e7ff);
            border: 1px solid #c7d2fe;
            border-radius: 16px;
            padding: 16px 20px;
            margin-bottom: 32px;
            display: flex;
            align-items: flex-start;
            gap: 12px;
            font-size: 13px;
            color: var(--text-primary);
            line-height: 1.6;
        }

        .info-box i {
            color: var(--primary);
            font-size: 18px;
            margin-top: 2px;
            flex-shrink: 0;
        }

        /* Section headers */
        .section-title {
            font-size: 16px;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-title .icon-circle {
            width: 32px; height: 32px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 14px;
        }

        .section-divider {
            height: 1px;
            background: linear-gradient(to right, #e5e7eb, transparent);
            margin: 28px 0;
        }

        /* Form groups */
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .field-group {
            margin-bottom: 22px;
        }

        .field-group label {
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
        }

        .field-group label .req {
            color: var(--danger);
            font-weight: 700;
        }

        .field-group label i {
            color: var(--primary-light);
            font-size: 13px;
        }

        .field-group .form-control,
        .field-group .form-select {
            border: 2px solid #e5e7eb;
            border-radius: 14px;
            padding: 13px 16px;
            font-size: 14px;
            font-family: 'Inter', sans-serif;
            color: var(--text-primary);
            background: #fafafa;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            width: 100%;
        }

        .field-group .form-control:hover,
        .field-group .form-select:hover {
            border-color: #c7d2fe;
            background: white;
        }

        .field-group .form-control:focus,
        .field-group .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
            background: white;
            outline: none;
        }

        .field-group .form-control.is-invalid,
        .field-group .form-select.is-invalid {
            border-color: var(--danger);
            box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.08);
        }

        .field-group textarea.form-control {
            resize: vertical;
            min-height: 90px;
        }

        .field-hint {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 6px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .field-error {
            color: var(--danger);
            font-size: 12px;
            margin-top: 6px;
            display: flex;
            align-items: center;
            gap: 4px;
            font-weight: 500;
        }

        /* Submit */
        .btn-submit {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            border: none;
            border-radius: 16px;
            font-size: 16px;
            font-weight: 700;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            position: relative;
            overflow: hidden;
            margin-top: 8px;
        }

        .btn-submit::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, transparent, rgba(255,255,255,0.15), transparent);
            transform: translateX(-100%);
            transition: transform 0.6s;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 35px rgba(99, 102, 241, 0.4);
        }

        .btn-submit:hover::before {
            transform: translateX(100%);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .terms-text {
            text-align: center;
            margin-top: 16px;
            font-size: 12px;
            color: var(--text-muted);
        }

        .terms-text a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
        }

        .terms-text a:hover { text-decoration: underline; }

        /* Footer */
        .page-footer {
            text-align: center;
            margin-top: 28px;
            padding: 16px;
            color: rgba(255,255,255,0.6);
            font-size: 13px;
        }

        .page-footer a {
            color: rgba(255,255,255,0.8);
            text-decoration: none;
        }

        .page-footer i { margin-right: 4px; }

        /* Responsive */
        @media (max-width: 768px) {
            body { padding: 12px; }
            .reg-header { padding: 32px 24px; }
            .reg-header h1 { font-size: 22px; }
            .reg-body { padding: 24px; }
            .form-row { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="page-wrapper">
        <!-- Back Link -->
        <a href="{{ route('home') }}" class="back-link" id="backToHome">
            <i class="fas fa-arrow-left"></i> Kembali ke Beranda
        </a>

        <div class="reg-card">
            <!-- Header -->
            <div class="reg-header">
                <div class="header-icon">
                    @if($schoolLogo)
                        <img src="{{ $schoolLogo }}" alt="Logo {{ $schoolName }}">
                    @else
                        <i class="fas fa-graduation-cap"></i>
                    @endif
                </div>
                <h1>{{ $schoolName }}</h1>
                <p class="subtitle">Sistem Penerimaan Murid Baru</p>
                <div class="badge-pill">
                    <span class="dot"></span>
                    Pendaftaran Online Dibuka
                </div>
            </div>

            <!-- Body -->
            <div class="reg-body">
                @if ($errors->any())
                    <div class="alert-errors">
                        <div class="alert-title">
                            <i class="fas fa-exclamation-triangle"></i> Terjadi Kesalahan
                        </div>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="info-box">
                    <i class="fas fa-lightbulb"></i>
                    <div>
                        <strong>Informasi Penting:</strong><br>
                        Isi semua field bertanda <span style="color:var(--danger);font-weight:700;">*</span> dengan data yang benar. Nomor registrasi akan otomatis diberikan setelah pendaftaran berhasil.
                    </div>
                </div>

                <form action="{{ route('registration.submit') }}" method="POST" id="registrationForm">
                    @csrf

                    <!-- Section: Data Pribadi -->
                    <div class="section-title">
                        <span class="icon-circle"><i class="fas fa-user"></i></span>
                        Data Pribadi
                    </div>

                    <div class="form-row">
                        <div class="field-group">
                            <label for="nisn">
                                <i class="fas fa-id-card"></i> NISN <span class="req">*</span>
                            </label>
                            <input
                                type="text"
                                id="nisn"
                                name="nisn"
                                class="form-control @error('nisn') is-invalid @enderror"
                                value="{{ old('nisn') }}"
                                placeholder="Masukkan 10 digit NISN"
                                maxlength="10"
                                pattern="[0-9]{10}"
                                required
                            >
                            @error('nisn')
                                <span class="field-error"><i class="fas fa-times-circle"></i> {{ $message }}</span>
                            @enderror
                            <span class="field-hint">Harus 10 digit angka</span>
                        </div>
                        <div class="field-group">
                            <label for="nama_lengkap">
                                <i class="fas fa-user-edit"></i> Nama Lengkap <span class="req">*</span>
                            </label>
                            <input
                                type="text"
                                id="nama_lengkap"
                                name="nama_lengkap"
                                class="form-control @error('nama_lengkap') is-invalid @enderror"
                                value="{{ old('nama_lengkap') }}"
                                placeholder="Sesuai dokumen resmi"
                                required
                            >
                            @error('nama_lengkap')
                                <span class="field-error"><i class="fas fa-times-circle"></i> {{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="section-divider"></div>

                    <!-- Section: Data Akademik -->
                    <div class="section-title">
                        <span class="icon-circle"><i class="fas fa-school"></i></span>
                        Data Akademik
                    </div>

                    <div class="form-row">
                        <div class="field-group">
                            <label for="asal_sekolah">
                                <i class="fas fa-building"></i> Asal Sekolah <span class="req">*</span>
                            </label>
                            <input
                                type="text"
                                id="asal_sekolah"
                                name="asal_sekolah"
                                class="form-control @error('asal_sekolah') is-invalid @enderror"
                                value="{{ old('asal_sekolah') }}"
                                placeholder="Nama SMP / MTs asal"
                                required
                            >
                            @error('asal_sekolah')
                                <span class="field-error"><i class="fas fa-times-circle"></i> {{ $message }}</span>
                            @enderror
                        </div>
                        <div class="field-group">
                            <label for="jurusan">
                                <i class="fas fa-layer-group"></i> Pilih Jurusan <span class="req">*</span>
                            </label>
                            <select
                                id="jurusan_id"
                                name="jurusan_id"
                                class="form-select @error('jurusan_id') is-invalid @enderror"
                                required
                            >
                                <option value="">-- Pilih Jurusan --</option>
                                @foreach(($jurusans ?? collect()) as $j)
                                    <option value="{{ $j->id }}" {{ (string) old('jurusan_id') === (string) $j->id ? 'selected' : '' }}>{{ $j->kode }} - {{ $j->nama }}</option>
                                @endforeach
                            </select>
                            @error('jurusan_id')
                                <span class="field-error"><i class="fas fa-times-circle"></i> {{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="field-group">
                        <label for="alamat">
                            <i class="fas fa-map-marker-alt"></i> Alamat Lengkap <span class="req">*</span>
                        </label>
                        <textarea
                            id="alamat"
                            name="alamat"
                            class="form-control @error('alamat') is-invalid @enderror"
                            rows="3"
                            placeholder="Jalan, No., RT/RW, Kelurahan, Kecamatan, Kota, Provinsi"
                            required
                        >{{ old('alamat') }}</textarea>
                        @error('alamat')
                            <span class="field-error"><i class="fas fa-times-circle"></i> {{ $message }}</span>
                        @enderror
                    </div>

                    <div class="section-divider"></div>

                    <!-- Section: Informasi Tambahan -->
                    <div class="section-title">
                        <span class="icon-circle"><i class="fas fa-info-circle"></i></span>
                        Informasi Tambahan
                    </div>

                    <div class="field-group">
                        <label for="nama_jaringan">
                            <i class="fas fa-user-friends"></i> Nama Jaringan / Perekomendasi
                        </label>
                        <input
                            type="text"
                            id="nama_jaringan"
                            name="nama_jaringan"
                            class="form-control"
                            value="{{ old('nama_jaringan') }}"
                            placeholder="Siapa yang merekomendasikan? (opsional)"
                        >
                        <span class="field-hint"><i class="fas fa-info-circle"></i> Opsional</span>
                    </div>

                    <div class="form-row">
                        <div class="field-group">
                            <label for="email">
                                <i class="fas fa-envelope"></i> Email
                            </label>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                class="form-control @error('email') is-invalid @enderror"
                                value="{{ old('email') }}"
                                placeholder="email@contoh.com"
                            >
                            @error('email')
                                <span class="field-error"><i class="fas fa-times-circle"></i> {{ $message }}</span>
                            @enderror
                            <span class="field-hint">Untuk notifikasi pendaftaran</span>
                        </div>
                        <div class="field-group">
                            <label for="no_telepon">
                                <i class="fas fa-phone"></i> No. Telepon / WhatsApp
                            </label>
                            <input
                                type="tel"
                                id="no_telepon"
                                name="no_telepon"
                                class="form-control @error('no_telepon') is-invalid @enderror"
                                value="{{ old('no_telepon') }}"
                                placeholder="08xxxxxxxxxx"
                            >
                            @error('no_telepon')
                                <span class="field-error"><i class="fas fa-times-circle"></i> {{ $message }}</span>
                            @enderror
                            <span class="field-hint">Untuk notifikasi WhatsApp</span>
                        </div>
                    </div>

                    <!-- Submit -->
                    <button type="submit" class="btn-submit" id="submitBtn">
                        <i class="fas fa-paper-plane"></i> Daftar Sekarang
                    </button>

                    <p class="terms-text">
                        Dengan mendaftar, Anda menyetujui <a href="#">Syarat & Ketentuan</a> yang berlaku
                    </p>
                </form>
            </div>
        </div>

        <!-- Footer -->
        <div class="page-footer">
            <p>
                <i class="fas fa-phone"></i> {{ $schoolPhone }} &nbsp;|&nbsp;
                <i class="fas fa-envelope"></i> {{ $schoolEmail }}
            </p>
            <p>{{ $footerText }}</p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // NISN input - only numbers
        document.getElementById('nisn').addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);
        });

        // Form submission with validation
        document.getElementById('registrationForm').addEventListener('submit', function(e) {
            const nisn = document.getElementById('nisn').value;
            if (nisn.length !== 10) {
                e.preventDefault();
                alert('NISN harus 10 digit!');
                return false;
            }

            // Disable button to prevent double submit
            const btn = document.getElementById('submitBtn');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
        });

        // Add entrance animations to fields
        document.querySelectorAll('.field-group').forEach((el, i) => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(10px)';
            el.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
            el.style.transitionDelay = (i * 0.05) + 's';
            setTimeout(() => {
                el.style.opacity = '1';
                el.style.transform = 'translateY(0)';
            }, 100);
        });
    </script>
</body>
</html>

