# Quick Testing Guide - Soft Delete Feature

## 🚀 Quick Start Testing (5 Minutes)

### Step 1: Check Browser Console (F12)
Open browser Developer Tools before testing to catch any JavaScript errors.

### Step 2: Test as Administrator
1. Login dengan role **administrator**
2. Buka menu **Data Pendaftar**
3. Lihat apakah tombol **Hapus** (merah, ikon trash) muncul di setiap baris
   - ✅ **Pass**: Tombol muncul
   - ❌ **Fail**: Tombol tidak muncul → Check role user

### Step 3: Test Delete Function
1. Klik tombol **Hapus** pada salah satu pendaftar
2. Modal konfirmasi harus muncul dengan:
   - Title: "Konfirmasi Hapus"
   - Nama pendaftar
   - Nomor registrasi
   - Dua tombol: "Batal" dan "Ya, Hapus"
3. Klik **"Ya, Hapus"**
4. Page akan refresh dan pendaftar hilang dari list
   - ✅ **Pass**: Data hilang dan ada message sukses
   - ❌ **Fail**: Error atau tidak ada perubahan → Check console

### Step 4: Check Sidebar Menu
1. Lihat sidebar kiri
2. Harus ada menu baru: **"Data Terhapus"** dengan ikon restore
   - ✅ **Pass**: Menu muncul
   - ❌ **Fail**: Menu tidak ada → Check sidebar code

### Step 5: View Trashed Data
1. Klik menu **"Data Terhapus"**
2. Page akan menampilkan data yang barusan dihapus
3. Check informasi yang ditampilkan:
   - Nomor registrasi
   - Nama lengkap
   - Tanggal/waktu dihapus
   - Nama admin yang menghapus
   - Tombol **Pulihkan** (hijau)

### Step 6: Test Restore
1. Klik tombol **Pulihkan** (hijau)
2. Modal konfirmasi harus muncul
3. Klik **"Ya, Pulihkan"**
4. Data akan kembali ke daftar aktif
5. Buka menu **Data Pendaftar** → Data harus kembali muncul
   - ✅ **Pass**: Data berhasil dipulihkan
   - ❌ **Fail**: Error atau data tidak kembali

### Step 7: Test as Panitia (Optional)
1. Logout
2. Login dengan role **panitia**
3. Buka menu **Data Pendaftar**
4. Tombol **Hapus** TIDAK boleh muncul
5. Menu **Data Terhapus** di sidebar TIDAK boleh ada
   - ✅ **Pass**: Fitur tidak accessible untuk Panitia
   - ❌ **Fail**: Panitia bisa akses fitur delete

---

## 🐛 Common Issues & Quick Fixes

### Issue: Tombol Hapus Tidak Muncul
**Possible Causes**:
1. Login bukan sebagai Administrator
   - **Fix**: Login dengan user yang role = 'administrator'

2. Blade cache
   - **Fix**: `php artisan view:clear`

### Issue: Modal Tidak Muncul Saat Klik Hapus
**Possible Causes**:
1. JavaScript error
   - **Fix**: Buka Console (F12), lihat error message
   - **Fix**: Refresh halaman (Ctrl+F5 untuk hard refresh)

2. modal.js tidak load
   - **Fix**: Check di Network tab apakah `modal.js` ter-load

3. CSRF token tidak ada
   - **Fix**: View page source, cari `<meta name="csrf-token"`
   - **Fix**: Jika tidak ada, restart development server

### Issue: Error 419 (CSRF Token Mismatch)
**Fix**:
```bash
php artisan cache:clear
php artisan config:clear
```
Kemudian refresh browser (Ctrl+F5)

### Issue: Error 500 Saat Delete
**Possible Causes**:
1. Migration belum dijalankan
   - **Fix**: `php artisan migrate`

2. Database issue
   - **Fix**: Check `storage/logs/laravel.log`

### Issue: Menu "Data Terhapus" Tidak Muncul
**Possible Causes**:
1. Login bukan Administrator
   - **Fix**: Login sebagai administrator

2. Blade cache
   - **Fix**: `php artisan view:clear`

### Issue: jQuery Error: "$ is not defined"
**This Should NOT Happen** (sudah diperbaiki)
- **If it happens**: Report immediately, ada file yang terlewat

---

## 📋 Testing Checklist

Print atau copy checklist ini untuk testing:

```
[ ] Browser console open (F12)
[ ] Login sebagai Administrator
[ ] Tombol Hapus muncul di Data Pendaftar
[ ] Klik Hapus → Modal muncul
[ ] Modal menampilkan nama & no reg yang benar
[ ] Klik "Ya, Hapus" → Data hilang dari list
[ ] Success message muncul
[ ] Menu "Data Terhapus" ada di sidebar
[ ] Klik "Data Terhapus" → Page terbuka
[ ] Data yang dihapus muncul di list
[ ] Info penghapusan lengkap (tanggal, user)
[ ] Klik "Pulihkan" → Modal muncul
[ ] Klik "Ya, Pulihkan" → Data restored
[ ] Buka Data Pendaftar → Data kembali muncul
[ ] Test with Panitia role → Fitur tidak accessible
[ ] No errors in console
```

---

## 🔍 Debugging Tips

### Browser Console
Press **F12** → Tab **Console**

**Good Signs** ✅:
- No red error messages
- "Modal.confirm called" appears when clicking delete
- "Confirm callback executed" appears after confirming

**Bad Signs** ❌:
- Red error messages (especially "$ is not defined")
- "CSRF token not found"
- 419 or 500 errors

### Laravel Log
Check `storage/logs/laravel.log` untuk server-side errors.

Recent errors ada di bagian paling bawah file.

### Network Tab (F12)
Lihat request yang gagal:
- Status 419 → CSRF issue
- Status 403 → Permission issue
- Status 500 → Server error

---

## ✅ Success Criteria

Feature dianggap sukses jika:
1. ✅ Administrator bisa delete pendaftar
2. ✅ Modal konfirmasi muncul dengan benar
3. ✅ Data masuk ke "Data Terhapus"
4. ✅ Administrator bisa restore data
5. ✅ Panitia TIDAK bisa akses fitur delete
6. ✅ Tidak ada JavaScript error di console
7. ✅ Pagination dan filter bekerja normal
8. ✅ Registration number tidak berubah

---

## 📱 Contact

Jika ada issue yang tidak bisa diselesaikan:
1. Screenshot error message dari console
2. Copy text dari `storage/logs/laravel.log` (bagian error terakhir)
3. Catat langkah-langkah yang dilakukan sebelum error
4. Report ke developer

---

**Remember**: DO NOT PUSH to repository until all tests pass! ✋
