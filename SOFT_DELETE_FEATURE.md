# Fitur Soft Delete Pendaftar

## Ringkasan
Fitur soft delete memungkinkan Administrator menghapus data pendaftar tanpa benar-benar menghapusnya dari database. Data yang dihapus dapat dipulihkan kembali kapan saja.

## Keuntungan
1. **Nomor registrasi tetap berurutan** - Tidak ada gap dalam urutan nomor registrasi baru
2. **Data dapat dipulihkan** - Kesalahan penghapusan dapat dibatalkan
3. **Audit trail lengkap** - Mencatat siapa menghapus, kapan, dan alasan
4. **Keamanan data** - Hanya Administrator yang bisa menghapus

## Cara Menggunakan

### Menghapus Pendaftar
1. Login sebagai **Administrator** (bukan Panitia)
2. Buka menu **Data Pendaftar**
3. Klik tombol **Hapus** (ikon trash merah) di baris pendaftar yang ingin dihapus
4. Konfirmasi penghapusan pada modal yang muncul
5. Data pendaftar akan dipindahkan ke **Data Terhapus**

### Melihat Data Terhapus
1. Login sebagai **Administrator**
2. Klik menu **Data Terhapus** di sidebar
3. Lihat daftar semua pendaftar yang telah dihapus
4. Informasi yang ditampilkan:
   - Nomor registrasi
   - Nama lengkap
   - Jurusan & gelombang
   - Tanggal & waktu dihapus
   - Nama admin yang menghapus
   - Alasan penghapusan

### Memulihkan Data
1. Buka menu **Data Terhapus**
2. Klik tombol **Pulihkan** (hijau) pada data yang ingin dipulihkan
3. Konfirmasi pemulihan
4. Data akan kembali ke daftar **Data Pendaftar** aktif

## Detail Teknis

### Database Changes
**Migration**: `2026_06_04_231213_add_soft_delete_to_pendaftar_table.php`

Kolom yang ditambahkan ke tabel `pendaftar`:
- `deleted_at` (timestamp, nullable) - Waktu penghapusan
- `deleted_by` (bigint, nullable) - ID user yang menghapus (foreign key ke `users.id`)
- `deleted_reason` (text, nullable) - Alasan penghapusan

### Model Changes
**File**: `app/Models/Pendaftar.php`

- Menambahkan trait `SoftDeletes`
- Menambahkan relationship `deletedBy()` ke model User (bukan Admin)
- Kolom `deleted_at`, `deleted_by`, dan `deleted_reason` ditambahkan ke `$fillable`

### Controller Methods
**File**: `app/Http/Controllers/PendaftarController.php`

1. **destroy($id)** - Soft delete pendaftar
   - Menyimpan user ID yang menghapus
   - Menyimpan alasan penghapusan
   - Melakukan soft delete

2. **restore($id)** - Memulihkan data terhapus
   - Mengembalikan data ke status aktif

3. **trashed()** - Menampilkan daftar data terhapus
   - Hanya data dengan `deleted_at` tidak null
   - Include informasi admin yang menghapus

### Routes
**File**: `routes/web.php`

```php
// Soft Delete routes (Administrator only)
Route::middleware(['checkRole:administrator'])->group(function () {
    Route::get('/pendaftar-trashed', [PendaftarController::class, 'trashed'])->name('pendaftar.trashed');
    Route::post('/pendaftar/{id}/restore', [PendaftarController::class, 'restore'])->name('pendaftar.restore');
});
```

Route delete menggunakan resource controller:
```php
Route::resource('pendaftar', PendaftarController::class);
// Otomatis membuat route: DELETE /pendaftar/{id}
```

### Views
1. **resources/views/pendaftar/index.blade.php**
   - Tombol hapus (hanya untuk Administrator)
   - Modal konfirmasi delete menggunakan Modal.confirm()

2. **resources/views/pendaftar/trashed.blade.php**
   - Daftar data terhapus
   - Tombol pulihkan
   - Filter dan pencarian
   - Pagination

3. **resources/views/partials/admin-sidebar.blade.php**
   - Menu "Data Terhapus" (hanya untuk Administrator)

## Hak Akses
- **Administrator**: Bisa hapus, lihat terhapus, dan pulihkan data
- **Panitia**: Tidak bisa menghapus atau mengakses data terhapus

## Instalasi di Server

1. Upload semua file yang diubah
2. Jalankan migration:
   ```bash
   php artisan migrate
   ```
3. Clear cache:
   ```bash
   php artisan config:clear
   php artisan route:clear
   php artisan view:clear
   ```

## Testing

### Test Delete
1. Login sebagai Administrator
2. Buka Data Pendaftar
3. Klik tombol Hapus pada pendaftar
4. Verifikasi modal konfirmasi muncul
5. Klik "Ya, Hapus"
6. Verifikasi data hilang dari daftar
7. Cek menu Data Terhapus, data harus muncul di sana

### Test Restore
1. Buka menu Data Terhapus
2. Klik tombol Pulihkan
3. Verifikasi modal konfirmasi muncul
4. Klik "Ya, Pulihkan"
5. Verifikasi data kembali ke Data Pendaftar

### Test Permissions
1. Login sebagai Panitia
2. Verifikasi tombol Hapus tidak muncul di Data Pendaftar
3. Verifikasi menu Data Terhapus tidak muncul di sidebar

## Troubleshooting

### Tombol Hapus Tidak Muncul
- Pastikan login sebagai Administrator (bukan Panitia)
- Check role di tabel `admins` atau `users`

### Modal Tidak Muncul
- Buka Developer Tools (F12) > Console
- Check error JavaScript
- Pastikan file `public/js/modal.js` ter-load
- Pastikan meta CSRF token ada di `layouts/admin.blade.php`

### Error 403 Forbidden
- Route dilindungi middleware `checkRole:administrator`
- Pastikan login dengan role Administrator

### Error 500 saat Delete
- Check log Laravel: `storage/logs/laravel.log`
- Pastikan migration sudah dijalankan
- Pastikan kolom `deleted_at`, `deleted_by`, `deleted_reason` ada di tabel

## File yang Dimodifikasi

1. **Migration**
   - `database/migrations/2026_06_04_231213_add_soft_delete_to_pendaftar_table.php` (NEW)

2. **Model**
   - `app/Models/Pendaftar.php`

3. **Controller**
   - `app/Http/Controllers/PendaftarController.php`

4. **Routes**
   - `routes/web.php`

5. **Views**
   - `resources/views/pendaftar/index.blade.php`
   - `resources/views/pendaftar/trashed.blade.php` (NEW)
   - `resources/views/layouts/admin.blade.php`
   - `resources/views/partials/admin-sidebar.blade.php`
   - `resources/views/pendaftar/verification-index.blade.php`

## FAQ

**Q: Apakah data benar-benar terhapus dari database?**
A: Tidak. Data masih ada di database, hanya ditandai dengan timestamp `deleted_at`. Data dapat dipulihkan kapan saja.

**Q: Apakah nomor registrasi akan berubah setelah delete?**
A: Tidak. Nomor registrasi pendaftar yang dihapus tidak akan digunakan lagi untuk pendaftar baru. Urutan nomor tetap berurutan tanpa gap.

**Q: Bisakah Panitia menghapus data?**
A: Tidak. Hanya Administrator yang bisa menghapus dan memulihkan data.

**Q: Apakah ada batas waktu untuk restore data?**
A: Tidak ada batas waktu. Data terhapus bisa dipulihkan kapan saja selama belum di-hard delete (dihapus permanen dari database).

**Q: Bagaimana cara hard delete (hapus permanen)?**
A: Saat ini belum ada fitur hard delete melalui UI. Jika diperlukan, bisa dilakukan manual melalui database atau dibuat fitur tambahan.

## Notes
- Fitur ini mengikuti best practice Laravel dengan menggunakan trait `SoftDeletes`
- Semua operasi delete/restore dicatat untuk audit trail
- UI menggunakan komponen modal modern yang sudah ada di sistem
- Kompatibel dengan sistem pagination dan filtering yang ada
