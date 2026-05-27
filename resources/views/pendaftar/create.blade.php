@extends('layouts.admin')

@section('title', 'Tambah Pendaftar - SPMB (Sistem Penerimaan Murid Baru)')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
        body { font-family: 'Inter', sans-serif !important; }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }
        .form-label {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 8px;
        }
        .form-control, .form-select {
            border-radius: 5px;
            border: 1px solid #e0e0e0;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2>Tambah Pendaftar</h2>
                    <p class="text-muted">Input data murid baru secara manual</p>
                </div>
                <a href="{{ route('pendaftar.index') }}" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>

            <div class="row">
                <div class="col-lg-8">
                    <div class="card p-4">
                        <form action="{{ route('pendaftar.store') }}" method="POST" id="formPendaftar">
                            @csrf

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="nisn" class="form-label">NISN <span class="text-danger">*</span></label>
                                        <input 
                                            type="text" 
                                            id="nisn" 
                                            name="nisn" 
                                            class="form-control @error('nisn') is-invalid @enderror"
                                            value="{{ old('nisn') }}"
                                            placeholder="Nomor Induk Siswa Nasional"
                                            required
                                        >
                                        @error('nisn')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="nama_lengkap" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                        <input 
                                            type="text" 
                                            id="nama_lengkap" 
                                            name="nama_lengkap" 
                                            class="form-control @error('nama_lengkap') is-invalid @enderror"
                                            value="{{ old('nama_lengkap') }}"
                                            placeholder="Nama lengkap siswa"
                                            required
                                        >
                                        @error('nama_lengkap')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="asal_sekolah" class="form-label">Asal Sekolah <span class="text-danger">*</span></label>
                                <input 
                                    type="text" 
                                    id="asal_sekolah" 
                                    name="asal_sekolah" 
                                    class="form-control @error('asal_sekolah') is-invalid @enderror"
                                    value="{{ old('asal_sekolah') }}"
                                    placeholder="SMP/Sekolah asal"
                                    required
                                >
                                @error('asal_sekolah')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="alamat" class="form-label">Alamat <span class="text-danger">*</span></label>
                                <textarea 
                                    id="alamat" 
                                    name="alamat" 
                                    class="form-control @error('alamat') is-invalid @enderror"
                                    rows="3"
                                    placeholder="Alamat lengkap"
                                    required
                                >{{ old('alamat') }}</textarea>
                                @error('alamat')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="jurusan" class="form-label">Jurusan <span class="text-danger">*</span></label>
                                        <select 
                                            id="jurusan_id" 
                                            name="jurusan_id" 
                                            class="form-select @error('jurusan_id') is-invalid @enderror"
                                            required
                                        >
                                            <option value="">-- Pilih Jurusan --</option>
                                            @foreach(($jurusans ?? collect()) as $j)
                                                <option value="{{ $j->id }}" {{ (string) old('jurusan_id') === (string) $j->id ? 'selected' : '' }}>{{ $j->kode }} - {{ $j->nama }}</option>
                                            @endforeach
                                        </select>
                                        @error('jurusan_id')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="nama_jaringan" class="form-label">Nama Jaringan (Perekomendasi)</label>
                                        <input 
                                            type="text" 
                                            id="nama_jaringan" 
                                            name="nama_jaringan" 
                                            class="form-control"
                                            value="{{ old('nama_jaringan') }}"
                                            placeholder="Opsional"
                                        >
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex gap-2 mt-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Simpan Pendaftar
                                </button>
                                <a href="{{ route('pendaftar.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Batal
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card p-4">
                        <h5 class="mb-3">
                            <i class="fas fa-info-circle"></i> Informasi
                        </h5>
                        <div class="alert alert-info">
                            <small><strong>Catatan:</strong></small>
                            <ul style="margin-bottom: 0; font-size: 13px;">
                                <li>Nomor registrasi akan otomatis ter-generate</li>
                                <li>Format: SPMB-TAHUN-URUTAN</li>
                                <li>Data logistik akan dibuat otomatis</li>
                                <li>Status awal: Belum Daftar Ulang</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
@endsection
