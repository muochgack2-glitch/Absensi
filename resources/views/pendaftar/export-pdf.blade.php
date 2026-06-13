<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Data Pendaftar - {{ $settings->nama_sekolah ?? 'SPMB' }}</title>
    <style>
        @page {
            margin: 1cm;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 3px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 16pt;
            color: #333;
        }
        .header p {
            margin: 5px 0;
            font-size: 9pt;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 8pt;
        }
        th {
            background-color: #4472C4;
            color: white;
            padding: 8px 4px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #ddd;
        }
        td {
            padding: 6px 4px;
            border: 1px solid #ddd;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .footer {
            margin-top: 20px;
            text-align: right;
            font-size: 9pt;
            color: #666;
        }
        .status-badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 7pt;
            font-weight: bold;
        }
        .status-diterima {
            background-color: #d4edda;
            color: #155724;
        }
        .status-belum {
            background-color: #fff3cd;
            color: #856404;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $settings->nama_sekolah ?? 'SISTEM PENERIMAAN MURID BARU' }}</h1>
        <p>{{ $settings->alamat_sekolah ?? '' }}</p>
        <p style="margin-top: 10px; font-weight: bold;">DATA PENDAFTAR</p>
        <p>Dicetak pada: {{ date('d F Y, H:i') }} WIB</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 3%;">No</th>
                <th style="width: 10%;">No. Reg</th>
                <th style="width: 8%;">NISN</th>
                <th style="width: 15%;">Nama Lengkap</th>
                <th style="width: 12%;">Asal Sekolah</th>
                <th style="width: 8%;">Jurusan</th>
                <th style="width: 10%;">Jaringan</th>
                <th style="width: 6%;">Gel.</th>
                <th style="width: 10%;">Status</th>
                <th style="width: 8%;">Ukuran</th>
                <th style="width: 10%;">Tgl Daftar</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pendaftars as $index => $p)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td>{{ $p->no_registrasi }}</td>
                <td>{{ $p->nisn }}</td>
                <td>{{ $p->nama_lengkap }}</td>
                <td>{{ $p->asal_sekolah }}</td>
                <td>{{ $p->masterJurusan ? $p->masterJurusan->kode : $p->jurusan }}</td>
                <td>{{ $p->nama_jaringan ?: 'PANITIA' }}</td>
                <td style="text-align: center;">{{ $p->gelombang }}</td>
                <td>
                    <span class="status-badge {{ $p->status_siswa === 'Diterima' ? 'status-diterima' : 'status-belum' }}">
                        {{ $p->status_siswa }}
                    </span>
                </td>
                <td style="text-align: center;">{{ $p->logistik ? $p->logistik->ukuran_kaos : '-' }}</td>
                <td>{{ $p->created_at->format('d/m/Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p><strong>Total Pendaftar: {{ $pendaftars->count() }}</strong></p>
        <p>Diterima: {{ $pendaftars->where('status_siswa', 'Diterima')->count() }} | 
           Belum Daftar Ulang: {{ $pendaftars->where('status_siswa', '!=', 'Diterima')->count() }}</p>
    </div>

    <script>
        // Auto print when page loads
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>

