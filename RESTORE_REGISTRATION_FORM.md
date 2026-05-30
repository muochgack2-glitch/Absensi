# Restore Registration Form & Receipt

Jika implementasi modal tidak cocok, gunakan salah satu cara berikut untuk restore:

## File yang Dimodifikasi:
1. `resources/views/registration/form.blade.php` - Form pendaftaran
2. `resources/views/registration/receipt.blade.php` - Halaman sukses

## Cara 1: Restore dari Backup (Recommended)

```bash
# Cari file backup
ls resources/views/registration/*.backup-*

# Restore form pendaftaran
cp resources/views/registration/form.blade.php.backup-YYYYMMDD-HHMMSS resources/views/registration/form.blade.php

# Restore halaman sukses
cp resources/views/registration/receipt.blade.php.backup-YYYYMMDD-HHMMSS resources/views/registration/receipt.blade.php
```

## Cara 2: Manual Restore

Jika backup tidak ada, kembalikan perubahan berikut:

### 1. Hapus include modal.js dan CSS
Hapus baris ini (sekitar line 694):
```html
<script src="{{ asset('js/modal.js') }}?v={{ time() }}"></script>

<style>
    /* Modal System Styles */
    ...semua CSS modal...
</style>
```

### 2. Kembalikan form submission script
Ganti script submit form dengan versi lama:
```javascript
// Form submission with validation
document.getElementById('registrationForm').addEventListener('submit', function(e) {
    const nisn = document.getElementById('nisn').value;
    if (nisn.length !== 10) {
        e.preventDefault();
        alert('NISN harus 10 digit!');
        return false;
    }

    // Disable button to prevent double submit
    const btn = document.getElementById('submitBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
});
```

### 3. Kembalikan link Syarat & Ketentuan
Ganti:
```html
<a href="#" onclick="showTermsModal(event)">Syarat & Ketentuan</a>
```

Dengan:
```html
<a href="#">Syarat & Ketentuan</a>
```

### 4. Hapus fungsi showTermsModal
Hapus seluruh fungsi `showTermsModal()` dan `window.showTermsModal = showTermsModal;`

---

## Perubahan yang Dilakukan

### ✅ Fitur Baru yang Ditambahkan:

1. **Modal Konfirmasi Sebelum Submit**
   - Menampilkan ringkasan data sebelum submit
   - User bisa review data sebelum dikirim
   - Mencegah kesalahan input

2. **Modal Syarat & Ketentuan**
   - Menampilkan S&K lengkap dalam modal
   - Lebih user-friendly daripada halaman terpisah
   - Scrollable content

3. **Modal Alert untuk Validasi**
   - Mengganti `alert()` browser dengan modal modern
   - Lebih konsisten dengan design system

### 📁 File yang Dimodifikasi:
- `resources/views/registration/form.blade.php`

### 💾 File Backup:
- `resources/views/registration/form.blade.php.backup-YYYYMMDD-HHMMSS`

---

## Testing

Setelah implementasi, test hal berikut:

1. ✅ Klik tombol "Daftar Sekarang" → Modal konfirmasi muncul
2. ✅ Klik "Syarat & Ketentuan" → Modal S&K muncul
3. ✅ Submit dengan NISN < 10 digit → Modal warning muncul
4. ✅ Klik "Ya, Daftar Sekarang" di modal → Form tersubmit
5. ✅ Klik "Periksa Lagi" di modal → Modal tertutup, form tidak submit
6. ✅ Klik backdrop/ESC → Modal tertutup

---

## Kontak

Jika ada masalah atau pertanyaan, hubungi developer.
