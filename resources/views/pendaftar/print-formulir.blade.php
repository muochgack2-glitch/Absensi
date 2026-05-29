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
    $docCity = $sys['document_city'] ?: $schoolCity;
    $docSignName = $sys['document_sign_name'] ?: '';
    $docSignTitle = $sys['document_sign_title'] ?: '';
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulir Lengkap - {{ $pendaftar->no_registrasi }}</title>
    @include('partials.favicon')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', 'Arial', sans-serif; background: #f1f5f9; padding: 20px; color: #1e293b; font-size: 12px; overflow-x: hidden; }

        @page {
            size: 215mm 330mm;
            margin: 10mm 12mm 10mm 12mm;
        }

        @media print {
            html { zoom: 1; }
            body { padding: 0; background: white; font-size: 12px; }
            .no-print { display: none !important; }
            .page {
                box-shadow: none;
                border: none;
                border-radius: 0;
                padding: 0;
                max-width: 100%;
                width: 100%;
                overflow: visible;
            }
            .section { margin-bottom: 14px; }
            .field-table td { padding: 5px 0; }
        }

        .no-print { text-align: center; margin-bottom: 24px; display: flex; gap: 10px; justify-content: center; flex-wrap: wrap; }
        .btn { font-family: 'Inter', sans-serif; border: none; padding: 12px 28px; border-radius: 12px; cursor: pointer; font-size: 13px; font-weight: 700; transition: all 0.3s; display: inline-flex; align-items: center; gap: 8px; text-decoration: none; }
        .btn:hover { transform: translateY(-2px); }
        .btn-print { background: linear-gradient(135deg, #6366f1, #a855f7); color: white; }
        .btn-print:hover { box-shadow: 0 8px 25px rgba(99,102,241,0.3); }
        .btn-back { background: white; color: #6366f1; border: 2px solid #6366f1 !important; }
        .btn-back:hover { background: #6366f1; color: white; }

        .page { width: 215mm; max-width: 215mm; background: white; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px 22px; margin: 0 auto; box-shadow: 0 4px 20px rgba(0,0,0,0.08); overflow-x: hidden; }

        @media screen and (max-width: 900px) {
            .page { width: 100%; max-width: 100%; padding: 16px; }
        }

        /* Header */
        .doc-header { display: flex; align-items: center; gap: 12px; border-bottom: 3px double #1e293b; padding-bottom: 10px; margin-bottom: 12px; }
        .doc-header .logo { font-size: 32px; }
        .doc-header .school-info { flex: 1; }
        .doc-header .school-name { font-size: 14px; font-weight: 800; color: #1e293b; }
        .doc-header .school-addr { font-size: 9px; color: #64748b; margin-top: 2px; line-height: 1.5; }
        .doc-header .doc-id { text-align: right; font-size: 9px; color: #94a3b8; }

        .doc-title { text-align: center; font-size: 13px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; color: #1e293b; margin: 10px 0; padding: 8px; border: 2px solid #e2e8f0; border-radius: 6px; background: #f8fafc; }

        /* Photo box */
        .photo-section { float: right; margin: 0 0 12px 16px; }
        .photo-box { width: 80px; height: 105px; border: 2px solid #1e293b; display: flex; align-items: center; justify-content: center; font-size: 8px; color: #94a3b8; text-align: center; line-height: 1.4; padding: 6px; }

        /* Sections */
        .section { margin-bottom: 14px; clear: both; }
        .section-head { font-size: 11px; font-weight: 700; background: #f1f5f9; padding: 5px 10px; border-left: 3px solid #6366f1; border-radius: 0 4px 4px 0; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px; color: #475569; }

        /* Form fields */
        .field-table { width: 100%; border-collapse: collapse; }
        .field-table td { padding: 5px 0; vertical-align: top; }
        .field-table .lbl { width: 155px; font-weight: 600; color: #475569; font-size: 12px; }
        .field-table .sep { width: 14px; text-align: center; color: #94a3b8; }
        .field-table .val { color: #1e293b; font-size: 12px; }
        .field-table .val-line { border-bottom: 1px solid #1e293b; min-height: 16px; padding-bottom: 1px; }
        .field-table .val-empty { border-bottom: 1px dotted #cbd5e1; min-height: 16px; padding-bottom: 1px; color: #94a3b8; font-style: italic; }

        /* Full-width field */
        .field-full { margin-bottom: 8px; }
        .field-full .lbl { font-weight: 600; color: #475569; font-size: 12px; margin-bottom: 3px; }
        .field-full .val-box { border: 1px solid #1e293b; border-radius: 3px; min-height: 36px; padding: 5px 7px; font-size: 12px; line-height: 1.5; }

        /* Checklist */
        .checklist { margin-top: 6px; }
        .check-row { display: flex; align-items: center; gap: 7px; margin-bottom: 4px; font-size: 10px; }
        .check-box { width: 12px; height: 12px; border: 2px solid #1e293b; border-radius: 2px; display: inline-flex; align-items: center; justify-content: center; font-size: 8px; font-weight: 800; flex-shrink: 0; }
        .check-box.checked { background: #6366f1; border-color: #6366f1; color: white; }

        /* Status grid */
        .status-grid { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 7px; margin-top: 7px; }
        .status-item { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 5px; padding: 7px; text-align: center; }
        .status-item .s-label { font-size: 8px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b; margin-bottom: 3px; }
        .status-item .s-value { font-size: 10px; font-weight: 700; }
        .s-green { color: #059669; }
        .s-red { color: #dc2626; }
        .s-yellow { color: #d97706; }

        /* Signatures */
        .sig-grid { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; margin-top: 20px; font-size: 9px; }
        .sig-box { text-align: center; }
        .sig-line { border-top: 1px solid #1e293b; margin-top: 45px; padding-top: 4px; font-weight: 600; }
        .sig-role { font-size: 8px; color: #64748b; }

        .doc-footer { text-align: center; margin-top: 16px; font-size: 8px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 8px; line-height: 1.6; }
    </style>
</head>
<body>
    <div class="no-print">
        <button class="btn btn-print" onclick="window.print()">Cetak Formulir Lengkap</button>
        <a href="{{ route('pendaftar.index') }}" class="btn btn-back">Kembali</a>
    </div>

    <div class="page">
        <div class="doc-header">
            <div class="logo">
                @if($logoUrl)
                    <img src="{{ $logoUrl }}" alt="Logo {{ $schoolName }}" style="width:52px;height:52px;object-fit:contain;">
                @else
                    🎓
                @endif
            </div>
            <div class="school-info">
                @if($docHeaderText)
                    <div style="font-size:10px;font-weight:800;letter-spacing:.08em;text-transform:uppercase;">{{ $docHeaderText }}</div>
                @endif
                <div class="school-name">{{ $schoolName }}</div>
                <div class="school-addr">{{ $schoolAddress }}, {{ $schoolCity }}<br>Telp: {{ $schoolPhone }} | Email: {{ $schoolEmail }}</div>
            </div>
            <div class="doc-id">
                No: FRM-{{ $pendaftar->no_registrasi }}<br>
                {{ now()->format('d/m/Y') }}
            </div>
        </div>

        <div class="doc-title">Formulir Pendaftaran Penerimaan Murid Baru<br><small style="font-size:10px;font-weight:600;letter-spacing:0;">Tahun Pelajaran {{ $sys['academic_year'] ?? date('Y').'/'.date('Y',strtotime('+1 year')) }}</small></div>

        <!-- Info Registrasi: 3 kotak berdampingan -->
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:8px;margin-bottom:10px;">
            <div style="border:1px solid #e2e8f0;border-radius:6px;padding:12px;text-align:center;">
                <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#64748b;margin-bottom:6px;">No. Registrasi</div>
                <div style="font-size:13px;font-weight:700;color:#6366f1;font-family:'Courier New',monospace;">{{ $pendaftar->no_registrasi }}</div>
            </div>
            <div style="border:1px solid #e2e8f0;border-radius:6px;padding:12px;text-align:center;">
                <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#64748b;margin-bottom:6px;">Gelombang</div>
                <div style="font-size:13px;font-weight:700;color:#1e293b;">Gelombang {{ $pendaftar->gelombang }}</div>
            </div>
            <div style="border:1px solid #e2e8f0;border-radius:6px;padding:12px;text-align:center;">
                <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#64748b;margin-bottom:6px;">Tanggal Pendaftaran</div>
                <div style="font-size:13px;font-weight:700;color:#1e293b;">{{ ($pendaftar->tgl_daftar ?? $pendaftar->created_at ?? now())->format('d-m-Y') }}</div>
            </div>
        </div>

        <!-- Section A + Foto -->
        <div style="display:flex;gap:12px;align-items:flex-start;margin-bottom:5px;page-break-inside:avoid;">
            <div style="flex:1;">
                <div style="font-weight:800;font-size:12px;margin-bottom:5px;background:#f1f5f9;padding:4px 0;border-radius:4px;padding-left:0;">A. Registrasi Murid Baru</div>
                <table style="width:100%;border-collapse:collapse;font-size:12px;">
                    @php
                        $jurusans = \App\Models\Jurusan::where('aktif', true)->orderBy('kode')->get();
                        $jurusanLabel = $jurusans->map(fn($j) => $j->kode)->join(' / ') . ' *)';
                        $selectedJurusan = $pendaftar->masterJurusan?->kode ?? $pendaftar->jurusan ?? '-';
                    @endphp
                    <tr>
                        <td style="width:22px;vertical-align:top;padding:5px 0;">1</td>
                        <td style="width:160px;vertical-align:top;padding:5px 0;">Jurusan yang dipilih</td>
                        <td style="width:12px;padding:5px 0;">:</td>
                        <td style="padding:5px 0;border-bottom:1px solid #e2e8f0;font-weight:700;">{{ $pendaftar->masterJurusan?->kode ?? $pendaftar->jurusan ?? '-' }} - {{ $pendaftar->masterJurusan?->nama ?? '-' }}</td>
                    </tr>
                    <tr><td style="padding:5px 0;">2</td><td style="padding:5px 0;">Nama Lengkap</td><td style="padding:5px 0;">:</td><td style="padding:5px 0;border-bottom:1px solid #e2e8f0;">{{ $pendaftar->nama_lengkap }}</td></tr>
                    <tr><td style="padding:5px 0;">3</td><td style="padding:5px 0;">Jenis Kelamin</td><td style="padding:5px 0;">:</td><td style="padding:5px 0;border-bottom:1px solid #e2e8f0;">{!! $pendaftar->jenis_kelamin === 'L' ? 'Laki-laki' : ($pendaftar->jenis_kelamin === 'P' ? 'Perempuan' : '<span style="color:#94a3b8;font-style:italic;">Belum diisi</span>') !!}</td></tr>
                    <tr><td style="padding:5px 0;">4</td><td style="padding:5px 0;">Tempat, tanggal lahir</td><td style="padding:5px 0;">:</td><td style="padding:5px 0;border-bottom:1px solid #e2e8f0;">{!! trim(($pendaftar->tempat_lahir ?: '') . ($pendaftar->tanggal_lahir ? ', ' . $pendaftar->tanggal_lahir->format('d-m-Y') : '')) ?: '<span style="color:#94a3b8;font-style:italic;">Belum diisi</span>' !!}</td></tr>
                    <tr><td style="padding:5px 0;">5</td><td style="padding:5px 0;">Agama</td><td style="padding:5px 0;">:</td><td style="padding:5px 0;border-bottom:1px solid #e2e8f0;">{!! $pendaftar->agama ?: '<span style="color:#94a3b8;font-style:italic;">Belum diisi</span>' !!}</td></tr>
                    <tr><td style="padding:5px 0;">6</td><td colspan="3" style="padding:5px 0;border-bottom:1px solid #e2e8f0;">Nomer HP/WA yang bisa dihubungi : {!! $pendaftar->no_telepon ?: '<span style="color:#94a3b8;font-style:italic;">Belum diisi</span>' !!}</td></tr>
                    <tr><td style="padding:5px 0;vertical-align:top;">7</td><td colspan="3" style="padding:5px 0;font-weight:600;">Alamat</td></tr>
                    @foreach([['Jalan',$pendaftar->alamat_jalan],['Dukuh',$pendaftar->alamat_dukuh],['RT',$pendaftar->alamat_rt],['RW',$pendaftar->alamat_rw],['Kelurahan',$pendaftar->alamat_kelurahan],['Kecamatan',$pendaftar->alamat_kecamatan],['Kabupaten',$pendaftar->alamat_kabupaten],['Provinsi',$pendaftar->alamat_provinsi]] as [$lbl,$val])
                    <tr><td></td><td style="padding:4px 0;padding-left:14px;color:#475569;">{{ $lbl }}</td><td style="padding:4px 0;">:</td><td style="padding:4px 0;border-bottom:1px solid #e2e8f0;">{!! $val ?: '<span style="color:#94a3b8;font-style:italic;">Belum diisi</span>' !!}</td></tr>
                    @endforeach
                    <tr><td style="padding:5px 0;">8</td><td style="padding:5px 0;">Asal Sekolah</td><td style="padding:5px 0;">:</td><td style="padding:5px 0;border-bottom:1px solid #e2e8f0;">{{ $pendaftar->asal_sekolah }}</td></tr>
                    <tr><td style="padding:5px 0;">9</td><td style="padding:5px 0;">NISN</td><td style="padding:5px 0;">:</td><td style="padding:5px 0;border-bottom:1px solid #e2e8f0;">{{ $pendaftar->nisn }}</td></tr>
                    <tr><td style="padding:5px 0;">10</td><td colspan="3" style="padding:5px 0;border-bottom:1px solid #e2e8f0;">No. Kartu Indonesia Pintar (KIP) : {!! $pendaftar->no_kip ?: '<span style="color:#94a3b8;font-style:italic;">Belum diisi</span>' !!}</td></tr>
                </table>
            </div>
            <div style="flex-shrink:0;text-align:center;">
                <div class="photo-box" style="width:70px;height:90px;">Pas Foto<br>3x4 cm</div>
                @if (class_exists('SimpleSoftwareIO\QrCode\Facades\QrCode'))
                    <div style="margin-top:4px;">{!! QrCode::size(50)->generate($pendaftar->no_registrasi) !!}</div>
                    <div style="font-size:6px;color:#94a3b8;margin-top:1px;">{{ $pendaftar->no_registrasi }}</div>
                @endif
            </div>
        </div>

        <!-- Section B: Identitas Orang Tua -->
        <div style="margin-top:8px;">
            <div style="font-weight:800;font-size:12px;margin-bottom:6px;background:#f1f5f9;padding:5px 0;border-radius:4px;">B. Identitas Orang Tua Calon Murid Baru</div>
            <table style="width:100%;border-collapse:collapse;font-size:12px;">
                <tr><td style="width:18px;vertical-align:top;padding:4px 0;">1</td><td colspan="3" style="padding:4px 0;font-weight:700;">Data Ayah Kandung</td></tr>
                @foreach([['Nama Ayah Kandung',$pendaftar->nama_ayah],['Pekerjaan',$pendaftar->pekerjaan_ayah],['Alamat',$pendaftar->alamat_ayah]] as [$lbl,$val])
                <tr><td></td><td style="width:150px;padding:4px 0;padding-left:8px;color:#475569;">{{ $lbl }}</td><td style="width:10px;padding:4px 0;">:</td><td style="padding:4px 0;border-bottom:1px solid #e2e8f0;">{!! $val ?: '<span style="color:#94a3b8;font-style:italic;">Belum diisi</span>' !!}</td></tr>
                @endforeach
                <tr><td style="padding:4px 0;">2</td><td colspan="3" style="padding:4px 0;font-weight:700;">Data Ibu Kandung</td></tr>
                @foreach([['Nama Ibu Kandung',$pendaftar->nama_ibu],['Pekerjaan',$pendaftar->pekerjaan_ibu],['Alamat',$pendaftar->alamat_ibu]] as [$lbl,$val])
                <tr><td></td><td style="padding:4px 0;padding-left:8px;color:#475569;">{{ $lbl }}</td><td style="padding:4px 0;">:</td><td style="padding:4px 0;border-bottom:1px solid #e2e8f0;">{!! $val ?: '<span style="color:#94a3b8;font-style:italic;">Belum diisi</span>' !!}</td></tr>
                @endforeach
                <tr><td style="padding:4px 0;vertical-align:top;">3</td><td colspan="3" style="padding:4px 0;font-weight:700;">Data Wali <span style="font-weight:400;font-size:10px;">(diisi jika peserta didik ikut wali)</span></td></tr>
                @foreach([['Nama Wali',$pendaftar->nama_wali],['Pekerjaan',$pendaftar->pekerjaan_wali],['Alamat',$pendaftar->alamat_wali]] as [$lbl,$val])
                <tr><td></td><td style="padding:4px 0;padding-left:8px;color:#475569;">{{ $lbl }}</td><td style="padding:4px 0;">:</td><td style="padding:4px 0;border-bottom:1px solid #e2e8f0;">{!! $val ?: '<span style="color:#94a3b8;font-style:italic;">Belum diisi</span>' !!}</td></tr>
                @endforeach
            </table>
        </div>

        <!-- C. Pernyataan -->
        <div style="margin-top:8px;font-size:12px;line-height:1.7;color:#1e293b;">
            <div style="font-weight:800;font-size:12px;margin-bottom:6px;background:#f1f5f9;padding:5px 0;border-radius:4px;">C. Pernyataan</div>
            <div style="display:flex;gap:6px;align-items:flex-start;">
                <span style="font-weight:700;white-space:nowrap;">Pernyataan</span>
                <span>: Saya menyatakan dengan sesungguhnya bahwa isian data dalam formulir ini adalah benar. Apabila ternyata data tersebut tidak benar/palsu, maka saya bersedia menerima sanksi berupa <strong>Pembatalan</strong> sebagai <strong>Calon Murid Baru</strong> {{ $schoolName }}.</span>
            </div>
        </div>

        <div style="margin-top:9px;border:1px solid #e2e8f0;border-radius:5px;padding:10px 20px;">
            <div style="display:flex;justify-content:space-between;align-items:flex-end;font-size:9.5px;gap:18px;">
                <div style="text-align:center;flex:1;">
                    <div style="margin-bottom:50px;">Petugas,</div>
                    <div style="display:inline-block;width:140px;border-bottom:1px solid #1e293b;"></div>
                </div>
                <div style="text-align:center;flex:1;">
                    <div>{{ $docCity }}, {{ now()->format('d F Y') }}</div>
                    <div style="margin-bottom:50px;margin-top:2px;">Calon Murid Baru,</div>
                    <div style="font-weight:600;">{{ $pendaftar->nama_lengkap }}</div>
                </div>
            </div>
        </div>

        <div class="doc-footer">
            <p>Dicetak pada: {{ now()->format('d-m-Y H:i') }} WIB &nbsp;|&nbsp; {{ $schoolName }} &nbsp;|&nbsp; Telp: {{ $schoolPhone }}</p>
            <p><em>{{ $printFooter }}</em></p>
        </div>
    </div>
</body>
</html>


