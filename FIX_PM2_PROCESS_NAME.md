# 🔧 FIX: PM2 Process Name Correction

## 🎯 MASALAH

**Root Cause:**
- Dashboard auto-healing menggunakan process name **`whatsapp-server`** (hardcoded)
- PM2 di server sebenarnya menggunakan process name **`wa-server`**
- Akibatnya:
  - ❌ Diagnostics tidak bisa detect process
  - ❌ Auto-fix tidak jalan
  - ❌ Error logs tidak muncul
  - ❌ Button dashboard tidak work dengan PM2

## ✅ SOLUSI YANG DITERAPKAN

### File Yang Diubah: `app/Http/Controllers/WhatsAppController.php`

#### 1. Method `diagnostics()` - Line ~780
**BEFORE:**
```php
// Find whatsapp-server process
if (isset($p['name']) && $p['name'] === 'whatsapp-server') {
```

**AFTER:**
```php
// Find wa-server process
if (isset($p['name']) && $p['name'] === 'wa-server') {
```

#### 2. Method `diagnostics()` - Error Log Check - Line ~828
**BEFORE:**
```php
$errorLog = shell_exec('pm2 logs whatsapp-server --err --lines 50 --nostream 2>&1');
```

**AFTER:**
```php
$errorLog = shell_exec('pm2 logs wa-server --err --lines 50 --nostream 2>&1');
```

#### 3. Method `autoFix()` - Process Not Found - Line ~905
**BEFORE:**
```php
$output = shell_exec('cd ' . base_path('../whatsapp-server') . ' && pm2 start server.js --name whatsapp-server 2>&1');
```

**AFTER:**
```php
$output = shell_exec('cd ' . base_path('../whatsapp-server') . ' && pm2 start server.js --name wa-server 2>&1');
```

#### 4. Method `autoFix()` - Import Path Error - Line ~912
**BEFORE:**
```php
shell_exec('pm2 delete whatsapp-server 2>&1');
$output = shell_exec('cd ' . base_path('../whatsapp-server') . ' && pm2 start server.js --name whatsapp-server 2>&1');
```

**AFTER:**
```php
shell_exec('pm2 delete wa-server 2>&1');
$output = shell_exec('cd ' . base_path('../whatsapp-server') . ' && pm2 start server.js --name wa-server 2>&1');
```

#### 5. Method `autoFix()` - Crash Loop - Line ~921
**BEFORE:**
```php
shell_exec('pm2 flush whatsapp-server 2>&1');
shell_exec('pm2 restart whatsapp-server 2>&1');
```

**AFTER:**
```php
shell_exec('pm2 flush wa-server 2>&1');
shell_exec('pm2 restart wa-server 2>&1');
```

#### 6. Method `autoFix()` - Process Stopped - Line ~930
**BEFORE:**
```php
shell_exec('pm2 restart whatsapp-server 2>&1');
```

**AFTER:**
```php
shell_exec('pm2 restart wa-server 2>&1');
```

#### 7. Method `getErrorLogs()` - Line ~1002
**BEFORE:**
```php
$errorLog = shell_exec('pm2 logs whatsapp-server --err --lines 100 --nostream 2>&1');
```

**AFTER:**
```php
$errorLog = shell_exec('pm2 logs wa-server --err --lines 100 --nostream 2>&1');
```

---

## 📊 TOTAL PERUBAHAN

- **File diubah:** 1 file (`WhatsAppController.php`)
- **Total baris diubah:** 7 lokasi
- **Process name:** `whatsapp-server` → `wa-server`

---

## ✅ VERIFIKASI

Setelah fix ini, dashboard akan:

1. ✅ **Detect PM2 process** dengan nama `wa-server`
2. ✅ **Auto-fix** bisa start/restart/delete process
3. ✅ **Error logs** bisa ditampilkan
4. ✅ **Diagnostics** menampilkan status yang benar
5. ✅ **Button dashboard** berfungsi normal

---

## 🚀 DEPLOYMENT

### Push ke Hosting:
```bash
git add app/Http/Controllers/WhatsAppController.php
git commit -m "Fix: Update PM2 process name from whatsapp-server to wa-server"
git push origin main
```

### Di Hosting:
```bash
cd /www/wwwroot/spmb
git pull origin main
php artisan config:clear
php artisan cache:clear
```

### Test Dashboard:
1. Buka dashboard WhatsApp Gateway
2. Scroll ke "Auto-Healing Diagnostics"
3. Klik "Refresh Diagnostics"
4. Seharusnya:
   - ✅ Process ditemukan
   - ✅ Status "All Systems Healthy" (kalau tidak ada issue)
   - ✅ Server health muncul (uptime, memory, dll)

---

## 🐛 CATATAN UNTUK MASA DEPAN

**PENTING:** Process name PM2 yang digunakan adalah **`wa-server`**

Jika ada file lain yang perlu akses PM2, pastikan menggunakan:
```bash
pm2 list                  # List all processes
pm2 logs wa-server        # View logs
pm2 restart wa-server     # Restart process
pm2 stop wa-server        # Stop process
pm2 delete wa-server      # Delete process
```

**JANGAN gunakan:**
- ❌ `spmb-wa-gateway` (nama lama)
- ❌ `whatsapp-server` (nama yang salah)
- ❌ `whatsapp` (nama yang salah)

**GUNAKAN:**
- ✅ `wa-server` (nama yang benar)

---

## 📞 TESTING CHECKLIST

Setelah deploy, test ini:

- [ ] Dashboard → Auto-Healing Diagnostics terlihat
- [ ] Klik "Refresh Diagnostics" → Process ditemukan
- [ ] Server Health menampilkan uptime dan memory
- [ ] Klik "View Error Logs" → Logs muncul
- [ ] Klik "Auto-Fix Issues" (jika ada issue) → Fix berhasil
- [ ] Fix History menampilkan data (setelah fix)

---

## ✅ STATUS

**Status:** ✅ COMPLETE  
**Tanggal:** 9 Juni 2026  
**Commit:** Pending push

**Siap untuk deploy ke hosting.**

---

## 🎉 KESIMPULAN

Setelah fix ini, **Auto-Healing Dashboard** akan berfungsi dengan sempurna karena menggunakan process name yang benar (`wa-server`).

User tidak perlu lagi SSH manual untuk cek PM2 logs atau restart server - semua bisa dari dashboard!

---

**Last Updated:** 9 Juni 2026  
**Fixed by:** Kiro AI Assistant
