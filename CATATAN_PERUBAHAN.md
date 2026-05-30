# Catatan Perubahan Project SPMB

Dokumen ini dibuat sebagai ringkasan kerja agar pengembangan bisa dilanjutkan walaupun riwayat chat hilang.

## Update Terbaru (2026-05-30)

### User Management UI/UX Modernization ✅
- **Status**: Completed
- **Deskripsi**: Modernisasi halaman User Management Index dengan komponen Blade modern
- **Perubahan**:
  - ✅ Semua buttons diganti dengan `<x-button>` dan `<x-icon-button>`
  - ✅ Filter section menggunakan `<x-section-card>` + `<x-form-group>` + `<x-input>` / `<x-select>`
  - ✅ Table menggunakan `<x-table>` + `<x-table-actions>`
  - ✅ Implementasi `<x-empty-state>` untuk tabel kosong
  - ✅ Implementasi `<x-pagination>` untuk pagination
  - ✅ Tambah 20+ icons untuk better visual recognition
  - ✅ Icons di badges (shield, tie, check, X, ban)
  - ✅ Icons di kolom tabel (user, email, clock, calendar)
  - ✅ Tooltips pada semua action buttons
  - ✅ Better spacing dan visual hierarchy
- **Files Modified**:
  - `resources/views/users/index.blade.php` (complete overhaul)
- **Documentation**:
  - `USER_MANAGEMENT_UI_IMPROVEMENTS.md` (detailed documentation)
- **Testing**: ✅ All features tested and working
- **Deployment**: Ready for production

### Settings Page UI/UX Modernization ✅
- **Status**: Completed
- **Deskripsi**: Modernisasi halaman Settings dengan komponen Blade modern, icons, dan UX yang lebih baik
- **Perubahan**:
  - ✅ Semua form input diganti dengan komponen modern (`<x-form-group>`, `<x-input>`, `<x-select>`)
  - ✅ Tambah 20+ icons Font Awesome untuk visual recognition
  - ✅ Implementasi `<x-empty-state>` untuk tabel jurusan kosong
  - ✅ Loading state pada tombol simpan (spinner + disabled)
  - ✅ Tab persistence menggunakan localStorage
  - ✅ Theme preset auto-sync dengan color pickers
  - ✅ File upload preview untuk logo dan favicon
  - ✅ Modal alerts untuk success/error messages
  - ✅ Improved jurusan management card dengan gradient background
  - ✅ Better visual hierarchy dan spacing
  - ✅ Responsive design untuk semua ukuran layar
- **Files Modified**:
  - `resources/views/settings/index.blade.php` (complete overhaul)
- **Documentation**:
  - `SETTINGS_UI_IMPROVEMENTS.md` (detailed documentation)
  - `SETTINGS_IMPROVEMENTS_VISUAL.md` (visual guide)
- **Testing**: ✅ All features tested and working
- **Deployment**: Ready for production

---

## 1. Settings Sistem

- Settings awalnya memakai file JSON `storage/app/private/settings/system.json`.
- Settings sudah dipindahkan ke database memakai model `App\Models\SettingSystem` dan tabel `setting_system`.
- `SettingsController` sekarang membaca/menyimpan settings ke database.
- `LandingController` sekarang mengambil settings dari database.
- View print yang sebelumnya membaca JSON juga sudah diarahkan ke `SettingSystem::instance()->toSettingsArray()`.

File penting:
- `app/Models/SettingSystem.php`
- `app/Http/Controllers/SettingsController.php`
- `app/Http/Controllers/LandingController.php`
- `database/migrations/2026_05_20_000008_expand_setting_system_table.php`
- `database/migrations/2026_05_20_000009_add_branding_and_document_fields_to_setting_system.php`
- `database/migrations/2026_05_20_000010_add_favicon_to_setting_system.php`
- `database/migrations/2026_05_20_000011_add_theme_colors_to_setting_system.php`

## 2. Branding, Favicon, Tema, dan Night Mode

- Halaman settings sudah dibuat tab Bootstrap standar.
- Tab settings:
  - Profil
  - Pendaftaran
  - Branding
  - Dokumen
- Branding sekarang mendukung:
  - website sekolah
  - Instagram URL
  - logo sekolah
  - favicon
  - preset warna tema
  - warna utama dan warna kedua
- Favicon dinamis memakai `resources/views/partials/favicon.blade.php`.
- Tema warna admin memakai `resources/views/partials/admin-theme-vars.blade.php`.
- Night/dark mode admin memakai `resources/views/partials/admin-theme.blade.php` dan disimpan di `localStorage`.

File penting:
- `resources/views/settings/index.blade.php`
- `resources/views/partials/favicon.blade.php`
- `resources/views/partials/admin-theme.blade.php`
- `resources/views/partials/admin-theme-vars.blade.php`

## 3. Layout Admin dan Navigasi

- Navbar dan sidebar admin sudah dipindahkan ke partial bersama.
- Layout utama admin dibuat di `resources/views/layouts/admin.blade.php`.
- Halaman admin utama sekarang memakai `@extends('layouts.admin')`.
- Sidebar tidak lagi punya menu `Tambah Pendaftar`; aksi tambah tetap ada di halaman Data Pendaftar.
- Active menu sidebar otomatis berdasarkan route.
- Mobile navigation admin memakai offcanvas drawer.
- Di mobile hanya ada satu tombol `Menu` agar tidak rancu.

File penting:
- `resources/views/layouts/admin.blade.php`
- `resources/views/partials/admin-navbar.blade.php`
- `resources/views/partials/admin-sidebar.blade.php`

Halaman yang sudah memakai layout admin:
- `resources/views/dashboard/index.blade.php`
- `resources/views/settings/index.blade.php`
- `resources/views/reports/index.blade.php`
- `resources/views/pendaftar/index.blade.php`
- `resources/views/pendaftar/create.blade.php`
- `resources/views/pendaftar/edit.blade.php`
- `resources/views/pendaftar/verification-index.blade.php`
- `resources/views/pendaftar/daftar-ulang-verification.blade.php`

Halaman print/PDF tidak memakai layout admin karena butuh layout print khusus.

## 4. Alur Pendaftaran dan Biodata Lengkap

Konsep alur yang disepakati:

```text
Calon siswa daftar data awal
↓
Admin melengkapi biodata lengkap
↓
status_data: awal / lengkap / terverifikasi
↓
Formulir cetak memakai biodata lengkap
↓
Daftar ulang diverifikasi
↓
status_siswa: Diterima
```

- Form landing/public tetap data awal agar ringan.
- Admin Tambah Pendaftar juga data awal.
- Admin Edit Pendaftar menjadi tempat melengkapi biodata lengkap.
- Setelah admin tambah pendaftar, sistem redirect ke Data Pendaftar dan muncul popup:
  - Data sudah dibuat
  - tombol `Edit Data Lengkap`
  - tombol `Tidak, nanti saja`
- Jika pilih `Edit Data Lengkap`, redirect ke halaman edit.

Kolom biodata tambahan:
- `status_data`
- `email`
- `no_telepon`
- `nik`
- `tempat_lahir`
- `tanggal_lahir`
- `jenis_kelamin`
- `agama`
- `tahun_lulus`
- `nama_ayah`
- `pekerjaan_ayah`
- `nama_ibu`
- `pekerjaan_ibu`
- `no_hp_ortu`

File penting:
- `database/migrations/2026_05_20_000012_add_complete_biodata_to_pendaftar_table.php`
- `app/Models/Pendaftar.php`
- `app/Http/Controllers/RegistrationController.php`
- `app/Http/Controllers/PendaftarController.php`
- `resources/views/pendaftar/edit.blade.php`
- `resources/views/pendaftar/index.blade.php`
- `resources/views/pendaftar/print-formulir.blade.php`

## 5. Status Siswa dan Badge

Logika status utama:

- Jika `status_siswa === 'Diterima'`
  - teks badge: `Diterima`
  - warna: hijau
- Selain itu:
  - teks badge: `Belum Daftar Ulang`
  - warna: merah

Perubahan dilakukan di:
- `resources/views/pendaftar/index.blade.php`
- `resources/views/pendaftar/verification-index.blade.php`
- `resources/views/pendaftar/print-formulir.blade.php`
- `resources/views/landing/index.blade.php`
- `public/css/landing.css`

Catatan:
- Landing page sebelumnya menampilkan status belum daftar ulang dengan warna hijau; sudah diperbaiki menjadi merah.

## 6. Seeder

### AdminSeeder

- `AdminSeeder` sekarang membaca username/password/nama dari `.env`.
- Menggunakan `updateOrCreate()` supaya aman dijalankan berkali-kali.
- Variabel `.env` yang ditambahkan:

```env
ADMIN_USERNAME=admin
ADMIN_PASSWORD=admin123
ADMIN_NAME="Administrator Sistem"

STAFF_USERNAME=staff1
STAFF_PASSWORD=staff123
STAFF_NAME="Staff Pendaftaran"
```

- `.env.example` juga sudah ditambahkan template dengan password `change-this-password`.

File penting:
- `database/seeders/AdminSeeder.php`
- `.env`
- `.env.example`

### PendaftarSeeder

- Data pendaftar yang ada di database saat itu sudah diekspor ke `database/seeders/PendaftarSeeder.php`.
- Total data yang diekspor: 6 data pendaftar.
- Seeder juga menyimpan data logistik pendaftar.
- `DatabaseSeeder` sudah memanggil:

```php
PendaftarSeeder::class,
```

File penting:
- `database/seeders/PendaftarSeeder.php`
- `database/seeders/DatabaseSeeder.php`

Catatan:
- `php artisan migrate` aman, tidak menghapus data.
- `php artisan migrate:fresh --seed` akan menghapus database lalu mengisi ulang dari seeder.

## 7. Perintah yang Sering Dipakai

Clear Blade view:

```bash
php artisan view:clear
```

Migrate biasa, data aman:

```bash
php artisan migrate
```

Reset database dan isi ulang dari seeder, data lama hilang:

```bash
php artisan migrate:fresh --seed
```

Seed admin saja:

```bash
php artisan db:seed --class=AdminSeeder
```

## 8. Catatan Lanjutan

Hal yang mungkin masih bisa dilanjutkan:

- Membuat fitur manajemen admin/password dari dashboard.
- Membersihkan CSS lokal lama di halaman admin agar semua komponen semakin konsisten.
- Merapikan ulang file print yang masih memiliki beberapa teks mojibake pada tombol print, jika terlihat di browser.
- Mengecek ulang `PendaftarSeeder` jika data pendaftar yang diekspor berisi data test/dummy yang tidak ingin dibawa ke production.

Jika chat hilang, instruksi untuk lanjut:

```text
Baca CATATAN_PERUBAHAN.md lalu lanjutkan dari kondisi project saat ini.
```

## 9. Update Tambahan Setelah Ringkasan Awal

### 9.1 Konsistensi Layout dan Navigasi Admin

- Sidebar menu `Tambah Pendaftar` dihapus agar tidak duplikatif dengan tombol aksi di halaman `Data Pendaftar`.
- Route `pendaftar.create` sekarang dianggap bagian dari menu `Data Pendaftar` (active state tetap menyala di menu Data Pendaftar).
- Mobile navigation diubah ke offcanvas drawer dengan satu tombol `Menu` (menghilangkan dua ikon hamburger yang sebelumnya membingungkan).
- Tombol logout navbar diberi class khusus agar tidak ikut mengecil saat ada CSS `.btn-sm` di halaman tertentu.
- Spacing desktop layout disesuaikan agar konten tidak terlalu menempel ke kanan (lebih centered dan rapi).

File terkait:
- `resources/views/partials/admin-sidebar.blade.php`
- `resources/views/partials/admin-navbar.blade.php`
- `resources/views/layouts/admin.blade.php`

### 9.2 Status Badge dan Alur Pendaftaran

- Badge status utama diseragamkan:
  - `Belum Daftar Ulang` = merah
  - `Diterima` = hijau
- Landing page status checker diperbaiki agar status belum daftar ulang tidak tampil hijau.
- Setelah admin tambah pendaftar:
  - redirect ke `Data Pendaftar`
  - muncul popup konfirmasi:
    - `Edit Data Lengkap`
    - `Tidak, nanti saja`
  - jika pilih `Edit Data Lengkap`, redirect ke halaman edit pendaftar tersebut.
- Halaman edit menampilkan notifikasi info jika `status_data` masih `awal`.

File terkait:
- `app/Http/Controllers/PendaftarController.php`
- `resources/views/pendaftar/index.blade.php`
- `resources/views/pendaftar/edit.blade.php`
- `resources/views/pendaftar/verification-index.blade.php`
- `resources/views/landing/index.blade.php`
- `public/css/landing.css`

### 9.3 Seeder dan Konfigurasi Admin

- `AdminSeeder` sudah memakai `updateOrCreate()` dan membaca kredensial dari `.env`.
- Variabel admin/staff ditambahkan ke `.env` dan `.env.example`.
- `PendaftarSeeder` sudah dibuat dari data database saat itu dan didaftarkan ke `DatabaseSeeder`.

File terkait:
- `database/seeders/AdminSeeder.php`
- `database/seeders/PendaftarSeeder.php`
- `database/seeders/DatabaseSeeder.php`
- `.env`
- `.env.example`

### 9.4 Polish UI Halaman List & Pengaturan

- Halaman **Pengaturan** dipoles:
  - Header lebih tegas, card/tab/form spacing lebih rapi.
- Halaman **Data Pendaftar** & **Verifikasi Daftar Ulang** dirombak total secara visual:
  - Penambahan **Page Header Card** dengan judul dan subtitle yang elegan.
  - Penambahan **Summary Grid** (4 kartu statistik mini) di bagian atas untuk melihat ringkasan data (Total, Belum Daftar Ulang, Diterima, dll) secara instan.
  - Penambahan **Data Panel** (wrapper tabel) dengan header gradient yang modern.
  - Penggunaan **Pill Badges** untuk Jurusan, Ukuran Kaos, dan Status agar lebih menarik.
  - Baris tabel kini memiliki efek hover yang halus.
- Normalisasi font admin ke `Inter` di semua halaman utama.
- CSS duplikat "chrome admin" dibersihkan.

File terkait:
- `resources/views/pendaftar/index.blade.php`
- `resources/views/pendaftar/verification-index.blade.php`
- `resources/views/settings/index.blade.php`
- `resources/views/dashboard/index.blade.php`
- `resources/views/layouts/admin.blade.php`

## 10. To-Do Berikutnya (Rencana Lanjutan)

Berikut adalah beberapa hal yang bisa dikerjakan selanjutnya untuk menyempurnakan sistem:

- [ ] **Polish Visual Dashboard**: Menyesuaikan tampilan dashboard agar seirama dengan halaman data pendaftar (header card, summary stat yang lebih modern).
- [ ] **Manajemen Admin**: Menambahkan fitur untuk ganti password admin atau kelola akun petugas langsung dari dashboard tanpa edit `.env`.
- [ ] **Audit Print & PDF**: Mengecek ulang semua template cetak (Registrasi, Formulir, Ambil Barang) untuk memastikan tidak ada lagi karakter "mojibake" (tulisan rusak) dan layout-nya pas di kertas F4/A4.
- [ ] **Import Data Pendaftar**: Menambahkan fitur import data dari Excel/CSV untuk memudahkan migrasi data lama.
- [ ] **Validasi Lanjutan**: Menambahkan validasi lebih ketat pada pengisian biodata lengkap (format NIK, nomor telepon, dll).
- [ ] **Audit PendaftarSeeder**: Memastikan data yang ada di seeder adalah data dummy yang aman, atau mengosongkannya jika ingin deploy bersih.

---
*Instruksi Besok: Baca CATATAN_PERUBAHAN.md lalu lanjutkan ke salah satu poin To-Do di atas.*

### 9.5 Perbaikan Icon/Teks Rusak (Mojibake)

- Dilakukan cleanup karakter rusak (mojibake) di berbagai halaman yang menampilkan ikon/simbol.
- Semua kemunculan pola rusak seperti `ðŸ`, `â†`, `âœ`, `Â·`, `ã€` sudah dibersihkan.
- Hasil pengecekan ulang: **tidak ada lagi match mojibake** di seluruh `resources/views/**/*.blade.php`.

File yang terdampak cleanup:
- `resources/views/reports/pdf.blade.php`
- `resources/views/reports/index.blade.php`
- `resources/views/registration/print-receipt.blade.php`
- `resources/views/pendaftar/print-registrasi.blade.php`
- `resources/views/pendaftar/print-ambil-barang.blade.php`
- `resources/views/pendaftar/print-formulir.blade.php`
- `resources/views/pendaftar/daftar-ulang-verification.blade.php`
- `resources/views/prints/bukti-ambil-barang.blade.php`

### 9.6 Manajemen Jurusan (Versi Rapi)

- Ditambahkan master jurusan dengan tabel baru `jurusan` (kode, nama, aktif, kuota).
- Ditambahkan kolom `jurusan_id` pada tabel `pendaftar` dan relasi Eloquent.
- Seeder jurusan dibuat untuk mengisi jurusan default (MPLB/AKL/BUSANA) dan ditambahkan ke `DatabaseSeeder`.
- Form pendaftaran public dan admin create/edit diubah agar memilih jurusan dari database (`jurusan_id`), bukan enum hardcode.
- Data pendaftar lama disinkronkan ke `jurusan_id` berdasarkan kolom string `jurusan`.

File terkait:
- `database/migrations/2026_05_20_000013_create_jurusans_table.php`
- `database/migrations/2026_05_20_000014_add_jurusan_id_to_pendaftar_table.php`
- `app/Models/Jurusan.php`
- `app/Models/Pendaftar.php`
- `database/seeders/JurusanSeeder.php`
- `database/seeders/DatabaseSeeder.php`
- `app/Http/Controllers/RegistrationController.php`
- `app/Http/Controllers/PendaftarController.php`
- `resources/views/registration/form.blade.php`
- `resources/views/pendaftar/create.blade.php`
- `resources/views/pendaftar/edit.blade.php`

### 9.7 Refactor Laporan ke Master Jurusan Dinamis

- Modul laporan direfactor agar menggunakan jurusan dari tabel master, bukan hardcode enum.
- Filter laporan sekarang memakai `jurusan_id` (id jurusan), bukan string jurusan.
- Rekap per jurusan dan rekap per jaringan pada laporan web sekarang mengikuti jurusan aktif di database.
- Export CSV jaringan sekarang membuat kolom jurusan secara dinamis berdasarkan jurusan aktif.
- `ReportController` sudah diperbarui untuk mendukung jurusan dinamis pada:
  - halaman laporan utama
  - stats API laporan
  - export excel pendaftar
  - export excel rekap jaringan
  - export PDF laporan
- Validasi create/update pendaftar juga sudah disesuaikan ke `jurusan_id`.

Catatan:
- Masih ada satu sisa variabel tampilan di `resources/views/reports/pdf.blade.php` yang menampilkan label berdasarkan `$jurusan` (string), namun proses filter utama sudah bekerja via `jurusan_id`.

File terkait:
- `app/Http/Controllers/ReportController.php`
- `app/Http/Controllers/PendaftarController.php`
- `app/Http/Controllers/RegistrationController.php`
- `resources/views/reports/index.blade.php`
- `resources/views/reports/pdf.blade.php`
- `resources/views/registration/form.blade.php`
- `resources/views/pendaftar/create.blade.php`
- `resources/views/pendaftar/edit.blade.php`

### 9.8 Penyelesaian Detail Laporan Jurusan Dinamis

- Filter jurusan di halaman laporan sudah memakai `jurusan_id` dan label singkatan jurusan dari master.
- Rekap per jurusan dan rekap jaringan di halaman laporan web/PDF sudah mengikuti jurusan aktif dari tabel master.
- Ikon/tombol rusak kecil yang tersisa pada halaman laporan juga dibersihkan (contoh ikon jam pasir, tombol print PDF, ikon jaringan).

File terkait:
- `resources/views/reports/index.blade.php`
- `resources/views/reports/pdf.blade.php`
- `CATATAN_PERUBAHAN.md`

- Final cleanup manual pada file laporan telah selesai. Semua sisa karakter mojibake/icon rusak di `resources/views/reports/index.blade.php` dan `resources/views/reports/pdf.blade.php` sudah dibersihkan.

- Cleanup mojibake pada template print pendaftar dan template print lama sudah dituntaskan. Semua pencarian pola rusak pada `resources/views/pendaftar/print-*.blade.php` dan `resources/views/prints/*.blade.php` sekarang **No matches found**.

### 9.9 Sinkronisasi Landing Page dengan Status & Jurusan Master

- Statistik landing disesuaikan dengan logika status terbaru:
  - Total
  - Belum Daftar Ulang
  - Diterima
  - Progress diterima (% dari total)
  - (opsional) data_awal tersedia di controller
- Kuota jurusan di landing sekarang diambil dari master jurusan (tabel `jurusan`) jika tersedia.
- `LandingController` sekarang menyediakan `jurusanQuota` dari jurusan aktif.

File terkait:
- `app/Http/Controllers/LandingController.php`
- `resources/views/landing/index.blade.php`

- Landing page stats ditambah item **Data Awal** (status_data = awal) agar panitia tahu berapa banyak data yang perlu dilengkapi.

File terkait:
- `resources/views/landing/index.blade.php`

### 9.10 Sisa Hardcode Jurusan Dibersihkan (Tahap 1-3)

- Filter jurusan pada halaman verifikasi daftar ulang dibuat dinamis dari tabel master jurusan.
- Receipt & print receipt pendaftaran public sekarang menampilkan jurusan secara dinamis (kode + nama) dari session (`jurusan_nama`).
- Template print formulir dan bukti ambil barang tidak lagi hardcode MPLB/AKL/BUSANA; jurusan ditampilkan/di-checklist dari master jurusan.
- Print controller sekarang eager-load relasi `masterJurusan` untuk menghindari query tambahan.

File terkait:
- `app/Http/Controllers/PendaftarController.php`
- `resources/views/pendaftar/verification-index.blade.php`
- `app/Http/Controllers/RegistrationController.php`
- `resources/views/registration/receipt.blade.php`
- `resources/views/registration/print-receipt.blade.php`
- `resources/views/pendaftar/print-formulir.blade.php`
- `resources/views/pendaftar/print-ambil-barang.blade.php`

### 9.11 Manajemen Master Jurusan via Dashboard Admin

- Ditambahkan tab "Master Jurusan" di halaman Pengaturan (`/settings`).
- Admin kini dapat:
  - Menambah jurusan baru (kode, nama, kuota).
  - Mengedit data jurusan yang sudah ada (kode, nama, kuota, status aktif/nonaktif).
  - Menghapus jurusan (jika belum ada pendaftar yang terkait).
- `SettingsController` diperbarui dengan metode `storeJurusan`, `updateJurusan`, dan `destroyJurusan`.
- Route baru ditambahkan di `routes/web.php` untuk operasi CRUD jurusan.
- Halaman Pengaturan kini menampilkan daftar semua jurusan dan form untuk manipulasi.

File terkait:
- `app/Http/Controllers/SettingsController.php`
- `resources/views/settings/index.blade.php`
- `routes/web.php`

### 9.12 Pemindahan Kuota dari Tab Pendaftaran ke Master Jurusan

- Field kuota jurusan di tab Pendaftaran halaman Pengaturan dihapus agar tidak dobel dengan fitur Master Jurusan.
- Tab Pendaftaran sekarang hanya menampilkan info bahwa kuota jurusan dikelola melalui tab Master Jurusan.
- `SettingsController` dibersihkan dari validasi dan proses update `jurusan_quota` pada form pengaturan umum.
- Perubahan kuota jurusan sekarang sepenuhnya dilakukan lewat form edit jurusan di tab Master Jurusan.

File terkait:
- `resources/views/settings/index.blade.php`
- `app/Http/Controllers/SettingsController.php`

### 9.13 Master Jurusan Digabung ke Tab Pendaftaran

- Tab terpisah `Master Jurusan` di halaman Pengaturan dihapus.
- Fitur manajemen jurusan dipindahkan langsung ke dalam tab `Pendaftaran` agar pengaturan alur pendaftaran dan master jurusan ada di satu tempat.
- Tab Pendaftaran sekarang berisi dua bagian utama:
  - Konfigurasi pendaftaran umum.
  - Master Jurusan (tambah, edit, ubah kuota, aktif/nonaktif, hapus).
- Struktur navigasi settings jadi lebih ringkas karena admin tidak perlu pindah tab terpisah untuk mengelola jurusan.

File terkait:
- `resources/views/settings/index.blade.php`

### 9.14 Tampilan Statistik Landing Diseragamkan dengan Dashboard

- Bagian statistik di landing page diubah agar gaya visualnya serupa dengan kartu statistik bagian atas pada dashboard admin.
- Statistik landing sekarang menampilkan 4 kartu gaya dashboard:
  - Total Pendaftar
  - Pendaftar Baru (Hari Ini)
  - Belum Daftar Ulang
  - Sudah Daftar Ulang
- `LandingController` ditambahkan perhitungan `baru_hari_ini` untuk mengisi kartu statistik harian.
- CSS landing diperbarui dengan varian `dashboard-style` untuk meniru struktur warna border-top, ikon badge, dan tipografi kartu dashboard.

File terkait:
- `app/Http/Controllers/LandingController.php`
- `resources/views/landing/index.blade.php`
- `public/css/landing.css`

### 9.15 Penyeragaman Tampilan Warna (Landing, Login, Admin)

- Warna utama dan sekunder untuk seluruh halaman (Landing, Login, Admin) kini mengacu pada pengaturan tema di `SettingSystem` (Pengaturan > Branding).
- Membuat partial baru `resources/views/partials/theme-vars.blade.php` yang berisi variabel CSS (--primary, --secondary, --theme-primary, --theme-secondary) berdasarkan pengaturan tema.
- Partial `theme-vars` di-include di:
  - `resources/views/layouts/app.blade.php` (untuk Landing Page)
  - `resources/views/auth/login.blade.php` (untuk Login Admin)
  - `resources/views/layouts/admin.blade.php` (mengambil dari `admin-theme-vars` yang kini merujuk ke `theme-vars`).
- CSS di `public/css/landing.css` dan `resources/views/auth/login.blade.php` diperbarui untuk menggunakan variabel CSS tersebut.
- CSS di `resources/views/partials/admin-theme-vars.blade.php` juga disesuaikan agar merujuk ke variabel tema global.
- Konfirmasi pengecekan: semua file utama sudah menggunakan variabel tema bersama, sisa hardcode warna lama sudah dibersihkan.

File terkait:
- `resources/views/partials/theme-vars.blade.php` (Baru)
- `resources/views/layouts/app.blade.php`
- `resources/views/auth/login.blade.php`
- `resources/views/layouts/admin.blade.php`
- `resources/views/partials/admin-theme-vars.blade.php`
- `public/css/landing.css`

### 9.16 Penyesuaian Istilah Lama ke "SPMB" (Sistem Penerimaan Murid Baru)

- Istilah lama "PPDB" dan "Siswa Baru" diubah total di seluruh halaman user interface (web portal pendaftaran, formulir, kuitansi cetak, PDF laporan, halaman login, settings, dan create pendaftar) agar konsisten dengan istilah utama "SPMB (Sistem Penerimaan Murid Baru)".
- Melakukan replacement teks pada:
  - `resources/views/settings/index.blade.php` (tab lama dinormalisasi menjadi Pendaftaran).
  - `resources/views/partials/meta.blade.php` & `resources/views/layouts/app.blade.php` (metadata & title global).
  - `resources/views/landing/index.blade.php` (topbar text, langkah-langkah, statistik label, whatsapp template).
  - `resources/views/auth/login.blade.php` (label login tagline).
  - `app/Http/Controllers/LandingController.php` (title halaman & meta keywords).
  - `resources/views/pendaftar/create.blade.php` (input manual murid baru).
  - File cetakan/kuitansi (`resources/views/prints/formulir-lengkap.blade.php`, `resources/views/prints/bukti-registrasi.blade.php`, `resources/views/prints/bukti-ambil-barang.blade.php`).
  - File preview pendaftaran (`print-registrasi`, `print-formulir`, `receipt`, `print-receipt`).
- Semua referensi istilah lama telah dinormalisasi menjadi "Sistem Penerimaan Murid Baru (SPMB)".

File terkait:
- Hampir seluruh template di `resources/views/**/*.blade.php`
- `app/Http/Controllers/LandingController.php`

### 9.17 Final Sweep Terminologi Lama

- Dilakukan sapu bersih final seluruh template Blade agar tidak ada lagi istilah lama yang tidak sesuai dengan branding aplikasi.
- Semua istilah dinormalisasi ke:
  - "SPMB (Sistem Penerimaan Murid Baru)"
  - "Pendaftaran"
  - "Murid Baru"
- `welcome.blade.php` juga diperbarui (title, meta description, tagline) agar konsisten dengan branding SPMB.
- ID tab Settings yang sebelumnya masih bernama teknis `tab-ppdb` / `pane-ppdb` diganti ke `tab-pendaftaran` / `pane-pendaftaran` untuk konsistensi istilah.
- Hasil verifikasi pencarian regex pada `resources/views/**/*.blade.php`: **No matches found** untuk istilah lama yang dibersihkan.

File terkait:
- `resources/views/welcome.blade.php`
- `resources/views/settings/index.blade.php`
- `resources/views/**/*.blade.php` (berbagai file yang sudah dibersihkan pada tahap sebelumnya)

### 9.19 Penyeragaman Tampilan Halaman Pendaftaran (form.blade.php)

- Halaman pendaftaran online publik (`resources/views/registration/form.blade.php`) kini sepenuhnya mengikuti aturan visual tema global yang dinamis.
- Meng-include partial `theme-vars` untuk mendapatkan variabel warna aktif (`--theme-primary` dan `--theme-secondary`).
- Mengganti warna background, border, glow, shadow, dan gradient header form pendaftaran untuk menggunakan warna yang disinkronkan dari data Branding sekolah.
- Menghapus background gradient statis lama dan orbs melayang yang bertabrakan dengan warna tema global baru, sehingga visual lebih rapi dan konsisten dengan Landing Page serta halaman Login.

File terkait:
- `resources/views/registration/form.blade.php`

### 9.20 Logo dan Footer Halaman Pendaftaran Dibuat Dinamis

- Halaman pendaftaran publik (`resources/views/registration/form.blade.php`) diperbarui agar menampilkan logo sekolah dari pengaturan sistem jika tersedia.
- Header icon kini memiliki fallback:
  - Jika logo tersedia -> tampilkan logo sekolah.
  - Jika logo belum ada -> tampilkan ikon default `fa-graduation-cap`.
- Footer halaman pendaftaran kini tidak hardcode lagi:
  - Nomor telepon mengambil dari `school_phone` atau fallback `school_contact`.
  - Email mengambil dari `school_email`.
  - Teks tambahan footer mengambil dari `print_footer_text`.
- Judul header form juga dibuat dinamis mengikuti `school_name`.

File terkait:
- `resources/views/registration/form.blade.php`

### 9.21 Penambahan Field Formulir Lengkap (Alamat Rinci, KIP, Orang Tua, Wali)

- Menambahkan field baru sesuai format formulir kertas yang digunakan sekolah:
  - No. KIP (`no_kip`).
  - Alamat rinci: `alamat_jalan`, `alamat_dukuh`, `alamat_rt`, `alamat_rw`, `alamat_kelurahan`, `alamat_kecamatan`, `alamat_kabupaten`, `alamat_provinsi`.
  - Alamat orang tua: `alamat_ayah`, `alamat_ibu`.
  - Data wali: `nama_wali`, `pekerjaan_wali`, `alamat_wali`.
- Form edit admin (`resources/views/pendaftar/edit.blade.php`) diperluas untuk input field-field tersebut.
- Template cetak formulir (`resources/views/pendaftar/print-formulir.blade.php`) diperbarui agar menampilkan data KIP, alamat rinci, identitas ayah/ibu/wali sesuai susunan A/B.

File terkait:
- `database/migrations/2026_05_26_000016_add_formulir_detail_fields_to_pendaftar_table.php`
- `app/Models/Pendaftar.php`
- `app/Http/Controllers/PendaftarController.php`
- `resources/views/pendaftar/edit.blade.php`
- `resources/views/pendaftar/print-formulir.blade.php`

### 9.22 Sub-Header Kategori di Form Edit Data Lengkap Pendaftar

- Halaman edit data lengkap pendaftar (`resources/views/pendaftar/edit.blade.php`) ditambahkan sub-header per kategori agar lebih mudah dipahami admin.
- Kategori sub-header yang ditambahkan:
  1. **A. Registrasi & Data Pribadi** — NISN, Nama Lengkap, NIK, KIP, Status Data
  2. **B. Data Diri Lengkap** — Tempat/Tanggal Lahir, Jenis Kelamin, Agama, Email, No. Telepon
  3. **C. Data Akademik** — Asal Sekolah, Tahun Lulus, Jurusan, Nama Jaringan
  4. **D. Alamat Tinggal Lengkap** — Alamat Ringkas, Jalan, Dukuh, RT, RW, Kelurahan, Kecamatan, Kabupaten, Provinsi
  5. **E. Identitas Orang Tua** — Data Ayah (Nama, Pekerjaan, Alamat), Data Ibu (Nama, Pekerjaan, Alamat), No. HP Ortu
  6. **F. Data Wali** — Nama, Pekerjaan, Alamat Wali (diisi jika murid ikut wali)

File terkait:
- `resources/views/pendaftar/edit.blade.php`

### 9.23 Cetak Formulir Disesuaikan ke Ukuran Kertas F4

- Template cetak formulir (`resources/views/pendaftar/print-formulir.blade.php`) dirapikan untuk ukuran kertas F4 (215mm × 330mm).
- Perubahan yang dilakukan:
  - Menambahkan `@page { size: 215mm 330mm; margin: 12mm 14mm; }` agar browser/printer langsung mengenali ukuran F4.
  - Mengubah `max-width` halaman dari `210mm` (A4) menjadi `215mm` (F4).
  - Menyesuaikan ukuran font, padding, margin, dan gap seluruh elemen agar konten muat rapi dalam satu halaman F4.
  - Isi data formulir tidak berubah sama sekali.

File terkait:
- `resources/views/pendaftar/print-formulir.blade.php`

### 9.24 Cetak Formulir Lengkap — Finalisasi

Halaman cetak formulir (`resources/views/pendaftar/print-formulir.blade.php`) telah selesai dirombak total dan difinalisasi dengan perubahan berikut:

**Struktur & Layout:**
- Ukuran kertas F4 (215mm × 330mm) dengan `@page` CSS.
- Margin `@page` top/bottom 5mm, kiri/kanan 14mm.
- Print otomatis scale 90% via `html { zoom: 0.9; }` di `@media print`.
- Foto 3x4 cm diposisikan di kanan sejajar dengan data registrasi section A.
- 3 kotak info registrasi (No. Registrasi, Gelombang, Tanggal Pendaftaran) di atas section A.

**Struktur Konten:**
- **A. Registrasi Murid Baru** — nomor urut 1–10 (Jurusan, Nama, JK, TTL, Agama, HP/WA, Alamat rinci, Asal Sekolah, NISN, KIP).
- **B. Identitas Orang Tua Calon Murid Baru** — Data Ayah, Ibu, dan Wali (nama, pekerjaan, alamat).
- **C. Status Daftar Ulang & Logistik** — 3 kotak (Daftar Ulang, Kain Seragam, Kaos).
- **D. Pernyataan** — teks pernyataan dinamis dengan nama sekolah dari pengaturan.
- **Tanda Tangan** — Petugas (kiri, garis untuk diisi manual) dan Calon Murid Baru (kanan, nama pendaftar).

**Detail Teknis:**
- Semua sub-header (A, B, C, D) punya shading tipis `#f1f5f9` dan rata kiri dengan konten.
- Field kosong tampil teks *Belum diisi* (abu-abu miring) agar admin tahu mana yang perlu dilengkapi.
- Titik-titik diganti garis solid tipis `#e2e8f0`.
- Tanggal tanda tangan dinamis dari `now()->format('d F Y')` dan kota dari pengaturan `document_city`.
- Jurusan yang dipilih tampil sebagai `KODE - Nama Jurusan` (dinamis dari master jurusan).
- Footer dinamis dari pengaturan sistem.

File terkait:
- `resources/views/pendaftar/print-formulir.blade.php`

### 9.25 Cetak Bukti Registrasi Admin — Finalisasi

Halaman cetak bukti registrasi dari admin (`resources/views/pendaftar/print-registrasi.blade.php`) telah selesai dirombak dan difinalisasi.

**Struktur 1 Lembar F4 (2 bagian setengah F4):**

**Bagian Atas — Bukti Registrasi Pendaftaran:**
- Kop sekolah dinamis (logo, nama, alamat, kontak dari pengaturan).
- Nomor registrasi full width dengan box berwarna.
- Tabel data pendaftar: Nama, NISN, Asal Sekolah, Jurusan (dinamis dari master jurusan), Gelombang, Tanggal Daftar.
- Tanda tangan kanan: kota + tanggal cetak dinamis + Panitia SPMB + garis.
- Footer dinamis dari pengaturan sistem.

**Bagian Bawah — Berkas Pendaftaran (Di tempel di stopmap):**
- Kop sekolah sama persis dengan bagian atas.
- Judul: BERKAS PENDAFTARAN (Di tempel di stopmap).
- Data dinamis: No. Pendaftaran / Gel, Jurusan, Nama Lengkap, Asal Sekolah, Alamat Rumah.
- Kotak foto 3x4.
- Keterangan:
  - Poin 1: Diterima / Tidak Diterima (dinamis dari `status_siswa`).
  - Poin 2: Daftar Ulang Tanggal (kosong untuk diisi manual).
- Tanda tangan kanan: kota + tanggal cetak dinamis + Panitia SPMB + garis.
- Footer dinamis dari pengaturan sistem.

**Detail Teknis:**
- Dipisahkan garis putus-putus (`dashed`) di antara dua bagian.
- Masing-masing bagian presisi `148mm` (setengah F4 = 330mm - 16mm margin / 2).
- `@page` margin `8mm` atas/bawah, `12mm` kiri/kanan.
- Font dokumen atas `12px`, dokumen bawah `13px`.
- Garis tabel `solid #e2e8f0` (tidak dotted).

File terkait:
- `resources/views/pendaftar/print-registrasi.blade.php`

### 9.26 Cetak Bukti Pendaftaran Public — Finalisasi

- Template cetak pendaftaran public dirombak agar konsisten tema dan pengaturan sistem.
- Header dinamis dari `SettingSystem` (logo, nama sekolah, alamat, kontak).
- Warna mengikuti `resources/views/partials/theme-vars.blade.php`.
- Nomor registrasi tampil dalam box khusus.
- Data pendaftar dari session `registration_data`.
- QR code otomatis jika library tersedia (fallback aman).
- Tanda tangan format panitia + kota/tanggal dinamis.
- Footer dinamis dari `print_footer_text`.

File terkait:
- `resources/views/registration/print-receipt.blade.php`

### 9.27 Cetak Bukti Ambil Barang Admin — Finalisasi

- Template cetak bukti ambil barang dirombak total agar rapi, dinamis, dan siap cetak F4.
- Ukuran kertas F4 ditambahkan via `@page`.
- Header dinamis mengikuti pengaturan sekolah dari `SettingSystem`.
- Judul + nomor registrasi + QR disatukan dalam 1 baris kompak untuk hemat ruang.
- Tabel barang disederhanakan: hanya Kain Seragam dan Kaos (Dasi/Badge dihapus).
- Garis tabel dinormalisasi menjadi solid.
- Format tanda tangan diseragamkan dengan cetak formulir (Kiri: Penerima, Kanan: kota/tanggal/Petugas Logistik/garis).
- Footer dibuat 1 baris agar ringkas.
- Salinan dibuat rangkap 2 (Atas: Pendaftar, Bawah: Arsip Sekolah) dipisahkan garis putus-putus.

File terkait:
- `resources/views/pendaftar/print-ambil-barang.blade.php`
