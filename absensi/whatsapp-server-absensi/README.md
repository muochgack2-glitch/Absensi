# WhatsApp Gateway - Absensi/Backup

Gateway ini untuk:
- **SEKARANG:** Backup gateway untuk SPMB (port 3001)
- **NANTI:** Gateway untuk sistem Absensi

## 🚀 Quick Start

### 1. Install Dependencies
```bash
npm install
```

### 2. Configure
```bash
cp .env.example .env
# Sudah auto-configured untuk port 3001
```

### 3. Start with PM2
```bash
pm2 start server.js --name wa-gateway-absensi
pm2 save
```

### 4. Scan QR Code
Buka `/admin/gateway` di Laravel SPMB → klik "QR Code" pada card Gateway Absensi

## ⚙️ Configuration

`.env` settings:
- `PORT=3001` - Berbeda dari primary (3000)
- `HOST=0.0.0.0` - Agar bisa diakses Laravel
- `SESSION_NAME=spmb-wa-session-absensi` - Session folder terpisah

## 🔍 Monitoring

```bash
# Status
pm2 status wa-gateway-absensi

# Logs
pm2 logs wa-gateway-absensi

# Restart
pm2 restart wa-gateway-absensi

# Stop
pm2 stop wa-gateway-absensi
```

## 🔄 Purpose Change (Future)

Saat aplikasi Absensi ready:
1. Gateway ini akan dipakai untuk Absensi
2. SPMB kembali single gateway (port 3000)
3. **Tidak perlu logout/scan QR ulang!**
4. Session tetap valid

## 📊 Resource

- RAM: ~200 MB
- CPU: ~5-10% (idle)
- Storage: ~50 MB (session + logs)

## 🌐 Endpoints

- `GET  /` - Health check
- `GET  /status` - Connection status
- `GET  /qr` - Get QR code
- `POST /send` - Send single message
- `POST /send-bulk` - Send bulk messages
- `POST /logout` - Logout & generate new QR
- `POST /restart` - Restart server

## 📝 Notes

- Session folder: `spmb-wa-session-absensi/`
- Port: 3001 (jangan conflict dengan primary 3000)
- Managed via Laravel UI: `/admin/gateway`
