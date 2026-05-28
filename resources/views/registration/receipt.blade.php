<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bukti Pendaftaran - SPMB (Sistem Penerimaan Murid Baru)</title>
    @include('partials.favicon')
    <meta name="description" content="Bukti pendaftaran SPMB (Sistem Penerimaan Murid Baru). Simpan nomor registrasi Anda.">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #6366f1;
            --primary-light: #818cf8;
            --secondary: #a855f7;
            --accent: #06b6d4;
            --success: #10b981;
            --success-dark: #059669;
            --text-primary: #1e1b4b;
            --text-secondary: #6b7280;
            --text-muted: #9ca3af;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #064e3b 0%, #065f46 25%, #047857 50%, #059669 75%, #064e3b 100%);
            background-size: 400% 400%;
            animation: gradientShift 15s ease infinite;
            padding: 20px;
            position: relative;
            overflow-x: hidden;
        }

        @keyframes gradientShift {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }

        body::before, body::after {
            content: '';
            position: fixed;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.25;
            z-index: 0;
            pointer-events: none;
        }
        body::before {
            width: 400px; height: 400px;
            background: #34d399;
            top: -100px; right: -50px;
            animation: float1 20s ease-in-out infinite;
        }
        body::after {
            width: 350px; height: 350px;
            background: #06b6d4;
            bottom: -80px; left: -80px;
            animation: float2 25s ease-in-out infinite;
        }

        @keyframes float1 {
            0%, 100% { transform: translate(0,0); }
            50% { transform: translate(-60px,60px); }
        }
        @keyframes float2 {
            0%, 100% { transform: translate(0,0); }
            50% { transform: translate(50px,-50px); }
        }

        .page-wrapper {
            position: relative;
            z-index: 1;
            max-width: 600px;
            margin: 0 auto;
        }

        .receipt-card {
            background: rgba(255,255,255,0.97);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            box-shadow: 0 25px 80px rgba(0,0,0,0.25);
            overflow: hidden;
            animation: slideUp 0.6s cubic-bezier(0.16,1,0.3,1);
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Success Header */
        .success-header {
            background: linear-gradient(135deg, var(--success) 0%, #059669 50%, #047857 100%);
            color: white;
            padding: 48px 40px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .success-header::before {
            content: '';
            position: absolute;
            inset: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.06'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }

        .success-icon {
            width: 80px; height: 80px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 36px;
            margin-bottom: 16px;
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255,255,255,0.3);
            animation: popIn 0.5s cubic-bezier(0.34,1.56,0.64,1) 0.3s both;
        }

        @keyframes popIn {
            from { transform: scale(0); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }

        .success-header h1 {
            font-size: 26px;
            font-weight: 800;
            margin-bottom: 6px;
            position: relative;
        }

        .success-header p {
            font-size: 15px;
            opacity: 0.9;
            position: relative;
        }

        /* Confetti burst */
        .confetti { position: absolute; width: 8px; height: 8px; border-radius: 2px; opacity: 0; }
        .c1 { background: #fbbf24; top: 30%; left: 15%; animation: confettiFall 2s ease 0.5s both; }
        .c2 { background: #f472b6; top: 20%; right: 20%; animation: confettiFall 2.2s ease 0.7s both; }
        .c3 { background: #60a5fa; top: 40%; left: 25%; animation: confettiFall 1.8s ease 0.6s both; }
        .c4 { background: #a78bfa; top: 25%; right: 30%; animation: confettiFall 2.5s ease 0.4s both; }
        .c5 { background: #34d399; top: 35%; left: 10%; animation: confettiFall 2s ease 0.8s both; }
        .c6 { background: #fb923c; top: 15%; right: 15%; animation: confettiFall 2.3s ease 0.5s both; }

        @keyframes confettiFall {
            0% { opacity: 1; transform: translateY(0) rotate(0deg); }
            100% { opacity: 0; transform: translateY(120px) rotate(360deg); }
        }

        /* Body */
        .receipt-body { padding: 36px 40px 40px; }

        /* Registration number */
        .reg-number-box {
            background: linear-gradient(135deg, #eef2ff, #e0e7ff);
            border: 2px dashed var(--primary);
            border-radius: 20px;
            padding: 24px;
            text-align: center;
            margin-bottom: 28px;
        }

        .reg-number-box .label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: var(--text-secondary);
            margin-bottom: 8px;
        }

        .reg-number-box .number {
            font-size: 30px;
            font-weight: 800;
            color: var(--primary);
            font-family: 'Courier New', monospace;
            letter-spacing: 2px;
        }

        /* Data section */
        .data-card {
            background: #f8fafc;
            border-radius: 18px;
            padding: 24px;
            margin-bottom: 24px;
            border: 1px solid #e2e8f0;
        }

        .data-card .card-title {
            font-size: 14px;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .data-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #e5e7eb;
        }

        .data-row:last-child { border-bottom: none; }

        .data-label {
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-secondary);
        }

        .data-value {
            font-size: 14px;
            font-weight: 600;
            color: var(--text-primary);
            text-align: right;
        }

        /* Info boxes */
        .info-alert {
            border-radius: 14px;
            padding: 14px 18px;
            margin-bottom: 16px;
            font-size: 13px;
            display: flex;
            gap: 10px;
            align-items: flex-start;
            line-height: 1.6;
        }

        .info-alert i { margin-top: 3px; flex-shrink: 0; }

        .info-blue {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            color: #1e3a5f;
        }
        .info-blue i { color: #3b82f6; }

        .info-amber {
            background: #fffbeb;
            border: 1px solid #fde68a;
            color: #78350f;
        }
        .info-amber i { color: #f59e0b; }

        /* Steps */
        .steps-card {
            background: #fffbeb;
            border: 1px solid #fde68a;
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 24px;
        }

        .steps-card h6 {
            font-size: 14px;
            font-weight: 700;
            color: #b45309;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .steps-card ol {
            margin: 0;
            padding-left: 18px;
            font-size: 13px;
            color: #78350f;
        }

        .steps-card li { margin-bottom: 6px; line-height: 1.5; }

        /* Buttons */
        .action-buttons {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-top: 28px;
        }

        .btn-action {
            padding: 14px 20px;
            border-radius: 14px;
            font-weight: 700;
            font-size: 14px;
            font-family: 'Inter', sans-serif;
            text-decoration: none;
            text-align: center;
            cursor: pointer;
            border: none;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-primary-action {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
        }
        .btn-primary-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(99,102,241,0.4);
            color: white;
        }

        .btn-outline-action {
            background: white;
            color: var(--primary);
            border: 2px solid var(--primary);
        }
        .btn-outline-action:hover {
            background: var(--primary);
            color: white;
        }

        /* Contact */
        .contact-footer {
            text-align: center;
            margin-top: 24px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            font-size: 12px;
            color: var(--text-muted);
            line-height: 1.8;
        }

        @media print {
            body { background: white; padding: 0; }
            body::before, body::after { display: none; }
            .action-buttons, .page-footer { display: none; }
            .receipt-card { box-shadow: none; }
        }

        @media (max-width: 640px) {
            body { padding: 12px; }
            .success-header { padding: 32px 24px; }
            .receipt-body { padding: 24px; }
            .reg-number-box .number { font-size: 22px; }
            .action-buttons { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="page-wrapper">
        <div class="receipt-card">
            <!-- Success Header -->
            <div class="success-header">
                <div class="confetti c1"></div>
                <div class="confetti c2"></div>
                <div class="confetti c3"></div>
                <div class="confetti c4"></div>
                <div class="confetti c5"></div>
                <div class="confetti c6"></div>
                <div class="success-icon">
                    <i class="fas fa-check"></i>
                </div>
                <h1>Pendaftaran Berhasil!</h1>
                <p>Selamat, data Anda telah berhasil tersimpan</p>
            </div>

            <!-- Body -->
            <div class="receipt-body">
                @if (session('success'))
                    <div class="info-alert info-blue" style="margin-bottom: 24px;">
                        <i class="fas fa-check-circle"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                <!-- Registration Number -->
                <div class="reg-number-box">
                    <div class="label">Nomor Registrasi Anda</div>
                    <div class="number">{{ $registrationData['no_registrasi'] }}</div>
                </div>

                <!-- Data -->
                <div class="data-card">
                    <div class="card-title">
                        <i class="fas fa-user-circle"></i> Data Pendaftaran
                    </div>
                    <div class="data-row">
                        <span class="data-label">Nama Lengkap</span>
                        <span class="data-value">{{ $registrationData['nama_lengkap'] }}</span>
                    </div>
                    <div class="data-row">
                        <span class="data-label">NISN</span>
                        <span class="data-value">{{ $registrationData['nisn'] }}</span>
                    </div>
                    <div class="data-row">
                        <span class="data-label">Jurusan</span>
                        <span class="data-value">
                            {{ $registrationData['jurusan'] }}{{ !empty($registrationData['jurusan_nama']) ? ' - ' . $registrationData['jurusan_nama'] : '' }}
                        </span>
                    </div>
                    <div class="data-row">
                        <span class="data-label">Asal Sekolah</span>
                        <span class="data-value">{{ $registrationData['asal_sekolah'] }}</span>
                    </div>
                    <div class="data-row">
                        <span class="data-label">Gelombang</span>
                        <span class="data-value">Gelombang {{ $registrationData['gelombang'] }}</span>
                    </div>
                    <div class="data-row">
                        <span class="data-label">Tanggal Daftar</span>
                        <span class="data-value">{{ $registrationData['tgl_daftar'] }}</span>
                    </div>
                </div>

                <!-- Important Notice -->
                <div class="info-alert info-blue">
                    <i class="fas fa-shield-halved"></i>
                    <span><strong>Penting:</strong> Simpan nomor registrasi Anda. Nomor ini digunakan untuk verifikasi daftar ulang dan pengambilan seragam.</span>
                </div>

                <!-- Next Steps -->
                <div class="steps-card">
                    <h6><i class="fas fa-list-check"></i> Langkah Selanjutnya</h6>
                    <ol>
                        <li>Lakukan verifikasi daftar ulang di sekolah pada jadwal yang ditentukan</li>
                        <li>Pilih ukuran seragam (kaos) saat verifikasi</li>
                        <li>Tunggu jadwal pengambilan seragam dan perlengkapan</li>
                        <li>Ambil bukti pengambilan barang</li>
                    </ol>
                </div>

                <!-- Buttons -->
                <div class="action-buttons">
                    <a href="{{ route('registration.print') }}" class="btn-action btn-primary-action" target="_blank">
                        <i class="fas fa-print"></i> Cetak Bukti
                    </a>
                    <a href="{{ route('home') }}" class="btn-action btn-outline-action">
                        <i class="fas fa-home"></i> Kembali
                    </a>
                </div>

                <!-- Contact -->
                <div class="contact-footer">
                    <i class="fas fa-phone"></i>
                    {{ $settings['school_phone'] ?? $settings['school_contact'] ?? '(021) 1234-5678' }}<br>
                    <i class="fas fa-envelope"></i>
                    {{ $settings['school_email'] ?? 'info@spmb.sch.id' }}<br>
                    @if (!empty($settings['school_website']))
                        <i class="fas fa-globe"></i>
                        <a href="{{ $settings['school_website'] }}" target="_blank">{{ $settings['school_website'] }}</a><br>
                    @endif
                    <i class="fas fa-clock"></i> Senin - Jumat, 07:00 - 15:00 WIB
                </div>
            </div>
        </div>
    </div>
</body>
</html>

