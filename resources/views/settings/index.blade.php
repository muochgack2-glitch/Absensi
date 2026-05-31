@extends('layouts.admin')

@section('title', 'Pengaturan Sistem - SPMB (Sistem Penerimaan Murid Baru)')

@push('styles')
<style>
        .settings-page-title {
            font-size: 30px;
            font-weight: 800;
            letter-spacing: -0.02em;
            margin: 0;
        }

        .settings-page-subtitle {
            font-size: 14px;
            color: var(--text-secondary);
            margin-top: 4px;
        }

        .section-title {
            font-size: 15px;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .section-title i {
            color: var(--primary);
        }

        .settings-tabs {
            border-bottom: 1px solid var(--border-light);
            gap: 8px;
        }

        .settings-tabs .nav-link {
            border: 1px solid transparent;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
            color: var(--text-secondary);
            font-weight: 600;
            padding: 10px 14px;
            transition: all 0.2s ease;
        }

        .settings-tabs .nav-link:hover {
            border-color: var(--border-light);
            color: var(--text-primary);
            background: var(--bg-secondary);
        }

        .settings-tabs .nav-link.active {
            color: var(--text-primary);
            background: var(--bg-primary);
            border-color: var(--border-light) var(--border-light) var(--bg-primary);
        }

        .settings-tab-pane {
            border: 1px solid var(--border-light);
            border-top: 0;
            border-radius: 0 0 12px 12px;
            padding: 24px;
            background: var(--bg-primary);
        }

        .settings-form .form-control-color {
            min-height: 42px;
            width: 100%;
            padding: 5px;
            cursor: pointer;
        }

        .settings-actions {
            border-top: 1px solid var(--border-light);
            margin-top: 24px;
            padding-top: 16px;
        }

        .jurusan-add-card {
            background: linear-gradient(135deg, var(--bg-secondary) 0%, var(--bg-tertiary) 100%);
            border: 2px dashed var(--border-medium);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .jurusan-add-card h6 {
            color: var(--text-primary);
            font-weight: 700;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .jurusan-table-wrapper {
            border: 1px solid var(--border-light);
            border-radius: 12px;
            overflow: hidden;
        }

        .jurusan-table-wrapper table {
            margin-bottom: 0;
        }

        .color-preview {
            display: inline-block;
            width: 32px;
            height: 32px;
            border-radius: 6px;
            border: 2px solid var(--border-light);
            vertical-align: middle;
            margin-left: 8px;
        }

        .file-preview-box {
            margin-top: 12px;
            padding: 12px;
            background: var(--bg-secondary);
            border: 1px solid var(--border-light);
            border-radius: 8px;
        }

        .file-preview-box img {
            max-width: 100%;
            border-radius: 6px;
        }

        @media (max-width: 768px) {
            .settings-page-title { font-size: 24px; }
            .settings-tabs .nav-link { padding: 8px 10px; font-size: 13px; }
            .settings-tab-pane { padding: 14px; }
        }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="settings-page-title">⚙️ Pengaturan Sistem</h1>
        <p class="settings-page-subtitle mb-0">Atur konfigurasi inti SPMB (Sistem Penerimaan Murid Baru) yang dipakai seluruh modul.</p>
    </div>
</div>

<x-section-card title="Konfigurasi Sistem" icon="fas fa-cog">
                <form id="jurusan-create-form" action="{{ route('settings.jurusan.store') }}" method="POST" class="d-none">
                    @csrf
                </form>
                @foreach(($jurusans ?? collect()) as $jurusan)
                    <form id="jurusan-edit-{{ $jurusan->id }}" action="{{ route('settings.jurusan.update', $jurusan) }}" method="POST" class="d-none">
                        @csrf
                        @method('PUT')
                    </form>
                    <form id="jurusan-delete-{{ $jurusan->id }}" action="{{ route('settings.jurusan.destroy', $jurusan) }}" method="POST" class="d-none" onsubmit="return confirm('Hapus jurusan {{ $jurusan->kode }}?');">
                        @csrf
                        @method('DELETE')
                    </form>
                @endforeach

                <form id="settings-form" class="settings-form" action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <ul class="nav nav-tabs settings-tabs" id="settingsTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="tab-profile" data-bs-toggle="tab" data-bs-target="#pane-profile" type="button" role="tab" aria-controls="pane-profile" aria-selected="true">
                                <i class="fas fa-school me-2"></i>Profil
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="tab-pendaftaran" data-bs-toggle="tab" data-bs-target="#pane-pendaftaran" type="button" role="tab" aria-controls="pane-pendaftaran" aria-selected="false">
                                <i class="fas fa-sliders me-2"></i>Pendaftaran
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="tab-branding" data-bs-toggle="tab" data-bs-target="#pane-branding" type="button" role="tab" aria-controls="pane-branding" aria-selected="false">
                                <i class="fas fa-bullhorn me-2"></i>Branding
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="tab-document" data-bs-toggle="tab" data-bs-target="#pane-document" type="button" role="tab" aria-controls="pane-document" aria-selected="false">
                                <i class="fas fa-id-card me-2"></i>Dokumen
                            </button>
                        </li>

                    </ul>

                    <div class="tab-content settings-tab-pane" id="settingsTabContent">
                        <div class="tab-pane fade show active" id="pane-profile" role="tabpanel" aria-labelledby="tab-profile" tabindex="0">
                            <p class="section-title mb-3"><i class="fas fa-school"></i> Profil Sekolah</p>
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <x-form-group label="Nama Sekolah" name="school_name" required="true">
                                        <x-input name="school_name" type="text" value="{{ old('school_name', $settings['school_name']) }}" icon="fas fa-school" required />
                                    </x-form-group>
                                </div>
                                <div class="col-md-3">
                                    <x-form-group label="Tahun Ajaran" name="academic_year" required="true">
                                        <x-input name="academic_year" type="text" value="{{ old('academic_year', $settings['academic_year']) }}" placeholder="2026/2027" icon="fas fa-calendar" required />
                                    </x-form-group>
                                </div>
                                <div class="col-md-3">
                                    <x-form-group label="Status Pendaftaran" name="registration_status" required="true">
                                        <x-select name="registration_status" required>
                                            <option value="open" {{ old('registration_status', $settings['registration_status']) === 'open' ? 'selected' : '' }}>🟢 Buka</option>
                                            <option value="closed" {{ old('registration_status', $settings['registration_status']) === 'closed' ? 'selected' : '' }}>🔴 Tutup</option>
                                        </x-select>
                                    </x-form-group>
                                </div>
                                <div class="col-md-8">
                                    <x-form-group label="Alamat Sekolah" name="school_address">
                                        <x-input name="school_address" type="text" value="{{ old('school_address', $settings['school_address']) }}" icon="fas fa-map-marker-alt" />
                                    </x-form-group>
                                </div>
                                <div class="col-md-4">
                                    <x-form-group label="Kontak Sekolah" name="school_contact" help="WhatsApp/Telepon">
                                        <x-input name="school_contact" type="text" value="{{ old('school_contact', $settings['school_contact']) }}" placeholder="08xxx" icon="fas fa-phone" />
                                    </x-form-group>
                                </div>
                                <div class="col-md-4">
                                    <x-form-group label="Kota/Kabupaten" name="school_city">
                                        <x-input name="school_city" type="text" value="{{ old('school_city', $settings['school_city'] ?? '') }}" icon="fas fa-city" />
                                    </x-form-group>
                                </div>
                                <div class="col-md-4">
                                    <x-form-group label="Telepon Sekolah" name="school_phone">
                                        <x-input name="school_phone" type="text" value="{{ old('school_phone', $settings['school_phone'] ?? '') }}" icon="fas fa-phone-alt" />
                                    </x-form-group>
                                </div>
                                <div class="col-md-4">
                                    <x-form-group label="Email Sekolah" name="school_email">
                                        <x-input name="school_email" type="email" value="{{ old('school_email', $settings['school_email'] ?? '') }}" icon="fas fa-envelope" />
                                    </x-form-group>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="pane-pendaftaran" role="tabpanel" aria-labelledby="tab-pendaftaran" tabindex="0">
                            <p class="section-title mb-3"><i class="fas fa-sliders"></i> Konfigurasi Pendaftaran SPMB</p>
                            <div class="row g-3 mb-4">
                                <div class="col-md-4">
                                    <x-form-group label="Biaya Pendaftaran (Rp)" name="registration_fee" required="true">
                                        <x-input name="registration_fee" type="number" value="{{ old('registration_fee', $settings['registration_fee']) }}" icon="fas fa-money-bill-wave" required />
                                    </x-form-group>
                                </div>
                                <div class="col-md-4">
                                    <x-form-group label="Gelombang Aktif" name="active_wave" required="true">
                                        <x-input name="active_wave" type="text" value="{{ old('active_wave', $settings['active_wave']) }}" icon="fas fa-wave-square" required />
                                    </x-form-group>
                                </div>
                                <div class="col-md-4">
                                    <x-form-group label="Nama Kepala Sekolah" name="principal_name">
                                        <x-input name="principal_name" type="text" value="{{ old('principal_name', $settings['principal_name']) }}" icon="fas fa-user-tie" />
                                    </x-form-group>
                                </div>
                            </div>

                            <hr class="my-4">

                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                                <div>
                                    <p class="section-title mb-1"><i class="fas fa-graduation-cap"></i> Master Jurusan</p>
                                    <p class="text-muted mb-0" style="font-size: 13px;">Kelola kode, nama, kuota, dan status aktif jurusan langsung dari tab Pendaftaran.</p>
                                </div>
                            </div>

                            <div class="jurusan-add-card">
                                <h6><i class="fas fa-plus-circle"></i> Tambah Jurusan Baru</h6>
                                <input type="hidden" form="jurusan-create-form" name="aktif" value="1">
                                <div class="row g-3 align-items-end">
                                    <div class="col-md-3">
                                        <x-form-group label="Kode" name="kode" required="true">
                                            <x-input form="jurusan-create-form" name="kode" type="text" placeholder="MPLB" required />
                                        </x-form-group>
                                    </div>
                                    <div class="col-md-5">
                                        <x-form-group label="Nama Jurusan" name="nama" required="true">
                                            <x-input form="jurusan-create-form" name="nama" type="text" placeholder="Manajemen Perkantoran dan Layanan Bisnis" required />
                                        </x-form-group>
                                    </div>
                                    <div class="col-md-2">
                                        <x-form-group label="Kuota" name="kuota" required="true">
                                            <x-input form="jurusan-create-form" name="kuota" type="number" value="0" required />
                                        </x-form-group>
                                    </div>
                                    <div class="col-md-2 d-grid">
                                        <button type="submit" form="jurusan-create-form" class="btn btn-primary" style="height: 42px;"><i class="fas fa-plus me-1"></i>Tambah</button>
                                    </div>
                                </div>
                            </div>

                            <div class="jurusan-table-wrapper">
                                <table class="table align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 17%;">Kode</th>
                                            <th>Nama Jurusan</th>
                                            <th style="width: 16%;">Kuota</th>
                                            <th style="width: 14%;">Status</th>
                                            <th style="width: 22%;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse(($jurusans ?? collect()) as $jurusan)
                                            <tr>
                                                <td>
                                                    <input form="jurusan-edit-{{ $jurusan->id }}" name="kode" type="text" class="form-control" value="{{ old('kode', $jurusan->kode) }}" required>
                                                </td>
                                                <td>
                                                    <input form="jurusan-edit-{{ $jurusan->id }}" name="nama" type="text" class="form-control" value="{{ old('nama', $jurusan->nama) }}" required>
                                                </td>
                                                <td>
                                                    <input form="jurusan-edit-{{ $jurusan->id }}" name="kuota" type="number" min="0" class="form-control" value="{{ old('kuota', $jurusan->kuota) }}" required>
                                                </td>
                                                <td>
                                                    <input type="hidden" form="jurusan-edit-{{ $jurusan->id }}" name="aktif" value="0">
                                                    <div class="form-check form-switch mb-0">
                                                        <input form="jurusan-edit-{{ $jurusan->id }}" class="form-check-input" type="checkbox" name="aktif" value="1" id="jurusan_aktif_{{ $jurusan->id }}" {{ $jurusan->aktif ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="jurusan_aktif_{{ $jurusan->id }}">{{ $jurusan->aktif ? 'Aktif' : 'Nonaktif' }}</label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex gap-2 flex-wrap">
                                                        <button type="submit" form="jurusan-edit-{{ $jurusan->id }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-save me-1"></i>Simpan</button>
                                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                                                onclick="Modal.confirm('Hapus jurusan <strong>{{ $jurusan->nama }}</strong>?<br><small class=\'text-muted\'>Data jurusan akan dihapus permanen.</small>', function() {
                                                                    document.getElementById('jurusan-delete-{{ $jurusan->id }}').submit();
                                                                }, { type: 'danger', title: 'Hapus Jurusan', confirmText: 'Ya, Hapus', cancelText: 'Batal' })">
                                                            <i class="fas fa-trash me-1"></i>Hapus
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5">
                                                    <x-empty-state icon="fas fa-graduation-cap" message="Belum ada data jurusan" description="Tambahkan jurusan baru menggunakan form di atas" size="sm" />
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="pane-branding" role="tabpanel" aria-labelledby="tab-branding" tabindex="0">
                            <p class="section-title mb-3"><i class="fas fa-palette"></i> Branding & Tema</p>
                            
                            <div class="row g-3 mb-4">
                                <div class="col-12">
                                    <h6 class="fw-bold mb-3"><i class="fas fa-share-alt me-2"></i>Media Sosial</h6>
                                </div>
                                <div class="col-md-3">
                                    <x-form-group label="Website Sekolah" name="school_website">
                                        <x-input name="school_website" type="url" value="{{ old('school_website', $settings['school_website'] ?? '') }}" placeholder="https://..." icon="fas fa-globe" />
                                    </x-form-group>
                                </div>
                                <div class="col-md-3">
                                    <x-form-group label="Instagram URL" name="instagram_url">
                                        <x-input name="instagram_url" type="url" value="{{ old('instagram_url', $settings['instagram_url'] ?? '') }}" placeholder="https://instagram.com/..." icon="fab fa-instagram" />
                                    </x-form-group>
                                </div>
                                <div class="col-md-3">
                                    <x-form-group label="YouTube Sekolah" name="school_youtube">
                                        <x-input name="school_youtube" type="url" value="{{ old('school_youtube', $settings['school_youtube'] ?? '') }}" placeholder="https://youtube.com/..." icon="fab fa-youtube" />
                                    </x-form-group>
                                </div>
                                <div class="col-md-3">
                                    <x-form-group label="TikTok Sekolah" name="tiktok_url">
                                        <x-input name="tiktok_url" type="url" value="{{ old('tiktok_url', $settings['tiktok_url'] ?? '') }}" placeholder="https://tiktok.com/@..." icon="fab fa-tiktok" />
                                    </x-form-group>
                                </div>
                            </div>

                            <hr class="my-4">

                            @php
                                $themePresets = [
                                    'purple' => ['label' => 'Ungu', 'primary' => '#667eea', 'secondary' => '#764ba2'],
                                    'blue' => ['label' => 'Biru', 'primary' => '#0ea5e9', 'secondary' => '#0369a1'],
                                    'green' => ['label' => 'Hijau', 'primary' => '#10b981', 'secondary' => '#047857'],
                                    'orange' => ['label' => 'Orange', 'primary' => '#f97316', 'secondary' => '#c2410c'],
                                    'red' => ['label' => 'Merah', 'primary' => '#ef4444', 'secondary' => '#991b1b'],
                                    'slate' => ['label' => 'Slate', 'primary' => '#475569', 'secondary' => '#0f172a'],
                                ];
                                $activePreset = old('theme_preset', $settings['theme_preset'] ?? 'purple');
                                $activePresetColors = $themePresets[$activePreset] ?? $themePresets['purple'];
                                $activePrimary = old('theme_primary', $settings['theme_primary'] ?: $activePresetColors['primary']);
                                $activeSecondary = old('theme_secondary', $settings['theme_secondary'] ?: $activePresetColors['secondary']);
                            @endphp

                            <div class="row g-3 mb-4">
                                <div class="col-12">
                                    <h6 class="fw-bold mb-3"><i class="fas fa-palette me-2"></i>Warna Tema</h6>
                                </div>
                                <div class="col-md-4">
                                    <x-form-group label="Preset Warna Tema" name="theme_preset" help="Pilih preset lalu simpan">
                                        <x-select name="theme_preset">
                                            @foreach($themePresets as $value => $preset)
                                                <option value="{{ $value }}" data-primary="{{ $preset['primary'] }}" data-secondary="{{ $preset['secondary'] }}" {{ $activePreset === $value ? 'selected' : '' }}>{{ $preset['label'] }}</option>
                                            @endforeach
                                        </x-select>
                                    </x-form-group>
                                </div>
                                <div class="col-md-4">
                                    <x-form-group label="Warna Utama" name="theme_primary">
                                        <input id="theme_primary" name="theme_primary" type="color" class="form-control form-control-color" value="{{ $activePrimary }}" title="Pilih warna utama">
                                        <span class="color-preview" style="background-color: {{ $activePrimary }};"></span>
                                    </x-form-group>
                                </div>
                                <div class="col-md-4">
                                    <x-form-group label="Warna Kedua" name="theme_secondary">
                                        <input id="theme_secondary" name="theme_secondary" type="color" class="form-control form-control-color" value="{{ $activeSecondary }}" title="Pilih warna kedua">
                                        <span class="color-preview" style="background-color: {{ $activeSecondary }};"></span>
                                    </x-form-group>
                                </div>
                            </div>

                            <hr class="my-4">
                            <div class="row g-3 mb-4">
                                <div class="col-12">
                                    <h6 class="fw-bold mb-3"><i class="fas fa-image me-2"></i>Logo & Favicon</h6>
                                </div>
                                <div class="col-md-6">
                                    <x-form-group label="Logo Sekolah" name="school_logo" help="Format: PNG, JPG, JPEG, WEBP (Max: 2MB)">
                                        <input id="school_logo" name="school_logo" type="file" class="form-control" accept=".png,.jpg,.jpeg,.webp">
                                        @if(!empty($settings['school_logo']))
                                            <div class="file-preview-box">
                                                <small class="text-muted d-block mb-2">Logo tersimpan: {{ $settings['school_logo'] }}</small>
                                                <img src="{{ asset('storage/' . $settings['school_logo']) }}" alt="Preview logo sekolah" class="img-fluid" style="max-width: 220px;">
                                            </div>
                                        @endif
                                    </x-form-group>
                                </div>
                                <div class="col-md-6">
                                    <x-form-group label="Favicon" name="favicon" help="Rekomendasi: 32x32 atau 64x64 px">
                                        <input id="favicon" name="favicon" type="file" class="form-control" accept=".ico,.png,.jpg,.jpeg,.webp,.svg">
                                        @if(!empty($settings['favicon']))
                                            <div class="file-preview-box">
                                                <small class="text-muted d-block mb-2">Favicon tersimpan: {{ $settings['favicon'] }}</small>
                                                <div class="d-flex align-items-center gap-2">
                                                    <img src="{{ asset('storage/' . $settings['favicon']) }}" alt="Preview favicon" class="rounded border" style="width:48px;height:48px;object-fit:contain;">
                                                    <span class="text-muted small">Preview favicon</span>
                                                </div>
                                            </div>
                                        @endif
                                    </x-form-group>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="pane-document" role="tabpanel" aria-labelledby="tab-document" tabindex="0">
                            <p class="section-title mb-3"><i class="fas fa-file-alt"></i> Identitas Dokumen Cetak</p>
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <x-form-group label="Footer Dokumen" name="print_footer_text" help="Contoh: Dokumen ini sah tanpa tanda tangan basah">
                                        <x-input name="print_footer_text" type="text" value="{{ old('print_footer_text', $settings['print_footer_text'] ?? '') }}" icon="fas fa-align-center" />
                                    </x-form-group>
                                </div>

                                <div class="col-md-6">
                                    <x-form-group label="Header Dokumen (Kop)" name="document_header_text" help="Contoh: PANITIA SPMB TAHUN AJARAN ...">
                                        <x-input name="document_header_text" type="text" value="{{ old('document_header_text', $settings['document_header_text'] ?? '') }}" icon="fas fa-heading" />
                                    </x-form-group>
                                </div>
                                <div class="col-md-4">
                                    <x-form-group label="Kota Dokumen" name="document_city" help="Contoh: Blora">
                                        <x-input name="document_city" type="text" value="{{ old('document_city', $settings['document_city'] ?? ($settings['school_city'] ?? '')) }}" icon="fas fa-map-marker-alt" />
                                    </x-form-group>
                                </div>
                                <div class="col-md-4">
                                    <x-form-group label="Nama Penandatangan" name="document_sign_name">
                                        <x-input name="document_sign_name" type="text" value="{{ old('document_sign_name', $settings['document_sign_name'] ?? ($settings['principal_name'] ?? '')) }}" icon="fas fa-user" />
                                    </x-form-group>
                                </div>
                                <div class="col-md-4">
                                    <x-form-group label="Jabatan Penandatangan" name="document_sign_title" help="Contoh: Kepala Sekolah">
                                        <x-input name="document_sign_title" type="text" value="{{ old('document_sign_title', $settings['document_sign_title'] ?? 'Kepala Sekolah') }}" icon="fas fa-id-badge" />
                                    </x-form-group>
                                </div>
                            </div>
                        </div>


                    </div>

                    <div class="d-flex gap-2 settings-actions">
                        <button id="btn-save-settings" type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Simpan Pengaturan
                        </button>
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Kembali
                        </a>
                    </div>
                </form>
            </x-section-card>
@endsection

@push('scripts')
<script>
    // Show success/error modal if session exists
    @if (session('success'))
        document.addEventListener('DOMContentLoaded', function() {
            Modal.alert('{{ addslashes(session('success')) }}', 'Berhasil!', 'success');
        });
    @endif

    @if (session('error'))
        document.addEventListener('DOMContentLoaded', function() {
            Modal.alert('{{ addslashes(session('error')) }}', 'Gagal!', 'danger');
        });
    @endif

    (function () {
        var key = 'settings_active_tab';
        var el = document.getElementById('settingsTabs');
        if (!el || !window.bootstrap) return;

        // Check URL parameter first
        var urlParams = new URLSearchParams(window.location.search);
        var tabParam = urlParams.get('tab');
        
        if (tabParam) {
            // Map tab parameter to tab ID
            var tabMap = {
                'profil': '#pane-profile',
                'pendaftaran': '#pane-pendaftaran',
                'branding': '#pane-branding',
                'dokumen': '#pane-document'
            };
            
            var targetPane = tabMap[tabParam];
            if (targetPane) {
                var trigger = document.querySelector('[data-bs-target="' + targetPane + '"]');
                if (trigger) {
                    bootstrap.Tab.getOrCreateInstance(trigger).show();
                    // Save to localStorage
                    localStorage.setItem(key, targetPane);
                }
            }
        } else {
            // If no URL parameter, use saved tab from localStorage
            var saved = localStorage.getItem(key);
            if (saved) {
                var trigger = document.querySelector('[data-bs-target="' + saved + '"]');
                if (trigger) {
                    bootstrap.Tab.getOrCreateInstance(trigger).show();
                }
            }
        }

        el.addEventListener('shown.bs.tab', function (event) {
            var target = event.target.getAttribute('data-bs-target');
            if (target) localStorage.setItem(key, target);
        });

        var preset = document.getElementById('theme_preset');
        var primary = document.getElementById('theme_primary');
        var secondary = document.getElementById('theme_secondary');

        if (preset && primary && secondary) {
            preset.addEventListener('change', function () {
                var option = preset.options[preset.selectedIndex];
                primary.value = option.getAttribute('data-primary') || primary.value;
                secondary.value = option.getAttribute('data-secondary') || secondary.value;
            });
        }
    })();

    // Loading state for save button
    document.getElementById('settings-form').addEventListener('submit', function() {
        var btn = document.getElementById('btn-save-settings');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Menyimpan...';
        
        // Re-enable after 10 seconds (safety)
        setTimeout(function() {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-save me-1"></i> Simpan Pengaturan';
        }, 10000);
    });

    // File input preview
    function setupFilePreview(inputId) {
        var input = document.getElementById(inputId);
        if (!input) return;
        
        input.addEventListener('change', function(e) {
            var file = e.target.files[0];
            if (file && file.type.startsWith('image/')) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    var preview = document.createElement('div');
                    preview.className = 'file-preview-box mt-2';
                    preview.innerHTML = '<small class="text-muted d-block mb-2">Preview baru:</small><img src="' + e.target.result + '" class="img-fluid" style="max-width: 220px;">';
                    
                    // Remove old preview if exists
                    var oldPreview = input.parentElement.querySelector('.file-preview-box:last-child');
                    if (oldPreview && oldPreview.querySelector('small').textContent.includes('Preview baru')) {
                        oldPreview.remove();
                    }
                    
                    input.parentElement.appendChild(preview);
                };
                reader.readAsDataURL(file);
            }
        });
    }

    setupFilePreview('school_logo');
    setupFilePreview('favicon');
</script>
@endpush
