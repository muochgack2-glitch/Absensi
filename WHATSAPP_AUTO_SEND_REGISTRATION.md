# WhatsApp Auto-Send on Registration

## 📋 Overview

Fitur auto-send WhatsApp notification yang otomatis mengirim pesan selamat datang kepada pendaftar baru setelah berhasil melakukan registrasi online.

## ✨ Features

1. **Automatic Notification**: Kirim WhatsApp otomatis setelah registrasi berhasil
2. **Template-Based**: Menggunakan template yang bisa dikustomisasi
3. **Smart Phone Detection**: Prioritas nomor HP (Wali → Ortu → Siswa)
4. **Fail-Safe**: Registrasi tetap berhasil meskipun WhatsApp gagal kirim
5. **Detailed Logging**: Log lengkap untuk monitoring dan debugging
6. **Enable/Disable**: Bisa diaktifkan/nonaktifkan via settings

## 🔧 How It Works

### Flow Diagram

```
User Submit Registration Form
         ↓
Validate Input Data
         ↓
Create Pendaftar Record
         ↓
Create Logistik Record
         ↓
Check Auto-Send Enabled? ──→ NO ──→ Skip WhatsApp
         ↓ YES
Check WhatsApp Connected? ──→ NO ──→ Skip WhatsApp (Log Warning)
         ↓ YES
Get Phone Number ──→ NO PHONE ──→ Skip WhatsApp (Log Info)
         ↓ HAS PHONE
Send WhatsApp via Template
         ↓
Log Result (Success/Failed)
         ↓
Redirect to Receipt Page
```

### Phone Number Priority

System akan mencari nomor HP dengan prioritas:

1. **Priority 1**: `no_hp_wali` (Nomor HP Wali)
2. **Priority 2**: `no_hp_ortu` (Nomor HP Orang Tua)
3. **Priority 3**: `no_telepon` (Nomor HP Siswa)

Jika tidak ada nomor HP sama sekali, notifikasi di-skip (tidak error).

## 📝 Template Message

### Default Template: `welcome_registration`

```
Assalamu'alaikum {nama},

Selamat! Pendaftaran Anda telah berhasil.

📋 *Detail Pendaftaran:*
No. Pendaftaran: {no_pendaftaran}
Nama: {nama}
Jurusan: {jurusan}

✅ Silakan login ke portal SPMB untuk melengkapi data dan melakukan pembayaran.

🔗 Portal: {portal_url}

Terima kasih telah mendaftar di {sekolah}.

Wassalamu'alaikum
```

### Available Variables

| Variable | Description | Example |
|----------|-------------|---------|
| `{nama}` | Nama lengkap pendaftar | "Ahmad Fauzi" |
| `{no_pendaftaran}` | Nomor registrasi | "SPMB-2026-0001" |
| `{jurusan}` | Nama jurusan | "Teknik Komputer dan Jaringan" |
| `{gelombang}` | Gelombang pendaftaran | "Gelombang 1" |
| `{portal_url}` | URL portal SPMB | "https://spmb.smkpgriblora.sch.id" |
| `{sekolah}` | Nama sekolah | "SMK PGRI Blora" |
| `{tanggal}` | Tanggal pendaftaran | "31-05-2026" |

## 🚀 Installation & Setup

### Step 1: Deploy Code

```bash
# Di server aaPanel
cd /www/wwwroot/spmb

# Pull latest code
git pull origin main

# No need to restart PM2 - Laravel code only
```

### Step 2: Verify Template Exists

```bash
# Check if template exists in database
php artisan tinker

# Run this in tinker:
\App\Models\WhatsAppTemplate::where('name', 'welcome_registration')->first();

# Should return template object
# If null, run seeder:
exit

# Run seeder
php artisan db:seed --class=WhatsAppSeeder
```

### Step 3: Enable Auto-Send

**Via Database**:
```sql
UPDATE whatsapp_settings 
SET value = 'true' 
WHERE key = 'wa_auto_send_enabled';
```

**Via Dashboard**:
1. Login sebagai admin
2. Buka: WhatsApp Gateway → Settings
3. Cari "Auto Send Enabled"
4. Set ke "Yes" atau "true"
5. Klik "Save Settings"

### Step 4: Verify WhatsApp Connected

```bash
# Check status
curl http://localhost:3000/status

# Should return:
# {"success":true,"status":"connected",...}
```

**If not connected**:
- Buka dashboard: `https://your-domain.com/whatsapp`
- Scan QR code jika muncul
- Atau logout dan scan ulang

## 🧪 Testing

### Test 1: Manual Registration Test

1. **Buka form registrasi**: `https://your-domain.com/register`
2. **Isi form dengan data test**:
   - NISN: `1234567890` (unique)
   - Nama: `Test User`
   - Asal Sekolah: `SMP Test`
   - Alamat: `Jl. Test No. 123`
   - Jurusan: Pilih salah satu
   - **No. Telepon**: `081234567890` (PENTING: isi nomor HP Anda)
3. **Submit form**
4. **Check WhatsApp**: Anda harus menerima pesan dalam 5-10 detik

### Test 2: Check Logs

**Laravel Logs**:
```bash
tail -f /www/wwwroot/spmb/storage/logs/laravel.log

# Look for:
# [INFO] WhatsApp notification sent successfully
# atau
# [WARNING] WhatsApp notification failed
```

**Database Logs**:
```sql
-- Check last WhatsApp log
SELECT * FROM whatsapp_logs 
WHERE type = 'auto_registration' 
ORDER BY id DESC 
LIMIT 5;

-- Check status
SELECT status, COUNT(*) as total 
FROM whatsapp_logs 
WHERE type = 'auto_registration' 
GROUP BY status;
```

### Test 3: Different Phone Number Scenarios

| Scenario | no_hp_wali | no_hp_ortu | no_telepon | Expected Result |
|----------|------------|------------|------------|-----------------|
| Wali only | 081234567890 | NULL | NULL | Send to wali |
| Ortu only | NULL | 081234567890 | NULL | Send to ortu |
| Siswa only | NULL | NULL | 081234567890 | Send to siswa |
| All filled | 081111111111 | 082222222222 | 083333333333 | Send to wali (priority 1) |
| None filled | NULL | NULL | NULL | Skip (no error) |

## 📊 Monitoring

### Dashboard Metrics

Buka: `https://your-domain.com/whatsapp`

Metrics yang ditampilkan:
- Total pesan terkirim (termasuk auto-send)
- Total pesan gagal
- Pesan hari ini
- Recent logs (filter by type: auto_registration)

### Database Queries

```sql
-- Total auto-send hari ini
SELECT COUNT(*) as total 
FROM whatsapp_logs 
WHERE type = 'auto_registration' 
AND DATE(created_at) = CURDATE();

-- Success rate auto-send
SELECT 
    status,
    COUNT(*) as total,
    ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM whatsapp_logs WHERE type = 'auto_registration'), 2) as percentage
FROM whatsapp_logs 
WHERE type = 'auto_registration'
GROUP BY status;

-- Failed auto-send dengan error message
SELECT 
    phone,
    error_message,
    created_at
FROM whatsapp_logs 
WHERE type = 'auto_registration' 
AND status = 'failed'
ORDER BY created_at DESC
LIMIT 10;
```

### Laravel Logs Monitoring

```bash
# Real-time monitoring
tail -f /www/wwwroot/spmb/storage/logs/laravel.log | grep -i "whatsapp"

# Check for errors
grep -i "whatsapp.*error" /www/wwwroot/spmb/storage/logs/laravel.log | tail -20

# Check success count today
grep "WhatsApp notification sent successfully" /www/wwwroot/spmb/storage/logs/laravel-$(date +%Y-%m-%d).log | wc -l
```

## 🔧 Configuration

### Settings (whatsapp_settings table)

| Key | Default | Description |
|-----|---------|-------------|
| `wa_auto_send_enabled` | `true` | Enable/disable auto-send |
| `wa_server_url` | `http://localhost:3000` | WhatsApp server URL |
| `wa_timeout` | `10` | Connection timeout (seconds) |
| `wa_retry_attempts` | `3` | Retry attempts if failed |

### Template Configuration

Edit template via dashboard:
1. Buka: WhatsApp Gateway → Templates
2. Cari "Pesan Selamat Datang - Pendaftaran Berhasil"
3. Klik "Edit"
4. Ubah message sesuai kebutuhan
5. Pastikan `auto_send` = checked
6. Pastikan `is_active` = checked
7. Save

## 🚨 Troubleshooting

### Issue 1: Pesan Tidak Terkirim

**Symptoms**: Registrasi berhasil tapi tidak ada WhatsApp

**Check**:
```bash
# 1. Check auto-send enabled
php artisan tinker
\App\Models\WhatsAppSetting::get('wa_auto_send_enabled');
# Should return 'true'

# 2. Check WhatsApp connected
curl http://localhost:3000/status
# Should return "status":"connected"

# 3. Check Laravel logs
tail -50 /www/wwwroot/spmb/storage/logs/laravel.log | grep -i "whatsapp"
```

**Solutions**:
- Enable auto-send: Update `wa_auto_send_enabled` to `true`
- Connect WhatsApp: Scan QR code di dashboard
- Check phone number: Pastikan ada nomor HP di form registrasi

### Issue 2: Template Not Found

**Error**: `Template not found or inactive`

**Solution**:
```bash
# Check template exists
php artisan tinker
\App\Models\WhatsAppTemplate::where('name', 'welcome_registration')->first();

# If null, run seeder
php artisan db:seed --class=WhatsAppSeeder

# Or create manually via dashboard
```

### Issue 3: WhatsApp Not Connected

**Symptoms**: Log shows "WhatsApp not connected, skipping notification"

**Solution**:
```bash
# Check PM2 status
pm2 status

# Check WhatsApp status
curl http://localhost:3000/status

# If not connected, logout and rescan
curl -X POST http://localhost:3000/logout
# Wait 10 seconds, check dashboard for QR
```

### Issue 4: No Phone Number

**Symptoms**: Log shows "No phone number available"

**Solution**:
- Pastikan form registrasi punya field nomor HP
- Atau tambahkan field `no_hp_wali` atau `no_hp_ortu`
- Update form registrasi untuk require phone number

### Issue 5: Rate Limiting

**Symptoms**: Beberapa pesan terkirim, sisanya gagal

**Solution**:
- Tunggu 1-2 menit
- Check rate limit setting: `wa_rate_limit` (default: 20/minute)
- Jangan test dengan banyak registrasi sekaligus

## 📝 Code Reference

### Files Modified

1. **app/Http/Controllers/RegistrationController.php**
   - Added `WhatsAppService` dependency injection
   - Added `sendWhatsAppNotification()` method
   - Added `getPhoneNumber()` helper method
   - Integrated auto-send after registration

2. **database/seeders/WhatsAppSeeder.php**
   - Added `welcome_registration` template
   - Added `wa_auto_send_enabled` setting

### Key Methods

**RegistrationController::sendWhatsAppNotification()**
```php
private function sendWhatsAppNotification(Pendaftar $pendaftar, ?Jurusan $jurusan): void
{
    // Check if auto-send enabled
    // Check if WhatsApp connected
    // Get phone number (priority: wali > ortu > siswa)
    // Prepare template data
    // Send via WhatsAppService
    // Log result
}
```

**RegistrationController::getPhoneNumber()**
```php
private function getPhoneNumber(Pendaftar $pendaftar): ?string
{
    // Priority 1: no_hp_wali
    // Priority 2: no_hp_ortu
    // Priority 3: no_telepon
    // Return null if none available
}
```

## 🎯 Best Practices

1. **Always Test First**: Test dengan nomor HP sendiri sebelum production
2. **Monitor Logs**: Check Laravel logs regularly untuk detect issues
3. **Keep Template Updated**: Update template sesuai kebutuhan sekolah
4. **Don't Spam**: Jangan kirim terlalu banyak pesan dalam waktu singkat
5. **Backup Phone Numbers**: Simpan nomor HP dengan benar di database
6. **Fail-Safe Design**: Registrasi tetap berhasil meskipun WhatsApp gagal
7. **Use Dedicated Number**: Gunakan nomor WhatsApp khusus untuk gateway

## 📚 Related Documentation

- `WHATSAPP_DATABASE_SCHEMA.md` - Database structure
- `WHATSAPP_AUTO_RECONNECT_FIX.md` - Auto-reconnect feature
- `WHATSAPP_MESSAGE_NOT_RECEIVED.md` - Troubleshooting guide
- `app/Services/WhatsAppService.php` - Service layer documentation

## 🔄 Future Enhancements

Fitur yang bisa ditambahkan:

1. **Multiple Templates**: Pilih template berbeda per gelombang
2. **Scheduled Send**: Delay pengiriman (misal: 5 menit setelah registrasi)
3. **Retry Logic**: Auto-retry jika gagal kirim
4. **Admin Notification**: Notif ke admin saat ada pendaftar baru
5. **Custom Variables**: Tambah variabel custom per sekolah
6. **A/B Testing**: Test beberapa template untuk optimize conversion

---

**Last Updated**: 2026-05-31  
**Version**: 1.0.0  
**Status**: Production Ready
