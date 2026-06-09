# ✅ FITUR AUTO-RESTART YANG SUDAH ADA

## 🎯 YA, SUDAH ADA! (Implemented di Task 4)

Auto-restart WhatsApp server **sudah diimplementasikan** sebelumnya sebagai **Task 4: Scheduled Auto-Restart Feature**.

---

## 📋 DETAIL FITUR

### 1. **Artisan Command: `wa:restart`**

**File:** `app/Console/Commands/RestartWhatsAppServer.php`

**Fitur:**
- ✅ Restart WhatsApp server via Node.js API (`/restart` endpoint)
- ✅ Check server status sebelum restart
- ✅ Konfirmasi interaktif (skip dengan `--force`)
- ✅ Wait 10 detik setelah restart
- ✅ Verify status setelah restart
- ✅ Colored console output (✅ ❌ ⚠️)
- ✅ Success/failure logging

**Cara Pakai Manual:**
```bash
# Dengan konfirmasi
php artisan wa:restart

# Tanpa konfirmasi (untuk cron)
php artisan wa:restart --force
```

---

### 2. **Scheduled Auto-Restart (Cron)**

**File:** `routes/console.php`

**Schedule:**
```php
Schedule::command('wa:restart --force')
    ->dailyAt('03:00')
    ->name('wa-server-auto-restart')
    ->onSuccess(function () {
        \Log::info('WhatsApp server auto-restart completed successfully');
    })
    ->onFailure(function () {
        \Log::error('WhatsApp server auto-restart failed');
    });
```

**Detail:**
- ✅ Jalan otomatis **setiap hari jam 3 pagi**
- ✅ Menggunakan flag `--force` (tanpa konfirmasi)
- ✅ Logging success/failure ke `storage/logs/laravel.log`
- ✅ Tidak overlapping (jika sebelumnya masih jalan)

**Waktu yang Dipilih:**
- **03:00 WIB** = Jam sepi (tidak ada aktivitas user)
- Cocok untuk maintenance otomatis
- Membersihkan memory leak
- Fresh start setiap hari

---

### 3. **Monitoring Status (Bonus)**

**Schedule:**
```php
Schedule::command('wa:monitor')
    ->everyFiveMinutes()
    ->name('wa-status-monitor')
    ->withoutOverlapping()
    ->runInBackground();
```

**Detail:**
- ✅ Monitor status **setiap 5 menit**
- ✅ Detect disconnect/reconnect
- ✅ Send Telegram notification jika status berubah
- ✅ Tidak overlapping
- ✅ Run di background

---

## 🔄 CARA KERJA AUTO-RESTART

### Flow Scheduled Restart:
```
03:00 WIB → Cron trigger
    ↓
php artisan wa:restart --force
    ↓
Check server status
    ↓
Call WhatsAppService->restart()
    ↓
POST to http://localhost:3000/restart
    ↓
Node.js: process.exit(0)
    ↓
PM2 auto-restart process
    ↓
Wait 10 seconds
    ↓
Check new status
    ↓
Log success/failure
    ↓
Done! ✅
```

**Total Time:** ~15 detik

---

## 📊 PERBEDAAN: AUTO-RESTART vs AUTO-FIX

| Feature | Auto-Restart (Task 4) | Auto-Fix (Task 8) |
|---------|----------------------|-------------------|
| **Trigger** | Scheduled (jam 3 pagi) | Manual (dashboard button) |
| **Tujuan** | Preventive maintenance | Fix detected issues |
| **Frekuensi** | 1x per hari | On-demand (max 3x/jam) |
| **Method** | Node.js API `/restart` | PM2 CLI commands |
| **Scope** | Server restart only | 4 jenis issue fixes |
| **User Action** | Tidak perlu (otomatis) | Perlu klik button |
| **Notification** | Log file only | Dashboard + Telegram |
| **Session** | Tetap (tidak perlu scan QR) | Depends on issue type |

---

## 🎯 KAPAN DIPAKAI?

### **Auto-Restart (Scheduled - Task 4):**
✅ Gunakan untuk:
- Preventive maintenance harian
- Clear memory leak
- Fresh start setiap hari
- Tidak ada issue, hanya maintenance
- User tidak perlu aware

**Contoh:**
```
Setiap jam 3 pagi (otomatis):
- Server restart
- Memory di-clear
- Session tetap (tidak perlu scan QR)
- User tidak terganggu (jam tidur)
```

### **Auto-Fix (On-Demand - Task 8):**
✅ Gunakan untuk:
- Ada masalah terdeteksi (PROCESS_NOT_FOUND, CRASH_LOOP, dll)
- Butuh fix segera (kapan saja)
- User aware ada masalah
- Dashboard show error badge
- Telegram notification masuk

**Contoh:**
```
Jam 10 pagi (saat ada masalah):
- Server crash (PROCESS_STOPPED)
- Dashboard detect issue
- User klik "Auto-Fix"
- Process restart
- Online kembali ✅
```

---

## 🔍 CEK STATUS AUTO-RESTART

### 1. Cek Schedule List
```bash
php artisan schedule:list
```

**Output yang diharapkan:**
```
  0 3 * * *  wa:restart --force .................. Next Due: 8 hours from now
  */5 * * * *  wa:monitor ........................ Next Due: 3 minutes from now
```

### 2. Cek Logs Restart Terakhir
```bash
tail -f storage/logs/laravel.log | grep "auto-restart"
```

**Output jika sukses:**
```
[2026-06-09 03:00:15] local.INFO: WhatsApp server auto-restart completed successfully
```

### 3. Test Manual Restart
```bash
php artisan wa:restart --force
```

**Output yang diharapkan:**
```
🔄 WhatsApp Gateway Server Restart
================================

Checking server status...
   Current status: connected

🔄 Restarting server...
✅ Server restarted successfully
⏳ Waiting 10 seconds for server to restart...
🔍 Checking server status...
   New status: connected
✅ Server restarted and reconnected successfully!
```

---

## ⚙️ CONFIGURASI CRON (Server)

Auto-restart **butuh Laravel Scheduler aktif** di server.

### Pastikan Cron Job Terinstall:
```bash
crontab -e
```

**Harus ada baris ini:**
```bash
* * * * * cd /www/wwwroot/spmb && php artisan schedule:run >> /dev/null 2>&1
```

### Cek Cron Running:
```bash
ps aux | grep schedule:run
```

Jika tidak ada cron, scheduled restart **tidak akan jalan otomatis**.

---

## 📝 LOGS & MONITORING

### 1. Laravel Logs
```bash
tail -f storage/logs/laravel.log
```

**Contoh log:**
```
[2026-06-09 03:00:00] local.INFO: Scheduled restart initiated
[2026-06-09 03:00:15] local.INFO: WhatsApp server auto-restart completed successfully
```

### 2. PM2 Logs
```bash
pm2 logs wa-server --lines 50
```

**Contoh output:**
```
03:00:12 AM: Received restart command
03:00:12 AM: Shutting down gracefully...
03:00:13 AM: Process exited with code 0
03:00:14 AM: PM2 restarting process...
03:00:15 AM: Server started successfully
03:00:15 AM: WhatsApp client initializing...
```

---

## ⚠️ TROUBLESHOOTING

### Problem: Auto-restart tidak jalan
**Cek:**
1. Cron job terinstall? → `crontab -l`
2. Laravel scheduler jalan? → `ps aux | grep schedule:run`
3. Logs ada error? → `tail storage/logs/laravel.log`

### Problem: Restart gagal
**Solusi:**
1. Test manual: `php artisan wa:restart --force`
2. Cek Node.js server: `curl http://localhost:3000/status`
3. Cek PM2: `pm2 status wa-server`

### Problem: Session hilang setelah restart
**Catatan:** 
- Scheduled restart via API (`/restart`) = Session **tetap** ✅
- Auto-fix via PM2 delete = Session **hilang** (perlu scan QR) ❌

---

## 🎉 KESIMPULAN

### ✅ SUDAH ADA (Task 4):
1. **Artisan command:** `php artisan wa:restart`
2. **Scheduled restart:** Setiap hari jam 3 pagi
3. **Monitoring:** Setiap 5 menit
4. **Logging:** Success/failure tracking
5. **Telegram notification:** Status change alerts

### ✅ BARU DITAMBAHKAN (Task 8):
1. **Auto-Fix dashboard:** 4 jenis issue fixes
2. **Diagnostics panel:** Real-time issue detection
3. **Error log viewer:** PM2 logs di dashboard
4. **Fix history:** Track 10 fix terakhir
5. **On-demand fix:** User bisa fix kapan saja

---

## 💡 REKOMENDASI

**Gunakan keduanya!**

1. **Auto-Restart (Task 4)** untuk maintenance harian
   - Preventive, bukan reactive
   - Jam 3 pagi (otomatis)
   - Clear memory leak
   - Session tetap

2. **Auto-Fix (Task 8)** untuk emergency fixes
   - Reactive, saat ada masalah
   - Kapan saja (on-demand)
   - Fix specific issues
   - Dari dashboard (mudah)

**Kombinasi keduanya = Server always healthy!** 🚀

---

## 📚 DOKUMENTASI TERKAIT

- `WA_GATEWAY_TOP3_FEATURES.md` - Dokumentasi Task 4 (Scheduled Restart)
- `AUTO_FIX_CAPABILITIES.md` - Dokumentasi Task 8 (Auto-Fix)
- `AUTO_HEALING_DASHBOARD.md` - Complete auto-healing guide
- `app/Console/Commands/RestartWhatsAppServer.php` - Source code command

---

**Version:** 1.0.0  
**Last Updated:** 9 Juni 2026  
**Status:** ✅ ALREADY IMPLEMENTED (Task 4)

---

**✅ AUTO-RESTART SUDAH ADA DAN JALAN OTOMATIS SETIAP HARI!** 🎉
