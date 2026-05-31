# 🔀 Fitur Kelola Jaringan

## Deskripsi
Fitur untuk mengelola dan menggabungkan (merge) data jaringan yang duplikat atau mirip dalam sistem SPMB. Fitur ini membantu memperkecil data ganda di daftar jaringan dan menjaga konsistensi data.

## Akses
- **Role**: Administrator dan Panitia
- **Menu**: Kelola Jaringan (sidebar)
  - Merge Jaringan
  - History Merge

## Fitur Utama

### 1. Merge Jaringan
Halaman untuk menggabungkan data jaringan yang duplikat atau mirip.

**Fitur:**
- ✅ **Auto-detect Duplicates**: Sistem otomatis mendeteksi nama jaringan yang mirip
- ✅ **Search & Filter**: Cari jaringan berdasarkan nama
- ✅ **Sort Options**: Urutkan berdasarkan nama atau jumlah pendaftar
- ✅ **Preview Before Merge**: Lihat preview data yang akan terpengaruh sebelum merge
- ✅ **Konfirmasi**: Popup konfirmasi dengan warning sebelum merge
- ✅ **Sample Data**: Tampilkan 5 sample pendaftar yang akan terpengaruh

**Cara Kerja:**
1. Pilih jaringan sumber (FROM) - jaringan yang akan digabungkan
2. Pilih jaringan target (TO) - jaringan tujuan penggabungan
3. Klik "Preview Merge" untuk melihat data yang akan terpengaruh
4. Review preview dan konfirmasi merge
5. Sistem akan:
   - Update semua data pendaftar dari jaringan sumber ke jaringan target
   - Simpan log history merge
   - Tampilkan notifikasi sukses

**Normalisasi:**
Sistem menggunakan normalisasi untuk mendeteksi duplikat:
- Uppercase semua huruf
- Trim whitespace
- Hapus karakter spesial
- Cek similarity menggunakan similar_text()

### 2. History Merge
Halaman untuk melihat riwayat penggabungan jaringan.

**Fitur:**
- ✅ **Statistics Cards**: Total merge, aktif, dan di-undo
- ✅ **Filter**: Filter berdasarkan status (semua, aktif, di-undo)
- ✅ **Detail History**: Lihat detail setiap merge
- ✅ **Undo Function**: Batalkan merge yang sudah dilakukan
- ✅ **Pagination**: Navigasi data history

**Informasi yang Ditampilkan:**
- Waktu merge
- Jaringan sumber dan target
- Jumlah data yang terpengaruh
- User yang melakukan merge (nama dan role)
- Status (aktif/di-undo)
- Informasi undo (jika sudah di-undo)

### 3. Undo Merge
Fitur untuk membatalkan merge yang sudah dilakukan.

**Cara Kerja:**
1. Buka halaman History Merge
2. Klik tombol "Undo" pada history yang ingin dibatalkan
3. Review detail merge di popup konfirmasi
4. Konfirmasi undo
5. Sistem akan:
   - Kembalikan data pendaftar ke jaringan sumber
   - Update status history menjadi "di-undo"
   - Simpan informasi user yang melakukan undo
   - Tampilkan notifikasi sukses

**Catatan:**
- Hanya merge yang belum di-undo yang bisa di-undo
- Undo bersifat permanen dan akan mengubah data di database

## Dampak Merge

Merge jaringan akan mempengaruhi:
- ✅ **Data Pendaftar**: Field `nama_jaringan` akan diupdate
- ✅ **Laporan**: Rekap per jaringan akan otomatis terupdate
- ✅ **Cetak Formulir**: Nama jaringan di formulir akan berubah
- ✅ **Export Excel/PDF**: Data export akan menggunakan nama jaringan baru
- ✅ **Dashboard**: Statistik per jaringan akan terupdate

## Keamanan

**Transaction:**
- Semua operasi merge menggunakan database transaction
- Jika terjadi error, semua perubahan akan di-rollback

**Logging:**
- Setiap merge dicatat di tabel `jaringan_merge_history`
- Informasi yang disimpan:
  - Jaringan sumber dan target
  - Jumlah data yang terpengaruh
  - User yang melakukan merge (ID, nama, role)
  - Waktu merge
  - Status undo
  - User yang melakukan undo (jika ada)

**Role Access:**
- Hanya Administrator dan Panitia yang bisa akses
- Menggunakan middleware `checkRole:administrator,panitia`

## Database

**Tabel: jaringan_merge_history**
```sql
- id (primary key)
- from_jaringan (varchar 255)
- to_jaringan (varchar 255)
- affected_count (integer)
- merged_by (foreign key ke users)
- merged_by_name (varchar 255)
- merged_by_role (varchar 50)
- is_undone (boolean, default false)
- undone_at (timestamp, nullable)
- undone_by (foreign key ke users, nullable)
- undone_by_name (varchar 255, nullable)
- undone_by_role (varchar 50, nullable)
- created_at
- updated_at
```

## Routes

```php
// Kelola Jaringan - Only for Administrator and Panitia
Route::middleware(['checkRole:administrator,panitia'])->prefix('admin/jaringan')->name('jaringan.')->group(function () {
    Route::get('/merge', [JaringanController::class, 'merge'])->name('merge');
    Route::post('/preview', [JaringanController::class, 'preview'])->name('preview');
    Route::post('/process-merge', [JaringanController::class, 'processMerge'])->name('process-merge');
    Route::get('/history', [JaringanController::class, 'history'])->name('history');
    Route::post('/undo/{id}', [JaringanController::class, 'undo'])->name('undo');
});
```

## Files

**Controller:**
- `app/Http/Controllers/JaringanController.php`

**Models:**
- `app/Models/JaringanMergeHistory.php`

**Views:**
- `resources/views/jaringan/merge.blade.php`
- `resources/views/jaringan/history.blade.php`

**Migration:**
- `database/migrations/2026_05_31_225147_create_jaringan_merge_history_table.php`

**Routes:**
- `routes/web.php` (section: Kelola Jaringan)

**Sidebar:**
- `resources/views/partials/admin-sidebar.blade.php` (menu: Kelola Jaringan)

## Testing

**Test Scenario:**
1. ✅ Login sebagai Administrator atau Panitia
2. ✅ Buka menu "Kelola Jaringan" > "Merge Jaringan"
3. ✅ Coba auto-detect duplicates
4. ✅ Pilih jaringan sumber dan target
5. ✅ Preview merge
6. ✅ Konfirmasi dan proses merge
7. ✅ Cek data pendaftar apakah sudah terupdate
8. ✅ Cek laporan apakah sudah terupdate
9. ✅ Buka "History Merge"
10. ✅ Coba undo merge
11. ✅ Cek data pendaftar apakah sudah kembali

**Edge Cases:**
- Merge dengan jaringan yang sama (sumber = target) → Error
- Merge jaringan yang tidak ada pendaftar → Warning
- Undo merge yang sudah di-undo → Disabled
- User dengan role selain Administrator/Panitia → 403 Forbidden

## Changelog

**v1.0.0 - 31 Mei 2026**
- ✅ Initial release
- ✅ Fitur merge jaringan
- ✅ Auto-detect duplicates
- ✅ Preview before merge
- ✅ History merge
- ✅ Undo merge
- ✅ Role access control (Administrator & Panitia)
- ✅ Transaction & logging
- ✅ Responsive UI dengan theme support
