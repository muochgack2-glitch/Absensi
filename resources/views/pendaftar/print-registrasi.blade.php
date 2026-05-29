@php
    $sys = \App\Models\SettingSystem::instance()->toSettingsArray();

    $schoolName    = $sys['school_name']       ?: 'SMK NEGERI JAKARTA';
    $schoolAddress = $sys['school_address']    ?: 'Jl. Pendidikan No. 123';
    $schoolCity    = $sys['school_city']       ?: 'Jakarta';
    $schoolPhone   = $sys['school_phone']      ?: ($sys['school_contact'] ?: '(021) 1234-5678');
    $schoolEmail   = $sys['school_email']      ?: 'info@smkn.sch.id';
    $printFooter   = $sys['print_footer_text'] ?: 'Dokumen ini dicetak otomatis oleh sistem SPMB (Sistem Penerimaan Murid Baru).';

    $logoUrl = !empty($sys['school_logo']) ? asset('storage/' . $sys['school_logo']) : null;
    $docHeaderText = $sys['document_header_text'] ?: '';
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bukti Registrasi - {{ $pendaftar->no_registrasi }}</title>
    @include('partials.favicon')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', 'Arial', sans-serif; background: #f1f5f9; padding: 18px; color: #1e293b; font-size: 11px; }

        @page { size: F4 portrait; margin: 8mm 12mm; }
        @media print {
            body { padding: 0; background: white; }
            .no-print { display: none !important; }
            .receipt {
                box-shadow: none;
                border: 1px solid #cbd5e1;
                border-radius: 0;
                height: 148mm;
                max-width: 100%;
                width: 100%;
                padding: 10mm 12mm;
                overflow: hidden;
            }
        }

        .no-print { text-align: center; margin-bottom: 16px; display: flex; gap: 10px; justify-content: center; flex-wrap: wrap; }
        .btn { font-family: 'Inter', sans-serif; border: none; padding: 10px 22px; border-radius: 10px; cursor: pointer; font-size: 12px; font-weight: 700; transition: all 0.25s; display: inline-flex; align-items: center; gap: 8px; text-decoration: none; }
        .btn:hover { transform: translateY(-1px); }
        .btn-print { background: linear-gradient(135deg, #6366f1, #8b5cf6); color: white; }
        .btn-back { background: white; color: #6366f1; border: 2px solid #6366f1 !important; }

        /* Half F4 content block */
        .receipt {
            width: 100%;
            max-width: 185mm;
            height: 148mm;
            overflow: hidden;
            background: white;
            border: 1px solid #dbe3ef;
            border-radius: 12px;
            padding: 14px 16px;
            margin: 0 auto;
            box-shadow: 0 3px 12px rgba(0,0,0,0.08);
        }

        .doc-header { display: grid; grid-template-columns: auto 1fr auto; align-items: center; gap: 10px; border-bottom: 2px solid #334155; padding-bottom: 8px; margin-bottom: 8px; }
        .logo { font-size: 26px; }
        .school-name { font-size: 13px; font-weight: 800; }
        .school-addr { font-size: 9px; color: #64748b; line-height: 1.4; }
        .doc-id { text-align: right; font-size: 9px; color: #64748b; }

        .doc-title { text-align: center; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; margin: 8px 0; padding: 6px; border: 1px solid #cbd5e1; border-radius: 7px; background: #f8fafc; }

        .top-row { display: grid; grid-template-columns: 1fr 70px; gap: 10px; align-items: start; margin-bottom: 8px; }
        .reg-box { padding: 9px; border: 1.5px solid #6366f1; border-radius: 8px; background: #eef2ff; }
        .reg-box .lbl { font-size: 10px; text-transform: uppercase; letter-spacing: 1px; color: #64748b; font-weight: 700; }
        .reg-box .num { font-size: 20px; font-weight: 800; color: #4f46e5; font-family: 'Courier New', monospace; margin-top: 2px; }

        .photo-box { width: 70px; height: 92px; border: 1.5px dashed #64748b; border-radius: 8px; display: flex; align-items: center; justify-content: center; text-align: center; color: #64748b; font-size: 9px; line-height: 1.3; padding: 6px; background: #f8fafc; }

        .section-head { font-size: 11px; font-weight: 700; background: #f1f5f9; padding: 5px 8px; border-left: 3px solid #6366f1; border-radius: 0 6px 6px 0; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.4px; color: #475569; }
        .info-table { width: 100%; border-collapse: collapse; }
        .info-table td { padding: 4px 0; vertical-align: top; }
        .info-table .lbl { width: 120px; font-weight: 600; color: #475569; font-size: 12px; }
        .info-table .sep { width: 12px; text-align: center; color: #94a3b8; font-size: 12px; }
        .info-table .val { font-size: 12px; border-bottom: 1px solid #e2e8f0; }

        .mini-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 6px; margin-top: 8px; }
        .mini-card { border: 1px solid #e2e8f0; border-radius: 7px; padding: 6px; background: #fafcff; }
        .mini-card .k { font-size: 8px; color: #64748b; font-weight: 700; text-transform: uppercase; }
        .mini-card .v { font-size: 10px; font-weight: 700; margin-top: 2px; }
        .s-green { color: #059669; }
        .s-red { color: #dc2626; }
        .s-yellow { color: #d97706; }

        .bottom-row { display: grid; grid-template-columns: 1fr 110px; gap: 10px; margin-top: 8px; align-items: end; }
        .qr-wrap { text-align: center; border: 1px solid #e2e8f0; border-radius: 7px; padding: 6px; }
        .qr-label { font-size: 8px; color: #64748b; font-weight: 700; margin-bottom: 4px; }

        .sig-line { border-top: 1px solid #1e293b; margin-top: 18px; padding-top: 3px; font-size: 9px; text-align: center; font-weight: 600; }
        .doc-footer { text-align: center; margin-top: 7px; font-size: 10px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 5px; line-height: 1.4; }
    </style>
</head>
<body>
    <div class="no-print">
        <button class="btn btn-print" onclick="window.print()">Cetak Bukti Registrasi</button>
        <a href="{{ route('pendaftar.index') }}" class="btn btn-back">Kembali</a>
    </div>

    <div class="receipt">
        <div class="doc-header">
            <div class="logo">
                @if($logoUrl)
                    <img src="{{ $logoUrl }}" alt="Logo {{ $schoolName }}" style="width:42px;height:42px;object-fit:contain;">
                @else
                    🎓
                @endif
            </div>
            <div>
                @if($docHeaderText)
                    <div style="font-size:10px;font-weight:800;letter-spacing:.08em;text-transform:uppercase;color:#0f172a;">{{ $docHeaderText }}</div>
                @endif
                <div class="school-name">{{ $schoolName }}</div>
                <div class="school-addr">{{ $schoolAddress }}, {{ $schoolCity }} · {{ $schoolPhone }} · {{ $schoolEmail }}</div>
            </div>
            <div class="doc-id">
                Doc: REG-{{ $pendaftar->no_registrasi }}<br>
                {{ now()->format('d/m/Y') }}
            </div>
        </div>

        <div class="doc-title">Bukti Registrasi Pendaftaran</div>

        <div class="reg-box" style="margin-bottom:8px;">
            <div class="lbl">Nomor Registrasi</div>
            <div class="num">{{ $pendaftar->no_registrasi }}</div>
        </div>

        <div class="section-head">Data Pendaftar</div>
        <table class="info-table">
            <tr><td class="lbl">Nama Lengkap</td><td class="sep">:</td><td class="val">{{ $pendaftar->nama_lengkap }}</td></tr>
            <tr><td class="lbl">NISN</td><td class="sep">:</td><td class="val">{{ $pendaftar->nisn }}</td></tr>
            <tr><td class="lbl">Asal Sekolah</td><td class="sep">:</td><td class="val">{{ $pendaftar->asal_sekolah }}</td></tr>
            <tr><td class="lbl">Jurusan</td><td class="sep">:</td><td class="val">{{ $pendaftar->masterJurusan?->kode ?? $pendaftar->jurusan }} - {{ $pendaftar->masterJurusan?->nama ?? '' }}</td></tr>
            <tr><td class="lbl">Gelombang</td><td class="sep">:</td><td class="val">{{ $pendaftar->gelombang }}</td></tr>
            <tr><td class="lbl">Tanggal Daftar</td><td class="sep">:</td><td class="val">{{ ($pendaftar->tgl_daftar ?? $pendaftar->created_at ?? now())->format('d-m-Y H:i') }} WIB</td></tr>
        </table>

        <div style="margin-top:16px;display:flex;justify-content:flex-end;">
            <div style="font-size:12px;line-height:1.8;">
                <div>{{ $docCity ?? $schoolCity }}, {{ now()->format('d F Y') }}</div>
                <div style="font-weight:700;">Panitia SPMB {{ $schoolName }}</div>
                <div style="margin-top:52px;border-bottom:1px solid #1e293b;width:160px;"></div>
            </div>
        </div>

        <div class="doc-footer">
            Dicetak: {{ now()->format('d-m-Y H:i') }} WIB - {{ $schoolName }}<br>
            {{ $printFooter }}
        </div>
    </div>

    {{-- ===== BAGIAN BAWAH: BERKAS STOPMAP ===== --}}
    <div class="receipt" style="margin-top:8px;border-top:2px dashed #94a3b8;">
        <div class="doc-header">
            <div class="logo">
                @if($logoUrl)
                    <img src="{{ $logoUrl }}" alt="Logo {{ $schoolName }}" style="width:42px;height:42px;object-fit:contain;">
                @else
                    🎓
                @endif
            </div>
            <div>
                @if($docHeaderText)
                    <div style="font-size:10px;font-weight:800;letter-spacing:.08em;text-transform:uppercase;color:#0f172a;">{{ $docHeaderText }}</div>
                @endif
                <div class="school-name">{{ $schoolName }}</div>
                <div class="school-addr">{{ $schoolAddress }}, {{ $schoolCity }} · {{ $schoolPhone }} · {{ $schoolEmail }}</div>
            </div>
            <div class="doc-id">
                Doc: STM-{{ $pendaftar->no_registrasi }}<br>
                {{ now()->format('d/m/Y') }}
            </div>
        </div>

        <div class="doc-title">BERKAS PENDAFTARAN<br><small style="font-size:12px;font-weight:600;letter-spacing:0;text-transform:none;">( Di tempel di stopmap )</small></div>

        <div style="display:grid;grid-template-columns:1fr 90px;gap:12px;align-items:start;margin-top:8px;">
            <table style="width:100%;border-collapse:collapse;font-size:13px;">
                <tr>
                    <td style="width:150px;padding:5px 0;font-weight:600;color:#475569;vertical-align:top;">No. Pendaftaran</td>
                    <td style="width:10px;padding:5px 0;">:</td>
                    <td style="padding:5px 0;border-bottom:1px solid #e2e8f0;">
                        {{ $pendaftar->no_registrasi }} &nbsp;/&nbsp; Gel : {{ $pendaftar->gelombang }}
                    </td>
                </tr>
                <tr>
                    <td style="padding:5px 0;font-weight:600;color:#475569;">Jurusan</td>
                    <td style="padding:5px 0;">:</td>
                    <td style="padding:5px 0;border-bottom:1px solid #e2e8f0;font-weight:700;">
                        {{ $pendaftar->masterJurusan?->nama ?? $pendaftar->jurusan ?? '-' }}
                    </td>
                </tr>
                <tr>
                    <td style="padding:5px 0;font-weight:600;color:#475569;">Nama Lengkap</td>
                    <td style="padding:5px 0;">:</td>
                    <td style="padding:5px 0;border-bottom:1px solid #e2e8f0;">{{ $pendaftar->nama_lengkap }}</td>
                </tr>
                <tr>
                    <td style="padding:5px 0;font-weight:600;color:#475569;">Asal Sekolah</td>
                    <td style="padding:5px 0;">:</td>
                    <td style="padding:5px 0;border-bottom:1px solid #e2e8f0;">{{ $pendaftar->asal_sekolah }}</td>
                </tr>
                <tr>
                    <td style="padding:5px 0;font-weight:600;color:#475569;vertical-align:top;">Alamat Rumah</td>
                    <td style="padding:5px 0;vertical-align:top;">:</td>
                    <td style="padding:5px 0;border-bottom:1px solid #e2e8f0;">{{ $pendaftar->alamat ?: ($pendaftar->alamat_jalan ? $pendaftar->alamat_jalan.', '.$pendaftar->alamat_kelurahan.', '.$pendaftar->alamat_kecamatan : '-') }}</td>
                </tr>
            </table>
            <div style="border:1.5px solid #94a3b8;border-radius:4px;width:90px;height:110px;display:flex;align-items:center;justify-content:center;font-size:12px;color:#94a3b8;text-align:center;line-height:1.4;">Foto<br>3x4</div>
        </div>

        <table style="width:100%;border-collapse:collapse;font-size:13px;margin-top:10px;">
            <tr>
                <td style="width:150px;padding:5px 0;font-weight:600;color:#475569;vertical-align:top;">Keterangan</td>
                <td style="width:10px;padding:5px 0;vertical-align:top;">:</td>
                <td style="padding:5px 0;">
                    <div style="font-size:13px;">1. Diterima / Tidak Diterima</div>
                    <div style="padding-left:13px;font-size:13px;">Sebagai Murid baru {{ $schoolName }}</div>
                    <div style="margin-top:5px;font-size:13px;">2. Daftar Ulang Tanggal, ………………………</div>
                </td>
            </tr>
        </table>

        <div style="margin-top:40px;display:flex;justify-content:flex-end;">
            <div style="font-size:13px;line-height:1.8;">
                <div>{{ $docCity ?? $schoolCity }}, {{ now()->format('d F Y') }}</div>
                <div style="font-weight:700;">Panitia SPMB {{ $schoolName }}</div>
                <div style="margin-top:52px;border-bottom:1px solid #1e293b;width:160px;"></div>
            </div>
        </div>

        <div class="doc-footer">
            Dicetak: {{ now()->format('d-m-Y H:i') }} WIB - {{ $schoolName }}<br>
            {{ $printFooter }}
        </div>
    </div>

</body>
</html>


