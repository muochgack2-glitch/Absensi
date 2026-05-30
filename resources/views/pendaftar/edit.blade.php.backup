@extends('layouts.admin')

@section('title', 'Edit Pendaftar - SPMB (Sistem Penerimaan Murid Baru)')

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
                    <h2>Edit Pendaftar</h2>
                    <p class="text-muted">No. Registrasi: <strong>{{ $pendaftar->no_registrasi }}</strong></p>
                </div>
                <a href="{{ route('pendaftar.index') }}" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>

            @if (Session::has('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i> {{ Session::get('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (($pendaftar->status_data ?? 'awal') === 'awal')
                <div class="alert alert-info border-0 shadow-sm" role="alert">
                    <div class="d-flex gap-3 align-items-start">
                        <div class="fs-4 text-primary"><i class="fas fa-circle-info"></i></div>
                        <div>
                            <strong>Data masih berstatus awal.</strong>
                            <div class="mt-1">Lengkapi biodata calon siswa seperti NIK, tempat/tanggal lahir, kontak, dan data orang tua sebelum proses verifikasi daftar ulang.</div>
                            <div class="mt-2 small text-muted">Setelah lengkap, ubah <strong>Status Kelengkapan Data</strong> menjadi <strong>Lengkap</strong> atau <strong>Terverifikasi</strong>.</div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="row">
                <div class="col-lg-8">
                    <div class="card p-4">
                        <form action="{{ route('pendaftar.update', $pendaftar->id_pendaftar) }}" method="POST" id="formPendaftar">
                            @csrf
                            @method('PUT')

                            {{-- ===== A. REGISTRASI & DATA PRIBADI ===== --}}
                            <div class="d-flex align-items-center gap-2 mb-3 mt-1">
                                <div style="width:28px;height:28px;border-radius:8px;background:var(--primary);display:flex;align-items:center;justify-content:center;color:#fff;font-size:13px;font-weight:800;flex-shrink:0;">A</div>
                                <h6 class="mb-0 fw-bold" style="color:#1e293b;">Registrasi &amp; Data Pribadi</h6>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="nisn" class="form-label">NISN <span class="text-danger">*</span></label>
                                        <input 
                                            type="text" 
                                            id="nisn" 
                                            name="nisn" 
                                            class="form-control @error('nisn') is-invalid @enderror"
                                            value="{{ old('nisn', $pendaftar->nisn) }}"
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
                                            value="{{ old('nama_lengkap', $pendaftar->nama_lengkap) }}"
                                            placeholder="Nama lengkap siswa"
                                            required
                                        >
                                        @error('nama_lengkap')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="nik" class="form-label">NIK</label>
                                        <input type="text" id="nik" name="nik" class="form-control @error('nik') is-invalid @enderror" value="{{ old('nik', $pendaftar->nik) }}" placeholder="NIK sesuai KK/KTP">
                                        @error('nik')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="no_kip" class="form-label">No. Kartu Indonesia Pintar (KIP)</label>
                                        <input type="text" id="no_kip" name="no_kip" class="form-control @error('no_kip') is-invalid @enderror" value="{{ old('no_kip', $pendaftar->no_kip) }}" placeholder="Nomor KIP (jika ada)">
                                        @error('no_kip')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="status_data" class="form-label">Status Kelengkapan Data</label>
                                        <select id="status_data" name="status_data" class="form-select @error('status_data') is-invalid @enderror" required>
                                            <option value="awal" {{ old('status_data', $pendaftar->status_data ?? 'awal') === 'awal' ? 'selected' : '' }}>Awal</option>
                                            <option value="lengkap" {{ old('status_data', $pendaftar->status_data ?? 'awal') === 'lengkap' ? 'selected' : '' }}>Lengkap</option>
                                            <option value="terverifikasi" {{ old('status_data', $pendaftar->status_data ?? 'awal') === 'terverifikasi' ? 'selected' : '' }}>Terverifikasi</option>
                                        </select>
                                        @error('status_data')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                    </div>
                                </div>
                            </div>

                            <hr class="my-3">
                            {{-- ===== B. DATA DIRI LENGKAP ===== --}}
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <div style="width:28px;height:28px;border-radius:8px;background:var(--primary);display:flex;align-items:center;justify-content:center;color:#fff;font-size:13px;font-weight:800;flex-shrink:0;">B</div>
                                <h6 class="mb-0 fw-bold" style="color:#1e293b;">Data Diri Lengkap</h6>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $pendaftar->email) }}" placeholder="email@contoh.com">
                                        @error('email')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="no_telepon" class="form-label">No. Telepon / WhatsApp</label>
                                        <input type="text" id="no_telepon" name="no_telepon" class="form-control @error('no_telepon') is-invalid @enderror" value="{{ old('no_telepon', $pendaftar->no_telepon) }}" placeholder="08xxxxxxxxxx">
                                        @error('no_telepon')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="tempat_lahir" class="form-label">Tempat Lahir</label>
                                        <input type="text" id="tempat_lahir" name="tempat_lahir" class="form-control @error('tempat_lahir') is-invalid @enderror" value="{{ old('tempat_lahir', $pendaftar->tempat_lahir) }}" placeholder="Kota lahir">
                                        @error('tempat_lahir')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="tanggal_lahir" class="form-label">Tanggal Lahir</label>
                                        <input type="date" id="tanggal_lahir" name="tanggal_lahir" class="form-control @error('tanggal_lahir') is-invalid @enderror" value="{{ old('tanggal_lahir', optional($pendaftar->tanggal_lahir)->format('Y-m-d')) }}">
                                        @error('tanggal_lahir')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
                                        <select id="jenis_kelamin" name="jenis_kelamin" class="form-select @error('jenis_kelamin') is-invalid @enderror">
                                            <option value="">-- Pilih --</option>
                                            <option value="L" {{ old('jenis_kelamin', $pendaftar->jenis_kelamin) === 'L' ? 'selected' : '' }}>Laki-laki</option>
                                            <option value="P" {{ old('jenis_kelamin', $pendaftar->jenis_kelamin) === 'P' ? 'selected' : '' }}>Perempuan</option>
                                        </select>
                                        @error('jenis_kelamin')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="agama" class="form-label">Agama</label>
                                        <input type="text" id="agama" name="agama" class="form-control @error('agama') is-invalid @enderror" value="{{ old('agama', $pendaftar->agama) }}" placeholder="Agama">
                                        @error('agama')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="tahun_lulus" class="form-label">Tahun Lulus</label>
                                        <input type="text" id="tahun_lulus" name="tahun_lulus" class="form-control @error('tahun_lulus') is-invalid @enderror" value="{{ old('tahun_lulus', $pendaftar->tahun_lulus) }}" placeholder="Contoh: 2026">
                                        @error('tahun_lulus')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                    </div>
                                </div>
                            </div>

                            <hr class="my-3">
                            {{-- ===== C. DATA AKADEMIK ===== --}}
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <div style="width:28px;height:28px;border-radius:8px;background:var(--primary);display:flex;align-items:center;justify-content:center;color:#fff;font-size:13px;font-weight:800;flex-shrink:0;">C</div>
                                <h6 class="mb-0 fw-bold" style="color:#1e293b;">Data Akademik</h6>
                            </div>
                            <div class="mb-3">
                                <label for="asal_sekolah" class="form-label">Asal Sekolah <span class="text-danger">*</span></label>
                                <input 
                                    type="text" 
                                    id="asal_sekolah" 
                                    name="asal_sekolah" 
                                    class="form-control @error('asal_sekolah') is-invalid @enderror"
                                    value="{{ old('asal_sekolah', $pendaftar->asal_sekolah) }}"
                                    placeholder="SMP/Sekolah asal"
                                    required
                                >
                                @error('asal_sekolah')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <hr class="my-3">
                            {{-- ===== D. ALAMAT TINGGAL LENGKAP ===== --}}
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <div style="width:28px;height:28px;border-radius:8px;background:var(--primary);display:flex;align-items:center;justify-content:center;color:#fff;font-size:13px;font-weight:800;flex-shrink:0;">D</div>
                                <h6 class="mb-0 fw-bold" style="color:#1e293b;">Alamat Tinggal Lengkap</h6>
                            </div>
                            <div class="mb-3">
                                <label for="alamat" class="form-label">Alamat Ringkas <span class="text-danger">*</span></label>
                                <textarea 
                                    id="alamat" 
                                    name="alamat" 
                                    class="form-control @error('alamat') is-invalid @enderror"
                                    rows="2"
                                    placeholder="Alamat ringkas"
                                    required
                                >{{ old('alamat', $pendaftar->alamat) }}</textarea>
                                @error('alamat')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="alamat_jalan" class="form-label">Alamat Jalan</label>
                                        <input type="text" id="alamat_jalan" name="alamat_jalan" class="form-control @error('alamat_jalan') is-invalid @enderror" value="{{ old('alamat_jalan', $pendaftar->alamat_jalan) }}" placeholder="Nama jalan dan nomor rumah">
                                        @error('alamat_jalan')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="alamat_dukuh" class="form-label">Dukuh</label>
                                        <input type="text" id="alamat_dukuh" name="alamat_dukuh" class="form-control @error('alamat_dukuh') is-invalid @enderror" value="{{ old('alamat_dukuh', $pendaftar->alamat_dukuh) }}" placeholder="Nama dukuh">
                                        @error('alamat_dukuh')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="alamat_rt" class="form-label">RT</label>
                                        <input type="text" id="alamat_rt" name="alamat_rt" class="form-control @error('alamat_rt') is-invalid @enderror" value="{{ old('alamat_rt', $pendaftar->alamat_rt) }}" placeholder="001">
                                        @error('alamat_rt')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="alamat_rw" class="form-label">RW</label>
                                        <input type="text" id="alamat_rw" name="alamat_rw" class="form-control @error('alamat_rw') is-invalid @enderror" value="{{ old('alamat_rw', $pendaftar->alamat_rw) }}" placeholder="002">
                                        @error('alamat_rw')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="alamat_kelurahan" class="form-label">Kelurahan</label>
                                        <input type="text" id="alamat_kelurahan" name="alamat_kelurahan" class="form-control @error('alamat_kelurahan') is-invalid @enderror" value="{{ old('alamat_kelurahan', $pendaftar->alamat_kelurahan) }}" placeholder="Nama kelurahan/desa">
                                        @error('alamat_kelurahan')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="alamat_kecamatan" class="form-label">Kecamatan</label>
                                        <input type="text" id="alamat_kecamatan" name="alamat_kecamatan" class="form-control @error('alamat_kecamatan') is-invalid @enderror" value="{{ old('alamat_kecamatan', $pendaftar->alamat_kecamatan) }}" placeholder="Nama kecamatan">
                                        @error('alamat_kecamatan')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="alamat_kabupaten" class="form-label">Kabupaten</label>
                                        <input type="text" id="alamat_kabupaten" name="alamat_kabupaten" class="form-control @error('alamat_kabupaten') is-invalid @enderror" value="{{ old('alamat_kabupaten', $pendaftar->alamat_kabupaten) }}" placeholder="Nama kabupaten/kota">
                                        @error('alamat_kabupaten')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="alamat_provinsi" class="form-label">Provinsi</label>
                                        <input type="text" id="alamat_provinsi" name="alamat_provinsi" class="form-control @error('alamat_provinsi') is-invalid @enderror" value="{{ old('alamat_provinsi', $pendaftar->alamat_provinsi) }}" placeholder="Nama provinsi">
                                        @error('alamat_provinsi')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                    </div>
                                </div>
                            </div>

                            <hr class="my-3">
                            {{-- ===== E. IDENTITAS ORANG TUA ===== --}}
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <div style="width:28px;height:28px;border-radius:8px;background:var(--primary);display:flex;align-items:center;justify-content:center;color:#fff;font-size:13px;font-weight:800;flex-shrink:0;">E</div>
                                <h6 class="mb-0 fw-bold" style="color:#1e293b;">Identitas Orang Tua Calon Murid Baru</h6>
                            </div>
                            <p class="text-muted small mb-3">Data Ayah Kandung</p>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="nama_ayah" class="form-label">Nama Ayah Kandung</label>
                                        <input type="text" id="nama_ayah" name="nama_ayah" class="form-control @error('nama_ayah') is-invalid @enderror" value="{{ old('nama_ayah', $pendaftar->nama_ayah) }}" placeholder="Nama ayah kandung">
                                        @error('nama_ayah')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="pekerjaan_ayah" class="form-label">Pekerjaan Ayah</label>
                                        <input type="text" id="pekerjaan_ayah" name="pekerjaan_ayah" class="form-control @error('pekerjaan_ayah') is-invalid @enderror" value="{{ old('pekerjaan_ayah', $pendaftar->pekerjaan_ayah) }}" placeholder="Pekerjaan ayah">
                                        @error('pekerjaan_ayah')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="alamat_ayah" class="form-label">Alamat Ayah</label>
                                <input type="text" id="alamat_ayah" name="alamat_ayah" class="form-control @error('alamat_ayah') is-invalid @enderror" value="{{ old('alamat_ayah', $pendaftar->alamat_ayah) }}" placeholder="Alamat ayah kandung">
                                @error('alamat_ayah')<span class="invalid-feedback">{{ $message }}</span>@enderror
                            </div>

                            <p class="text-muted small mb-3 mt-2">Data Ibu Kandung</p>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="nama_ibu" class="form-label">Nama Ibu Kandung</label>
                                        <input type="text" id="nama_ibu" name="nama_ibu" class="form-control @error('nama_ibu') is-invalid @enderror" value="{{ old('nama_ibu', $pendaftar->nama_ibu) }}" placeholder="Nama ibu kandung">
                                        @error('nama_ibu')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="pekerjaan_ibu" class="form-label">Pekerjaan Ibu</label>
                                        <input type="text" id="pekerjaan_ibu" name="pekerjaan_ibu" class="form-control @error('pekerjaan_ibu') is-invalid @enderror" value="{{ old('pekerjaan_ibu', $pendaftar->pekerjaan_ibu) }}" placeholder="Pekerjaan ibu">
                                        @error('pekerjaan_ibu')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="alamat_ibu" class="form-label">Alamat Ibu</label>
                                <input type="text" id="alamat_ibu" name="alamat_ibu" class="form-control @error('alamat_ibu') is-invalid @enderror" value="{{ old('alamat_ibu', $pendaftar->alamat_ibu) }}" placeholder="Alamat ibu kandung">
                                @error('alamat_ibu')<span class="invalid-feedback">{{ $message }}</span>@enderror
                            </div>

                            <div class="mb-3">
                                <label for="no_hp_ortu" class="form-label">No. HP Orang Tua/Wali</label>
                                <input type="text" id="no_hp_ortu" name="no_hp_ortu" class="form-control @error('no_hp_ortu') is-invalid @enderror" value="{{ old('no_hp_ortu', $pendaftar->no_hp_ortu) }}" placeholder="08xxxxxxxxxx">
                                @error('no_hp_ortu')<span class="invalid-feedback">{{ $message }}</span>@enderror
                            </div>

                            <hr class="my-3">
                            {{-- ===== F. DATA WALI ===== --}}
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <div style="width:28px;height:28px;border-radius:8px;background:var(--primary);display:flex;align-items:center;justify-content:center;color:#fff;font-size:13px;font-weight:800;flex-shrink:0;">F</div>
                                <h6 class="mb-0 fw-bold" style="color:#1e293b;">Data Wali <small class="text-muted fw-normal">(diisi jika murid ikut wali)</small></h6>
                            </div>
                            <div class="border rounded p-3 mb-3">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="nama_wali" class="form-label">Nama Wali</label>
                                            <input type="text" id="nama_wali" name="nama_wali" class="form-control @error('nama_wali') is-invalid @enderror" value="{{ old('nama_wali', $pendaftar->nama_wali) }}" placeholder="Nama wali">
                                            @error('nama_wali')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="pekerjaan_wali" class="form-label">Pekerjaan Wali</label>
                                            <input type="text" id="pekerjaan_wali" name="pekerjaan_wali" class="form-control @error('pekerjaan_wali') is-invalid @enderror" value="{{ old('pekerjaan_wali', $pendaftar->pekerjaan_wali) }}" placeholder="Pekerjaan wali">
                                            @error('pekerjaan_wali')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="alamat_wali" class="form-label">Alamat Wali</label>
                                            <input type="text" id="alamat_wali" name="alamat_wali" class="form-control @error('alamat_wali') is-invalid @enderror" value="{{ old('alamat_wali', $pendaftar->alamat_wali) }}" placeholder="Alamat wali">
                                            @error('alamat_wali')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-3">
                            {{-- ===== G. JURUSAN & JARINGAN ===== --}}
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <div style="width:28px;height:28px;border-radius:8px;background:var(--primary);display:flex;align-items:center;justify-content:center;color:#fff;font-size:13px;font-weight:800;flex-shrink:0;">G</div>
                                <h6 class="mb-0 fw-bold" style="color:#1e293b;">Jurusan &amp; Jaringan</h6>
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
                                                <option value="{{ $j->id }}" {{ (string) old('jurusan_id', $pendaftar->jurusan_id) === (string) $j->id ? 'selected' : '' }}>{{ $j->kode }} - {{ $j->nama }}</option>
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
                                            value="{{ old('nama_jaringan', $pendaftar->nama_jaringan) }}"
                                            placeholder="Opsional"
                                        >
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex gap-2 mt-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Simpan Perubahan
                                </button>
                                <a href="{{ route('pendaftar.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Batal
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
@endsection
