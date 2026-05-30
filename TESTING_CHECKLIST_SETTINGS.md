# Testing Checklist - Settings Page

## 🧪 Testing Instructions

Sebelum push ke production, test semua fitur berikut:

---

## ✅ Tab 1: Profil Sekolah

### Visual Check
- [ ] Semua input fields memiliki icon
- [ ] Required fields memiliki tanda asterisk (*)
- [ ] Spacing dan alignment rapi
- [ ] Icons tampil dengan benar

### Functionality Check
- [ ] Isi semua field dan klik "Simpan Pengaturan"
- [ ] Loading state muncul (spinner + "Menyimpan...")
- [ ] Modal success muncul setelah save
- [ ] Data tersimpan dengan benar
- [ ] Refresh page, data masih ada

---

## ✅ Tab 2: Pendaftaran

### Visual Check
- [ ] Form "Tambah Jurusan Baru" memiliki gradient background
- [ ] Border dashed terlihat
- [ ] Icon plus-circle di header
- [ ] Table wrapper memiliki border rounded

### Functionality Check - Tambah Jurusan
- [ ] Isi Kode: "TEST"
- [ ] Isi Nama: "Test Jurusan"
- [ ] Isi Kuota: "50"
- [ ] Klik "Tambah"
- [ ] Modal success muncul
- [ ] Jurusan baru muncul di table

### Functionality Check - Edit Jurusan
- [ ] Edit field Kode/Nama/Kuota di table
- [ ] Toggle switch Aktif/Nonaktif
- [ ] Klik "Simpan" pada row
- [ ] Modal success muncul
- [ ] Perubahan tersimpan

### Functionality Check - Hapus Jurusan
- [ ] Klik tombol "Hapus" pada jurusan test
- [ ] Modal confirm muncul dengan pesan yang benar
- [ ] Klik "Ya, Hapus"
- [ ] Modal success muncul
- [ ] Jurusan terhapus dari table

### Empty State Check
- [ ] Hapus semua jurusan
- [ ] Empty state component muncul
- [ ] Icon graduation cap tampil
- [ ] Message dan description tampil

---

## ✅ Tab 3: Branding

### Visual Check - Media Sosial
- [ ] Semua input memiliki icon yang sesuai
  - [ ] Website: globe icon
  - [ ] Instagram: instagram icon
  - [ ] YouTube: youtube icon
  - [ ] TikTok: tiktok icon
- [ ] Section header "Media Sosial" tampil dengan icon

### Visual Check - Warna Tema
- [ ] Section header "Warna Tema" tampil dengan icon
- [ ] Color preview circles tampil di samping color picker
- [ ] Preview circles menampilkan warna yang benar

### Functionality Check - Theme Preset
- [ ] Pilih preset "Biru"
- [ ] Color pickers auto-update ke warna biru
- [ ] Preview circles berubah warna
- [ ] Pilih preset lain, warna berubah lagi

### Visual Check - Logo & Favicon
- [ ] Section header "Logo & Favicon" tampil dengan icon
- [ ] File preview boxes tampil dengan styling yang benar
- [ ] Existing logo/favicon tampil jika ada

### Functionality Check - File Upload
- [ ] Pilih file logo baru
- [ ] Preview baru muncul di bawah input
- [ ] Klik "Simpan Pengaturan"
- [ ] Loading state muncul
- [ ] Modal success muncul
- [ ] Logo baru tersimpan dan tampil

---

## ✅ Tab 4: Dokumen

### Visual Check
- [ ] Semua input memiliki icon
- [ ] Help text tampil di bawah input
- [ ] Section header tampil dengan icon

### Functionality Check
- [ ] Isi semua field dokumen
- [ ] Klik "Simpan Pengaturan"
- [ ] Loading state muncul
- [ ] Modal success muncul
- [ ] Data tersimpan dengan benar

---

## ✅ Tab Persistence

### Test Steps
- [ ] Buka tab "Branding"
- [ ] Refresh page (F5)
- [ ] Tab "Branding" masih aktif (tidak kembali ke "Profil")
- [ ] Pindah ke tab "Dokumen"
- [ ] Refresh page
- [ ] Tab "Dokumen" masih aktif

---

## ✅ Loading State

### Test Steps
- [ ] Isi form di tab manapun
- [ ] Klik "Simpan Pengaturan"
- [ ] Button berubah menjadi:
  - [ ] Text: "Menyimpan..."
  - [ ] Icon: spinner (berputar)
  - [ ] State: disabled (tidak bisa diklik)
- [ ] Setelah save selesai, button kembali normal

---

## ✅ Modal Alerts

### Success Modal
- [ ] Save settings berhasil
- [ ] Modal success muncul dengan:
  - [ ] Title: "Berhasil!"
  - [ ] Icon: checkmark (hijau)
  - [ ] Message: "Pengaturan sistem berhasil disimpan."
  - [ ] Button: "OK"

### Error Modal (Test dengan data invalid)
- [ ] Kosongkan field required
- [ ] Klik "Simpan Pengaturan"
- [ ] Modal error muncul dengan:
  - [ ] Title: "Gagal!"
  - [ ] Icon: X (merah)
  - [ ] Message: error message
  - [ ] Button: "OK"

---

## ✅ Responsive Design

### Desktop (≥768px)
- [ ] Layout 3 kolom untuk form fields
- [ ] Spacing rapi
- [ ] Semua elemen terlihat dengan baik

### Tablet (768px - 991px)
- [ ] Layout 2 kolom untuk form fields
- [ ] Spacing masih rapi
- [ ] Tidak ada overflow

### Mobile (<768px)
- [ ] Layout 1 kolom (full width)
- [ ] Tabs masih bisa diklik
- [ ] Form masih usable
- [ ] Buttons tidak terpotong

---

## ✅ Browser Compatibility

Test di browser berikut:
- [ ] Chrome (latest)
- [ ] Firefox (latest)
- [ ] Edge (latest)
- [ ] Safari (jika ada Mac)

---

## ✅ Console Check

### Browser Console
- [ ] Buka Developer Tools (F12)
- [ ] Buka tab Console
- [ ] Tidak ada error merah
- [ ] Tidak ada warning penting

### Network Tab
- [ ] Buka tab Network
- [ ] Refresh page
- [ ] Semua assets load dengan status 200
- [ ] Tidak ada 404 errors

---

## ✅ Accessibility

### Keyboard Navigation
- [ ] Tab key berpindah antar input dengan benar
- [ ] Enter key submit form
- [ ] Esc key close modal

### Screen Reader (Optional)
- [ ] Labels terbaca dengan benar
- [ ] Required fields teridentifikasi
- [ ] Error messages terbaca

---

## 🐛 Known Issues to Check

### Potential Issues
- [ ] Form tidak submit → Check console errors
- [ ] Modal tidak muncul → Check modal.js loaded
- [ ] Icons tidak tampil → Check Font Awesome loaded
- [ ] Color picker tidak sync → Check JavaScript errors
- [ ] Tab tidak persist → Check localStorage enabled

---

## 📝 Test Results

### Test Date: _______________
### Tested By: _______________
### Browser: _______________
### Device: _______________

### Issues Found:
1. _______________________________________________
2. _______________________________________________
3. _______________________________________________

### Status:
- [ ] ✅ All tests passed - Ready for production
- [ ] ⚠️ Minor issues found - Can deploy with notes
- [ ] ❌ Critical issues found - Need fixes before deploy

---

## 🚀 Deployment Checklist

Setelah semua test passed:
- [ ] Backup database
- [ ] Git pull origin main
- [ ] php artisan optimize:clear
- [ ] Test di production environment
- [ ] Verify all features work
- [ ] Monitor for errors

---

**Note**: Jika menemukan bug, catat di section "Issues Found" dan report ke developer.
