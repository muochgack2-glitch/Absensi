# Ringkasan Troubleshooting: Diagnostics Panel Loading Terus

## 🔍 **MASALAH SAAT INI**
Dashboard Auto-Healing diagnostics panel stuck di "Loading diagnostics..." dengan error **500 Internal Server Error**.

## 📋 **ROOT CAUSE ANALYSIS**

### Error Message:
```
Call to undefined function App\Http\Controllers\shell_exec()
```
**Location:** `WhatsAppController.php` line 761 (method `diagnostics()`)

### Possible Causes:
1. **PHP-FPM Configuration** - `shell_exec` mungkin didisable untuk web requests di php.ini PHP-FPM
2. **Safe Mode** - PHP safe_mode masih aktif (deprecated tapi masih ada di beberapa server)
3. **Suhosin Extension** - Security extension yang membatasi shell commands
4. **Open Basedir Restrictions** - PHP open_basedir membatasi akses ke `/usr/bin/pm2`

---

## ✅ **LANGKAH DIAGNOSTIK**

### 1. Test Shell Exec dari Browser
Saya sudah membuat file test: `public/test-shell.php`

**Cara test:**
```bash
# Akses via browser:
https://spmb.smkpgriblora.sch.id/test-shell.php

# Atau via curl dari server:
curl http://localhost/test-shell.php
```

**Output yang diharapkan:**
- ✅ shell_exec function EXISTS
- Disabled functions: (none) atau daftar fungsi tapi **TIDAK ada shell_exec**
- whoami: root atau www atau nama user web server
- PM2 version: 5.x.x
- PM2 jlist: JSON output dari PM2

**Jika gagal:**
- ❌ shell_exec function NOT FOUND = fungsi didisable
- Output kosong = permissions atau path issue

---

### 2. Cek PHP-FPM Configuration

**File yang perlu dicek:** `/www/server/php/83/etc/php.ini` atau `/etc/php/8.3/fpm/php.ini`

```bash
# Cek disable_functions di php.ini
grep -n "disable_functions" /www/server/php/83/etc/php.ini

# atau
grep -n "disable_functions" /etc/php/8.3/fpm/php.ini
```

**Yang harus TIDAK ada:**
- `shell_exec` **HARUS TIDAK** ada di dalam list `disable_functions`
- Jika ada, remove dari list

**Contoh yang benar:**
```ini
disable_functions = pcntl_alarm,pcntl_fork,pcntl_waitpid,pcntl_wait,pcntl_wifexited,pcntl_wifstopped,pcntl_wifsignaled,pcntl_wifcontinued,pcntl_wexitstatus,pcntl_wtermsig,pcntl_wstopsig,pcntl_signal,pcntl_signal_get_handler,pcntl_signal_dispatch,pcntl_get_last_error,pcntl_strerror,pcntl_sigprocmask,pcntl_sigwaitinfo,pcntl_sigtimedwait,pcntl_exec,pcntl_getpriority,pcntl_setpriority,pcntl_async_signals,pcntl_unshare
```
**PERHATIKAN:** `shell_exec` **TIDAK** ada dalam list di atas ✅

**Contoh yang salah:**
```ini
disable_functions = shell_exec,exec,system,passthru,...
```
**PERHATIKAN:** `shell_exec` **ADA** dalam list ❌

---

### 3. Restart PHP-FPM setelah edit php.ini

**Jika aaPanel:**
```bash
# Via aaPanel UI:
Software Store → PHP 8.3 → Restart

# Via command line:
/etc/init.d/php-fpm-83 restart

# atau
systemctl restart php-fpm-83
```

**Verifikasi restart berhasil:**
```bash
systemctl status php-fpm-83
```

---

## 🔧 **SOLUSI ALTERNATIF**

### Opsi A: Enable shell_exec (RECOMMENDED)
1. Edit `/www/server/php/83/etc/php.ini`
2. Cari baris `disable_functions = ...`
3. Hapus `shell_exec` dari list (jika ada)
4. Restart PHP-FPM: `/etc/init.d/php-fpm-83 restart`
5. Test di browser: `https://spmb.smkpgriblora.sch.id/test-shell.php`

### Opsi B: Gunakan exec() sebagai fallback
Jika `shell_exec` tidak bisa dienable, gunakan `exec()` atau `proc_open()`:

**Ubah di WhatsAppController.php:**
```php
// OLD (line 761):
$pm2Output = shell_exec('/usr/bin/pm2 jlist 2>&1');

// NEW (gunakan exec):
$output = [];
exec('/usr/bin/pm2 jlist 2>&1', $output);
$pm2Output = implode("\n", $output);
```

### Opsi C: Laravel Artisan Command Wrapper
Buat artisan command yang memanggil PM2, lalu panggil dari controller:

```bash
php artisan make:command GetPM2Status
```

Di controller:
```php
use Illuminate\Support\Facades\Artisan;

Artisan::call('pm2:status');
$output = Artisan::output();
```

---

## 📝 **CHECKLIST TROUBLESHOOTING**

**Jalankan langkah ini secara berurutan:**

- [ ] 1. Test `https://spmb.smkpgriblora.sch.id/test-shell.php` di browser
  - Jika ERROR 404: file tidak terupload, upload file `public/test-shell.php`
  - Jika tampil hasil: lanjut ke step 2
  
- [ ] 2. Lihat hasil test-shell.php:
  - [ ] ✅ shell_exec EXISTS? 
  - [ ] ✅ Disabled functions TIDAK ada shell_exec?
  - [ ] ✅ whoami menampilkan output?
  - [ ] ✅ PM2 version menampilkan output?
  - [ ] ✅ PM2 jlist menampilkan JSON?
  
- [ ] 3. Jika semua ✅ di step 2:
  - [ ] Test diagnostics endpoint: `curl http://localhost/whatsapp/diagnostics`
  - [ ] Jika masih error, cek `storage/logs/laravel.log`
  
- [ ] 4. Jika ada yang ❌ di step 2:
  - [ ] Edit `/www/server/php/83/etc/php.ini`
  - [ ] Hapus `shell_exec` dari `disable_functions`
  - [ ] Restart PHP-FPM: `/etc/init.d/php-fpm-83 restart`
  - [ ] Ulangi test dari step 1

---

## 🎯 **EXPECTED OUTCOME**

**Setelah fix berhasil:**

1. **Test shell:** `https://spmb.smkpgriblora.sch.id/test-shell.php`
   - Semua test menampilkan output ✅
   
2. **Diagnostics endpoint:** `GET /whatsapp/diagnostics`
   - Response 200 OK
   - JSON dengan data process PM2
   
3. **Dashboard UI:** `https://spmb.smkpgriblora.sch.id/whatsapp`
   - Diagnostics panel menampilkan data (tidak stuck di "Loading...")
   - Status badges: Healthy, Warning, atau Error
   - Auto-fix buttons aktif jika ada issue

---

## 📚 **FILES REFERENCE**

### Files yang terlibat:
1. **Backend:**
   - `app/Http/Controllers/WhatsAppController.php` - diagnostics(), autoFix()
   - `app/Http/Middleware/CheckRole.php` - AJAX JSON response
   - `routes/web.php` - route definitions

2. **Frontend:**
   - `resources/views/whatsapp/index.blade.php` - diagnostics panel UI
   - JavaScript functions: `loadDiagnostics()`, `updateDiagnosticsUI()`

3. **Test Files:**
   - `public/test-shell.php` - **NEW** Shell exec test page

4. **Configuration:**
   - `/www/server/php/83/etc/php.ini` - PHP configuration
   - `/etc/php/8.3/fpm/pool.d/www.conf` - PHP-FPM pool config

---

## 💡 **QUICK FIX COMMAND**

**Jika sudah tau masalahnya di disable_functions:**

```bash
# Backup original
cp /www/server/php/83/etc/php.ini /www/server/php/83/etc/php.ini.backup

# Remove shell_exec dari disable_functions (gunakan sed)
sed -i 's/shell_exec,//g; s/,shell_exec//g' /www/server/php/83/etc/php.ini

# Restart PHP-FPM
/etc/init.d/php-fpm-83 restart

# Verify
curl http://localhost/test-shell.php
```

---

## ⚠️ **CATATAN KEAMANAN**

**Mengaktifkan `shell_exec` adalah security risk!**

**Mitigasi:**
1. ✅ **SUDAH DITERAPKAN:** Rate limiting di auto-fix (max 3x/hour)
2. ✅ **SUDAH DITERAPKAN:** Role-based access (hanya administrator & admin_wa)
3. ✅ **SUDAH DITERAPKAN:** Full path untuk commands (`/usr/bin/pm2` bukan `pm2`)
4. ⚠️ **BELUM:** Consider creating dedicated PHP-FPM pool untuk admin dengan shell_exec enabled
5. ⚠️ **BELUM:** Input validation untuk PM2 commands (currently hardcoded, aman)

**Best Practice:**
- Jangan expose `shell_exec` ke semua users
- Gunakan full path untuk binary (`/usr/bin/pm2`)
- Validasi semua input jika ada user-supplied parameters
- Log semua shell command execution
- Consider using queues untuk long-running commands

---

## 🔄 **NEXT STEPS AFTER FIX**

**Setelah shell_exec berhasil:**

1. **Test Manual:**
   - [ ] Buka dashboard: https://spmb.smkpgriblora.sch.id/whatsapp
   - [ ] Diagnostics panel loads dengan data
   - [ ] Test "Auto Fix" button (jika ada issue)
   - [ ] Test "View Error Logs" button
   - [ ] Verify auto-refresh setiap 60 detik

2. **Clean Up:**
   - [ ] Hapus test file: `rm public/test-shell.php` (OPTIONAL, bisa disimpan untuk debugging)
   
3. **Documentation:**
   - [ ] Update AUTO_HEALING_IMPLEMENTATION_SUMMARY.md
   - [ ] Mark Task 8 as COMPLETED ✅

4. **Monitoring:**
   - [ ] Monitor `storage/logs/laravel.log` untuk errors
   - [ ] Monitor Telegram notifications
   - [ ] Check UserActivityLog untuk auto-fix history

---

## 📞 **BANTUAN TROUBLESHOOTING**

**Jika masih stuck, provide info:**
1. Output dari `test-shell.php`
2. Output dari `grep disable_functions /www/server/php/83/etc/php.ini`
3. Output dari `systemctl status php-fpm-83`
4. Screenshot error di browser console
5. Latest error dari `storage/logs/laravel.log`

**Debugging via SSH:**
```bash
# Test PM2 as web server user
su - www
/usr/bin/pm2 jlist

# Test PHP shell_exec via CLI
php -r "echo shell_exec('/usr/bin/pm2 -v');"

# Check PHP-FPM error log
tail -f /www/server/php/83/var/log/php-fpm.log
```
