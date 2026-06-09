# 🔧 TROUBLESHOOT 500 ERROR - WhatsApp Diagnostics

## ❌ ERROR YANG TERJADI

```
Failed to load resource: the server responded with a status of 500 ()
whatsapp/diagnostics:1

Failed to load diagnostics: SyntaxError: Unexpected token '<', "<!DOCTYPE "... is not valid JSON
whatsapp:3663
```

**Artinya:**
- Controller return HTML error page, bukan JSON
- Ada PHP error di method `diagnostics()` atau `autoFix()`
- Kemungkinan besar: shell command `pm2` tidak ditemukan atau error

---

## 🔍 KEMUNGKINAN PENYEBAB

### 1. **PM2 Tidak Ditemukan di PATH** (Paling Mungkin)
**Masalah:**
- PHP tidak bisa execute `pm2` command
- PM2 tidak ada di system PATH untuk user web server (www-data/nginx)
- PM2 terinstall untuk user root, tapi PHP jalan sebagai user lain

**Cek:**
```bash
# Login SSH sebagai user yang sama dengan web server
which pm2
# Output: /usr/bin/pm2 atau /root/.nvm/versions/node/vXX.XX.X/bin/pm2

# Cek dari PHP
php -r "echo shell_exec('which pm2');"
# Jika kosong = PM2 tidak ditemukan
```

### 2. **Permission Issue**
**Masalah:**
- User web server tidak punya permission execute PM2
- File/folder tidak readable

### 3. **Safe Mode atau disable_functions**
**Masalah:**
- `shell_exec()` disabled di php.ini
- Safe mode enabled

### 4. **Path Base Path Salah**
**Masalah:**
- `base_path('../whatsapp-server')` pointing ke folder yang salah
- Folder tidak exist

---

## ✅ SOLUSI - STEP BY STEP

### SOLUSI 1: Gunakan Full Path untuk PM2 (Recommended)

**Update WhatsAppController.php** dengan full path PM2:

```php
// Ganti semua command PM2 dengan full path
// Dari:
shell_exec('pm2 list 2>&1')

// Menjadi:
shell_exec('/usr/bin/pm2 list 2>&1')
// ATAU
shell_exec('/root/.nvm/versions/node/v20.11.0/bin/pm2 list 2>&1')
```

**Cari full path PM2 di server:**
```bash
which pm2
# Copy output path, contoh: /usr/local/bin/pm2
```

**File yang perlu diubah:**
- `app/Http/Controllers/WhatsAppController.php`
  - Line ~774: `pm2 jlist`
  - Line ~828: `pm2 logs wa-server`
  - Line ~905: `pm2 start`
  - Line ~912: `pm2 delete`
  - Line ~914: `pm2 start`
  - Line ~921: `pm2 flush`
  - Line ~923: `pm2 restart`
  - Line ~930: `pm2 restart`
  - Line ~1002: `pm2 logs wa-server`

---

### SOLUSI 2: Tambah Error Handling yang Lebih Baik

**Update method diagnostics() di WhatsAppController.php:**

```php
public function diagnostics()
{
    try {
        // Cek dulu apakah PM2 ada
        $pm2Path = shell_exec('which pm2 2>&1');
        
        if (empty(trim($pm2Path))) {
            return response()->json([
                'success' => false,
                'message' => 'PM2 not found in system PATH. Please install PM2 or add to PATH.',
                'data' => [
                    'process' => null,
                    'issues' => [[
                        'type' => 'error',
                        'code' => 'PM2_NOT_FOUND',
                        'title' => 'PM2 Not Found',
                        'description' => 'PM2 is not installed or not in system PATH',
                        'auto_fixable' => false
                    ]],
                    'recommendations' => ['Install PM2 globally or add to PATH'],
                    'fix_history' => [],
                    'timestamp' => now()->toIso8601String()
                ]
            ]);
        }

        // Get PM2 process list
        $pm2Output = shell_exec('pm2 jlist 2>&1');
        
        // Log untuk debugging
        \Log::info('PM2 Output', ['output' => $pm2Output]);
        
        $pm2Processes = json_decode($pm2Output, true);
        
        // Cek jika output bukan valid JSON
        if (json_last_error() !== JSON_ERROR_NONE) {
            \Log::error('PM2 JSON Parse Error', [
                'error' => json_last_error_msg(),
                'output' => $pm2Output
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to parse PM2 output: ' . json_last_error_msg(),
                'data' => [
                    'raw_output' => $pm2Output
                ]
            ], 500);
        }
        
        // ... rest of the code ...
        
    } catch (\Exception $e) {
        \Log::error('Diagnostics Error', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Failed to get diagnostics: ' . $e->getMessage()
        ], 500);
    }
}
```

---

### SOLUSI 3: Cek Laravel Logs untuk Error Detail

**Di server, jalankan:**
```bash
# Cek error logs
tail -f /www/wwwroot/spmb/storage/logs/laravel.log

# Atau lihat 100 baris terakhir
tail -100 /www/wwwroot/spmb/storage/logs/laravel.log | grep -i error
```

**Cari error seperti:**
- `shell_exec(): Unable to execute`
- `pm2: command not found`
- `Permission denied`
- Stack trace PHP

---

### SOLUSI 4: Test PM2 dari PHP CLI

**Buat file test di root project:**

```php
<?php
// test_pm2_from_php.php

echo "Testing PM2 from PHP...\n\n";

// Test 1: Check if shell_exec enabled
echo "1. Testing shell_exec():\n";
if (function_exists('shell_exec')) {
    echo "   ✅ shell_exec is enabled\n\n";
} else {
    echo "   ❌ shell_exec is DISABLED\n\n";
    exit(1);
}

// Test 2: Check which pm2
echo "2. Finding PM2 path:\n";
$pm2Path = shell_exec('which pm2 2>&1');
echo "   Path: " . ($pm2Path ?: '(not found)') . "\n\n";

// Test 3: Try pm2 list
echo "3. Testing 'pm2 list':\n";
$output = shell_exec('pm2 list 2>&1');
echo "   Output: \n" . $output . "\n\n";

// Test 4: Try pm2 jlist
echo "4. Testing 'pm2 jlist':\n";
$output = shell_exec('pm2 jlist 2>&1');
echo "   Output: \n" . $output . "\n\n";
$json = json_decode($output, true);
if (json_last_error() === JSON_ERROR_NONE) {
    echo "   ✅ Valid JSON\n";
    echo "   Process count: " . count($json) . "\n";
} else {
    echo "   ❌ Invalid JSON: " . json_last_error_msg() . "\n";
}

// Test 5: Check current user
echo "\n5. Current PHP user:\n";
echo "   User: " . shell_exec('whoami') . "\n";
echo "   UID: " . shell_exec('id -u') . "\n";

echo "\nDone!\n";
```

**Jalankan dari terminal:**
```bash
cd /www/wwwroot/spmb
php test_pm2_from_php.php
```

**Jalankan dari web browser:**
```
https://spmb.smkpgriblora.sch.id/test_pm2_from_php.php
```

Compare hasilnya - jika berbeda berarti permission issue.

---

### SOLUSI 5: Tambah PATH Environment di PHP

**Update `.env` file:**
```bash
# Add PM2 to PATH
PM2_PATH=/usr/bin/pm2
# Atau
PM2_PATH=/root/.nvm/versions/node/v20.11.0/bin/pm2
```

**Update WhatsAppController.php:**
```php
// Di constructor atau method
$pm2Path = env('PM2_PATH', 'pm2'); // Default 'pm2' jika tidak ada

// Ganti semua shell_exec
shell_exec("{$pm2Path} list 2>&1");
shell_exec("{$pm2Path} jlist 2>&1");
```

---

### SOLUSI 6: Disable Auto-Healing (Temporary Workaround)

Jika semua solusi di atas tidak work, temporary disable auto-healing:

**Update `resources/views/whatsapp/index.blade.php`:**

```javascript
// Comment out loadDiagnostics call
document.addEventListener('DOMContentLoaded', function() {
    // loadDiagnostics(); // Disable temporarily
    loadServerHealth();
    loadStatus();
    // ... rest of the code
});
```

**Atau hide panel:**
```blade
{{-- Hide Auto-Healing Panel temporarily --}}
@if(false)
<div class="card border-0 shadow-sm mb-4">
    <!-- Auto-healing content -->
</div>
@endif
```

---

## 📋 QUICK FIX CHECKLIST

**Jalankan di server (SSH):**

```bash
# 1. Cek PM2 path
which pm2

# 2. Test PM2 dari PHP
cd /www/wwwroot/spmb
php -r "echo shell_exec('which pm2');"

# 3. Cek Laravel logs
tail -50 storage/logs/laravel.log

# 4. Test dengan file test
php test_pm2_from_php.php

# 5. Cek permission
ls -la ../whatsapp-server/

# 6. Cek PHP disabled functions
php -i | grep disable_functions
```

---

## 🎯 REKOMENDASI SOLUSI TERBAIK

**STEP 1:** Cari full path PM2
```bash
which pm2
```

**STEP 2:** Update WhatsAppController.php dengan full path PM2

**STEP 3:** Git commit & push

**STEP 4:** Di hosting:
```bash
cd /www/wwwroot/spmb
git pull origin main
php artisan config:clear
```

**STEP 5:** Test lagi di browser

---

## 📞 NEED HELP?

Kirim output dari command ini:
```bash
which pm2
php -r "echo shell_exec('which pm2');"
tail -50 storage/logs/laravel.log
```

---

**File ini:** `TROUBLESHOOT_500_ERROR.md`
