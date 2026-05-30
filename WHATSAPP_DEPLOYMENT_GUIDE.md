# WhatsApp Gateway - Deployment & Testing Guide

Panduan lengkap untuk testing lokal dan deployment ke hosting/panel.

---

## 📋 Table of Contents

1. [Testing di Lokal (Development)](#testing-di-lokal)
2. [Pull & Update dari GitHub](#pull--update-dari-github)
3. [Deployment ke Hosting/Panel](#deployment-ke-hostingpanel)
4. [Troubleshooting](#troubleshooting)

---

## 🧪 Testing di Lokal (Development)

### Prerequisites
- Node.js v18+ sudah terinstall
- Git sudah terinstall
- Port 3000 available

### Langkah-langkah:

#### 1. Clone/Pull Repository
```bash
# Jika belum clone
git clone https://github.com/muochgack2-glitch/SPMB.git
cd SPMB

# Jika sudah clone, pull update terbaru
git pull origin main
```

#### 2. Install Dependencies WhatsApp Server
```bash
cd whatsapp-server
npm install
```

#### 3. Configure Environment (Optional)
```bash
# Copy .env.example ke .env jika belum ada
cp .env.example .env

# Edit .env jika perlu (default sudah OK untuk lokal)
# PORT=3000
# HOST=localhost
```

#### 4. Start WhatsApp Server
```bash
npm start
```

Output yang benar:
```
[INFO] WhatsApp Gateway Server running on http://localhost:3000
[INFO] Connecting to WhatsApp...
[INFO] QR Code generated, scan with WhatsApp
```

#### 5. Buka Testing Panel
Buka browser:
```
http://localhost:3000/
```

Anda akan lihat:
- ✅ Connection Status (real-time)
- ✅ QR Code untuk scan
- ✅ Form Send Message

#### 6. Scan QR Code
1. Buka WhatsApp di HP
2. Tap menu (3 titik) → **Linked Devices**
3. Tap **Link a Device**
4. Scan QR code di browser

Setelah scan, status akan berubah jadi **Connected ✓**

#### 7. Test Send Message
1. Isi nomor HP (format: 081234567890)
2. Isi pesan
3. Klik **Send Message**
4. Cek HP, pesan harus masuk

#### 8. Stop Server
```bash
# Tekan Ctrl+C di terminal
```

---

## 🔄 Pull & Update dari GitHub

### Scenario 1: Update Code Saja (Tidak Ada Perubahan Dependencies)

```bash
# 1. Stop WhatsApp server jika sedang running (Ctrl+C)

# 2. Pull update terbaru
git pull origin main

# 3. Restart WhatsApp server
cd whatsapp-server
npm start
```

### Scenario 2: Update dengan Perubahan Dependencies

```bash
# 1. Stop WhatsApp server

# 2. Pull update
git pull origin main

# 3. Update dependencies
cd whatsapp-server
npm install

# 4. Restart server
npm start
```

### Scenario 3: Update Laravel + WhatsApp Server

```bash
# 1. Stop WhatsApp server

# 2. Pull update
git pull origin main

# 3. Update Laravel dependencies
composer install

# 4. Run migrations (jika ada)
php artisan migrate

# 5. Clear cache
php artisan optimize:clear

# 6. Update WhatsApp server dependencies
cd whatsapp-server
npm install

# 7. Start WhatsApp server
npm start

# 8. Start Laravel (di terminal lain)
cd ..
php artisan serve
```

---

## 🚀 Deployment ke Hosting/Panel

### Prerequisites di Hosting:
- ✅ SSH access
- ✅ Node.js v18+ terinstall
- ✅ PM2 terinstall (untuk keep server running)
- ✅ Port 3000 available (atau port lain yang tidak dipakai)

### Langkah-langkah Deployment:

#### 1. Login ke Hosting via SSH
```bash
ssh username@your-hosting.com
```

#### 2. Navigate ke Project Directory
```bash
cd /path/to/your/laravel/project
# Contoh: cd /home/username/public_html/spmb
```

#### 3. Pull Latest Code
```bash
git pull origin main
```

#### 4. Install/Update Dependencies

**Laravel:**
```bash
composer install --optimize-autoloader --no-dev
php artisan migrate --force
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

**WhatsApp Server:**
```bash
cd whatsapp-server
npm install --production
```

#### 5. Configure Environment

**WhatsApp Server (.env):**
```bash
nano .env
```

Edit sesuai hosting:
```env
PORT=3000
HOST=0.0.0.0          # Penting! Bukan localhost
LOG_LEVEL=info
LARAVEL_API_URL=https://spmb.smkpgriblora.sch.id
```

**Laravel (.env):**
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

#### 6. Install PM2 (Jika Belum Ada)
```bash
npm install -g pm2
```

#### 7. Start WhatsApp Server dengan PM2
```bash
cd whatsapp-server

# Start server
pm2 start server.js --name spmb-wa-gateway

# Save PM2 process list
pm2 save

# Setup auto-start on reboot
pm2 startup
# Copy-paste command yang muncul dan jalankan
```

#### 8. Verify Server Running
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

#### 9. Scan QR Code

**Opsi A: Via Browser (Jika Port 3000 Accessible)**
```
http://your-domain.com:3000/
```

**Opsi B: Via SSH Tunnel (Jika Port 3000 Tidak Accessible)**

Di komputer lokal:
```bash
ssh -L 3000:localhost:3000 username@your-hosting.com
```

Lalu buka browser lokal:
```
http://localhost:3000/
```

**Opsi C: Via PM2 Logs (Lihat QR di Terminal)**
```bash
pm2 logs spmb-wa-gateway
```

Scan QR code yang muncul dengan WhatsApp.

#### 10. Test Send Message

**Via cURL:**
```bash
curl -X POST http://localhost:3000/send \
  -H "Content-Type: application/json" \
  -d '{"phone":"081234567890","message":"Test dari hosting"}'
```

**Via Laravel (Setelah integration selesai):**
- Login ke admin panel
- Buka menu WhatsApp Gateway
- Test send message

---

## 🔧 PM2 Commands (Untuk Manage Server di Hosting)

### View Status
```bash
pm2 status
```

### View Logs
```bash
pm2 logs spmb-wa-gateway
pm2 logs spmb-wa-gateway --lines 100
```

### Restart Server
```bash
pm2 restart spmb-wa-gateway
```

### Stop Server
```bash
pm2 stop spmb-wa-gateway
```

### Start Server
```bash
pm2 start spmb-wa-gateway
```

### Delete Process
```bash
pm2 delete spmb-wa-gateway
```

### Monitor Resources
```bash
pm2 monit
```

### Save Process List
```bash
pm2 save
```

---

## 🐛 Troubleshooting

### 1. Port 3000 Already in Use

**Problem:**
```
Error: listen EADDRINUSE: address already in use ::1:3000
```

**Solution:**

**Windows:**
```bash
# Find process using port 3000
netstat -ano | findstr :3000

# Kill process (replace PID with actual number)
taskkill /F /PID <PID>
```

**Linux/Mac:**
```bash
# Find process
lsof -i :3000

# Kill process
kill -9 <PID>
```

**Or use different port:**
Edit `whatsapp-server/.env`:
```env
PORT=3001
```

### 2. QR Code Not Showing

**Problem:** QR code tidak muncul atau stuck loading

**Solution:**
```bash
# Restart server
pm2 restart spmb-wa-gateway

# Check logs
pm2 logs spmb-wa-gateway

# If session corrupted, delete and restart
cd whatsapp-server
rm -rf spmb-wa-session/
pm2 restart spmb-wa-gateway
```

### 3. Connection Closed / Disconnected

**Problem:** Status berubah jadi "disconnected" terus-menerus

**Solution:**
1. Cek koneksi internet
2. Cek WhatsApp di HP tidak logout
3. Restart server:
   ```bash
   pm2 restart spmb-wa-gateway
   ```
4. Jika masih gagal, scan QR ulang:
   ```bash
   cd whatsapp-server
   rm -rf spmb-wa-session/
   pm2 restart spmb-wa-gateway
   ```

### 4. Message Failed to Send

**Problem:** Pesan gagal terkirim

**Checklist:**
- ✅ Status connection = "connected"
- ✅ Format nomor benar (08xxx atau 628xxx)
- ✅ Nomor tujuan terdaftar di WhatsApp
- ✅ Tidak spam (max 1 pesan/detik)

**Test:**
```bash
# Check status
curl http://localhost:3000/status

# Test send to your own number
curl -X POST http://localhost:3000/send \
  -H "Content-Type: application/json" \
  -d '{"phone":"YOUR_NUMBER","message":"Test"}'
```

### 5. Server Crash / Not Running

**Problem:** PM2 status = "errored" atau "stopped"

**Solution:**
```bash
# View error logs
pm2 logs spmb-wa-gateway --err

# Restart
pm2 restart spmb-wa-gateway

# If still error, delete and start fresh
pm2 delete spmb-wa-gateway
cd whatsapp-server
pm2 start server.js --name spmb-wa-gateway
pm2 save
```

### 6. Dependencies Error

**Problem:**
```
Error: Cannot find module '@whiskeysockets/baileys'
```

**Solution:**
```bash
cd whatsapp-server
rm -rf node_modules package-lock.json
npm install
pm2 restart spmb-wa-gateway
```

### 7. Permission Denied (Linux/Mac)

**Problem:**
```
Error: EACCES: permission denied
```

**Solution:**
```bash
# Fix permissions
chmod -R 755 whatsapp-server/
chown -R $USER:$USER whatsapp-server/

# Or run with sudo (not recommended)
sudo pm2 start server.js --name spmb-wa-gateway
```

---

## 📞 Support & Resources

### Documentation
- [Baileys GitHub](https://github.com/WhiskeySockets/Baileys)
- [Express.js Docs](https://expressjs.com/)
- [PM2 Docs](https://pm2.keymetrics.io/)

### Common Issues
- **WhatsApp banned number**: Jangan spam, max 1 pesan/detik
- **Session expired**: Scan QR ulang setiap beberapa minggu
- **Server restart**: Session akan persist, tidak perlu scan ulang

### Tips
1. **Gunakan nomor khusus** untuk gateway (bukan nomor pribadi)
2. **Monitor logs** secara berkala: `pm2 logs spmb-wa-gateway`
3. **Backup session** folder secara berkala
4. **Set auto-restart** di PM2: `pm2 startup && pm2 save`
5. **Rate limiting**: Jangan kirim lebih dari 20 pesan/menit

---

## 🔐 Security Notes

1. **Port 3000** sebaiknya tidak di-expose ke public
2. Gunakan **localhost** untuk komunikasi Laravel ↔ WA Server
3. Jangan commit **session files** ke git (sudah di .gitignore)
4. Gunakan **HTTPS** untuk Laravel API
5. Set **firewall rules** untuk port 3000

---

## ✅ Checklist Deployment

- [ ] Node.js v18+ terinstall
- [ ] PM2 terinstall
- [ ] Git pull latest code
- [ ] npm install di whatsapp-server/
- [ ] Configure .env (PORT, HOST, LARAVEL_API_URL)
- [ ] Start dengan PM2
- [ ] Scan QR code
- [ ] Test send message
- [ ] Setup PM2 auto-start
- [ ] Monitor logs
- [ ] Configure Laravel .env (WA_API_URL)

---

**Last Updated:** 2026-05-30
**Version:** 1.0.0
