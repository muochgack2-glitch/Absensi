@extends('layouts.admin')

@section('title', 'Pengaturan Sistem - SPMB (Sistem Penerimaan Murid Baru)')

@push('styles')
<style>
        .settings-card {
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.06);
            background: #fff;
        }

        .settings-page-title {
            font-size: 30px;
            font-weight: 800;
            letter-spacing: -0.02em;
            margin: 0;
        }

        .settings-page-subtitle {
            font-size: 14px;
            color: #64748b;
            margin-top: 4px;
        }

        .section-title {
            font-size: 15px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 12px;
        }

        .settings-tabs {
            border-bottom: 1px solid #e2e8f0;
            gap: 8px;
        }

        .settings-tabs .nav-link {
            border: 1px solid transparent;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
            color: #475569;
            font-weight: 600;
            padding: 10px 14px;
        }

        .settings-tabs .nav-link:hover {
            border-color: #e2e8f0;
            color: #334155;
            background: #f8fafc;
        }

        .settings-tabs .nav-link.active {
            color: #0f172a;
            background: #fff;
            border-color: #e2e8f0 #e2e8f0 #fff;
        }

        .settings-tab-pane {
            border: 1px solid #e2e8f0;
            border-top: 0;
            border-radius: 0 0 12px 12px;
            padding: 18px;
            background: #fff;
        }

        .settings-form .form-label {
            font-weight: 600;
            color: #334155;
            font-size: 13px;
            margin-bottom: 6px;
        }

        .settings-form .form-control,
        .settings-form .form-select {
            border-radius: 10px;
            border: 1px solid #cbd5e1;
            min-height: 42px;
        }

        .settings-form .form-control:focus,
        .settings-form .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.14);
        }

        .settings-form .form-control-color {
            min-height: 42px;
            width: 100%;
            padding: 5px;
        }

        .settings-actions {
            border-top: 1px solid #e2e8f0;
            margin-top: 16px;
            padding-top: 14px;
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
                    <h1 class="settings-page-title">Pengaturan Sistem</h1>
                    <p class="settings-page-subtitle mb-0">Atur konfigurasi inti SPMB (Sistem Penerimaan Murid Baru) yang dipakai seluruh modul.</p>
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger"><i class="fas fa-triangle-exclamation"></i> {{ session('error') }}</div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Gagal menyimpan:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="settings-card p-4">
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
                            <p class="section-title mb-3">Profil Sekolah</p>
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label for="school_name" class="form-label">Nama Sekolah</label>
                                    <input id="school_name" name="school_name" type="text" class="form-control" value="{{ old('school_name', $settings['school_name']) }}" required>
                                </div>
                                <div class="col-md-3">
                                    <label for="academic_year" class="form-label">Tahun Ajaran</label>
                                    <input id="academic_year" name="academic_year" type="text" class="form-control" value="{{ old('academic_year', $settings['academic_year']) }}" placeholder="2026/2027" required>
                                </div>
                                <div class="col-md-3">
                                    <label for="registration_status" class="form-label">Status Pendaftaran</label>
                                    <select id="registration_status" name="registration_status" class="form-select" required>
                                        <option value="open" {{ old('registration_status', $settings['registration_status']) === 'open' ? 'selected' : '' }}>Buka</option>
                                        <option value="closed" {{ old('registration_status', $settings['registration_status']) === 'closed' ? 'selected' : '' }}>Tutup</option>
                                    </select>
                                </div>
                                <div class="col-md-8">
                                    <label for="school_address" class="form-label">Alamat Sekolah</label>
                                    <input id="school_address" name="school_address" type="text" class="form-control" value="{{ old('school_address', $settings['school_address']) }}">
                                </div>
                                <div class="col-md-4">
                                    <label for="school_contact" class="form-label">Kontak Sekolah</label>
                                    <input id="school_contact" name="school_contact" type="text" class="form-control" value="{{ old('school_contact', $settings['school_contact']) }}" placeholder="WA/Telepon">
                                </div>
                                <div class="col-md-4">
                                    <label for="school_city" class="form-label">Kota/Kabupaten</label>
                                    <input id="school_city" name="school_city" type="text" class="form-control" value="{{ old('school_city', $settings['school_city'] ?? '') }}">
                                </div>
                                <div class="col-md-4">
                                    <label for="school_phone" class="form-label">Telepon Sekolah</label>
                                    <input id="school_phone" name="school_phone" type="text" class="form-control" value="{{ old('school_phone', $settings['school_phone'] ?? '') }}">
                                </div>
                                <div class="col-md-4">
                                    <label for="school_email" class="form-label">Email Sekolah</label>
                                    <input id="school_email" name="school_email" type="email" class="form-control" value="{{ old('school_email', $settings['school_email'] ?? '') }}">
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="pane-pendaftaran" role="tabpanel" aria-labelledby="tab-pendaftaran" tabindex="0">
                            <p class="section-title mb-3">Konfigurasi Pendaftaran SPMB</p>
                            <div class="row g-3 mb-4">
                                <div class="col-md-4">
                                    <label for="registration_fee" class="form-label">Biaya Pendaftaran (Rp)</label>
                                    <input id="registration_fee" name="registration_fee" type="number" min="0" class="form-control" value="{{ old('registration_fee', $settings['registration_fee']) }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="active_wave" class="form-label">Gelombang Aktif</label>
                                    <input id="active_wave" name="active_wave" type="text" class="form-control" value="{{ old('active_wave', $settings['active_wave']) }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="principal_name" class="form-label">Nama Kepala Sekolah</label>
                                    <input id="principal_name" name="principal_name" type="text" class="form-control" value="{{ old('principal_name', $settings['principal_name']) }}">
                                </div>
                            </div>

                            <hr class="my-4">

                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                                <div>
                                    <p class="section-title mb-1">Master Jurusan</p>
                                    <p class="text-muted mb-0" style="font-size: 13px;">Kelola kode, nama, kuota, dan status aktif jurusan langsung dari tab Pendaftaran.</p>
                                </div>
                            </div>

                            <div class="card border-0 shadow-sm mb-4">
                                <div class="card-body">
                                    <h6 class="fw-bold mb-3">Tambah Jurusan Baru</h6>
                                    <input type="hidden" form="jurusan-create-form" name="aktif" value="1">
                                    <div class="row g-3 align-items-end">
                                        <div class="col-md-3">
                                            <label for="new_jurusan_kode" class="form-label">Kode</label>
                                            <input id="new_jurusan_kode" form="jurusan-create-form" name="kode" type="text" class="form-control" placeholder="MPLB" required>
                                        </div>
                                        <div class="col-md-5">
                                            <label for="new_jurusan_nama" class="form-label">Nama Jurusan</label>
                                            <input id="new_jurusan_nama" form="jurusan-create-form" name="nama" type="text" class="form-control" placeholder="Manajemen Perkantoran dan Layanan Bisnis" required>
                                        </div>
                                        <div class="col-md-2">
                                            <label for="new_jurusan_kuota" class="form-label">Kuota</label>
                                            <input id="new_jurusan_kuota" form="jurusan-create-form" name="kuota" type="number" min="0" class="form-control" value="0" required>
                                        </div>
                                        <div class="col-md-2 d-grid">
                                            <button type="submit" form="jurusan-create-form" class="btn btn-primary"><i class="fas fa-plus me-1"></i>Tambah</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table align-middle">
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
                                                        <button type="submit" form="jurusan-delete-{{ $jurusan->id }}" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash me-1"></i>Hapus</button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center text-muted py-4">Belum ada data jurusan.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="pane-branding" role="tabpanel" aria-labelledby="tab-branding" tabindex="0">
                            <p class="section-title mb-3">Branding</p>
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label for="school_website" class="form-label">Website Sekolah</label>
                                    <input id="school_website" name="school_website" type="url" class="form-control" value="{{ old('school_website', $settings['school_website'] ?? '') }}" placeholder="https://...">
                                </div>
                                <div class="col-md-6">
                                    <label for="instagram_url" class="form-label">Instagram URL</label>
                                    <input id="instagram_url" name="instagram_url" type="url" class="form-control" value="{{ old('instagram_url', $settings['instagram_url'] ?? '') }}" placeholder="https://instagram.com/...">
                                </div>
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
                                <div class="col-md-4">
                                    <label for="theme_preset" class="form-label">Preset Warna Tema</label>
                                    <select id="theme_preset" name="theme_preset" class="form-select">
                                        @foreach($themePresets as $value => $preset)
                                            <option value="{{ $value }}" data-primary="{{ $preset['primary'] }}" data-secondary="{{ $preset['secondary'] }}" {{ $activePreset === $value ? 'selected' : '' }}>{{ $preset['label'] }}</option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">Pilih preset lalu simpan. Color picker akan otomatis mengikuti.</small>
                                </div>
                                <div class="col-md-4">
                                    <label for="theme_primary" class="form-label">Warna Utama</label>
                                    <input id="theme_primary" name="theme_primary" type="color" class="form-control form-control-color" value="{{ $activePrimary }}" title="Pilih warna utama">
                                </div>
                                <div class="col-md-4">
                                    <label for="theme_secondary" class="form-label">Warna Kedua</label>
                                    <input id="theme_secondary" name="theme_secondary" type="color" class="form-control form-control-color" value="{{ $activeSecondary }}" title="Pilih warna kedua">
                                </div>
                                <div class="col-md-6">
                                    <label for="school_logo" class="form-label">Logo Sekolah</label>
                                    <input id="school_logo" name="school_logo" type="file" class="form-control" accept=".png,.jpg,.jpeg,.webp">
                                    @if(!empty($settings['school_logo']))
                                        <small class="text-muted d-block mt-1">Logo tersimpan: {{ $settings['school_logo'] }}</small>
                                        <div class="mt-2" style="max-width: 220px;">
                                            <img src="{{ asset('storage/' . $settings['school_logo']) }}" alt="Preview logo sekolah" class="img-fluid rounded border">
                                        </div>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <label for="favicon" class="form-label">Favicon</label>
                                    <input id="favicon" name="favicon" type="file" class="form-control" accept=".ico,.png,.jpg,.jpeg,.webp,.svg">
                                    <small class="text-muted d-block mt-1">Rekomendasi: 32x32 atau 64x64 px.</small>
                                    @if(!empty($settings['favicon']))
                                        <small class="text-muted d-block mt-1">Favicon tersimpan: {{ $settings['favicon'] }}</small>
                                        <div class="mt-2 d-flex align-items-center gap-2">
                                            <img src="{{ asset('storage/' . $settings['favicon']) }}" alt="Preview favicon" class="rounded border" style="width:48px;height:48px;object-fit:contain;">
                                            <span class="text-muted small">Preview favicon</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="pane-document" role="tabpanel" aria-labelledby="tab-document" tabindex="0">
                            <p class="section-title mb-3">Identitas Dokumen Cetak</p>
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label for="print_footer_text" class="form-label">Footer Dokumen</label>
                                    <input id="print_footer_text" name="print_footer_text" type="text" class="form-control" placeholder="Contoh: Dokumen ini sah tanpa tanda tangan basah" value="{{ old('print_footer_text', $settings['print_footer_text'] ?? '') }}">
                                </div>

                                <div class="col-md-6">
                                    <label for="document_header_text" class="form-label">Header Dokumen (Kop)</label>
                                    <input id="document_header_text" name="document_header_text" type="text" class="form-control" placeholder="Contoh: PANITIA SPMB TAHUN AJARAN ..." value="{{ old('document_header_text', $settings['document_header_text'] ?? '') }}">
                                </div>
                                <div class="col-md-4">
                                    <label for="document_city" class="form-label">Kota Dokumen</label>
                                    <input id="document_city" name="document_city" type="text" class="form-control" value="{{ old('document_city', $settings['document_city'] ?? ($settings['school_city'] ?? '')) }}" placeholder="Contoh: Blora">
                                </div>
                                <div class="col-md-4">
                                    <label for="document_sign_name" class="form-label">Nama Penandatangan</label>
                                    <input id="document_sign_name" name="document_sign_name" type="text" class="form-control" value="{{ old('document_sign_name', $settings['document_sign_name'] ?? ($settings['principal_name'] ?? '')) }}" placeholder="Nama lengkap">
                                </div>
                                <div class="col-md-4">
                                    <label for="document_sign_title" class="form-label">Jabatan Penandatangan</label>
                                    <input id="document_sign_title" name="document_sign_title" type="text" class="form-control" value="{{ old('document_sign_title', $settings['document_sign_title'] ?? 'Kepala Sekolah') }}" placeholder="Contoh: Kepala Sekolah">
                                </div>
                            </div>
                        </div>


                    </div>

                    

                    <div class="d-flex gap-2 settings-actions">
                        <button id="btn-save-settings" type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Simpan Pengaturan</button>
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">Kembali</a>
                    </div>
                </form>
            </div>
@endsection

@push('scripts')
<script>
                        (function () {
                            var key = 'settings_active_tab';
                            var el = document.getElementById('settingsTabs');
                            if (!el || !window.bootstrap) return;

                            var saved = localStorage.getItem(key);
                            if (saved) {
                                var trigger = document.querySelector('[data-bs-target="' + saved + '"]');
                                if (trigger) {
                                    bootstrap.Tab.getOrCreateInstance(trigger).show();
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
                    </script>
@endpush
