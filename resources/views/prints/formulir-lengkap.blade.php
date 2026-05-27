<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulir Lengkap SPMB</title>
    @include('partials.favicon')
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.5;
            color: #333;
            background: white;
        }
        
        .container {
            width: 100%;
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            border-bottom: 3px solid #2c3e50;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        
        .header-title h1 {
            font-size: 20px;
            color: #2c3e50;
            margin: 5px 0;
            font-weight: 700;
        }
        
        .header-title p {
            font-size: 11px;
            color: #666;
            margin: 2px 0;
        }
        
        .header-info {
            font-size: 10px;
            color: #999;
            margin-top: 5px;
        }
        
        .section-title {
            font-size: 13px;
            font-weight: 700;
            color: #fff;
            background-color: #34495e;
            padding: 8px 12px;
            margin: 18px 0 10px 0;
            border-radius: 2px;
        }
        
        .form-row {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 12px;
        }
        
        .form-group {
            flex: 1;
            min-width: 45%;
        }
        
        .form-group.full {
            flex-basis: 100%;
        }
        
        .label {
            font-weight: 600;
            color: #2c3e50;
            font-size: 11px;
            margin-bottom: 3px;
            display: block;
        }
        
        .value {
            color: #555;
            font-size: 11px;
            padding: 6px;
            border: 1px solid #ddd;
            border-radius: 3px;
            background: #f9f9f9;
            min-height: 22px;
            line-height: 1.8;
        }
        
        .value-empty {
            min-height: 30px;
            display: flex;
            align-items: center;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 12px 0;
            font-size: 11px;
        }
        
        th {
            background-color: #ecf0f1;
            padding: 8px;
            text-align: left;
            font-weight: 600;
            border-bottom: 2px solid #34495e;
            color: #2c3e50;
        }
        
        td {
            padding: 6px 8px;
            border-bottom: 1px solid #ddd;
        }
        
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .checklist {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin: 8px 0;
            font-size: 11px;
        }
        
        .checkbox-item {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .checkbox {
            width: 16px;
            height: 16px;
            border: 1px solid #999;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
        }
        
        .important-section {
            background-color: #e8f4f8;
            border-left: 4px solid #3498db;
            padding: 10px 12px;
            margin: 15px 0;
            border-radius: 3px;
            font-size: 11px;
            line-height: 1.6;
        }
        
        .important-section strong {
            display: block;
            color: #2980b9;
            margin-bottom: 5px;
        }
        
        .signature-section {
            margin-top: 30px;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            padding: 20px 10%;
            text-align: center;
        }
        
        .signature-item {
            text-align: center;
        }
        
        .signature-label {
            font-size: 10px;
            color: #666;
            margin-bottom: 40px;
        }
        
        .signature-line {
            border-top: 1px solid #333;
            margin-top: 5px;
        }
        
        .signature-name {
            font-size: 10px;
            margin-top: 3px;
            color: #333;
        }
        
        .footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #ccc;
            font-size: 9px;
            color: #999;
        }
        
        .text-center {
            text-align: center;
        }
        
        .text-bold {
            font-weight: 600;
        }
        
        .page-break {
            page-break-after: always;
        }
        
        @page {
            size: A4;
            margin: 10mm;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="header-title">
                <h1>FORMULIR LENGKAP PENDAFTARAN SPMB</h1>
                <p>Sistem Penerimaan Murid Baru Tahun Ajaran {{ date('Y') }}/{{ date('Y') + 1 }}</p>
                <div class="header-info">
                    No. Pendaftaran: <strong>{{ $pendaftar->no_pendaftaran ?? '-' }}</strong> | 
                    Jalur: <strong>{{ $pendaftar->jalur_pendaftaran ?? '-' }}</strong>
                </div>
            </div>
        </div>

        <!-- A. DATA PRIBADI -->
        <div class="section-title">A. DATA PRIBADI CALON PESERTA DIDIK</div>
        
        <div class="form-row">
            <div class="form-group">
                <label class="label">1. Nama Lengkap *</label>
                <div class="value">{{ $pendaftar->nama_lengkap ?? '' }}</div>
            </div>
            <div class="form-group">
                <label class="label">2. NISN *</label>
                <div class="value">{{ $pendaftar->nisn ?? '' }}</div>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="label">3. Jenis Kelamin *</label>
                <div class="value">{{ $pendaftar->jenis_kelamin ?? '' }}</div>
            </div>
            <div class="form-group">
                <label class="label">4. Tempat Lahir *</label>
                <div class="value">{{ $pendaftar->tempat_lahir ?? '' }}</div>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="label">5. Tanggal Lahir *</label>
                <div class="value">{{ $pendaftar->tanggal_lahir?->format('d-m-Y') ?? '' }}</div>
            </div>
            <div class="form-group">
                <label class="label">6. Anak Ke- *</label>
                <div class="value">{{ $pendaftar->anak_ke ?? '' }}</div>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="label">7. Agama *</label>
                <div class="value">{{ $pendaftar->agama ?? '' }}</div>
            </div>
            <div class="form-group">
                <label class="label">8. Kewarganegaraan *</label>
                <div class="value">{{ $pendaftar->kewarganegaraan ?? 'Indonesia' }}</div>
            </div>
        </div>

        <!-- B. DATA ALAMAT -->
        <div class="section-title">B. DATA ALAMAT TINGGAL</div>
        
        <div class="form-row">
            <div class="form-group full">
                <label class="label">1. Alamat Jalan/Nomor *</label>
                <div class="value">{{ $pendaftar->alamat ?? '' }}</div>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="label">2. Kelurahan/Desa *</label>
                <div class="value">{{ $pendaftar->kelurahan ?? '' }}</div>
            </div>
            <div class="form-group">
                <label class="label">3. Kecamatan *</label>
                <div class="value">{{ $pendaftar->kecamatan ?? '' }}</div>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="label">4. Kota/Kabupaten *</label>
                <div class="value">{{ $pendaftar->kota ?? '' }}</div>
            </div>
            <div class="form-group">
                <label class="label">5. Provinsi *</label>
                <div class="value">{{ $pendaftar->provinsi ?? '' }}</div>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="label">6. Kode Pos *</label>
                <div class="value">{{ $pendaftar->kode_pos ?? '' }}</div>
            </div>
            <div class="form-group">
                <label class="label">7. Nomor Telepon *</label>
                <div class="value">{{ $pendaftar->no_telepon ?? '' }}</div>
            </div>
        </div>

        <!-- C. DATA KONTAK -->
        <div class="section-title">C. DATA KONTAK DIGITAL</div>
        
        <div class="form-row">
            <div class="form-group">
                <label class="label">1. Email *</label>
                <div class="value">{{ $pendaftar->email ?? '' }}</div>
            </div>
            <div class="form-group">
                <label class="label">2. No. WhatsApp/HP *</label>
                <div class="value">{{ $pendaftar->no_whatsapp ?? '' }}</div>
            </div>
        </div>

        <!-- D. DATA ASAL SEKOLAH -->
        <div class="section-title">D. DATA ASAL SEKOLAH</div>
        
        <div class="form-row">
            <div class="form-group">
                <label class="label">1. Nama Sekolah Asal *</label>
                <div class="value">{{ $pendaftar->sekolah_asal ?? '' }}</div>
            </div>
            <div class="form-group">
                <label class="label">2. NPSN Sekolah *</label>
                <div class="value">{{ $pendaftar->npsn_sekolah ?? '' }}</div>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="label">3. Kota Sekolah *</label>
                <div class="value">{{ $pendaftar->kota_sekolah ?? '' }}</div>
            </div>
            <div class="form-group">
                <label class="label">4. Tahun Lulus *</label>
                <div class="value">{{ $pendaftar->tahun_lulus ?? '' }}</div>
            </div>
        </div>

        <!-- E. DATA PRESTASI (jika ada) -->
        @if($pendaftar->jalur_pendaftaran === 'Prestasi' || !empty($pendaftar->prestasi))
        <div class="section-title">E. DATA PRESTASI</div>
        
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Jenis Prestasi</th>
                    <th>Tingkat</th>
                    <th>Penyelenggara</th>
                    <th>Tahun</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>{{ $pendaftar->prestasi ?? '' }}</td>
                    <td>{{ $pendaftar->tingkat_prestasi ?? '' }}</td>
                    <td>{{ $pendaftar->penyelenggara_prestasi ?? '' }}</td>
                    <td>{{ $pendaftar->tahun_prestasi ?? '' }}</td>
                </tr>
            </tbody>
        </table>
        @endif

        <!-- F. DATA ORANG TUA/WALI -->
        <div class="section-title">F. DATA ORANG TUA/WALI</div>
        
        <div class="form-row">
            <div class="form-group full">
                <label class="label"><strong>Ayah</strong></label>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="label">1. Nama Lengkap *</label>
                <div class="value">{{ $pendaftar->nama_ayah ?? '' }}</div>
            </div>
            <div class="form-group">
                <label class="label">2. Pekerjaan *</label>
                <div class="value">{{ $pendaftar->pekerjaan_ayah ?? '' }}</div>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="label">3. Penghasilan Bulanan *</label>
                <div class="value">{{ $pendaftar->penghasilan_ayah ?? '' }}</div>
            </div>
            <div class="form-group">
                <label class="label">4. No. WhatsApp *</label>
                <div class="value">{{ $pendaftar->no_ayah ?? '' }}</div>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group full">
                <label class="label"><strong>Ibu</strong></label>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="label">1. Nama Lengkap *</label>
                <div class="value">{{ $pendaftar->nama_ibu ?? '' }}</div>
            </div>
            <div class="form-group">
                <label class="label">2. Pekerjaan *</label>
                <div class="value">{{ $pendaftar->pekerjaan_ibu ?? '' }}</div>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="label">3. Penghasilan Bulanan *</label>
                <div class="value">{{ $pendaftar->penghasilan_ibu ?? '' }}</div>
            </div>
            <div class="form-group">
                <label class="label">4. No. WhatsApp *</label>
                <div class="value">{{ $pendaftar->no_ibu ?? '' }}</div>
            </div>
        </div>

        <!-- G. PERNYATAAN -->
        <div class="section-title">G. PERNYATAAN</div>
        
        <div class="important-section">
            <strong>Saya menyatakan bahwa:</strong>
            <ul style="margin-left: 20px; margin-top: 5px;">
                <li>Semua data yang saya isi dalam formulir ini adalah benar dan dapat dipertanggungjawabkan.</li>
                <li>Saya telah memahami dan menerima semua ketentuan yang berlaku dalam proses pendaftaran.</li>
                <li>Saya bersedia menerima hasil seleksi sesuai dengan kebijakan yang telah ditetapkan.</li>
                <li>Segala risiko dari kelengkapan dokumen adalah tanggung jawab saya.</li>
            </ul>
        </div>

        <!-- Signature Section -->
        <div class="signature-section">
            <div class="signature-item">
                <div class="signature-label">Calon Peserta Didik</div>
                <div style="height: 40px;"></div>
                <div class="signature-line"></div>
                <div class="signature-name">{{ $pendaftar->nama_lengkap ?? '(_________________)' }}</div>
            </div>
            <div class="signature-item">
                <div class="signature-label">Orang Tua/Wali</div>
                <div style="height: 40px;"></div>
                <div class="signature-line"></div>
                <div class="signature-name">(_________________)</div>
            </div>
            <div class="signature-item">
                <div class="signature-label">Panitia Pendaftaran</div>
                <div style="height: 40px;"></div>
                <div class="signature-line"></div>
                <div class="signature-name">(_________________)</div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Formulir yang sudah ditandatangani oleh kedua belah pihak dianggap sah sebagai bukti pendaftaran resmi.</p>
            <p>Dicetak pada: {{ date('d-m-Y H:i:s') }} | SPMB (Sistem Penerimaan Murid Baru) © {{ date('Y') }}</p>
        </div>
    </div>
</body>
</html>

