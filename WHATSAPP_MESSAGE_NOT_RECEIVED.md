# Troubleshooting: Notifikasi Terkirim tapi Pesan Tidak Diterima

## 🐛 Problem

- ✅ Dashboard menampilkan "Berhasil! Message sent successfully"
- ✅ Log di database status "sent"
- ❌ Pesan tidak sampai ke WhatsApp penerima

## 🔍 Possible Causes

### 1. WhatsApp Gateway Tidak Benar-Benar Connected

**Symptoms**:
- Status dashboard "Connected" tapi sebenarnya session expired
- PM2 logs menunjukkan reconnect attempts

**Check**:
```bash
# Check PM2 logs
pm2 logs spmb-wa-gateway --lines 50

# Check status via API
curl http://localhost:3000/status
```

**Expected Output**:
```json
{
  "success": true,
  "status": "connected",
  "qrAvailable": false
}
```

**Solution**:
- Jika status bukan "connected", logout dan scan QR lagi
- Restart PM2: `pm2 restart spmb-wa-gateway`

---

### 2. Format Nomor HP Salah

**Symptoms**:
- Pesan "terkirim" tapi tidak sampai
- Nomor HP tidak valid

**Common Issues**:
- Nomor pakai format lokal: `081234567890` (harus `6281234567890`)
- Ada spasi atau karakter lain: `0812 3456 7890`
- Nomor tidak ada WhatsApp-nya

**Check Format**:
```bash
# Test dengan script
php test-wa-send.php
```

**Correct Formats**:
- ✅ `6281234567890` (recommended)
- ✅ `081234567890` (akan di-convert ke 62xxx)
- ✅ `+6281234567890` (akan di-convert ke 62xxx)
- ❌ `0812 3456 7890` (ada spasi)
- ❌ `(0812) 345-6789` (ada karakter)

**Solution**:
- Pastikan nomor HP di database format 08xxx atau 62xxx
- Server akan auto-convert ke format WhatsApp: `62xxx@s.whatsapp.net`

---

### 3. WhatsApp Server Error (Silent Fail)

**Symptoms**:
- HTTP response 200 OK
- Laravel log "Message sent successfully"
- Tapi WhatsApp server sebenarnya gagal kirim

**Root Cause**:
WhatsApp server (Baileys) bisa return HTTP 200 tapi `success: false` di response body.

**Check**:
```bash
# Check Laravel logs
tail -f /www/wwwroot/spmb/storage/logs/laravel.log

# Look for:
# "WhatsApp server returned success=false"
```

**Solution**:
Sudah diperbaiki di commit terbaru - service sekarang check `success` field di response.

---

### 4. Nomor Tujuan Tidak Valid / Tidak Ada WhatsApp

**Symptoms**:
- Nomor HP valid tapi tidak punya WhatsApp
- Nomor HP tidak aktif

**Check**:
1. Pastikan nomor HP punya WhatsApp
2. Test kirim manual dari HP yang di-pair
3. Coba nomor HP lain yang pasti ada WhatsApp-nya

**Solution**:
- Gunakan nomor HP yang valid dan aktif WhatsApp
- Test dengan nomor HP Anda sendiri dulu

---

### 5. Rate Limiting / Spam Detection

**Symptoms**:
- Pesan pertama terkirim
- Pesan berikutnya tidak sampai
- WhatsApp block sementara

**Check PM2 Logs**:
```bash
pm2 logs spmb-wa-gateway --lines 100 | grep -i "error\|fail\|block"
```

**Solution**:
- Tunggu beberapa menit sebelum kirim lagi
- Jangan kirim terlalu banyak pesan dalam waktu singkat
- Gunakan delay 1-2 detik antar pesan (sudah ada di bulk send)

---

### 6. Session Expired / Logged Out

**Symptoms**:
- Status "connected" tapi pesan tidak terkirim
- PM2 logs: "Connection closed"

**Check**:
```bash
pm2 logs spmb-wa-gateway --lines 30
```

**Look for**:
```
[WARN] Connection closed
[INFO] Reconnecting...
```

**Solution**:
```bash
# Logout dan scan QR lagi
curl -X POST http://localhost:3000/logout

# Atau dari dashboard: Settings → Logout WhatsApp
# Tunggu 10 detik, QR akan muncul otomatis
# Scan QR dengan WhatsApp
```

---

## 🧪 Testing Steps

### Step 1: Test WhatsApp Server Directly

```bash
# Di server aaPanel
cd /www/wwwroot/spmb

# Edit nomor HP test di script
nano test-wa-send.php
# Ganti: $testPhone = '6281234567890'; dengan nomor HP Anda

# Run test
php test-wa-send.php
```

**Expected Output**:
```
✓ WhatsApp is connected
✓ Server returned success=true
✓ Message sent to WhatsApp server
✅ TEST PASSED!
```

**If Failed**:
- Check error message
- Check PM2 logs
- Verify phone number format

### Step 2: Check Laravel Logs

```bash
# Real-time logs
tail -f /www/wwwroot/spmb/storage/logs/laravel.log

# Kirim pesan dari dashboard
# Lihat log output
```

**Look for**:
```
[INFO] Attempting to send WhatsApp message
[INFO] WhatsApp server response
[INFO] WhatsApp message sent successfully
```

**Or if failed**:
```
[WARNING] WhatsApp server returned success=false
[ERROR] WhatsApp message send failed
```

### Step 3: Check PM2 Logs

```bash
# Real-time logs
pm2 logs spmb-wa-gateway

# Last 50 lines
pm2 logs spmb-wa-gateway --lines 50
```

**Look for**:
```
[INFO] Message sent to 6281234567890
```

**Or errors**:
```
[ERROR] Failed to send message
[WARN] Connection closed
```

### Step 4: Test dengan Nomor HP Sendiri

1. Gunakan nomor HP Anda sendiri sebagai tujuan
2. Kirim pesan test dari dashboard
3. Check WhatsApp di HP Anda
4. Jika sampai = server OK, masalah di nomor tujuan
5. Jika tidak sampai = masalah di server/koneksi

---

## 🔧 Solutions

### Solution 1: Improved Logging (Already Implemented)

**File**: `app/Services/WhatsAppService.php`

**Changes**:
- ✅ Log request sebelum kirim
- ✅ Log response dari server (status code + body)
- ✅ Check `success` field di response body
- ✅ Log detail error dengan trace

**Deploy**:
```bash
cd /www/wwwroot/spmb
git pull origin main
# No restart needed - Laravel code
```

### Solution 2: Verify WhatsApp Connection

```bash
# Check status
curl http://localhost:3000/status | jq '.'

# If not connected, logout and rescan
curl -X POST http://localhost:3000/logout

# Wait 10 seconds for QR
sleep 10

# Get QR
curl http://localhost:3000/qr | jq '.qr' -r

# Or check dashboard for QR
```

### Solution 3: Test Phone Number Format

```php
// Test di Laravel Tinker
php artisan tinker

// Test format nomor
$phone = '081234567890';
$formatted = preg_replace('/\D/', '', $phone);
if (substr($formatted, 0, 1) === '0') {
    $formatted = '62' . substr($formatted, 1);
}
echo $formatted . '@s.whatsapp.net';
// Should output: 6281234567890@s.whatsapp.net
```

### Solution 4: Manual Test via cURL

```bash
# Test send message directly to WhatsApp server
curl -X POST http://localhost:3000/send \
  -H "Content-Type: application/json" \
  -d '{
    "phone": "6281234567890",
    "message": "Test message from cURL"
  }'

# Check response
# Should return: {"success":true,"message":"Message sent successfully",...}
```

---

## 📊 Diagnostic Checklist

Run through this checklist:

- [ ] **WhatsApp Server Running**: `pm2 status` shows "online"
- [ ] **WhatsApp Connected**: `curl http://localhost:3000/status` returns `"status":"connected"`
- [ ] **No Errors in PM2 Logs**: `pm2 logs spmb-wa-gateway --lines 50` no errors
- [ ] **Phone Number Valid**: Format 62xxx, has WhatsApp
- [ ] **Test Send Works**: `php test-wa-send.php` returns success
- [ ] **Laravel Logs Clean**: No errors in `storage/logs/laravel.log`
- [ ] **Message in Database**: Check `whatsapp_logs` table, status "sent"
- [ ] **Received on Phone**: Check WhatsApp on test phone

---

## 🎯 Quick Fix Commands

```bash
# 1. Check everything
pm2 status
curl http://localhost:3000/status
tail -20 /www/wwwroot/spmb/storage/logs/laravel.log

# 2. If not connected, restart
pm2 restart spmb-wa-gateway
sleep 5
curl http://localhost:3000/status

# 3. If still not connected, logout and rescan
curl -X POST http://localhost:3000/logout
# Check dashboard for QR, scan with WhatsApp

# 4. Test send
php /www/wwwroot/spmb/test-wa-send.php

# 5. Check logs
pm2 logs spmb-wa-gateway --lines 30
```

---

## 📝 Common Scenarios

### Scenario 1: "Connected" tapi Pesan Tidak Sampai

**Diagnosis**:
```bash
# Check if really connected
curl http://localhost:3000/status

# Check PM2 logs for errors
pm2 logs spmb-wa-gateway --lines 50

# Test direct send
curl -X POST http://localhost:3000/send \
  -H "Content-Type: application/json" \
  -d '{"phone":"6281234567890","message":"Test"}'
```

**Fix**:
- Logout dan scan QR lagi
- Restart PM2
- Check nomor HP format

### Scenario 2: Pesan Pertama Sampai, Berikutnya Tidak

**Diagnosis**: Rate limiting / spam detection

**Fix**:
- Tunggu 1-2 menit antar pesan
- Jangan kirim terlalu banyak pesan sekaligus
- Gunakan bulk send dengan delay (sudah ada)

### Scenario 3: Nomor Tertentu Tidak Terima

**Diagnosis**: Nomor tidak valid / tidak ada WhatsApp

**Fix**:
- Verify nomor HP punya WhatsApp
- Test dengan nomor lain
- Check format nomor (62xxx)

---

## 📞 Support

Jika masih bermasalah setelah semua troubleshooting:

1. **Collect Information**:
   ```bash
   # Save logs
   pm2 logs spmb-wa-gateway --lines 100 > wa-server.log
   tail -100 /www/wwwroot/spmb/storage/logs/laravel.log > laravel.log
   
   # Save status
   curl http://localhost:3000/status > wa-status.json
   
   # Save test result
   php test-wa-send.php > test-result.txt
   ```

2. **Check Database**:
   ```sql
   -- Check last 10 logs
   SELECT id, phone, status, error_message, created_at 
   FROM whatsapp_logs 
   ORDER BY id DESC 
   LIMIT 10;
   
   -- Check failed messages
   SELECT phone, error_message, COUNT(*) as count
   FROM whatsapp_logs 
   WHERE status = 'failed'
   GROUP BY phone, error_message;
   ```

3. **Provide Details**:
   - PM2 logs (wa-server.log)
   - Laravel logs (laravel.log)
   - Status response (wa-status.json)
   - Test result (test-result.txt)
   - Database logs (SQL query result)
   - Screenshot dari dashboard

---

**Last Updated**: 2026-05-31  
**Version**: 1.0.0  
**Status**: Troubleshooting Guide
