# 🌐 Dual WhatsApp Gateway Setup

Setup dual gateway untuk SPMB dengan sistem failover otomatis.

## 📦 Struktur

```
SPMB/
├── whatsapp-server/           → Port 3000 (SPMB Primary)
├── whatsapp-server-absensi/   → Port 3001 (Backup SPMB / Future: Absensi)
```

## 🚀 Setup di Server

### 1. Setup Gateway Absensi (Port 3001)

```bash
cd whatsapp-server-absensi

# Copy .env.example ke .env
cp .env.example .env

# Install dependencies
npm install

# Start dengan PM2
pm2 start server.js --name wa-gateway-absensi
pm2 save
```

### 2. Run Migration

```bash
php artisan migrate
```

Ini akan menambahkan 3 settings:
- `wa_server_url_backup` = http://localhost:3001
- `wa_failover_enabled` = true
- `wa_failover_timeout` = 5 seconds

### 3. Scan QR Code

#### Gateway SPMB (Primary):
- Buka: `/admin/gateway`
- Klik tombol "QR Code" pada card Gateway SPMB
- Scan dengan nomor WA SPMB (08123...)

#### Gateway Absensi (Backup):
- Buka: `/admin/gateway`
- Klik tombol "QR Code" pada card Gateway Absensi  
- Scan dengan nomor WA Absensi (08987...)

### 4. Verify

```bash
pm2 list

# Should show:
# wa-gateway-spmb      (online)
# wa-gateway-absensi   (online)

# Test status
curl http://localhost:3000/status
curl http://localhost:3001/status
```

## ⚙️ Failover Logic

### Auto Failover:
- Laravel cek primary (port 3000) health setiap request
- Jika primary offline/timeout > 5 detik → auto switch ke backup (port 3001)
- Jika primary kembali online → auto switch kembali ke primary

### Manual Control:
- Buka `/admin/gateway`
- Monitor status kedua gateway real-time
- Restart/Logout gateway jika perlu
- View logs untuk troubleshooting

## 🔧 Maintenance

### Restart Gateway:
```bash
pm2 restart wa-gateway-spmb
pm2 restart wa-gateway-absensi
```

### View Logs:
```bash
pm2 logs wa-gateway-spmb --lines 50
pm2 logs wa-gateway-absensi --lines 50
```

### Logout & Rescan QR:
Via UI `/admin/gateway` → klik tombol "Logout" → scan QR baru

## 📊 Resource Usage

Per gateway: ~200 MB RAM, ~5-10% CPU
Total 2 gateway: ~400-600 MB RAM

## 🔄 Future: Migrate ke Absensi

Saat aplikasi Absensi siap:

1. Update database:
```sql
UPDATE whatsapp_settings 
SET label = 'Absensi Server URL',
    description = 'WhatsApp Gateway for Absensi System'
WHERE key = 'wa_server_url_backup';
```

2. Buat `AbsensiWhatsAppService.php`:
```php
$this->serverUrl = 'http://localhost:3001';
```

3. Gateway absensi (3001) sekarang untuk Absensi!
4. SPMB kembali single gateway (3000)

**Tidak perlu scan QR ulang!**

## ❓ Troubleshooting

**Gateway offline?**
- Check PM2: `pm2 list`
- Restart: `pm2 restart wa-gateway-xxx`
- Check logs: `pm2 logs wa-gateway-xxx`

**QR code tidak muncul?**
- Logout via UI
- Wait 5-10 seconds
- Refresh page, QR akan muncul

**Failover tidak work?**
- Check `wa_failover_enabled` = true
- Check `wa_failover_timeout` tidak terlalu kecil
- Check logs: `storage/logs/laravel.log`
