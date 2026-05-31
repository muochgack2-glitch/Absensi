# 🔀 Fitur Kelola Jaringan - Dokumentasi Lengkap

## 📋 Deskripsi
Fitur untuk mengelola dan menggabungkan (merge) data jaringan dengan 2 mode: **Full** (merge semua siswa per jaringan) dan **Selective** (pilih siswa spesifik dari jaringan berbeda).

## 🎯 Fitur Utama

### 1. **Merge Full** - Merge Semua Siswa Per Jaringan
Gabungkan semua siswa dari satu jaringan ke jaringan lain.

**Fitur:**
- ✅ Pilih jaringan sumber (FROM) dan tujuan (TO)
- ✅ Auto-detect jaringan yang mirip/duplikat
- ✅ Preview sebelum merge
- ✅ Konfirmasi dengan warning
- ✅ Simpan ID siswa yang di-merge (untuk undo akurat)

**Use Case:**
- Merge "SMP 1" ke "SMP NEGERI 1" (semua siswa)
- Merge "SD Negeri 01" ke "SD NEGERI 1" (typo/duplikat)

---

### 2. **Merge Selective** - Pilih Siswa Spesifik
Pilih siswa tertentu untuk digabungkan, bisa dari jaringan berbeda.

**Fitur:**
- ✅ **Search & Filter**
  - Search by: No. Registrasi, Nama, Jaringan
  - Filter by: Jaringan
  - Sort by: No. Registrasi, Nama, Jaringan (A-Z / Z-A)
  
- ✅ **Bulk Actions**
  - Pilih Semua (halaman saat ini)
  - Pilih Per Jaringan (modal dengan daftar jaringan)
  - Clear Semua
  
- ✅ **Selection Counter (Sticky)**
  - Tampilkan jumlah siswa terpilih
  - Tombol Clear dan Lanjut Merge
  - Sticky di top saat scroll
  
- ✅ **Preview Detail**
  - Grouped by jaringan asal
  - Tampilkan jumlah per jaringan
  - Total siswa yang akan di-merge
  
- ✅ **Pagination**
  - 50 siswa per halaman
  - Maintain selection across pages

**Use Case:**
- Pilih 3 siswa dari "SMP 1" + 2 siswa dari "SMP 2" → merge ke "SMP NEGERI 1"
- Pilih siswa yang salah input jaringan → pindahkan ke jaringan yang benar
- Pilih siswa berdasarkan kriteria tertentu → gabungkan

---

### 3. **History Merge**
Riwayat lengkap semua merge yang pernah dilakukan.

**Fitur:**
- ✅ Filter by Status (Semua, Aktif, Di-undo)
- ✅ Filter by Tipe (Semua, Full, Selective)
- ✅ Tampilkan detail:
  - Waktu merge
  - Tipe merge (Full/Selective)
  - Jaringan sumber dan tujuan
  - Jumlah siswa
  - User yang melakukan (nama + role)
  - Status (Aktif/Di-undo)
  - Info undo (jika sudah di-undo)
- ✅ Pagination (20 per halaman)

---

### 4. **Undo Merge**
Batalkan merge yang sudah dilakukan dengan akurat.

**Cara Kerja:**
- Sistem simpan ID siswa yang di-merge di field JSON
- Saat undo, kembalikan HANYA siswa yang di-merge (pakai ID)
- Siswa baru yang daftar setelah merge TIDAK terpengaruh

**Contoh:**
```
Merge (10:00): 10 siswa dari "SMP 1" → "SMP NEGERI 1"
Daftar Baru (11:00): 2 siswa baru di "SMP NEGERI 1"
Undo (12:00): Hanya 10 siswa yang di-merge dikembalikan
              2 siswa baru tetap di "SMP NEGERI 1"
```

---

## 🗄️ Struktur Database

### Tabel: `jaringan_merge_history`

| Field | Type | Description |
|-------|------|-------------|
| id | bigint | Primary key |
| merge_type | enum('full','selective') | Tipe merge |
| from_jaringan | varchar(255) | Jaringan sumber (bisa multiple untuk selective) |
| to_jaringan | varchar(255) | Jaringan tujuan |
| affected_count | integer | Jumlah siswa yang di-merge |
| **pendaftar_ids** | **json** | **Array ID siswa yang di-merge** |
| merged_by | bigint | User ID yang merge |
| merged_by_name | varchar(255) | Nama user |
| merged_by_role | varchar(50) | Role user |
| is_undone | boolean | Status undo |
| undone_at | timestamp | Waktu undo |
| undone_by | bigint | User ID yang undo |
| undone_by_name | varchar(255) | Nama user yang undo |
| undone_by_role | varchar(50) | Role user yang undo |
| created_at | timestamp | Waktu merge |
| updated_at | timestamp | - |

**Contoh Data:**

**Full Merge:**
```json
{
  "id": 1,
  "merge_type": "full",
  "from_jaringan": "SMP 1",
  "to_jaringan": "SMP NEGERI 1",
  "affected_count": 10,
  "pendaftar_ids": [1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
  "merged_by": 1,
  "merged_by_name": "Admin",
  "merged_by_role": "administrator",
  "is_undone": false
}
```

**Selective Merge:**
```json
{
  "id": 2,
  "merge_type": "selective",
  "from_jaringan": "SMP 1, SMP 2, SMP 3",
  "to_jaringan": "SMP NEGERI 1",
  "affected_count": 5,
  "pendaftar_ids": [15, 18, 22, 35, 41],
  "merged_by": 1,
  "merged_by_name": "Admin",
  "merged_by_role": "administrator",
  "is_undone": false
}
```

---

## 🛣️ Routes

```php
Route::middleware(['checkRole:administrator,panitia'])->prefix('admin/jaringan')->name('jaringan.')->group(function () {
    // Full Mode
    Route::get('/merge', [JaringanController::class, 'merge'])->name('merge');
    Route::post('/preview', [JaringanController::class, 'preview'])->name('preview');
    Route::post('/process-merge', [JaringanController::class, 'processMerge'])->name('process-merge');
    
    // Selective Mode
    Route::get('/merge-selective', [JaringanController::class, 'mergeSelective'])->name('merge-selective');
    Route::post('/preview-selective', [JaringanController::class, 'previewSelective'])->name('preview-selective');
    Route::post('/process-merge-selective', [JaringanController::class, 'processMergeSelective'])->name('process-merge-selective');
    
    // History & Undo
    Route::get('/history', [JaringanController::class, 'history'])->name('history');
    Route::post('/undo/{id}', [JaringanController::class, 'undo'])->name('undo');
});
```

---

## 📁 Files

**Controller:**
- `app/Http/Controllers/JaringanController.php`

**Models:**
- `app/Models/JaringanMergeHistory.php`

**Views:**
- `resources/views/jaringan/merge.blade.php` (Mode Full)
- `resources/views/jaringan/merge-selective.blade.php` (Mode Selective)
- `resources/views/jaringan/history.blade.php` (History)

**Migrations:**
- `database/migrations/2026_05_31_225147_create_jaringan_merge_history_table.php`
- `database/migrations/2026_05_31_232012_add_merge_details_to_jaringan_merge_history_table.php`

**Routes:**
- `routes/web.php` (section: Kelola Jaringan)

**Sidebar:**
- `resources/views/partials/admin-sidebar.blade.php`

---

## 🎨 UI/UX Flow

### **Mode Full:**
```
1. Pilih Jaringan FROM (dropdown)
2. Pilih Jaringan TO (dropdown)
3. Visual indicator (FROM → TO)
4. Klik "Preview Merge"
5. Modal preview (jumlah + sample 5 siswa)
6. Konfirmasi "Ya, Proses Merge"
7. Success message + reload
```

### **Mode Selective:**
```
1. Search/Filter siswa
2. Centang siswa yang ingin di-merge
   - Manual (satu-satu)
   - Bulk: Pilih Semua
   - Bulk: Pilih Per Jaringan
3. Selection counter muncul (sticky)
4. Klik "Lanjut Merge"
5. Modal: Pilih Jaringan TO
6. Klik "Preview Merge"
7. Modal preview (grouped by jaringan)
8. Konfirmasi "Ya, Proses Merge"
9. Success message + reload
```

---

## 🔒 Keamanan & Validasi

**Role Access:**
- Hanya Administrator dan Panitia
- Middleware: `checkRole:administrator,panitia`

**Validasi:**
- ✅ FROM ≠ TO (tidak bisa merge ke dirinya sendiri)
- ✅ Minimal 1 siswa dipilih (selective)
- ✅ Jaringan tujuan harus dipilih
- ✅ Check if already undone (tidak bisa undo 2x)

**Transaction:**
- Semua operasi merge menggunakan DB transaction
- Auto-rollback jika terjadi error
- Data konsisten

**Audit Trail:**
- Setiap merge tercatat lengkap
- Simpan: siapa, kapan, apa, berapa
- Undo juga tercatat (siapa, kapan)

---

## 📊 Dampak Merge

Merge akan mempengaruhi:
- ✅ Data Pendaftar (field `nama_jaringan`)
- ✅ Laporan (rekap per jaringan)
- ✅ Dashboard (statistik per jaringan)
- ✅ Cetak Formulir (nama jaringan di formulir)
- ✅ Export Excel/PDF (data export)

---

## 🧪 Testing Scenario

### **Test Mode Full:**
1. Login sebagai Administrator/Panitia
2. Buka "Kelola Jaringan" > "Merge Full"
3. Pilih FROM: "SMP 1", TO: "SMP NEGERI 1"
4. Klik Preview → Lihat 10 siswa
5. Proses Merge → Success
6. Cek Data Pendaftar → "SMP 1" kosong, "SMP NEGERI 1" bertambah
7. Cek History → Ada record baru (tipe: Full)
8. Undo → Siswa kembali ke "SMP 1"

### **Test Mode Selective:**
1. Buka "Kelola Jaringan" > "Merge Selective"
2. Search "Ahmad" → Muncul hasil
3. Centang 3 siswa dari jaringan berbeda
4. Klik "Pilih Per Jaringan" → Pilih "SMP 2" → Semua siswa SMP 2 tercentang
5. Total 8 siswa terpilih
6. Klik "Lanjut Merge" → Pilih TO: "SMP NEGERI 1"
7. Preview → Lihat grouped by jaringan
8. Proses Merge → Success
9. Cek Data → 8 siswa pindah ke "SMP NEGERI 1"
10. Cek History → Ada record (tipe: Selective, from: "SMP 1, SMP 2, SMP 3")

### **Test Undo Akurat:**
1. Merge 10 siswa dari "SMP 1" ke "SMP NEGERI 1" (jam 10:00)
2. Tambah 2 siswa baru di "SMP NEGERI 1" (jam 11:00)
3. Total "SMP NEGERI 1" = 15 siswa (10 lama + 5 existing + 2 baru)
4. Undo merge (jam 12:00)
5. Hasil: "SMP 1" = 10 siswa (kembali), "SMP NEGERI 1" = 7 siswa (5 existing + 2 baru)
6. ✅ 2 siswa baru TIDAK terpengaruh undo

---

## 🚀 Changelog

**v2.0.0 - 31 Mei 2026**
- ✅ Tambah Mode Selective (pilih siswa spesifik)
- ✅ Search & Filter siswa
- ✅ Bulk Actions (pilih semua, pilih per jaringan)
- ✅ Selection counter (sticky)
- ✅ Simpan ID siswa di JSON untuk undo akurat
- ✅ Filter history by tipe (Full/Selective)
- ✅ Siswa bisa dari jaringan berbeda (selective)
- ✅ Pagination (50 siswa per halaman)
- ✅ Preview grouped by jaringan (selective)

**v1.0.0 - 31 Mei 2026**
- ✅ Mode Full (merge semua siswa per jaringan)
- ✅ Auto-detect duplicates
- ✅ Preview before merge
- ✅ History merge
- ✅ Undo merge
- ✅ Role access control

---

## 💡 Tips & Best Practices

1. **Gunakan Mode Full** untuk merge jaringan duplikat/typo
2. **Gunakan Mode Selective** untuk koreksi data individual
3. **Selalu Preview** sebelum merge untuk memastikan data benar
4. **Cek History** secara berkala untuk audit
5. **Undo segera** jika ada kesalahan merge
6. **Backup database** sebelum merge dalam jumlah besar

---

## 🐛 Known Issues & Limitations

1. **Selective Undo**: Saat ini undo selective mengembalikan ke jaringan pertama di list `from_jaringan`. Untuk undo yang lebih akurat, perlu simpan original jaringan per siswa.
   
2. **Pagination Selection**: Selection tidak persist across pagination. User harus centang di setiap halaman.

3. **Large Dataset**: Untuk jaringan dengan ribuan siswa, proses merge bisa lambat. Consider adding queue/background job.

---

## 📞 Support

Jika ada pertanyaan atau issue, hubungi tim development.
