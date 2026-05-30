{{-- Modern Form Template for Pendaftar --}}
{{-- This is a partial that can be included in both create and edit --}}

<div class="dashboard-content">
    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-3">
        <div>
            <h2 class="mb-2">{{ isset($pendaftar) ? 'Edit Pendaftar' : 'Tambah Pendaftar Baru' }}</h2>
            @if(isset($pendaftar))
                <p class="text-muted mb-0">No. Registrasi: <strong>{{ $pendaftar->no_registrasi }}</strong></p>
            @else
                <p class="text-muted mb-0">Isi data pendaftar baru dengan lengkap</p>
            @endif
        </div>
        <x-button variant="secondary" outline="true" icon="fas fa-arrow-left" href="{{ route('pendaftar.index') }}">
            Kembali
        </x-button>
    </div>

    {{-- Success Alert --}}
    @if (Session::has('success'))
        <x-alert type="success" dismissible="true">
            {{ Session::get('success') }}
        </x-alert>
    @endif

    {{-- Error Alert --}}
    @if ($errors->any())
        <x-alert type="danger" dismissible="true">
            <strong>Terjadi kesalahan:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <hr class="my-2">
            <small class="text-muted">Debug - Error fields: {{ implode(', ', $errors->keys()) }}</small>
        </x-alert>
    @endif

    {{-- Info Alert for Data Awal --}}
    @if (isset($pendaftar) && ($pendaftar->status_data ?? 'awal') === 'awal')
        <x-info-card type="info" title="Data masih berstatus awal" dismissible="false">
            Lengkapi biodata calon siswa seperti NIK, tempat/tanggal lahir, kontak, dan data orang tua sebelum proses verifikasi daftar ulang.
            <br><small class="text-muted">Setelah lengkap, ubah <strong>Status Kelengkapan Data</strong> menjadi <strong>Lengkap</strong> atau <strong>Terverifikasi</strong>.</small>
        </x-info-card>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <form action="{{ isset($pendaftar) ? route('pendaftar.update', $pendaftar->id_pendaftar) : route('pendaftar.store') }}" method="POST" id="formPendaftar">
                @csrf
                @if(isset($pendaftar))
                    @method('PUT')
                @endif

                {{-- A. REGISTRASI & DATA PRIBADI --}}
                <x-section-card title="Registrasi & Data Pribadi" icon="fas fa-user-circle" class="mb-4">
                    <div class="row">
                        <div class="col-md-6">
                            <x-form-group label="NISN" name="nisn" required="true">
                                <x-input 
                                    name="nisn" 
                                    value="{{ old('nisn', $pendaftar->nisn ?? '') }}"
                                    placeholder="Nomor Induk Siswa Nasional"
                                    icon="fas fa-id-card"
                                />
                            </x-form-group>
                        </div>
                        <div class="col-md-6">
                            <x-form-group label="Nama Lengkap" name="nama_lengkap" required="true">
                                <x-input 
                                    name="nama_lengkap" 
                                    value="{{ old('nama_lengkap', $pendaftar->nama_lengkap ?? '') }}"
                                    placeholder="Nama lengkap siswa"
                                    icon="fas fa-user"
                                />
                            </x-form-group>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <x-form-group label="NIK" name="nik">
                                <x-input 
                                    name="nik" 
                                    value="{{ old('nik', $pendaftar->nik ?? '') }}"
                                    placeholder="NIK sesuai KK/KTP"
                                    icon="fas fa-address-card"
                                />
                            </x-form-group>
                        </div>
                        <div class="col-md-6">
                            <x-form-group label="No. Kartu Indonesia Pintar (KIP)" name="no_kip">
                                <x-input 
                                    name="no_kip" 
                                    value="{{ old('no_kip', $pendaftar->no_kip ?? '') }}"
                                    placeholder="Nomor KIP (jika ada)"
                                    icon="fas fa-credit-card"
                                />
                            </x-form-group>
                        </div>
                    </div>

                    @if(isset($pendaftar))
                    <div class="row">
                        <div class="col-md-6">
                            <x-form-group label="Status Kelengkapan Data" name="status_data" required="true">
                                <x-select name="status_data">
                                    <option value="awal" {{ old('status_data', $pendaftar->status_data ?? 'awal') === 'awal' ? 'selected' : '' }}>Awal</option>
                                    <option value="lengkap" {{ old('status_data', $pendaftar->status_data ?? 'awal') === 'lengkap' ? 'selected' : '' }}>Lengkap</option>
                                    <option value="terverifikasi" {{ old('status_data', $pendaftar->status_data ?? 'awal') === 'terverifikasi' ? 'selected' : '' }}>Terverifikasi</option>
                                </x-select>
                            </x-form-group>
                        </div>
                    </div>
                    @endif
                </x-section-card>

                {{-- B. DATA DIRI LENGKAP --}}
                <x-section-card title="Data Diri Lengkap" icon="fas fa-address-book" class="mb-4">
                    <div class="row">
                        <div class="col-md-6">
                            <x-form-group label="Email" name="email">
                                <x-input 
                                    name="email" 
                                    type="email"
                                    value="{{ old('email', $pendaftar->email ?? '') }}"
                                    placeholder="email@contoh.com"
                                    icon="fas fa-envelope"
                                />
                            </x-form-group>
                        </div>
                        <div class="col-md-6">
                            <x-form-group label="No. Telepon / WhatsApp" name="no_telepon">
                                <x-input 
                                    name="no_telepon" 
                                    value="{{ old('no_telepon', $pendaftar->no_telepon ?? '') }}"
                                    placeholder="08xxxxxxxxxx"
                                    icon="fas fa-phone"
                                />
                            </x-form-group>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <x-form-group label="Tempat Lahir" name="tempat_lahir">
                                <x-input 
                                    name="tempat_lahir" 
                                    value="{{ old('tempat_lahir', $pendaftar->tempat_lahir ?? '') }}"
                                    placeholder="Kota lahir"
                                    icon="fas fa-map-marker-alt"
                                />
                            </x-form-group>
                        </div>
                        <div class="col-md-4">
                            <x-form-group label="Tanggal Lahir" name="tanggal_lahir">
                                <x-input 
                                    name="tanggal_lahir" 
                                    type="date"
                                    value="{{ old('tanggal_lahir', isset($pendaftar) ? optional($pendaftar->tanggal_lahir)->format('Y-m-d') : '') }}"
                                    icon="fas fa-calendar"
                                />
                            </x-form-group>
                        </div>
                        <div class="col-md-4">
                            <x-form-group label="Jenis Kelamin" name="jenis_kelamin">
                                <x-select name="jenis_kelamin">
                                    <option value="">-- Pilih --</option>
                                    <option value="L" {{ old('jenis_kelamin', $pendaftar->jenis_kelamin ?? '') === 'L' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="P" {{ old('jenis_kelamin', $pendaftar->jenis_kelamin ?? '') === 'P' ? 'selected' : '' }}>Perempuan</option>
                                </x-select>
                            </x-form-group>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <x-form-group label="Agama" name="agama">
                                <x-input 
                                    name="agama" 
                                    value="{{ old('agama', $pendaftar->agama ?? '') }}"
                                    placeholder="Agama"
                                />
                            </x-form-group>
                        </div>
                        <div class="col-md-6">
                            <x-form-group label="Tahun Lulus" name="tahun_lulus">
                                <x-input 
                                    name="tahun_lulus" 
                                    value="{{ old('tahun_lulus', $pendaftar->tahun_lulus ?? '') }}"
                                    placeholder="Contoh: 2026"
                                    icon="fas fa-graduation-cap"
                                />
                            </x-form-group>
                        </div>
                    </div>
                </x-section-card>

                {{-- C. DATA AKADEMIK --}}
                <x-section-card title="Data Akademik" icon="fas fa-school" class="mb-4">
                    <x-form-group label="Asal Sekolah" name="asal_sekolah" required="true">
                        <x-input 
                            name="asal_sekolah" 
                            value="{{ old('asal_sekolah', $pendaftar->asal_sekolah ?? '') }}"
                            placeholder="SMP/Sekolah asal"
                            icon="fas fa-school"
                        />
                    </x-form-group>

                    <x-form-group label="Jurusan yang Dipilih" name="jurusan_id" required="true">
                        <x-select name="jurusan_id">
                            <option value="">-- Pilih Jurusan --</option>
                            @foreach($jurusans as $jurusan)
                                <option value="{{ $jurusan->id }}" 
                                    {{ old('jurusan_id', $pendaftar->jurusan_id ?? '') == $jurusan->id ? 'selected' : '' }}>
                                    {{ $jurusan->kode }} - {{ $jurusan->nama }}
                                </option>
                            @endforeach
                        </x-select>
                    </x-form-group>

                    <x-form-group label="Nama Jaringan (Opsional)" name="nama_jaringan">
                        <x-input 
                            name="nama_jaringan" 
                            value="{{ old('nama_jaringan', $pendaftar->nama_jaringan ?? '') }}"
                            placeholder="Nama jaringan/referensi"
                            icon="fas fa-network-wired"
                        />
                    </x-form-group>
                </x-section-card>

                {{-- D. ALAMAT TINGGAL --}}
                <x-section-card title="Alamat Tinggal Lengkap" icon="fas fa-home" class="mb-4">
                    <x-form-group label="Alamat Ringkas" name="alamat" required="true">
                        <x-textarea 
                            name="alamat" 
                            rows="2"
                            placeholder="Alamat ringkas"
                            value="{{ old('alamat', $pendaftar->alamat ?? '') }}"
                        ></x-textarea>
                    </x-form-group>

                    <div class="row">
                        <div class="col-md-6">
                            <x-form-group label="Alamat Jalan" name="alamat_jalan">
                                <x-input 
                                    name="alamat_jalan" 
                                    value="{{ old('alamat_jalan', $pendaftar->alamat_jalan ?? '') }}"
                                    placeholder="Nama jalan dan nomor rumah"
                                />
                            </x-form-group>
                        </div>
                        <div class="col-md-6">
                            <x-form-group label="Dukuh" name="alamat_dukuh">
                                <x-input 
                                    name="alamat_dukuh" 
                                    value="{{ old('alamat_dukuh', $pendaftar->alamat_dukuh ?? '') }}"
                                    placeholder="Nama dukuh"
                                />
                            </x-form-group>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <x-form-group label="RT" name="alamat_rt">
                                <x-input 
                                    name="alamat_rt" 
                                    value="{{ old('alamat_rt', $pendaftar->alamat_rt ?? '') }}"
                                    placeholder="001"
                                />
                            </x-form-group>
                        </div>
                        <div class="col-md-3">
                            <x-form-group label="RW" name="alamat_rw">
                                <x-input 
                                    name="alamat_rw" 
                                    value="{{ old('alamat_rw', $pendaftar->alamat_rw ?? '') }}"
                                    placeholder="002"
                                />
                            </x-form-group>
                        </div>
                        <div class="col-md-6">
                            <x-form-group label="Kelurahan" name="alamat_kelurahan">
                                <x-input 
                                    name="alamat_kelurahan" 
                                    value="{{ old('alamat_kelurahan', $pendaftar->alamat_kelurahan ?? '') }}"
                                    placeholder="Nama kelurahan/desa"
                                />
                            </x-form-group>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <x-form-group label="Kecamatan" name="alamat_kecamatan">
                                <x-input 
                                    name="alamat_kecamatan" 
                                    value="{{ old('alamat_kecamatan', $pendaftar->alamat_kecamatan ?? '') }}"
                                    placeholder="Nama kecamatan"
                                />
                            </x-form-group>
                        </div>
                        <div class="col-md-4">
                            <x-form-group label="Kabupaten" name="alamat_kabupaten">
                                <x-input 
                                    name="alamat_kabupaten" 
                                    value="{{ old('alamat_kabupaten', $pendaftar->alamat_kabupaten ?? '') }}"
                                    placeholder="Nama kabupaten/kota"
                                />
                            </x-form-group>
                        </div>
                        <div class="col-md-4">
                            <x-form-group label="Provinsi" name="alamat_provinsi">
                                <x-input 
                                    name="alamat_provinsi" 
                                    value="{{ old('alamat_provinsi', $pendaftar->alamat_provinsi ?? '') }}"
                                    placeholder="Nama provinsi"
                                />
                            </x-form-group>
                        </div>
                    </div>
                </x-section-card>

                {{-- E. IDENTITAS ORANG TUA --}}
                <x-section-card title="Identitas Orang Tua Calon Murid Baru" icon="fas fa-users" class="mb-4">
                    <h6 class="text-muted mb-3"><i class="fas fa-male me-2"></i>Data Ayah Kandung</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <x-form-group label="Nama Ayah Kandung" name="nama_ayah">
                                <x-input 
                                    name="nama_ayah" 
                                    value="{{ old('nama_ayah', $pendaftar->nama_ayah ?? '') }}"
                                    placeholder="Nama ayah kandung"
                                    icon="fas fa-user"
                                />
                            </x-form-group>
                        </div>
                        <div class="col-md-6">
                            <x-form-group label="Pekerjaan Ayah" name="pekerjaan_ayah">
                                <x-input 
                                    name="pekerjaan_ayah" 
                                    value="{{ old('pekerjaan_ayah', $pendaftar->pekerjaan_ayah ?? '') }}"
                                    placeholder="Pekerjaan ayah"
                                    icon="fas fa-briefcase"
                                />
                            </x-form-group>
                        </div>
                    </div>
                    <x-form-group label="Alamat Ayah" name="alamat_ayah">
                        <x-input 
                            name="alamat_ayah" 
                            value="{{ old('alamat_ayah', $pendaftar->alamat_ayah ?? '') }}"
                            placeholder="Alamat ayah kandung"
                            icon="fas fa-home"
                        />
                    </x-form-group>

                    <h6 class="text-muted mb-3 mt-4"><i class="fas fa-female me-2"></i>Data Ibu Kandung</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <x-form-group label="Nama Ibu Kandung" name="nama_ibu">
                                <x-input 
                                    name="nama_ibu" 
                                    value="{{ old('nama_ibu', $pendaftar->nama_ibu ?? '') }}"
                                    placeholder="Nama ibu kandung"
                                    icon="fas fa-user"
                                />
                            </x-form-group>
                        </div>
                        <div class="col-md-6">
                            <x-form-group label="Pekerjaan Ibu" name="pekerjaan_ibu">
                                <x-input 
                                    name="pekerjaan_ibu" 
                                    value="{{ old('pekerjaan_ibu', $pendaftar->pekerjaan_ibu ?? '') }}"
                                    placeholder="Pekerjaan ibu"
                                    icon="fas fa-briefcase"
                                />
                            </x-form-group>
                        </div>
                    </div>
                    <x-form-group label="Alamat Ibu" name="alamat_ibu">
                        <x-input 
                            name="alamat_ibu" 
                            value="{{ old('alamat_ibu', $pendaftar->alamat_ibu ?? '') }}"
                            placeholder="Alamat ibu kandung"
                            icon="fas fa-home"
                        />
                    </x-form-group>

                    <x-form-group label="No. HP Orang Tua/Wali" name="no_hp_ortu">
                        <x-input 
                            name="no_hp_ortu" 
                            value="{{ old('no_hp_ortu', $pendaftar->no_hp_ortu ?? '') }}"
                            placeholder="08xxxxxxxxxx"
                            icon="fas fa-phone"
                        />
                    </x-form-group>
                </x-section-card>

                {{-- F. DATA WALI --}}
                <x-section-card title="Data Wali" icon="fas fa-user-shield" class="mb-4">
                    <x-alert type="info">
                        <small>Diisi jika murid ikut wali (bukan orang tua kandung)</small>
                    </x-alert>

                    <div class="row">
                        <div class="col-md-4">
                            <x-form-group label="Nama Wali" name="nama_wali">
                                <x-input 
                                    name="nama_wali" 
                                    value="{{ old('nama_wali', $pendaftar->nama_wali ?? '') }}"
                                    placeholder="Nama wali"
                                />
                            </x-form-group>
                        </div>
                        <div class="col-md-4">
                            <x-form-group label="Pekerjaan Wali" name="pekerjaan_wali">
                                <x-input 
                                    name="pekerjaan_wali" 
                                    value="{{ old('pekerjaan_wali', $pendaftar->pekerjaan_wali ?? '') }}"
                                    placeholder="Pekerjaan wali"
                                />
                            </x-form-group>
                        </div>
                        <div class="col-md-4">
                            <x-form-group label="No. HP Wali" name="no_hp_wali">
                                <x-input 
                                    name="no_hp_wali" 
                                    value="{{ old('no_hp_wali', $pendaftar->no_hp_wali ?? '') }}"
                                    placeholder="08xxxxxxxxxx"
                                />
                            </x-form-group>
                        </div>
                    </div>
                    <x-form-group label="Alamat Wali" name="alamat_wali">
                        <x-input 
                            name="alamat_wali" 
                            value="{{ old('alamat_wali', $pendaftar->alamat_wali ?? '') }}"
                            placeholder="Alamat wali"
                        />
                    </x-form-group>
                </x-section-card>

                {{-- Form Actions --}}
                <div class="d-flex gap-3 justify-content-end">
                    <a href="{{ route('pendaftar.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times me-2"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i> {{ isset($pendaftar) ? 'Simpan Perubahan' : 'Simpan Data' }}
                    </button>
                </div>
            </form>
        </div>

        {{-- Sidebar Info --}}
        <div class="col-lg-4">
            <x-info-card type="info" title="Petunjuk Pengisian">
                <ul class="mb-0 ps-3">
                    <li class="mb-2">Field dengan tanda <span class="text-danger">*</span> wajib diisi</li>
                    <li class="mb-2">Pastikan data yang diisi sudah benar</li>
                    <li class="mb-2">Data dapat diubah kembali setelah disimpan</li>
                    <li>Lengkapi semua data untuk verifikasi daftar ulang</li>
                </ul>
            </x-info-card>

            @if(isset($pendaftar))
            <x-section-card title="Informasi Pendaftar" icon="fas fa-info-circle" class="mt-3">
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td class="text-muted">No. Registrasi</td>
                        <td class="fw-bold">{{ $pendaftar->no_registrasi }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Jurusan</td>
                        <td><span class="badge bg-primary">{{ $pendaftar->jurusan }}</span></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Gelombang</td>
                        <td>{{ $pendaftar->gelombang }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Status</td>
                        <td>
                            @if($pendaftar->status_siswa === 'Diterima')
                                <span class="badge bg-success">Diterima</span>
                            @else
                                <span class="badge bg-warning">Belum Daftar Ulang</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </x-section-card>
            @endif
        </div>
    </div>
</div>


@push('scripts')
<script>
    // Debug: Log form submission
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('formPendaftar');
        if (form) {
            form.addEventListener('submit', function(e) {
                console.log('=== FORM SUBMIT ===');
                console.log('Form action:', form.action);
                console.log('Form method:', form.method);
                console.log('Form data:', new FormData(form));
                
                // Log all form fields
                const formData = new FormData(form);
                for (let [key, value] of formData.entries()) {
                    console.log(key + ':', value);
                }
            });
        }
    });

    // Show success modal if session exists
    @if (Session::has('success'))
        document.addEventListener('DOMContentLoaded', function() {
            Modal.alert('{{ addslashes(Session::get('success')) }}', 'Berhasil!', 'success');
        });
    @endif

    @if (Session::has('error'))
        document.addEventListener('DOMContentLoaded', function() {
            Modal.alert('{{ addslashes(Session::get('error')) }}', 'Gagal!', 'danger');
        });
    @endif
</script>
@endpush
