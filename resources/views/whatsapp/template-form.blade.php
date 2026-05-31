@extends('layouts.admin')

@section('title', isset($template) ? 'Edit Template' : 'Buat Template')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">{{ isset($template) ? '✏️ Edit Template' : '➕ Buat Template Baru' }}</h1>
            <p class="text-muted mb-0">{{ isset($template) ? 'Ubah template pesan WhatsApp' : 'Buat template pesan untuk pengiriman otomatis' }}</p>
        </div>
        <div>
            <a href="{{ route('whatsapp.templates') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form method="POST" action="{{ isset($template) ? route('whatsapp.templates.update', $template->id) : route('whatsapp.templates.store') }}">
                        @csrf
                        @if(isset($template))
                        @method('PUT')
                        @endif

                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Template <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $template->name ?? '') }}" required>
                            <small class="text-muted">Nama unik untuk identifikasi (gunakan underscore, contoh: welcome_message)</small>
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="label" class="form-label">Label <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('label') is-invalid @enderror" id="label" name="label" value="{{ old('label', $template->label ?? '') }}" required>
                            <small class="text-muted">Label yang ditampilkan di admin (contoh: Pesan Selamat Datang)</small>
                            @error('label')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="type" class="form-label">Tipe <span class="text-danger">*</span></label>
                                <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                    <option value="">-- Pilih Tipe --</option>
                                    <option value="registration" {{ old('type', $template->type ?? '') == 'registration' ? 'selected' : '' }}>Registrasi</option>
                                    <option value="payment" {{ old('type', $template->type ?? '') == 'payment' ? 'selected' : '' }}>Pembayaran</option>
                                    <option value="reminder" {{ old('type', $template->type ?? '') == 'reminder' ? 'selected' : '' }}>Pengingat</option>
                                    <option value="notification" {{ old('type', $template->type ?? '') == 'notification' ? 'selected' : '' }}>Notifikasi</option>
                                    <option value="custom" {{ old('type', $template->type ?? '') == 'custom' ? 'selected' : '' }}>Custom</option>
                                </select>
                                @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Opsi</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $template->is_active ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        Aktif
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="auto_send" name="auto_send" value="1" {{ old('auto_send', $template->auto_send ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="auto_send">
                                        Auto Send (kirim otomatis)
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Deskripsi</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="2">{{ old('description', $template->description ?? '') }}</textarea>
                            <small class="text-muted">Deskripsi singkat tentang template ini</small>
                            @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="message" class="form-label">Pesan Template <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('message') is-invalid @enderror" id="message" name="message" rows="10" required>{{ old('message', $template->message ?? '') }}</textarea>
                            <small class="text-muted">
                                Gunakan variabel: {nama}, {no_pendaftaran}, {jurusan}, {portal_url}, {sekolah}, dll
                            </small>
                            @error('message')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>{{ isset($template) ? 'Update Template' : 'Simpan Template' }}
                            </button>
                            <a href="{{ route('whatsapp.templates') }}" class="btn btn-outline-secondary">
                                Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                    <h6 class="card-title">
                        <i class="fas fa-info-circle me-2 text-primary"></i>Variabel Template
                    </h6>
                    <p class="small text-muted mb-2">Variabel yang bisa digunakan dalam template:</p>
                    <ul class="small mb-0 ps-3">
                        <li><code>{nama}</code> - Nama lengkap pendaftar</li>
                        <li><code>{no_pendaftaran}</code> - Nomor pendaftaran</li>
                        <li><code>{jurusan}</code> - Nama jurusan</li>
                        <li><code>{portal_url}</code> - URL portal SPMB</li>
                        <li><code>{sekolah}</code> - Nama sekolah</li>
                        <li><code>{tanggal_tes}</code> - Tanggal tes</li>
                        <li><code>{waktu_tes}</code> - Waktu tes</li>
                        <li><code>{tempat_tes}</code> - Lokasi tes</li>
                    </ul>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="card-title">
                        <i class="fas fa-lightbulb me-2 text-warning"></i>Tips
                    </h6>
                    <ul class="small mb-0 ps-3">
                        <li class="mb-2">Gunakan nama template yang deskriptif</li>
                        <li class="mb-2">Pastikan variabel sesuai dengan data yang tersedia</li>
                        <li class="mb-2">Test template sebelum mengaktifkan auto-send</li>
                        <li class="mb-2">Gunakan bahasa yang sopan dan profesional</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
