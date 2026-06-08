# Cara Testing Fitur WA Gateway Reset & Reconnect

## Persiapan
1. Pastikan Node.js WA Gateway server sudah running di aaPanel
2. Pastikan Laravel app sudah running
3. Login sebagai user dengan role `administrator` atau `admin_wa`

## Test Scenario 1: Reset When Connected (Normal Case)

### Langkah-langkah:
1. Buka browser dan akses `/whatsapp` (WA Gateway Dashboard)
2. Pastikan status menunjukkan **"Connected"** (badge hijau)
3. Hover mouse ke tombol **"Reset & Reconnect"** - akan muncul tooltip info
4. Klik tombol **"Reset & Reconnect"**
5. Muncul konfirmasi dialog: "Yakin ingin reset koneksi WhatsApp? Anda perlu scan QR code ulang."
6. Klik **OK**

### Expected Result:
- ✅ Tombol berubah menjadi "Resetting..." dengan spinner
- ✅ Muncul alert hijau: "Koneksi berhasil direset. Generating QR code baru..."
- ✅ Setelah 3 detik, status berubah menjadi **"Waiting QR Scan"** (badge kuning)
- ✅ QR code section muncul otomatis di bawah status
- ✅ QR code ter-display dengan baik
- ✅ Tombol kembali normal

### Verifikasi:
1. Buka WhatsApp di HP
2. Tap menu (⋮) → "Perangkat Tertaut"
3. Tap "Tautkan Perangkat"
4. Scan QR code yang muncul
5. Status berubah menjadi **"Connected"** (badge hijau)
6. QR section hilang otomatis

---

## Test Scenario 2: Reset When Disconnected

### Simulasi Disconnect:
**Cara 1 - Logout dari HP:**
1. Buka WhatsApp di HP
2. Tap menu (⋮) → "Perangkat Tertaut"
3. Pilih device "SPMB Gateway"
4. Tap "Keluar"
5. Dashboard akan show status **"Disconnected"** (badge merah)

**Cara 2 - Stop Node Server (untuk test ekstrim):**
1. SSH ke server aaPanel
2. Run: `pm2 stop wa-gateway` (atau nama process PM2 Anda)
3. Dashboard akan show status **"Disconnected"** dengan pesan error

### Langkah Test:
1. Pastikan status **"Disconnected"**
2. Klik tombol **"Reset & Reconnect"**
3. Konfirmasi dialog

### Expected Result:
- **Jika Node server running:**
  - ✅ Sama seperti Scenario 1
  - ✅ QR muncul dalam 3 detik
  - ✅ Bisa scan ulang
  
- **Jika Node server down:**
  - ✅ Muncul alert merah: "Gagal reset koneksi: Connection failed"
  - ✅ Status tetap "Disconnected"
  - ✅ Tombol kembali normal
  - ⚠️ User harus start Node server dulu: `pm2 start wa-gateway`

---

## Test Scenario 3: Multiple Quick Resets (Stress Test)

### Langkah:
1. Klik **"Reset & Reconnect"**
2. Tunggu 1 detik
3. Klik **"Reset & Reconnect"** lagi (sebelum QR muncul)
4. Konfirmasi kedua kali

### Expected Result:
- ✅ Tombol disabled saat proses reset pertama
- ✅ Request kedua tidak akan jalan karena tombol disabled
- ✅ Setelah reset selesai, tombol enabled lagi
- ✅ QR muncul hanya sekali
- ✅ Tidak ada error di console

---

## Test Scenario 4: Auto-Refresh Status

### Langkah:
1. Buka WA Gateway dashboard
2. Perhatikan badge status di atas
3. Jangan klik apapun, hanya observasi
4. Status akan auto-refresh setiap 5 detik

### Expected Result:
- ✅ Setiap 5 detik, badge status di-update otomatis
- ✅ "Last Update" timestamp berubah setiap refresh
- ✅ "Reconnect Attempts" counter ter-update jika ada reconnection
- ✅ Jika status berubah dari "qr" ke "connected", QR section hilang otomatis
- ✅ Tidak ada flickering atau lag di UI

---

## Test Scenario 5: QR Inline Display

### Langkah:
1. Reset koneksi (status = "Waiting QR Scan")
2. Perhatikan QR section yang muncul
3. Klik tombol **"Refresh QR"** di dalam QR section

### Expected Result:
- ✅ QR section muncul otomatis saat status = 'qr'
- ✅ QR code ter-display dengan baik (tidak blur, tidak putih)
- ✅ Ada instruksi cara scan di sebelah QR
- ✅ Klik "Refresh QR" akan reload QR tanpa refresh halaman
- ✅ Saat scan berhasil, QR section hilang otomatis

---

## Test Scenario 6: Dark Mode Compatibility

### Langkah:
1. Login ke dashboard
2. Toggle dark mode ON
3. Akses WA Gateway dashboard
4. Reset koneksi
5. Perhatikan semua element

### Expected Result:
- ✅ Alert success/error readable di dark mode
- ✅ QR code section readable (white bg untuk QR tetap putih)
- ✅ Badge status colors contrast baik
- ✅ Tombol reset visible dan readable
- ✅ Tooltip readable
- ✅ Table logs readable

---

## Test Scenario 7: Role Permission

### Test dengan role berbeda:

**Admin WA (role: admin_wa):**
- ✅ Bisa akses `/whatsapp`
- ✅ Bisa klik "Reset & Reconnect"
- ✅ Bisa reset koneksi

**Administrator (role: administrator):**
- ✅ Bisa akses `/whatsapp`
- ✅ Bisa klik "Reset & Reconnect"
- ✅ Bisa reset koneksi

**Admin Biasa (role: admin):**
- ❌ Redirect ke dashboard atau 403
- ❌ Tidak bisa akses WA Gateway

**User Biasa:**
- ❌ Redirect ke login atau 403

---

## Debugging

### Check Node.js Server Log:
```bash
pm2 logs wa-gateway --lines 50
```

Expected log saat reset:
```
Logout requested - preparing to disconnect and generate new QR...
Successfully logged out from WhatsApp
Session folder deleted successfully
Starting reconnection to generate new QR code...
QR Code generated, scan with WhatsApp
```

### Check Laravel Log:
```bash
tail -f storage/logs/laravel.log
```

Expected log:
```
[INFO] WhatsApp logout requested
[INFO] WhatsApp server response: {"success":true,"message":"Logged out successfully..."}
```

### Check Browser Console:
1. Buka DevTools (F12)
2. Tab Console
3. Tidak boleh ada error merah
4. Harus ada log:
```
Resetting connection...
Connection reset successful
Status updated: qr
```

---

## Troubleshooting

### Problem: Tombol "Reset & Reconnect" tidak muncul
**Solution:**
- Clear cache: `php artisan view:clear`
- Hard refresh browser: `Ctrl + Shift + R`

### Problem: Klik reset tidak ada response
**Solution:**
- Check browser console untuk error
- Verify route ada: `php artisan route:list | grep logout`
- Verify CSRF token valid

### Problem: QR tidak muncul setelah reset
**Solution:**
- Check Node.js server running: `pm2 list`
- Check session folder ter-delete: `ls whatsapp-server/spmb-wa-session/`
- Manual trigger reconnect: restart PM2

### Problem: Alert tidak muncul
**Solution:**
- Check `showAlert()` function ada di script
- Check Bootstrap loaded
- Check no JavaScript error di console

### Problem: Status stuck "Disconnected"
**Solution:**
- Restart Node.js server: `pm2 restart wa-gateway`
- Delete session manual: `rm -rf whatsapp-server/spmb-wa-session/*`
- Start server: `pm2 start wa-gateway`
- Klik "Reset & Reconnect" di dashboard

---

## Success Criteria

Fitur dianggap berhasil jika:
- ✅ Tombol reset berfungsi tanpa error
- ✅ QR muncul otomatis dalam 3-5 detik
- ✅ Scan QR berhasil reconnect
- ✅ Alert success/error muncul dengan benar
- ✅ Status auto-refresh setiap 5 detik
- ✅ QR section show/hide otomatis sesuai status
- ✅ Tooltip informatif
- ✅ Dark mode compatible
- ✅ Role permission work correctly
- ✅ Tidak ada error di console
- ✅ Tidak perlu SSH/PM2 command manual

---

## Performance Benchmark

- Reset request: < 1 detik
- QR generation: 2-3 detik
- Status refresh: < 500ms
- Total reset process: 3-5 detik
- UI responsive: tidak ada freeze

---

## Deployment Checklist

Sebelum deploy ke production:
- [ ] Test semua scenario di atas
- [ ] Verify di staging environment
- [ ] Backup database
- [ ] Clear cache: `php artisan view:clear`
- [ ] Test dengan user real (admin_wa role)
- [ ] Monitor Node.js server log
- [ ] Siapkan rollback plan
- [ ] Dokumentasikan untuk user
