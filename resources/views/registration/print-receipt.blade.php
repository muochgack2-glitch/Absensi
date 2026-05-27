@php
    $sys = \App\Models\SettingSystem::instance()->toSettingsArray();
    $schoolName    = $sys['school_name']       ?: 'SPMB';
    $schoolAddress = $sys['school_address']    ?: '';
    $schoolCity    = $sys['school_city']       ?: '';
    $schoolPhone   = $sys['school_phone']      ?: ($sys['school_contact'] ?: '');
    $schoolEmail   = $sys['school_email']      ?: '';
    $printFooter   = $sys['print_footer_text'] ?: 'Dokumen ini dicetak otomatis oleh sistem SPMB (Sistem Penerimaan Murid Baru).';
    $logoUrl       = !empty($sys['school_logo']) ? asset('storage/' . $sys['school_logo']) : null;
    $docHeaderText = $sys['document_header_text'] ?: '';
    $docCity       = $sys['document_city'] ?: $schoolCity;
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bukti Pendaftaran - {{ $registrationData['no_registrasi'] }}</title>
    @include('partials.favicon')
    @include('partials.theme-vars')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', 'Arial', sans-serif; background: #f1f5f9; padding: 20px; color: #1e293b; font-size: 12px; }

        @page { size: F4 portrait; margin: 10mm 12mm; }
        @media print {
            body { padding: 0; background: white; font-size: 12px; }
            .no-print { display: none !important; }
            .receipt { box-shadow: none; border: 1px solid #cbd5e1; border-radius: 0; padding: 0; max-width: 100%; width: 100%; }
        }

        .no-print { text-align: center; margin-bottom: 20px; display: flex; gap: 10px; justify-content: center; flex-wrap: wrap; }
        .btn { font-family: 'Inter', sans-serif; border: none; padding: 10px 24px; border-radius: 10px; cursor: pointer; font-size: 13px; font-weight: 700; transition: all 0.25s; display: inline-flex; align-items: center; gap: 8px; text-decoration: none; }
        .btn-print { background: linear-gradient(135deg, var(--theme-primary, #6366f1), var(--theme-secondary, #a855f7)); color: white; }
        .btn-back { background: white; color: var(--theme-primary, #6366f1); border: 2px solid var(--theme-primary, #6366f1) !important; }

        .receipt { width: 100%; max-width: 185mm; background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px 18px; margin: 0 auto; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }

        /* Header */
        .doc-header { display: grid; grid-template-columns: auto 1fr auto; align-items: center; gap: 10px; border-bottom: 2px solid #334155; padding-bottom: 8px; margin-bottom: 10px; }
        .school-name { font-size: 13px; font-weight: 800; color: #1e293b; }
        .school-addr { font-size: 9px; color: #64748b; line-height: 1.4; margin-top: 2px; }
        .doc-id { text-align: right; font-size: 9px; color: #64748b; }

        .doc-title { text-align: center; font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; color: #1e293b; margin: 8px 0; padding: 7px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; }

        /* Reg number */
        .reg-box { padding: 10px 14px; border: 1.5px solid var(--theme-primary, #6366f1); border-radius: 8px; background: #eef2ff; margin-bottom: 10px; }
        .reg-box .lbl { font-size: 9px; text-transform: uppercase; letter-spacing: 1px; color: #64748b; font-weight: 700; }
        .reg-box .num { font-size: 20px; font-weight: 800; color: var(--theme-primary, #4f46e5); font-family: 'Courier New', monospace; margin-top: 2px; }

        /* Info table */
        .section-head { font-size: 10px; font-weight: 700; background: #f1f5f9; padding: 5px 0; border-left: 3px solid var(--theme-primary, #6366f1); padding-left: 8px; border-radius: 0 4px 4px 0; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.4px; color: #475569; }
        .info-table { width: 100%; border-collapse: collapse; }
        .info-table td { padding: 4px 0; vertical-align: top; }
        .info-table .lbl { width: 130px; font-weight: 600; color: #475569; font-size: 11px; }
        .info-table .sep { width: 12px; text-align: center; color: #94a3b8; }
        .info-table .val { font-size: 11px; border-bottom: 1px solid #e2e8f0; }

        /* Notes */
        .notes-box { background: #f8fafc; border-radius: 8px; padding: 10px 14px; margin: 10px 0; font-size: 10px; line-height: 1.8; color: #475569; border: 1px solid #e2e8f0; }
        .notes-box p { display: flex; align-items: center; gap: 6px; }
        .notes-box .check { color: #10b981; font-weight: 700; }

        /* Signature */
        .sig-wrap { margin-top: 16px; display: flex; justify-content: flex-end; }
        .sig-block { font-size: 11px; line-height: 1.8; text-align: left; }
        .sig-line { margin-top: 52px; border-bottom: 1px solid #1e293b; width: 160px; }

        /* Footer */
        .doc-footer { text-align: center; margin-top: 10px; font-size: 9px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 6px; line-height: 1.6; }
    </style>
</head>
<body>
    <div class="no-print">
        <button class="btn btn-print" onclick="window.print()">Cetak / Simpan PDF</button>
        <a href="{{ route('registration.receipt') }}" class="btn btn-back">Kembali</a>
    </div>

    <div class="receipt">
        <!-- Header -->
        <div class="doc-header">
            <div>
                @if($logoUrl)
                    <img src="{{ $logoUrl }}" alt="Logo {{ $schoolName }}" style="width:42px;height:42px;object-fit:contain;">
                @else
                    <span style="font-size:28px;">🎓</span>
                @endif
            </div>
            <div>
                @if($docHeaderText)
                    <div style="font-size:9px;font-weight:800;letter-spacing:.08em;text-transform:uppercase;color:#0f172a;">{{ $docHeaderText }}</div>
                @endif
                <div class="school-name">{{ $schoolName }}</div>
                <div class="school-addr">{{ $schoolAddress }}{{ $schoolCity ? ', '.$schoolCity : '' }} · {{ $schoolPhone }}{{ $schoolEmail ? ' · '.$schoolEmail : '' }}</div>
            </div>
            <div class="doc-id">
                Doc: REG-{{ $registrationData['no_registrasi'] }}<br>
                {{ date('d/m/Y') }}
            </div>
        </div>

        <div class="doc-title">Bukti Pendaftaran</div>

        <!-- Nomor Registrasi -->
        <div class="reg-box">
            <div class="lbl">Nomor Registrasi</div>
            <div class="num">{{ $registrationData['no_registrasi'] }}</div>
        </div>

        <!-- Data Pendaftar -->
        <div class="section-head">Data Pendaftar</div>
        <table class="info-table">
            <tr><td class="lbl">Nama Lengkap</td><td class="sep">:</td><td class="val">{{ $registrationData['nama_lengkap'] }}</td></tr>
            <tr><td class="lbl">NISN</td><td class="sep">:</td><td class="val">{{ $registrationData['nisn'] }}</td></tr>
            <tr><td class="lbl">Jurusan</td><td class="sep">:</td><td class="val">{{ $registrationData['jurusan'] }}{{ !empty($registrationData['jurusan_nama']) ? ' - ' . $registrationData['jurusan_nama'] : '' }}</td></tr>
            <tr><td class="lbl">Asal Sekolah</td><td class="sep">:</td><td class="val">{{ $registrationData['asal_sekolah'] }}</td></tr>
            <tr><td class="lbl">Gelombang</td><td class="sep">:</td><td class="val">Gelombang {{ $registrationData['gelombang'] }}</td></tr>
            <tr><td class="lbl">Tanggal Pendaftaran</td><td class="sep">:</td><td class="val">{{ $registrationData['tgl_daftar'] }}</td></tr>
        </table>

        <!-- Catatan Penting -->
        <div class="notes-box">
            <p><span class="check">✓</span> Simpan bukti ini sebagai dokumen resmi pendaftaran</p>
            <p><span class="check">✓</span> Gunakan nomor registrasi saat verifikasi daftar ulang di sekolah</p>
            <p><span class="check">✓</span> Waktu verifikasi sesuai jadwal yang telah ditentukan panitia</p>
        </div>

        <!-- QR Code -->
        @if (class_exists('SimpleSoftwareIO\QrCode\Facades\QrCode'))
            <div style="text-align:center;margin:10px 0;">
                {!! QrCode::size(80)->generate($registrationData['no_registrasi']) !!}
                <div style="font-size:9px;color:#64748b;margin-top:4px;">{{ $registrationData['no_registrasi'] }}</div>
            </div>
        @endif

        <!-- Tanda Tangan -->
        <div class="sig-wrap">
            <div class="sig-block">
                <div>{{ $docCity }}, {{ date('d F Y') }}</div>
                <div style="font-weight:700;">Panitia SPMB {{ $schoolName }}</div>
                <div class="sig-line"></div>
            </div>
        </div>

        <!-- Footer -->
        <div class="doc-footer">
            Dicetak: {{ date('d-m-Y H:i') }} WIB &nbsp;|&nbsp; {{ $schoolName }}<br>
            {{ $printFooter }}
        </div>
    </div>
</body>
</html>
