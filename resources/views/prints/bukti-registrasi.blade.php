<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bukti Registrasi</title>
    @include('partials.favicon')
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        
        .container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            border-bottom: 3px solid #2c3e50;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        
        .header-logo {
            margin-bottom: 10px;
        }
        
        .header-logo img {
            height: 50px;
            margin-right: 10px;
            vertical-align: middle;
        }
        
        .header-title {
            display: inline-block;
        }
        
        .header-title h1 {
            font-size: 18px;
            color: #2c3e50;
            margin: 5px 0;
            font-weight: 700;
        }
        
        .header-title p {
            font-size: 12px;
            color: #666;
            margin: 2px 0;
        }
        
        .content {
            margin: 20px 0;
        }
        
        .section-title {
            font-size: 14px;
            font-weight: 700;
            color: #fff;
            background-color: #34495e;
            padding: 8px 12px;
            margin: 15px 0 10px 0;
            border-radius: 3px;
        }
        
        .form-group {
            display: flex;
            margin-bottom: 12px;
            padding: 8px;
            border-bottom: 1px dotted #ccc;
        }
        
        .form-group.full {
            flex-basis: 100%;
        }
        
        .form-group.half {
            flex-basis: 48%;
            margin-right: 2%;
        }
        
        .form-group.half:nth-child(even) {
            margin-right: 0;
        }
        
        .label {
            flex: 0 0 35%;
            font-weight: 600;
            color: #2c3e50;
            padding-right: 10px;
            text-align: left;
        }
        
        .value {
            flex: 1;
            color: #555;
            padding-left: 10px;
            border-left: 1px solid #ddd;
        }
        
        .value.highlight {
            color: #e74c3c;
            font-weight: 600;
        }
        
        .important-info {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 12px;
            margin: 15px 0;
            border-radius: 3px;
            font-size: 12px;
        }
        
        .important-info strong {
            display: block;
            margin-bottom: 5px;
            color: #856404;
        }
        
        .signature-section {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
            padding: 0 10%;
        }
        
        .signature-item {
            text-align: center;
            width: 40%;
        }
        
        .signature-line {
            border-top: 1px solid #333;
            width: 100%;
            margin-top: 50px;
            padding-top: 5px;
        }
        
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ccc;
            font-size: 11px;
            color: #999;
        }
        
        .no-margin {
            margin: 0;
        }
        
        .text-center {
            text-align: center;
        }
        
        .status-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }
        
        .status-badge.pending {
            background-color: #ffeaa7;
            color: #d63031;
        }
        
        .status-badge.approved {
            background-color: #55efc4;
            color: #00b894;
        }
        
        .status-badge.rejected {
            background-color: #ff7675;
            color: #d63031;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }
        
        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
            font-size: 12px;
        }
        
        th {
            background-color: #ecf0f1;
            font-weight: 600;
            color: #2c3e50;
        }
        
        .barcode {
            text-align: center;
            margin: 15px 0;
        }
        
        .barcode img {
            max-width: 200px;
            height: auto;
        }
        
        @page {
            size: A4;
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="header-title">
                <h1>✓ BUKTI REGISTRASI</h1>
                <p>SPMB (Sistem Penerimaan Murid Baru)</p>
                <p style="margin-top: 5px; font-size: 11px;">Tahun Ajaran {{ date('Y') }}/{{ date('Y') + 1 }}</p>
            </div>
        </div>

        <!-- Content -->
        <div class="content">
            <!-- Status Section -->
            <div class="form-group full">
                <div class="label">Status Registrasi:</div>
                <div class="value">
                    <span class="status-badge {{ strtolower($pendaftar->status ?? 'pending') }}">
                        {{ ucfirst($pendaftar->status ?? 'Pending') }}
                    </span>
                </div>
            </div>

            <!-- Data Registrasi -->
            <div class="section-title">📋 DATA REGISTRASI</div>
            
            <div style="display: flex; flex-wrap: wrap;">
                <div class="form-group half">
                    <div class="label">Nomor Pendaftaran:</div>
                    <div class="value highlight">{{ $pendaftar->no_pendaftaran ?? '-' }}</div>
                </div>
                <div class="form-group half">
                    <div class="label">Tanggal Registrasi:</div>
                    <div class="value">{{ $pendaftar->created_at?->format('d-m-Y H:i') ?? '-' }}</div>
                </div>
            </div>

            <div style="display: flex; flex-wrap: wrap;">
                <div class="form-group half">
                    <div class="label">Jalur Pendaftaran:</div>
                    <div class="value">{{ $pendaftar->jalur_pendaftaran ?? '-' }}</div>
                </div>
                <div class="form-group half">
                    <div class="label">Tahun Ajaran:</div>
                    <div class="value">{{ date('Y') }}/{{ date('Y') + 1 }}</div>
                </div>
            </div>

            <!-- Data Pribadi Calon Siswa -->
            <div class="section-title">👤 DATA PRIBADI CALON SISWA</div>
            
            <div style="display: flex; flex-wrap: wrap;">
                <div class="form-group half">
                    <div class="label">Nama Lengkap:</div>
                    <div class="value">{{ $pendaftar->nama_lengkap ?? '-' }}</div>
                </div>
                <div class="form-group half">
                    <div class="label">Jenis Kelamin:</div>
                    <div class="value">{{ $pendaftar->jenis_kelamin ?? '-' }}</div>
                </div>
            </div>

            <div style="display: flex; flex-wrap: wrap;">
                <div class="form-group half">
                    <div class="label">Tempat Lahir:</div>
                    <div class="value">{{ $pendaftar->tempat_lahir ?? '-' }}</div>
                </div>
                <div class="form-group half">
                    <div class="label">Tanggal Lahir:</div>
                    <div class="value">{{ $pendaftar->tanggal_lahir?->format('d-m-Y') ?? '-' }}</div>
                </div>
            </div>

            <div class="form-group full">
                <div class="label">NISN:</div>
                <div class="value">{{ $pendaftar->nisn ?? '-' }}</div>
            </div>

            <!-- Kontak -->
            <div class="section-title">📞 DATA KONTAK</div>
            
            <div style="display: flex; flex-wrap: wrap;">
                <div class="form-group half">
                    <div class="label">Email:</div>
                    <div class="value">{{ $pendaftar->email ?? '-' }}</div>
                </div>
                <div class="form-group half">
                    <div class="label">No. WhatsApp:</div>
                    <div class="value">{{ $pendaftar->no_whatsapp ?? '-' }}</div>
                </div>
            </div>

            <!-- Important Notice -->
            <div class="important-info">
                <strong>⚠️ CATATAN PENTING:</strong>
                <ul style="margin-left: 20px;">
                    <li>Bukti registrasi ini merupakan tanda bahwa anda telah terdaftar dalam sistem SPMB.</li>
                    <li>Pastikan semua data yang tertera sudah sesuai dan benar.</li>
                    <li>Simpan nomor pendaftaran anda untuk proses selanjutnya.</li>
                    <li>Untuk perubahan data, silakan hubungi panitia pendaftaran.</li>
                </ul>
            </div>
        </div>

        <!-- Signature Section -->
        <div class="signature-section">
            <div class="signature-item">
                <p class="no-margin" style="font-size: 12px;">Peserta Didik,</p>
                <div class="signature-line"></div>
                <p class="no-margin" style="font-size: 12px; margin-top: 5px;">{{ $pendaftar->nama_lengkap ?? '(_________________)' }}</p>
            </div>
            <div class="signature-item">
                <p class="no-margin" style="font-size: 12px;">Panitia Pendaftaran,</p>
                <div class="signature-line"></div>
                <p class="no-margin" style="font-size: 12px; margin-top: 5px;">(_________________)</p>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Dokumen ini merupakan bukti sah pendaftaran. Cetak pada: {{ date('d-m-Y H:i:s') }}</p>
            <p>SPMB (Sistem Penerimaan Murid Baru) - All Rights Reserved © {{ date('Y') }}</p>
        </div>
    </div>
</body>
</html>

