# Testing Opsi A - Link Filter Tahun

## 📦 Persiapan Data Test

### Step 1: Jalankan Seeder Test
```bash
php artisan db:seed --class=TestTahunAjaran2027Seeder
```

**Seeder ini akan:**
- ✅ Membuat tahun ajaran 2027/2028 (ACTIVE)
- ✅ Arsipkan tahun ajaran 2026/2027 (ARCHIVED)
- ✅ Membuat 15 pendaftar dummy untuk tahun 2027/2028
- ✅ Update setting active tahun ke 2027/2028

---

## 🧪 Skenario Testing:

### Test 1: Halaman Data Pendaftar (Default)
**URL:** `http://127.0.0.1:8000/pendaftar`

**Expected:**
- Badge menampilkan: "Tahun Aktif: 2027/2028" (hijau)
- Data yang muncul: pendaftar tahun 2027/2028 saja
- Tidak ada link "Kembali ke tahun aktif"

---

### Test 2: Halaman Data Pendaftar dengan Parameter Tahun
**URL:** `http://127.0.0.1:8000/pendaftar?tahun=2026/2027`

**Expected:**
- Badge menampilkan: "Tahun 2026/2027" (abu-abu)
- Data yang muncul: pendaftar tahun 2026/2027 (166 siswa)
- Ada link "← Kembali ke tahun aktif"

---

### Test 3: Dari Halaman Detail Tahun Pelajaran
**Langkah:**
1. Buka: `http://127.0.0.1:8000/admin/tahun-ajaran/1` (Tahun 2026/2027)
2. Scroll ke bawah sampai tabel pendaftar
3. Klik link "Lihat semua data pendaftar tahun 2026/2027"

**Expected:**
- Redirect ke: `/pendaftar?tahun=2026/2027`
- Badge: "Tahun 2026/2027"
- Data: 166 pendaftar tahun 2026/2027

---

### Test 4: Dashboard
**URL:** `http://127.0.0.1:8000/dashboard`

**Expected:**
- Hanya tampilkan data tahun 2027/2028 (tahun aktif)
- Recent pendaftar: kosong (atau hanya tahun 2027/2028)
- Statistik: hanya tahun aktif

---

## ✅ Checklist:

- [ ] Test 1: Default tahun aktif berfungsi
- [ ] Test 2: Parameter ?tahun= bekerja
- [ ] Test 3: Link dari detail tahun ajaran correct
- [ ] Test 4: Dashboard filter tahun aktif
- [ ] Link "Kembali ke tahun aktif" berfungsi
- [ ] Badge warna sesuai (hijau = aktif, abu = arsip)

---

## 🐛 Jika Ada Bug:

1. Check variabel `$selectedTahun` dan `$activeTahun` terkirim ke view
2. Check query `where('tahun_ajaran', ...)` jalan
3. Check route parameter `?tahun=` dipass dengan benar

---

## 🧹 Cleanup Setelah Testing

### Step Final: Hapus Data Test & Restore
```bash
php artisan db:seed --class=CleanupTestTahunAjaran2027Seeder
```

**Cleanup ini akan:**
- ✅ Hapus 15 pendaftar tahun 2027/2028
- ✅ Hapus tahun ajaran 2027/2028
- ✅ Restore tahun ajaran 2026/2027 sebagai ACTIVE
- ✅ Update setting kembali ke 2026/2027

**⚠️ JANGAN LUPA JALANKAN CLEANUP INI!**
