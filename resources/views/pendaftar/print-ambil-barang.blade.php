@php
    $sys = \App\Models\SettingSystem::instance()->toSettingsArray();
    $schoolName    = $sys['school_name']       ?: 'SMK';
    $schoolAddress = $sys['school_address']    ?: '';
    $schoolCity    = $sys['school_city']       ?: '';
    $schoolPhone   = $sys['school_phone']      ?: ($sys['school_contact'] ?: '');
    $schoolEmail   = $sys['school_email']      ?: '';
    $printFooter   = $sys['print_footer_text'] ?: 'Dokumen ini dicetak otomatis oleh sistem SPMB (Sistem Penerimaan Murid Baru).';
    $logoUrl       = !empty($sys['school_logo']) ? asset('storage/' . $sys['school_logo']) : null;
    $docHeaderText = $sys['document_header_text'] ?: '';
    $docCity       = $sys['document_city'] ?: $schoolCity;
    $docSignName   = $sys['document_sign_name'] ?: '';
    $docSignTitle  = $sys['document_sign_title'] ?: 'Petugas Logistik';
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bukti Ambil Barang - {{ $pendaftar->no_registrasi }}</title>
    @include('partials.favicon')
    @include('partials.theme-vars')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', 'Arial', sans-serif; background: #f1f5f9; padding: 20px; color: #1e293b; font-size: 12px; }

        @page { size: F4 portrait; margin: 8mm 12mm; }
        @media print {
            body { padding: 0; background: white; }
            .no-print { display: none !important; }
            .page {
                box-shadow: none;
                border: none;
                border-radius: 0;
                padding: 0;
                max-width: 100%;
                width: 100%;
            }
        }

        .no-print { text-align: center; margin-bottom: 20px; display: flex; gap: 10px; justify-content: center; flex-wrap: wrap; }
        .btn { font-family: 'Inter', sans-serif; border: none; padding: 10px 24px; border-radius: 10px; cursor: pointer; font-size: 13px; font-weight: 700; transition: all 0.25s; display: inline-flex; align-items: center; gap: 8px; text-decoration: none; }
        .btn-print { background: linear-gradient(135deg, var(--theme-primary, #059669), var(--theme-secondary, #10b981)); color: white; }
        .btn-back { background: white; color: var(--theme-primary, #059669); border: 2px solid var(--theme-primary, #059669) !important; }

        .page { width: 100%; max-width: 185mm; overflow: hidden; background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 12px 16px; margin: 0 auto; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }

        .doc-header { display: flex; align-items: center; gap: 10px; border-bottom: 2px solid #334155; padding-bottom: 7px; margin-bottom: 8px; }
        .doc-header .school-info { flex: 1; }
        .doc-header .school-name { font-size: 13px; font-weight: 800; color: #1e293b; }
        .doc-header .school-addr { font-size: 8px; color: #64748b; margin-top: 2px; line-height: 1.4; }
        .doc-header .doc-id { text-align: right; font-size: 8px; color: #94a3b8; }

        .doc-title { text-align: center; font-size: 13px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; color: #1e293b; margin: 10px 0; padding: 8px; border: 2px solid var(--theme-primary, #059669); border-radius: 6px; background: #f0fdf4; }

        .reg-box { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin: 10px 0; padding: 10px 14px; background: #ecfdf5; border: 1.5px solid var(--theme-primary, #059669); border-radius: 8px; }
        .reg-box .lbl { font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #64748b; }
        .reg-box .num { font-size: 18px; font-weight: 800; color: var(--theme-primary, #059669); font-family: 'Courier New', monospace; }

        .section { margin-bottom: 8px; }
        .section-head { font-size: 9px; font-weight: 700; background: #f1f5f9; padding: 4px 0 4px 8px; border-left: 3px solid var(--theme-primary, #059669); border-radius: 0 4px 4px 0; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.4px; color: #475569; }

        .info-table { width: 100%; border-collapse: collapse; }
        .info-table td { padding: 3px 0; vertical-align: top; }
        .info-table .lbl { width: 140px; font-weight: 600; color: #475569; font-size: 11px; }
        .info-table .sep { width: 14px; text-align: center; color: #94a3b8; }
        .info-table .val { color: #1e293b; font-size: 11px; border-bottom: 1px solid #e2e8f0; }

        .items-table { width: 100%; border-collapse: collapse; margin-top: 6px; }
        .items-table th { background: #334155; color: white; padding: 8px 10px; text-align: left; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.4px; }
        .items-table th:first-child { border-radius: 4px 0 0 0; }
        .items-table th:last-child { border-radius: 0 4px 0 0; }
        .items-table td { padding: 5px 8px; border: 1px solid #e2e8f0; font-size: 10px; }
        .items-table tr:nth-child(even) { background: #f8fafc; }
        .items-table .center { text-align: center; }

        .badge { display: inline-block; padding: 2px 10px; border-radius: 50px; font-size: 10px; font-weight: 700; }
        .badge-green { background: #d1fae5; color: #065f46; }
        .badge-red { background: #fee2e2; color: #991b1b; }
        .badge-yellow { background: #fef3c7; color: #92400e; }

        .cbox { width: 14px; height: 14px; border: 2px solid #1e293b; border-radius: 3px; display: inline-flex; align-items: center; justify-content: center; font-size: 9px; font-weight: 800; }
        .cbox.checked { background: #059669; border-color: #059669; color: white; }

        .notes-box { background: #fffbeb; border: 1px solid #fde68a; border-radius: 6px; padding: 10px 14px; margin: 12px 0; font-size: 10px; line-height: 1.8; color: #78350f; }
        .notes-box .title { font-weight: 700; font-size: 11px; color: #92400e; margin-bottom: 5px; }
        .notes-box ol { margin: 0; padding-left: 16px; }

        .sig-grid { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; margin-top: 20px; font-size: 10px; }
        .sig-box { text-align: center; }
        .sig-line { border-top: 1px solid #1e293b; margin-top: 44px; padding-top: 4px; font-weight: 600; }
        .sig-role { font-size: 9px; color: #64748b; }
        .stamp-area { border: 2px dashed #cbd5e1; border-radius: 6px; width: 80px; height: 80px; margin: 8px auto; display: flex; align-items: center; justify-content: center; font-size: 9px; color: #94a3b8; text-align: center; }

        .doc-footer { text-align: center; margin-top: 14px; font-size: 9px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 8px; line-height: 1.6; }
    </style>
</head>
<body>
    <div class="no-print">
        <button class="btn btn-print" onclick="window.print()">Cetak Bukti Ambil Barang</button>
        <a href="{{ route('pendaftar.index') }}" class="btn btn-back">Kembali</a>
    </div>

    <div class="page">
        <div class="doc-header">
            <div>
                @if($logoUrl)
                    <img src="{{ $logoUrl }}" alt="Logo {{ $schoolName }}" style="width:44px;height:44px;object-fit:contain;">
                @else
                    <span style="font-size:30px;">??</span>
                @endif
            </div>
            <div class="school-info">
                @if($docHeaderText)
                    <div style="font-size:9px;font-weight:800;letter-spacing:.08em;text-transform:uppercase;">{{ $docHeaderText }}</div>
                @endif
                <div class="school-name">{{ $schoolName }}</div>
                <div class="school-addr">{{ $schoolAddress }}{{ $schoolCity ? ', '.$schoolCity : '' }} � Telp: {{ $schoolPhone }}{{ $schoolEmail ? ' � '.$schoolEmail : '' }}</div>
            </div>
            <div class="doc-id">
                No: BAB-{{ $pendaftar->no_registrasi }}<br>
                {{ now()->format('d/m/Y') }}
            </div>
        </div>

        <div style="display:flex;align-items:center;justify-content:space-between;gap:10px;margin:10px 0;padding:10px 14px;border:2px solid var(--theme-primary, #059669);border-radius:8px;background:#f0fdf4;">
            <div style="font-size:12px;font-weight:800;text-transform:uppercase;letter-spacing:0.5px;color:#1e293b;">Bukti Pengambilan Barang / Perlengkapan</div>
            <div style="text-align:center;">
                <div style="font-size:8px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:#64748b;">Nomor Registrasi</div>
                <div style="font-size:16px;font-weight:800;color:var(--theme-primary, #059669);font-family:'Courier New',monospace;">{{ $pendaftar->no_registrasi }}</div>
            </div>
            @if (class_exists('SimpleSoftwareIO\QrCode\Facades\QrCode'))
                <div>{!! QrCode::size(50)->generate($pendaftar->no_registrasi) !!}</div>
            @endif
        </div>

        <!-- Data Penerima -->
        <div class="section">
            <div class="section-head">Data Penerima</div>
            <table class="info-table">
                <tr><td class="lbl">Nama Lengkap</td><td class="sep">:</td><td class="val" style="font-weight:700;">{{ $pendaftar->nama_lengkap }}</td></tr>
                <tr><td class="lbl">NISN</td><td class="sep">:</td><td class="val">{{ $pendaftar->nisn }}</td></tr>
                <tr><td class="lbl">Jurusan</td><td class="sep">:</td><td class="val">{{ $pendaftar->masterJurusan?->kode ?? $pendaftar->jurusan }} - {{ $pendaftar->masterJurusan?->nama ?? '' }}</td></tr>
                <tr><td class="lbl">Gelombang</td><td class="sep">:</td><td class="val">Gelombang {{ $pendaftar->gelombang }}</td></tr>
                <tr>
                    <td class="lbl">Status Daftar Ulang</td>
                    <td class="sep">:</td>
                    <td class="val">
                        <span class="badge {{ $pendaftar->logistik->status_bayar === 'Lunas' ? 'badge-green' : 'badge-red' }}">
                            {{ $pendaftar->logistik->status_bayar === 'Lunas' ? 'Sudah Daftar Ulang' : 'Belum Daftar Ulang' }}
                        </span>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Daftar Barang -->
        <div class="section">
            <div class="section-head">Daftar Barang yang Diserahkan</div>
            <table class="items-table">
                <thead>
                    <tr>
                        <th style="width:36px;">No</th>
                        <th>Nama Barang</th>
                        <th style="width:70px;">Ukuran</th>
                        <th style="width:60px;">Jumlah</th>
                        <th style="width:80px;">Status</th>
                        <th style="width:70px;">Diterima</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="center">1</td>
                        <td>Kain Seragam Sekolah</td>
                        <td class="center">-</td>
                        <td class="center">1 set</td>
                        <td class="center">
                            <span class="badge {{ $pendaftar->logistik->status_kain === 'Sudah' ? 'badge-green' : 'badge-red' }}">
                                {{ $pendaftar->logistik->status_kain }}
                            </span>
                        </td>
                        <td class="center">
                            <span class="cbox {{ $pendaftar->logistik->status_kain === 'Sudah' ? 'checked' : '' }}">
                                {{ $pendaftar->logistik->status_kain === 'Sudah' ? 'v' : '' }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        @php $sk = $pendaftar->logistik->status_kaos; @endphp
                        <td class="center">2</td>
                        <td>Kaos Olahraga</td>
                        <td class="center">{{ $pendaftar->logistik->ukuran_kaos ?? '-' }}</td>
                        <td class="center">1 pcs</td>
                        <td class="center">
                            <span class="badge {{ $sk === 'Sudah' ? 'badge-green' : ($sk === 'Proses' ? 'badge-yellow' : 'badge-red') }}">
                                {{ $sk }}
                            </span>
                        </td>
                        <td class="center">
                            <span class="cbox {{ $sk === 'Sudah' ? 'checked' : '' }}">
                                {{ $sk === 'Sudah' ? 'v' : '' }}
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Tanda Tangan -->
        <div style="margin-top:8px;border:1px solid #e2e8f0;border-radius:6px;padding:8px 14px;">
            <div style="display:flex;justify-content:space-between;align-items:flex-end;font-size:10px;gap:20px;">
                <div style="text-align:center;flex:1;">
                    <div style="margin-bottom:50px;">Penerima,</div>
                    <div style="font-weight:600;">{{ $pendaftar->nama_lengkap }}</div>
                </div>
                <div style="text-align:center;flex:1;">
                    <div>{{ $docCity }}, {{ now()->format('d F Y') }}</div>
                    <div style="margin-bottom:50px;margin-top:3px;">{{ $docSignTitle }},</div>
                    <div style="display:inline-block;width:150px;border-bottom:1px solid #1e293b;"></div>
                </div>
            </div>
        </div>

        <div class="doc-footer">
            <p>Dicetak: {{ now()->format('d-m-Y H:i') }} WIB &nbsp;|&nbsp; {{ $schoolName }} &nbsp;|&nbsp; {{ $printFooter }}</p>
        </div>
    </div>

    {{-- ===== SALINAN ARSIP SEKOLAH ===== --}}
    <div class="page" style="margin-top:8px;border-top:2px dashed #94a3b8;">
        <div class="doc-header">
            <div>
                @if($logoUrl)
                    <img src="{{ $logoUrl }}" alt="Logo {{ $schoolName }}" style="width:44px;height:44px;object-fit:contain;">
                @else
                    <span style="font-size:30px;">??</span>
                @endif
            </div>
            <div class="school-info">
                @if($docHeaderText)
                    <div style="font-size:9px;font-weight:800;letter-spacing:.08em;text-transform:uppercase;">{{ $docHeaderText }}</div>
                @endif
                <div class="school-name">{{ $schoolName }}</div>
                <div class="school-addr">{{ $schoolAddress }}{{ $schoolCity ? ', '.$schoolCity : '' }} � Telp: {{ $schoolPhone }}{{ $schoolEmail ? ' � '.$schoolEmail : '' }}</div>
            </div>
            <div class="doc-id">
                No: BAB-{{ $pendaftar->no_registrasi }}<br>
                {{ now()->format('d/m/Y') }}
            </div>
        </div>

        <div style="display:flex;align-items:center;justify-content:space-between;gap:10px;margin:10px 0;padding:10px 14px;border:2px solid var(--theme-primary, #059669);border-radius:8px;background:#f0fdf4;">
            <div style="font-size:12px;font-weight:800;text-transform:uppercase;letter-spacing:0.5px;color:#1e293b;">Bukti Pengambilan Barang / Perlengkapan <span style="font-size:9px;font-weight:600;text-transform:none;letter-spacing:0;">(Lembar Arsip Sekolah)</span></div>
            <div style="text-align:center;">
                <div style="font-size:8px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:#64748b;">Nomor Registrasi</div>
                <div style="font-size:16px;font-weight:800;color:var(--theme-primary, #059669);font-family:'Courier New',monospace;">{{ $pendaftar->no_registrasi }}</div>
            </div>
            @if (class_exists('SimpleSoftwareIO\QrCode\Facades\QrCode'))
                <div>{!! QrCode::size(50)->generate($pendaftar->no_registrasi) !!}</div>
            @endif
        </div>

        <div class="section">
            <div class="section-head">Data Penerima</div>
            <table class="info-table">
                <tr><td class="lbl">Nama Lengkap</td><td class="sep">:</td><td class="val" style="font-weight:700;">{{ $pendaftar->nama_lengkap }}</td></tr>
                <tr><td class="lbl">NISN</td><td class="sep">:</td><td class="val">{{ $pendaftar->nisn }}</td></tr>
                <tr><td class="lbl">Jurusan</td><td class="sep">:</td><td class="val">{{ $pendaftar->masterJurusan?->kode ?? $pendaftar->jurusan }} - {{ $pendaftar->masterJurusan?->nama ?? '' }}</td></tr>
                <tr><td class="lbl">Gelombang</td><td class="sep">:</td><td class="val">Gelombang {{ $pendaftar->gelombang }}</td></tr>
                <tr>
                    <td class="lbl">Status Daftar Ulang</td>
                    <td class="sep">:</td>
                    <td class="val">
                        <span class="badge {{ $pendaftar->logistik->status_bayar === 'Lunas' ? 'badge-green' : 'badge-red' }}">
                            {{ $pendaftar->logistik->status_bayar === 'Lunas' ? 'Sudah Daftar Ulang' : 'Belum Daftar Ulang' }}
                        </span>
                    </td>
                </tr>
            </table>
        </div>

        <div class="section">
            <div class="section-head">Daftar Barang yang Diserahkan</div>
            <table class="items-table">
                <thead>
                    <tr>
                        <th style="width:36px;">No</th>
                        <th>Nama Barang</th>
                        <th style="width:70px;">Ukuran</th>
                        <th style="width:60px;">Jumlah</th>
                        <th style="width:80px;">Status</th>
                        <th style="width:70px;">Diterima</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="center">1</td>
                        <td>Kain Seragam Sekolah</td>
                        <td class="center">-</td>
                        <td class="center">1 set</td>
                        <td class="center">
                            <span class="badge {{ $pendaftar->logistik->status_kain === 'Sudah' ? 'badge-green' : 'badge-red' }}">
                                {{ $pendaftar->logistik->status_kain }}
                            </span>
                        </td>
                        <td class="center">
                            <span class="cbox {{ $pendaftar->logistik->status_kain === 'Sudah' ? 'checked' : '' }}">
                                {{ $pendaftar->logistik->status_kain === 'Sudah' ? 'v' : '' }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        @php $sk2 = $pendaftar->logistik->status_kaos; @endphp
                        <td class="center">2</td>
                        <td>Kaos Olahraga</td>
                        <td class="center">{{ $pendaftar->logistik->ukuran_kaos ?? '-' }}</td>
                        <td class="center">1 pcs</td>
                        <td class="center">
                            <span class="badge {{ $sk2 === 'Sudah' ? 'badge-green' : ($sk2 === 'Proses' ? 'badge-yellow' : 'badge-red') }}">
                                {{ $sk2 }}
                            </span>
                        </td>
                        <td class="center">
                            <span class="cbox {{ $sk2 === 'Sudah' ? 'checked' : '' }}">
                                {{ $sk2 === 'Sudah' ? 'v' : '' }}
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div style="margin-top:8px;border:1px solid #e2e8f0;border-radius:6px;padding:8px 14px;">
            <div style="display:flex;justify-content:space-between;align-items:flex-end;font-size:10px;gap:20px;">
                <div style="text-align:center;flex:1;">
                    <div style="margin-bottom:50px;">Penerima,</div>
                    <div style="font-weight:600;">{{ $pendaftar->nama_lengkap }}</div>
                </div>
                <div style="text-align:center;flex:1;">
                    <div>{{ $docCity }}, {{ now()->format('d F Y') }}</div>
                    <div style="margin-bottom:50px;margin-top:3px;">{{ $docSignTitle }},</div>
                    <div style="display:inline-block;width:150px;border-bottom:1px solid #1e293b;"></div>
                </div>
            </div>
        </div>

        <div class="doc-footer">
            <p>Dicetak: {{ now()->format('d-m-Y H:i') }} WIB &nbsp;|&nbsp; {{ $schoolName }} &nbsp;|&nbsp; {{ $printFooter }}</p>
        </div>
    </div>
</body>
</html>
