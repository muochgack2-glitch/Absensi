<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bukti Ambil Barang</title>
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
            max-width: 850px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            border-bottom: 3px solid #27ae60;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        
        .header-title {
            display: inline-block;
        }
        
        .header-title h1 {
            font-size: 18px;
            color: #27ae60;
            margin: 5px 0;
            font-weight: 700;
        }
        
        .header-title p {
            font-size: 12px;
            color: #666;
            margin: 2px 0;
        }
        
        .header-info {
            font-size: 10px;
            color: #999;
            margin-top: 5px;
        }
        
        .status-header {
            background-color: #27ae60;
            color: white;
            padding: 12px;
            text-align: center;
            border-radius: 5px;
            margin-bottom: 20px;
            font-weight: 600;
        }
        
        .section-title {
            font-size: 13px;
            font-weight: 700;
            color: #fff;
            background-color: #16a085;
            padding: 8px 12px;
            margin: 18px 0 10px 0;
            border-radius: 2px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-bottom: 12px;
        }
        
        .info-item {
            background-color: #f0f0f0;
            padding: 10px;
            border-radius: 3px;
            border-left: 3px solid #27ae60;
        }
        
        .info-label {
            font-weight: 600;
            color: #2c3e50;
            font-size: 11px;
            display: block;
            margin-bottom: 3px;
        }
        
        .info-value {
            color: #555;
            font-size: 12px;
            font-weight: 500;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            font-size: 11px;
        }
        
        th {
            background-color: #27ae60;
            color: white;
            padding: 10px 8px;
            text-align: left;
            font-weight: 600;
            border: 1px solid #1e8449;
        }
        
        td {
            padding: 10px 8px;
            border: 1px solid #ddd;
        }
        
        tbody tr:nth-child(odd) {
            background-color: #f9f9f9;
        }
        
        tbody tr:hover {
            background-color: #ecf0f1;
        }
        
        .checkbox-cell {
            text-align: center;
            font-size: 20px;
        }
        
        .checkbox-empty {
            width: 20px;
            height: 20px;
            border: 2px solid #bbb;
            display: inline-block;
            border-radius: 3px;
        }
        
        .checkbox-checked::before {
            content: "✓";
            color: #27ae60;
            font-weight: bold;
        }
        
        .item-list {
            list-style: none;
            padding: 0;
        }
        
        .item-list li {
            padding: 6px 8px;
            margin-bottom: 4px;
            background-color: #ecf0f1;
            border-left: 3px solid #27ae60;
            font-size: 11px;
        }
        
        .item-list li::before {
            content: "□ ";
            margin-right: 8px;
            color: #27ae60;
            font-weight: bold;
        }
        
        .notes-section {
            background-color: #fef5e7;
            border-left: 4px solid #f39c12;
            padding: 12px;
            margin: 15px 0;
            border-radius: 3px;
            font-size: 11px;
        }
        
        .notes-section strong {
            display: block;
            color: #d68910;
            margin-bottom: 5px;
        }
        
        .notes-section ul {
            margin-left: 20px;
            line-height: 1.7;
        }
        
        .condition-section {
            background-color: #fadbd8;
            border-left: 4px solid #e74c3c;
            padding: 12px;
            margin: 15px 0;
            border-radius: 3px;
            font-size: 11px;
        }
        
        .condition-section strong {
            display: block;
            color: #c0392b;
            margin-bottom: 5px;
        }
        
        .signature-section {
            margin-top: 30px;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 40px;
            padding: 20px 10%;
            text-align: center;
        }
        
        .signature-item {
            text-align: center;
        }
        
        .signature-label {
            font-size: 11px;
            color: #666;
            font-weight: 600;
            margin-bottom: 50px;
        }
        
        .signature-line {
            border-top: 2px solid #333;
            margin-top: 5px;
        }
        
        .signature-name {
            font-size: 10px;
            margin-top: 5px;
            color: #333;
        }
        
        .footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #ccc;
            font-size: 10px;
            color: #999;
        }
        
        .highlight {
            background-color: #ffffcc;
            padding: 2px 4px;
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
                <h1>📦 BUKTI AMBIL BARANG</h1>
                <p>SPMB (Sistem Penerimaan Murid Baru) - Tahun Ajaran {{ date('Y') }}/{{ date('Y') + 1 }}</p>
                <div class="header-info">
                    Nomor Pendaftaran: <span class="highlight">{{ $pendaftar->no_pendaftaran ?? '-' }}</span>
                </div>
            </div>
        </div>

        <!-- Status Header -->
        <div class="status-header">
            ✓ DITERIMA - BUKTI PENGAMBILAN BARANG PERLENGKAPAN SEKOLAH
        </div>

        <!-- Data Pendaftar -->
        <div class="section-title">📋 DATA PESERTA DIDIK</div>
        
        <div class="info-grid">
            <div class="info-item">
                <span class="info-label">Nama Lengkap</span>
                <span class="info-value">{{ $pendaftar->nama_lengkap ?? '-' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">NISN</span>
                <span class="info-value">{{ $pendaftar->nisn ?? '-' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">No. Pendaftaran</span>
                <span class="info-value">{{ $pendaftar->no_pendaftaran ?? '-' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Jalur Pendaftaran</span>
                <span class="info-value">{{ $pendaftar->jalur_pendaftaran ?? '-' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Email</span>
                <span class="info-value" style="word-break: break-all;">{{ $pendaftar->email ?? '-' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">No. WhatsApp</span>
                <span class="info-value">{{ $pendaftar->no_whatsapp ?? '-' }}</span>
            </div>
        </div>

        <!-- Daftar Barang yang Diambil -->
        <div class="section-title">📦 DAFTAR BARANG YANG DIAMBIL</div>
        
        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 50%;">Nama Barang</th>
                    <th style="width: 15%;">Jumlah</th>
                    <th style="width: 15%;">Satuan</th>
                    <th style="width: 15%;">Kondisi</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>Seragam Sekolah (Batik)</td>
                    <td class="checkbox-cell">2</td>
                    <td>Stel</td>
                    <td class="checkbox-cell"><span class="checkbox-empty"></span></td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>Seragam Olahraga</td>
                    <td class="checkbox-cell">2</td>
                    <td>Stel</td>
                    <td class="checkbox-cell"><span class="checkbox-empty"></span></td>
                </tr>
                <tr>
                    <td>3</td>
                    <td>Topi Sekolah</td>
                    <td class="checkbox-cell">1</td>
                    <td>Buah</td>
                    <td class="checkbox-cell"><span class="checkbox-empty"></span></td>
                </tr>
                <tr>
                    <td>4</td>
                    <td>Dasi/Ikat Pinggang</td>
                    <td class="checkbox-cell">2</td>
                    <td>Buah</td>
                    <td class="checkbox-cell"><span class="checkbox-empty"></span></td>
                </tr>
                <tr>
                    <td>5</td>
                    <td>Badge/Lambang Sekolah</td>
                    <td class="checkbox-cell">3</td>
                    <td>Buah</td>
                    <td class="checkbox-cell"><span class="checkbox-empty"></span></td>
                </tr>
                <tr>
                    <td>6</td>
                    <td>Sepatu Sekolah</td>
                    <td class="checkbox-cell">1</td>
                    <td>Pasang</td>
                    <td class="checkbox-cell"><span class="checkbox-empty"></span></td>
                </tr>
                <tr>
                    <td>7</td>
                    <td>Kaos Kaki Sekolah</td>
                    <td class="checkbox-cell">3</td>
                    <td>Pasang</td>
                    <td class="checkbox-cell"><span class="checkbox-empty"></span></td>
                </tr>
                <tr>
                    <td>8</td>
                    <td>Tas Sekolah</td>
                    <td class="checkbox-cell">1</td>
                    <td>Buah</td>
                    <td class="checkbox-cell"><span class="checkbox-empty"></span></td>
                </tr>
                <tr>
                    <td colspan="5" style="text-align: center; background-color: #ecf0f1;">
                        <strong>Catatan: Kotak kondisi untuk diisi dengan tanda ✓ jika barang dalam kondisi baik, atau "CACAT" jika ada kerusakan</strong>
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Keterangan -->
        <div class="section-title">📝 KETERANGAN PENGAMBILAN</div>
        
        <div class="info-grid" style="grid-template-columns: repeat(1, 1fr);">
            <div class="info-item">
                <span class="info-label">Tanggal Pengambilan</span>
                <span class="info-value">{{ now()->format('d-m-Y H:i:s') }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Petugas Penyerah Barang</span>
                <span class="info-value">______________________</span>
            </div>
            <div class="info-item">
                <span class="info-label">Lokasi Pengambilan</span>
                <span class="info-value">Ruang Logistik Pendaftaran - Gedung Utama</span>
            </div>
        </div>

        <!-- Kondisi Barang -->
        <div class="condition-section">
            <strong>⚠️ PERNYATAAN KONDISI BARANG</strong>
            <p>Saya menyatakan bahwa semua barang yang saya terima dalam kondisi:</p>
            <ul>
                <li>✓ Baik dan sesuai dengan daftar di atas</li>
                <li>✓ Dalam jumlah yang tepat</li>
                <li>✓ Saya menerima barang dengan senang hati dan tidak ada tuntutan lebih lanjut</li>
            </ul>
        </div>

        <!-- Catatan Penting -->
        <div class="notes-section">
            <strong>📝Œ CATATAN PENTING</strong>
            <ul>
                <li>Periksa semua barang dengan cermat sebelum meninggalkan lokasi pengambilan</li>
                <li>Apabila ada barang yang cacat atau rusak, segera laporkan kepada petugas</li>
                <li>Simpan bukti pengambilan ini untuk referensi</li>
                <li>Setiap peserta didik bertanggung jawab atas barang yang diterimanya</li>
                <li>Hilangnya atau kerusakan barang di luar lokasi pengambilan adalah tanggung jawab peserta didik</li>
            </ul>
        </div>

        <!-- Signature Section -->
        <div class="signature-section">
            <div class="signature-item">
                <div class="signature-label">Penerima (Peserta Didik / Orang Tua),</div>
                <div style="height: 45px;"></div>
                <div class="signature-line"></div>
                <div class="signature-name">{{ $pendaftar->nama_lengkap ?? '(_________________)' }}</div>
            </div>
            <div class="signature-item">
                <div class="signature-label">Petugas Penyerahan,</div>
                <div style="height: 45px;"></div>
                <div class="signature-line"></div>
                <div class="signature-name">(_________________)</div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong>Bukti pengambilan barang ini merupakan tanda bahwa peserta didik telah menerima perlengkapan sekolah.</strong></p>
            <p>Dicetak pada: {{ date('d-m-Y H:i:s') }} | SPMB (Sistem Penerimaan Murid Baru) © {{ date('Y') }}</p>
            <p style="margin-top: 5px;">Dokumen ini sah sebagai bukti resmi pengambilan barang. Harap disimpan dengan baik.</p>
        </div>
    </div>
</body>
</html>

