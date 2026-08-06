# WhatsApp Gateway Backup untuk SPMB

Gateway ini berfungsi sebagai **backup/failover** untuk gateway utama SPMB.

## 📋 Konfigurasi

### Port & Host
- **Port**: 3001 (berbeda dengan gateway utama yang menggunakan 3000)
- **Host**: 0.0.0.0 (dapat diakses dari luar)
- **Session Name**: `spmb-wa-session-backup`

### Environment Variables
File `.env` sudah dikonfigurasi dengan:
```env
PORT=3001
HOST=0.0.0.0
LARAVEL_API_URL=http://localhost:8000
SESSION_NAME=spmb-wa-session-backup
LOG_LEVEL=info
AUTO_RECONNECT=true
MAX_RECONNECT_ATTEMPTS=5
RECONNECT_INTERVAL=5000
```

## 🚀 Cara Menjalankan

### 1. Install Dependencies (jika belum)
```bash
cd absensi/whatsapp-server-absensi
npm install
```

### 2. Setup Environment
```bash
# Sudah ada .env, tapi bisa dicek ulang
cp .env.example .env  # jika perlu reset
```

### 3. Jalankan Server
```bash
npm start
```

### 4. Scan QR Code
- Buka browser: http://localhost:3001
- Klik "Get QR Code" atau akses: http://localhost:3001/qr
- Scan dengan WhatsApp (nomor yang berbeda dari gateway utama)

## 🔗 API Endpoints

### Status & Health
- `GET /` - Server info
- `GET /status` - Connection status
- `GET /health` - Server health metrics
- `GET /qr` - Get QR code untuk scan

### Messaging
- `POST /send` - Send single message
  ```json
  {
    "phone": "081234567890",
    "message": "Test message"
  }
  ```

- `POST /send-bulk` - Send multiple messages
  ```json
  {
    "messages": [
      {"phone": "081234567890", "message": "Message 1"},
      {"phone": "081234567891", "message": "Message 2"}
    ]
  }
  ```

### Management
- `POST /restart` - Restart gateway server
- `POST /logout` - Logout & generate new QR

## ⚙️ Integrasi dengan Laravel SPMB

Gateway ini sudah terdaftar di Laravel SPMB sebagai backup gateway:

### Database Settings (whatsapp_settings table)
```
wa_server_url = http://localhost:3000 (primary)
wa_server_url_backup = http://localhost:3001 (backup)
wa_failover_enabled = true
wa_failover_timeout = 5
```

### Failover Logic
`WhatsAppService.php` akan otomatis:
1. Coba connect ke gateway primary (port 3000)
2. Jika gagal/timeout, switch ke backup (port 3001)
3. Health check setiap request untuk mendeteksi gateway yang aktif

### Testing Failover
```bash
# Stop gateway primary
# cd whatsapp-server && npm stop  (atau Ctrl+C)

# Laravel akan otomatis switch ke backup gateway
```

## 📊 Monitoring Gateway

### Check Status
```bash
curl http://localhost:3001/status
```

### Check Health
```bash
curl http://localhost:3001/health
```

Response health check:
```json
{
  "success": true,
  "uptime": 123.45,
  "memory": {
    "rss": 45,
    "heapTotal": 30,
    "heapUsed": 20,
    "percentage": 66
  },
  "cpu": {...},
  "connection": {
    "status": "connected",
    "reconnectAttempts": 0
  }
}
```

## 🔧 Management via Laravel UI

Akses gateway management di Laravel:
```
URL: /admin/gateway
```

Fitur yang tersedia:
- ✅ View status kedua gateway (primary & backup)
- ✅ Display QR code untuk scan
- ✅ Restart gateway
- ✅ Logout & generate new QR
- ✅ View logs
- ✅ Monitor uptime, memory, CPU

## 🎯 Production Setup with PM2

### Install PM2
```bash
npm install -g pm2
```

### Start with PM2
```bash
pm2 start server.js --name "wa-backup-spmb"
```

### PM2 Commands
```bash
pm2 status                    # Check status
pm2 logs wa-backup-spmb       # View logs
pm2 restart wa-backup-spmb    # Restart
pm2 stop wa-backup-spmb       # Stop
pm2 delete wa-backup-spmb     # Remove from PM2
```

### Auto-start on Boot
```bash
pm2 startup
pm2 save
```

## ⚠️ Catatan Penting

1. **Nomor WhatsApp Berbeda**: Gateway backup harus menggunakan nomor WhatsApp yang berbeda dari gateway primary
2. **Port Berbeda**: Pastikan port 3001 tidak digunakan aplikasi lain
3. **Session Folder**: Session disimpan di folder `spmb-wa-session-backup/` (berbeda dari primary)
4. **Rencana Masa Depan**: Gateway ini nantinya akan direpurpose untuk sistem Absensi

## 🔄 Repurpose untuk Absensi (Future)

Ketika sistem Absensi sudah siap:
1. Update `.env`:
   ```env
   SESSION_NAME=absensi-wa-session
   LARAVEL_API_URL=http://localhost:8001
   ```
2. Logout dari WhatsApp SPMB backup
3. Scan dengan nomor WhatsApp baru untuk Absensi
4. Integrasi dengan Laravel Absensi

---

**Version**: 1.0.0  
**Purpose**: Backup Gateway for SPMB (Future: Absensi Gateway)  
**Last Updated**: 12 Juni 2026
