<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPMB (Sistem Penerimaan Murid Baru) - Portal Pendaftaran</title>
    <meta name="description" content="Sistem Penerimaan Murid Baru SPMB. Daftar online dengan mudah, cepat, dan transparan.">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #6366f1;
            --primary-light: #818cf8;
            --primary-dark: #4f46e5;
            --secondary: #a855f7;
            --accent: #06b6d4;
            --success: #10b981;
            --text-primary: #1e1b4b;
            --text-secondary: #6b7280;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #1e1b4b 0%, #312e81 25%, #4c1d95 50%, #5b21b6 75%, #1e1b4b 100%);
            background-size: 400% 400%;
            animation: gradientShift 15s ease infinite;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            overflow-x: hidden;
            overflow-y: auto;
        }

        @keyframes gradientShift {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }

        /* Floating orbs */
        body::before, body::after {
            content: '';
            position: fixed;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.3;
            z-index: 0;
            pointer-events: none;
        }
        body::before {
            width: 500px; height: 500px;
            background: var(--accent);
            top: -120px; right: -100px;
            animation: float1 20s ease-in-out infinite;
        }
        body::after {
            width: 400px; height: 400px;
            background: var(--secondary);
            bottom: -100px; left: -100px;
            animation: float2 25s ease-in-out infinite;
        }

        @keyframes float1 {
            0%, 100% { transform: translate(0, 0); }
            50% { transform: translate(-80px, 80px); }
        }
        @keyframes float2 {
            0%, 100% { transform: translate(0, 0); }
            50% { transform: translate(60px, -60px); }
        }

        /* Particle dots */
        .particles {
            position: fixed;
            inset: 0;
            z-index: 0;
            pointer-events: none;
            overflow: hidden;
        }

        .particle {
            position: absolute;
            width: 4px; height: 4px;
            background: rgba(255,255,255,0.15);
            border-radius: 50%;
        }

        .p1 { top: 15%; left: 10%; animation: twinkle 4s ease infinite; }
        .p2 { top: 30%; right: 15%; animation: twinkle 3s ease 0.5s infinite; }
        .p3 { bottom: 25%; left: 20%; animation: twinkle 5s ease 1s infinite; }
        .p4 { top: 60%; right: 25%; animation: twinkle 3.5s ease 1.5s infinite; }
        .p5 { bottom: 40%; left: 40%; animation: twinkle 4.5s ease 0.8s infinite; }
        .p6 { top: 10%; right: 40%; animation: twinkle 3.8s ease 0.3s infinite; }
        .p7 { bottom: 15%; right: 10%; animation: twinkle 4.2s ease 1.2s infinite; }
        .p8 { top: 50%; left: 5%; animation: twinkle 3.2s ease 0.6s infinite; }

        @keyframes twinkle {
            0%, 100% { opacity: 0.15; transform: scale(1); }
            50% { opacity: 0.6; transform: scale(1.5); }
        }

        .hero-wrapper {
            position: relative;
            z-index: 1;
            max-width: 960px;
            width: 100%;
        }

        /* Main card */
        .hero-card {
            background: rgba(255, 255, 255, 0.97);
            backdrop-filter: blur(20px);
            border-radius: 28px;
            box-shadow: 0 30px 100px rgba(0, 0, 0, 0.3), 0 0 0 1px rgba(255,255,255,0.1);
            overflow: hidden;
            display: grid;
            grid-template-columns: 1fr 1fr;
            animation: cardEntry 0.7s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes cardEntry {
            from { opacity: 0; transform: translateY(40px) scale(0.97); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        /* Left side */
        .hero-left {
            padding: 56px 48px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .brand-icon {
            width: 64px; height: 64px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 28px;
            margin-bottom: 24px;
            box-shadow: 0 8px 24px rgba(99,102,241,0.3);
            animation: iconFloat 4s ease-in-out infinite;
        }

        @keyframes iconFloat {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-6px); }
        }

        .hero-left h1 {
            font-size: 38px;
            font-weight: 900;
            color: var(--text-primary);
            letter-spacing: -1px;
            line-height: 1.1;
            margin-bottom: 8px;
        }

        .hero-left .tagline {
            font-size: 16px;
            color: var(--text-secondary);
            margin-bottom: 32px;
            line-height: 1.5;
        }

        .features-list {
            list-style: none;
            padding: 0;
            margin: 0 0 36px 0;
        }

        .features-list li {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 0;
            font-size: 14px;
            color: #374151;
            font-weight: 500;
        }

        .feature-icon {
            width: 32px; height: 32px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            flex-shrink: 0;
        }

        .fi-1 { background: #eef2ff; color: var(--primary); }
        .fi-2 { background: #f0fdf4; color: var(--success); }
        .fi-3 { background: #faf5ff; color: var(--secondary); }
        .fi-4 { background: #ecfeff; color: var(--accent); }

        .cta-group {
            display: flex;
            gap: 12px;
        }

        .btn-cta {
            padding: 15px 32px;
            border-radius: 14px;
            font-weight: 700;
            font-size: 15px;
            font-family: 'Inter', sans-serif;
            text-decoration: none;
            cursor: pointer;
            border: none;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: inline-flex;
            align-items: center;
            gap: 8px;
            position: relative;
            overflow: hidden;
        }

        .btn-register {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            box-shadow: 0 4px 20px rgba(99,102,241,0.3);
        }

        .btn-register::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, transparent, rgba(255,255,255,0.2), transparent);
            transform: translateX(-100%);
            transition: transform 0.5s;
        }

        .btn-register:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(99,102,241,0.45);
            color: white;
        }

        .btn-register:hover::before { transform: translateX(100%); }

        .btn-admin {
            background: transparent;
            color: var(--primary);
            border: 2px solid #e0e7ff;
        }

        .btn-admin:hover {
            background: #eef2ff;
            border-color: var(--primary-light);
            color: var(--primary);
        }

        /* Right side */
        .hero-right {
            background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 50%, #ede9fe 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 48px;
            position: relative;
            overflow: hidden;
        }

        .hero-right::before {
            content: '';
            position: absolute;
            inset: 0;
            background: url("data:image/svg+xml,%3Csvg width='40' height='40' viewBox='0 0 40 40' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%236366f1' fill-opacity='0.04'%3E%3Cpath d='M20 20.5V18H0v-2h20v-2h2v6h-2zm0-13V2H0V0h20v-2h2v10h-2zM0 20h2v2H0v-2zm4 0h2v2H4v-2zm4 0h2v2H8v-2zm4 0h2v2h-2v-2z'/%3E%3C/g%3E%3C/svg%3E");
        }

        /* Stats cards */
        .stats-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            position: relative;
            z-index: 1;
            width: 100%;
        }

        .stat-card {
            background: rgba(255,255,255,0.85);
            backdrop-filter: blur(10px);
            border-radius: 18px;
            padding: 20px;
            text-align: center;
            border: 1px solid rgba(255,255,255,0.5);
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            transition: all 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 25px rgba(99,102,241,0.12);
        }

        .stat-card .stat-icon {
            width: 44px; height: 44px;
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            margin-bottom: 10px;
        }

        .si-1 { background: linear-gradient(135deg, #dbeafe, #bfdbfe); color: #2563eb; }
        .si-2 { background: linear-gradient(135deg, #d1fae5, #a7f3d0); color: #059669; }
        .si-3 { background: linear-gradient(135deg, #ede9fe, #ddd6fe); color: #7c3aed; }
        .si-4 { background: linear-gradient(135deg, #cffafe, #a5f3fc); color: #0891b2; }

        .stat-card .stat-value {
            font-size: 24px;
            font-weight: 800;
            color: var(--text-primary);
            margin-bottom: 2px;
        }

        .stat-card .stat-label {
            font-size: 11px;
            font-weight: 600;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Bottom badge */
        .trust-badge {
            margin-top: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
            background: rgba(255,255,255,0.7);
            backdrop-filter: blur(10px);
            padding: 10px 20px;
            border-radius: 50px;
            font-size: 12px;
            font-weight: 600;
            color: var(--text-secondary);
            border: 1px solid rgba(255,255,255,0.5);
            position: relative;
            z-index: 1;
        }

        .trust-badge .dot {
            width: 8px; height: 8px;
            background: var(--success);
            border-radius: 50%;
            animation: blink 2s ease-in-out infinite;
        }

        @keyframes blink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.3; }
        }

        /* Responsive */
        @media (max-width: 768px) {
            body { padding: 12px; align-items: flex-start; padding-top: 40px; }

            .hero-card {
                grid-template-columns: 1fr;
            }

            .hero-left {
                padding: 36px 28px;
                text-align: center;
            }

            .brand-icon { margin: 0 auto 20px; }

            .hero-left h1 { font-size: 28px; }

            .features-list li { justify-content: center; }

            .cta-group {
                flex-direction: column;
            }

            .btn-cta { justify-content: center; }

            .hero-right {
                padding: 32px 28px;
            }

            .stats-grid { gap: 10px; }
        }
    </style>
</head>
<body>
    <!-- Particles -->
    <div class="particles">
        <div class="particle p1"></div>
        <div class="particle p2"></div>
        <div class="particle p3"></div>
        <div class="particle p4"></div>
        <div class="particle p5"></div>
        <div class="particle p6"></div>
        <div class="particle p7"></div>
        <div class="particle p8"></div>
    </div>

    <div class="hero-wrapper">
        <div class="hero-card">
            <!-- Left Side -->
            <div class="hero-left">
                <div class="brand-icon">
                    <i class="fas fa-graduation-cap"></i>
                </div>

                <h1>SPMB (Sistem Penerimaan Murid Baru)</h1>
                <p class="tagline">Sistem Penerimaan Murid Baru<br>One Day Service - Cepat & Transparan</p>

                <ul class="features-list">
                    <li>
                        <span class="feature-icon fi-1"><i class="fas fa-bolt"></i></span>
                        Pendaftaran Online Mudah & Cepat
                    </li>
                    <li>
                        <span class="feature-icon fi-2"><i class="fas fa-check-double"></i></span>
                        Verifikasi Transparan & Real-time
                    </li>
                    <li>
                        <span class="feature-icon fi-3"><i class="fas fa-clock"></i></span>
                        Proses One Day Service
                    </li>
                    <li>
                        <span class="feature-icon fi-4"><i class="fas fa-traffic-light"></i></span>
                        Tracking Status Pendaftaran
                    </li>
                </ul>

                <div class="cta-group">
                    <a href="{{ route('registration.form') }}" class="btn-cta btn-register" id="btnDaftar">
                        <i class="fas fa-pen-to-square"></i> Daftar Sekarang
                    </a>
                    <a href="{{ route('login') }}" class="btn-cta btn-admin" id="btnLogin">
                        <i class="fas fa-right-to-bracket"></i> Admin
                    </a>
                </div>
            </div>

            <!-- Right Side -->
            <div class="hero-right">
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon si-1"><i class="fas fa-users"></i></div>
                        <div class="stat-value">500+</div>
                        <div class="stat-label">Pendaftar</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon si-2"><i class="fas fa-user-check"></i></div>
                        <div class="stat-value">98%</div>
                        <div class="stat-label">Diterima</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon si-3"><i class="fas fa-award"></i></div>
                        <div class="stat-value">3</div>
                        <div class="stat-label">Jurusan</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon si-4"><i class="fas fa-star"></i></div>
                        <div class="stat-value">A</div>
                        <div class="stat-label">Akreditasi</div>
                    </div>
                </div>

                <div class="trust-badge">
                    <span class="dot"></span>
                    Pendaftaran Gelombang Baru Dibuka
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

