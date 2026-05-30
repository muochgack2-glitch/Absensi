# WhatsApp Gateway - aaPanel + Cloudflare Tunnel Setup

Panduan khusus untuk deployment WhatsApp Gateway di aaPanel lokal dengan Cloudflare Tunnel.

---

## 🏗️ Arsitektur Setup

```
┌─────────────────────────────────────────────────────────────┐
│  Internet (Public)                                          │
│  https://spmb.smkpgriblora.sch.id                          │
└──────────────────┬──────────────────────────────────────────┘
                   │
                   ↓
┌─────────────────────────────────────────────────────────────┐
│  Cloudflare Tunnel                                          │
│  - Expose local server to internet                          │
│  - SSL/TLS termination                                      │
└──────────────────┬──────────────────────────────────────────┘
                   │
                   ↓
┌─────────────────────────────────────────────────────────────┐
│  Server Lokal (aaPanel)                                     │
│  ┌─────────────────────────────────────────────────────┐   │
│  │  Laravel SPMB (Port 80/443)                         │   │
│  │  - Web Interface                                    │   │
│  │  - Admin Panel                                      │   │
│  └──────────────────┬──────────────────────────────────┘   │
│                     │ HTTP (localhost:3000)                 │
│                     ↓                                        │
│  ┌─────────────────────────────────────────────────────┐   │
│  │  WhatsApp Gateway (Port 3000)                       │   │
│  │  - Baileys Server                                   │   │
│  │  - PM2 Process                                      │   │
│  └─────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────┘
```

---

## 📋 Prerequisites

### Di Server Lokal (aaPanel):
- ✅ aaPanel sudah terinstall
- ✅ Node.js v18+ terinstall
- ✅ PM2 terinstall
- ✅ Git terinstall
- ✅ Laravel SPMB sudah running

### Cloudflare:
- ✅ Domain sudah di Cloudflare
- ✅ Cloudflare Tunnel sudah setup
- ✅ Tunnel sudah pointing ke server lokal

---

## 🚀 Installation Steps

### 1. Login ke aaPanel

Buka browser:
```
http://your-local-ip:7800
```

Login dengan credentials aaPanel Anda.

### 2. Install Node.js (Jika Belum Ada)

**Via aaPanel:**
1. Klik **App Store**
2. Cari **Node.js**
3. Install **Node.js 18.x** atau lebih baru

**Via SSH:**
```bash
# Login SSH
ssh root@your-local-ip

# Install Node.js via NodeSource
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt-get install -y nodejs

# Verify
node --version
npm --version
```

### 3. Install PM2

```bash
npm install -g pm2
```

### 4. Navigate ke Project Directory

```bash
# Biasanya di /www/wwwroot/
cd /www/wwwroot/spmb.smkpgriblora.sch.id

# Atau sesuai path project Anda
cd /path/to/your/laravel/project
```

### 5. Pull Latest Code

```bash
git pull origin main
```

### 6. Install WhatsApp Server Dependencies

```bash
cd whatsapp-server
npm install
```

### 7. Configure Environment

```bash
nano .env
```

Edit sesuai setup lokal:
```env
# Port untuk WhatsApp server
PORT=3000

# Host - gunakan 0.0.0.0 agar bisa diakses dari Laravel
HOST=0.0.0.0

# Laravel API URL - gunakan localhost karena satu server
LARAVEL_API_URL=http://localhost

# Session name
SESSION_NAME=spmb-wa-session

# Log level
LOG_LEVEL=info

# Auto reconnect
AUTO_RECONNECT=true
MAX_RECONNECT_ATTEMPTS=5
RECONNECT_INTERVAL=5000
```

### 8. Start WhatsApp Server dengan PM2

```bash
# Start server
pm2 start server.js --name spmb-wa-gateway

# Save PM2 process list
pm2 save

# Setup auto-start on reboot
pm2 startup
# Copy-paste command yang muncul dan jalankan
```

### 9. Verify Server Running

```bash
pm2 status
pm2 logs spmb-wa-gateway
```

Output yang benar:
```
┌─────┬──────────────────────┬─────────┬─────────┐
│ id  │ name                 │ status  │ restart │
├─────┼──────────────────────┼─────────┼─────────┤
│ 0   │ spmb-wa-gateway      │ online  │ 0       │
└─────┴──────────────────────┴─────────┴─────────┘
```

### 10. Configure Laravel

Edit Laravel `.env`:
```bash
cd ..
nano .env
```

Tambahkan:
```env
# WhatsApp Gateway Configuration
WA_API_URL=http://localhost:3000
WA_AUTO_SEND=true
```

Clear cache:
```bash
php artisan config:clear
php artisan cache:clear
```

---

## 📱 Scan QR Code

### Opsi 1: Via SSH Tunnel (RECOMMENDED)

**Di komputer lokal Anda (bukan server):**

```bash
# Buat SSH tunnel
ssh -L 3000:localhost:3000 root@your-local-ip
```

**Buka browser di komputer lokal:**
```
http://localhost:3000/
```

Scan QR code yang muncul dengan WhatsApp.

### Opsi 2: Via aaPanel File Manager

1. Login ke aaPanel
2. Buka **File Manager**
3. Navigate ke `/www/wwwroot/spmb.../whatsapp-server/`
4. Buka terminal di aaPanel
5. Jalankan:
   ```bash
   pm2 logs spmb-wa-gateway
   ```
6. QR code akan muncul di logs (dalam bentuk ASCII art)
7. Scan dengan WhatsApp

### Opsi 3: Via Cloudflare Tunnel (Temporary)

**⚠️ HANYA UNTUK SCAN QR, JANGAN PERMANENT!**

1. Edit Cloudflare Tunnel config
2. Tambahkan temporary rule:
   ```yaml
   ingress:
     - hostname: wa-temp.smkpgriblora.sch.id
       service: http://localhost:3000
     - hostname: spmb.smkpgriblora.sch.id
       service: http://localhost:80
     - service: http_status:404
   ```
3. Restart tunnel
4. Buka `https://wa-temp.smkpgriblora.sch.id/`
5. Scan QR code
6. **HAPUS rule temporary** setelah scan selesai

---

## 🧪 Testing

### 1. Test dari Server (Localhost)

```bash
# Check status
curl http://localhost:3000/status

# Send test message
curl -X POST http://localhost:3000/send \
  -H "Content-Type: application/json" \
  -d '{"phone":"081234567890","message":"Test dari server lokal"}'
```

### 2. Test dari Laravel

Setelah Laravel integration selesai:
1. Login ke admin panel: `https://spmb.smkpgriblora.sch.id/login`
2. Buka menu **WhatsApp Gateway**
3. Cek status connection
4. Test send message

### 3. Test Auto-send

1. Buka form registrasi publik: `https://spmb.smkpgriblora.sch.id/daftar`
2. Isi form dengan nomor HP Anda
3. Submit
4. Cek HP, harus ada notifikasi WhatsApp

---

## 🔧 aaPanel Configuration

### 1. Add PM2 to Supervisor (Optional)

Agar PM2 auto-start saat server reboot:

1. Login aaPanel
2. Klik **App Store** → **Supervisor**
3. Install Supervisor
4. Add new program:
   - **Name:** spmb-wa-gateway
   - **Run Directory:** `/www/wwwroot/spmb.../whatsapp-server/`
   - **Start Command:** `pm2 resurrect`
   - **User:** `www` atau `root`

### 2. Firewall Rules

**Di aaPanel:**
1. Klik **Security**
2. **JANGAN** buka port 3000 ke public
3. Port 3000 hanya untuk localhost

**Di Server Firewall (UFW):**
```bash
# Port 3000 TIDAK perlu dibuka karena hanya localhost
# Hanya port 80, 443, 7800 (aaPanel), 22 (SSH) yang perlu dibuka

sudo ufw status
```

### 3. Monitor Resources

**Via aaPanel:**
1. Dashboard → **System Status**
2. Monitor CPU, RAM, Disk usage

**Via PM2:**
```bash
pm2 monit
```

---

## 🔄 Update & Maintenance

### Pull Update dari GitHub

```bash
# 1. Navigate ke project
cd /www/wwwroot/spmb.smkpgriblora.sch.id

# 2. Pull latest code
git pull origin main

# 3. Update Laravel
composer install --optimize-autoloader --no-dev
php artisan migrate --force
php artisan optimize:clear

# 4. Update WhatsApp server
cd whatsapp-server
npm install

# 5. Restart PM2
pm2 restart spmb-wa-gateway

# 6. Check logs
pm2 logs spmb-wa-gateway
```

### Backup Session

**Penting! Backup session agar tidak perlu scan QR ulang:**

```bash
# Backup session folder
cd /www/wwwroot/spmb.smkpgriblora.sch.id/whatsapp-server
tar -czf spmb-wa-session-backup-$(date +%Y%m%d).tar.gz spmb-wa-session/

# Move to backup directory
mv spmb-wa-session-backup-*.tar.gz /www/backup/

# Restore jika perlu
tar -xzf /www/backup/spmb-wa-session-backup-YYYYMMDD.tar.gz
pm2 restart spmb-wa-gateway
```

---

## 🐛 Troubleshooting

### 1. PM2 Not Found

```bash
# Install PM2 globally
npm install -g pm2

# Or use npx
npx pm2 start server.js --name spmb-wa-gateway
```

### 2. Permission Denied

```bash
# Fix ownership
chown -R www:www /www/wwwroot/spmb.smkpgriblora.sch.id/whatsapp-server/

# Fix permissions
chmod -R 755 /www/wwwroot/spmb.smkpgriblora.sch.id/whatsapp-server/
```

### 3. Port 3000 Already in Use

```bash
# Find process
lsof -i :3000

# Kill process
kill -9 <PID>

# Or change port in .env
nano .env
# PORT=3001
```

### 4. Connection Refused dari Laravel

**Problem:** Laravel tidak bisa connect ke `http://localhost:3000`

**Solution:**

Cek apakah WA server running:
```bash
pm2 status
curl http://localhost:3000/status
```

Jika error, cek logs:
```bash
pm2 logs spmb-wa-gateway --err
```

Pastikan `.env` Laravel benar:
```env
WA_API_URL=http://localhost:3000
```

### 5. QR Code Expired

**Problem:** QR code sudah expired sebelum di-scan

**Solution:**
```bash
# Restart server untuk generate QR baru
pm2 restart spmb-wa-gateway

# Atau delete session dan restart
cd /www/wwwroot/spmb.../whatsapp-server
rm -rf spmb-wa-session/
pm2 restart spmb-wa-gateway
```

### 6. WhatsApp Disconnected

**Problem:** Status berubah jadi "disconnected" tiba-tiba

**Possible Causes:**
- Internet connection lost
- WhatsApp di HP logout
- Session expired
- Server restart

**Solution:**
```bash
# Check logs
pm2 logs spmb-wa-gateway

# Restart server
pm2 restart spmb-wa-gateway

# If still disconnected, scan QR again
rm -rf spmb-wa-session/
pm2 restart spmb-wa-gateway
```

---

## 🔐 Security Best Practices

### 1. Port 3000 TIDAK Boleh Public

**JANGAN** expose port 3000 ke internet:
- ❌ Jangan buka di firewall
- ❌ Jangan tambahkan di Cloudflare Tunnel permanent
- ✅ Hanya untuk localhost communication

### 2. Use Dedicated WhatsApp Number

- ✅ Gunakan nomor khusus untuk gateway
- ❌ Jangan pakai nomor pribadi
- ✅ Nomor bisnis lebih aman

### 3. Rate Limiting

Jangan spam:
- Max 1 pesan/detik
- Max 20 pesan/menit
- Delay 1 detik antar pesan (sudah built-in di bulk send)

### 4. Monitor Logs

```bash
# Check logs regularly
pm2 logs spmb-wa-gateway

# Save logs to file
pm2 logs spmb-wa-gateway > /www/backup/wa-logs-$(date +%Y%m%d).log
```

### 5. Backup Session Regularly

```bash
# Add to crontab
crontab -e

# Backup every day at 2 AM
0 2 * * * cd /www/wwwroot/spmb.../whatsapp-server && tar -czf /www/backup/wa-session-$(date +\%Y\%m\%d).tar.gz spmb-wa-session/
```

---

## 📊 Monitoring

### PM2 Monitoring

```bash
# Real-time monitoring
pm2 monit

# Status
pm2 status

# Logs
pm2 logs spmb-wa-gateway

# Resource usage
pm2 show spmb-wa-gateway
```

### aaPanel Monitoring

1. Login aaPanel
2. Dashboard → **System Status**
3. Monitor:
   - CPU usage
   - RAM usage
   - Disk usage
   - Network traffic

### Cloudflare Analytics

1. Login Cloudflare Dashboard
2. Select domain
3. **Analytics** → **Traffic**
4. Monitor requests to your domain

---

## ✅ Deployment Checklist

- [ ] Node.js v18+ terinstall di aaPanel
- [ ] PM2 terinstall
- [ ] Git pull latest code
- [ ] npm install di whatsapp-server/
- [ ] Configure .env (PORT=3000, HOST=0.0.0.0)
- [ ] Start dengan PM2: `pm2 start server.js --name spmb-wa-gateway`
- [ ] PM2 save: `pm2 save`
- [ ] PM2 startup: `pm2 startup` (copy-paste command)
- [ ] Scan QR code via SSH tunnel
- [ ] Test send message via cURL
- [ ] Configure Laravel .env (WA_API_URL=http://localhost:3000)
- [ ] Test dari admin panel (setelah integration)
- [ ] Setup backup cron job
- [ ] Monitor logs: `pm2 logs spmb-wa-gateway`

---

## 🆘 Quick Commands Reference

```bash
# Navigate to project
cd /www/wwwroot/spmb.smkpgriblora.sch.id/whatsapp-server

# PM2 Commands
pm2 status                          # Check status
pm2 logs spmb-wa-gateway           # View logs
pm2 restart spmb-wa-gateway        # Restart
pm2 stop spmb-wa-gateway           # Stop
pm2 start spmb-wa-gateway          # Start
pm2 monit                          # Monitor

# Test Commands
curl http://localhost:3000/status   # Check status
curl http://localhost:3000/qr       # Get QR (JSON)

# Maintenance
git pull origin main                # Update code
npm install                         # Update dependencies
pm2 restart spmb-wa-gateway        # Restart server

# Backup
tar -czf wa-session-backup.tar.gz spmb-wa-session/

# Restore
tar -xzf wa-session-backup.tar.gz
pm2 restart spmb-wa-gateway
```

---

**Setup:** aaPanel + Cloudflare Tunnel  
**Last Updated:** 2026-05-30  
**Version:** 1.0.0
