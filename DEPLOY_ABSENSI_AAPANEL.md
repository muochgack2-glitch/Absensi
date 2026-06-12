# 🚀 Panduan Deploy Absensi ke aaPanel

## Tujuan
Deploy sistem Absensi (Laravel 13) ke hosting aaPanel agar WhatsApp Gateway Backup (Port 3001) bisa running 24/7 sebagai backup untuk gateway SPMB.

---

## 📋 Persiapan

### A. Yang Dibutuhkan di Sisi Lokal
1. ✅ Repository Absensi sudah siap di GitHub
2. ✅ File `.env` untuk production sudah dikonfigurasi
3. ✅ WhatsApp Gateway Backup (port 3001) sudah tested dan running

### B. Yang Dibutuhkan di Hosting
1. **VPS/Server** dengan aaPanel installed
2. **Domain** untuk Absensi (contoh: absensi.sekolah.sch.id)
3. **SSL Certificate** (bisa pakai Let's Encrypt gratis)
4. **Requirements:**
   - PHP 8.2 atau lebih tinggi
   - Node.js 18+ (untuk WhatsApp Gateway)
   - MySQL/MariaDB
   - Composer
   - PM2 (untuk menjalankan Node.js server)

---

## 🔧 LANGKAH 1: Setup Server di aaPanel

### 1.1 Login ke aaPanel
```bash
# Akses via browser
https://your-server-ip:7800

# Login dengan kredensial yang sudah dibuat saat install aaPanel
```

### 1.2 Install Software Stack
Di aaPanel Dashboard → App Store, install:

1. **PHP 8.4** (PENTING: Laravel 13 membutuhkan PHP 8.4+)
   - Jika PHP 8.4 belum tersedia di aaPanel, install manual:
   
   ```bash
   # SSH ke server
   ssh root@your-server-ip
   
   # Install PHP 8.4 dari Remi repository
   yum install -y https://rpms.remirepo.net/enterprise/remi-release-8.rpm
   yum-config-manager --enable remi-php84
   yum install -y php84 php84-php-fpm php84-php-cli php84-php-common \
       php84-php-mysqlnd php84-php-pdo php84-php-xml php84-php-mbstring \
       php84-php-json php84-php-zip php84-php-gd php84-php-curl \
       php84-php-tokenizer php84-php-fileinfo php84-php-bcmath
   
   # Verify
   php84 --version  # Should show PHP 8.4.x
   
   # Create symlink
   ln -sf /usr/bin/php84 /usr/bin/php
   php --version
   ```
   
   - Extensions yang diperlukan:
     - ✅ OpenSSL
     - ✅ PDO
     - ✅ Mbstring
     - ✅ Tokenizer
     - ✅ XML
     - ✅ Ctype
     - ✅ JSON
     - ✅ BCMath
     - ✅ Fileinfo
     - ✅ Redis (optional, untuk cache)

2. **MySQL 8.0** (atau MariaDB 10.6+)

3. **Nginx** (web server)

4. **PM2 Manager** (untuk Node.js)
   - Install via aaPanel App Store atau manual

5. **Composer** (PHP dependency manager)

### 1.3 Install Node.js
```bash
# SSH ke server, install Node.js 18+
curl -fsSL https://rpm.nodesource.com/setup_18.x | sudo bash -
yum install -y nodejs

# Verify
node --version  # should be v18.x or higher
npm --version
```

### 1.4 Install PM2 Globally
```bash
npm install -g pm2

# Verify
pm2 --version
```

---

## 🌐 LANGKAH 2: Setup Website di aaPanel

### 2.1 Buat Website Baru
1. **aaPanel → Website → Add Site**
2. **Domain:** `absensi.sekolah.sch.id`
3. **Document Root:** `/www/wwwroot/absensi.sekolah.sch.id`
4. **PHP Version:** PHP 8.2
5. **Database:** Buat database baru
   - Database Name: `absensi_db`
   - Username: `absensi_user`
   - Password: (generate secure password)

### 2.2 Konfigurasi SSL
1. **aaPanel → Website → Settings (domain) → SSL**
2. Pilih **Let's Encrypt**
3. Klik **Apply**
4. Tunggu sampai SSL aktif (centang hijau)

### 2.3 Set Document Root ke `public/`
1. **aaPanel → Website → Settings (domain) → Site Directory**
2. **Running Directory:** Ubah ke `/public`
3. **Prevent cross-site access:** Enable
4. Save

---

## 📦 LANGKAH 3: Clone & Setup Laravel

### 3.1 SSH ke Server
```bash
ssh root@your-server-ip
```

### 3.2 Navigate ke Web Root
```bash
cd /www/wwwroot/absensi.sekolah.sch.id
```

### 3.3 Clone Repository
```bash
# Hapus default files jika ada
rm -rf *
rm -rf .[^.]*

# Clone dari GitHub (gunakan Absensi repo)
git clone https://github.com/muochgack2-glitch/Absensi.git .

# Atau jika private repo, gunakan Personal Access Token
git clone https://YOUR_TOKEN@github.com/muochgack2-glitch/Absensi.git .

# Masuk ke folder absensi (karena structure monorepo)
cd absensi
```

### 3.4 Install Dependencies

⚠️ **SOLUSI ERROR COMPATIBILITY:**

Jika dapat error seperti screenshot (symfony/clock requires PHP >=8.4), ada 2 opsi:

**OPSI A: Upgrade PHP ke 8.4 (Recommended)**
```bash
# Install PHP 8.4
yum install -y https://rpms.remirepo.net/enterprise/remi-release-8.rpm
yum-config-manager --enable remi-php84
yum install -y php84 php84-php-fpm php84-php-cli php84-php-common \
    php84-php-mysqlnd php84-php-pdo php84-php-xml php84-php-mbstring \
    php84-php-json php84-php-zip php84-php-gd php84-php-curl \
    php84-php-tokenizer php84-php-fileinfo php84-php-bcmath

# Update composer to use PHP 8.4
which php84  # Get path, usually /usr/bin/php84
composer config platform.php 8.4.0
```

**OPSI B: Downgrade Laravel ke 11 (Jika tidak bisa upgrade PHP)**
```bash
# Ubah composer.json
nano composer.json

# Change:
# "laravel/framework": "^13.0" → "laravel/framework": "^11.0"

# Save, then:
composer update
```

Setelah salah satu opsi di atas, lanjutkan:

```bash
# Install PHP dependencies
composer install --optimize-autoloader --no-dev

# Install Node.js dependencies untuk WhatsApp Gateway
cd whatsapp-server-absensi
npm install
cd ..
```

### 3.5 Setup Environment
```bash
# Copy .env.example
cp .env.example .env

# Edit .env untuk production
nano .env
```

**Konfigurasi `.env` untuk Production:**
```env
APP_NAME="Sistem Absensi"
APP_ENV=production
APP_KEY=base64:... (akan di-generate nanti)
APP_DEBUG=false
APP_URL=https://absensi.sekolah.sch.id

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=absensi_db
DB_USERNAME=absensi_user
DB_PASSWORD=your_secure_password_here

# WhatsApp Gateway Backup (akan running di server ini)
WA_SERVER_URL=http://localhost:3001

# Cache & Session (gunakan file atau redis jika tersedia)
CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync

# Mail (jika diperlukan)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_email@gmail.com
MAIL_PASSWORD=your_app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@sekolah.sch.id
MAIL_FROM_NAME="${APP_NAME}"
```

Save dengan `Ctrl+O`, Enter, `Ctrl+X`.

### 3.6 Generate Application Key
```bash
php artisan key:generate
```

### 3.7 Setup Database
```bash
# Run migrations
php artisan migrate --force

# Seed database (jika ada)
php artisan db:seed --force
```

### 3.8 Setup Storage & Permissions
```bash
# Create symlink for storage
php artisan storage:link

# Set correct permissions
chown -R www:www /www/wwwroot/absensi.sekolah.sch.id/absensi
chmod -R 755 /www/wwwroot/absensi.sekolah.sch.id/absensi
chmod -R 775 /www/wwwroot/absensi.sekolah.sch.id/absensi/storage
chmod -R 775 /www/wwwroot/absensi.sekolah.sch.id/absensi/bootstrap/cache
```

### 3.9 Optimize Laravel
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 📱 LANGKAH 4: Setup WhatsApp Gateway Backup (Port 3001)

### 4.1 Konfigurasi Gateway
```bash
cd /www/wwwroot/absensi.sekolah.sch.id/absensi/whatsapp-server-absensi

# Edit .env untuk production
nano .env
```

**Konfigurasi `.env` untuk Gateway:**
```env
PORT=3001
HOST=0.0.0.0
SESSION_NAME=spmb-wa-session-backup
NODE_ENV=production
```

Save dengan `Ctrl+O`, Enter, `Ctrl+X`.

### 4.2 Test Manual (Optional)
```bash
# Test run untuk memastikan tidak ada error
npm start

# Jika running OK, stop dengan Ctrl+C
```

### 4.3 Setup PM2 untuk Auto-Start
```bash
# Start with PM2
pm2 start server.js --name "wa-backup-gateway"

# Save PM2 process list
pm2 save

# Setup auto-start on server reboot
pm2 startup

# Copy-paste command yang muncul dan jalankan
# Contoh: sudo env PATH=$PATH:/usr/bin pm2 startup systemd -u root --hp /root

# Verify status
pm2 status
pm2 logs wa-backup-gateway
```

### 4.4 Konfigurasi Firewall (Jika Perlu)
```bash
# Allow port 3001 jika firewall aktif
firewall-cmd --permanent --add-port=3001/tcp
firewall-cmd --reload

# Atau di aaPanel Security
# aaPanel → Security → Firewall Rules → Add Rule
# Port: 3001, Protocol: TCP
```

---

## 🔗 LANGKAH 5: Konfigurasi Nginx untuk WhatsApp Gateway

### 5.1 Edit Nginx Config
```bash
# Edit site config
nano /www/server/panel/vhost/nginx/absensi.sekolah.sch.id.conf
```

### 5.2 Tambahkan Proxy untuk Port 3001
Tambahkan di dalam `server {}` block:

```nginx
# WhatsApp Gateway Backup Proxy
location /wa-backup/ {
    proxy_pass http://localhost:3001/;
    proxy_http_version 1.1;
    proxy_set_header Upgrade $http_upgrade;
    proxy_set_header Connection 'upgrade';
    proxy_set_header Host $host;
    proxy_cache_bypass $http_upgrade;
    proxy_set_header X-Real-IP $remote_addr;
    proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
    proxy_set_header X-Forwarded-Proto $scheme;
    
    # Timeouts untuk WhatsApp connection
    proxy_connect_timeout 60s;
    proxy_send_timeout 60s;
    proxy_read_timeout 60s;
}
```

Save dan reload Nginx:
```bash
nginx -t  # Test config
nginx -s reload  # Reload if test OK
```

---

## 🔐 LANGKAH 6: Setup WhatsApp QR Code

### 6.1 Akses Gateway UI
Buka browser:
```
https://absensi.sekolah.sch.id/wa-backup/
```

### 6.2 Scan QR Code
1. Akan muncul QR code
2. Buka WhatsApp di HP
3. Tap menu (⋮) → **"Perangkat Tertaut"**
4. Tap **"Tautkan Perangkat"**
5. Scan QR code yang muncul di browser
6. Tunggu sampai status berubah **"Connected"**

### 6.3 Verify Connection
```bash
# Check PM2 logs
pm2 logs wa-backup-gateway

# Should see: "WhatsApp client is ready!"
```

---

## ⚙️ LANGKAH 7: Update SPMB untuk Gunakan Gateway Backup

### 7.1 Update Database Settings SPMB
SSH ke server SPMB, lalu:

```bash
# Masuk ke Laravel tinker
php artisan tinker

# Update wa_server_url_backup
DB::table('whatsapp_settings')
    ->where('key', 'wa_server_url_backup')
    ->update(['value' => 'https://absensi.sekolah.sch.id/wa-backup']);

# Verify
DB::table('whatsapp_settings')
    ->where('key', 'wa_server_url_backup')
    ->first();

# Exit tinker
exit
```

### 7.2 Clear Cache SPMB
```bash
php artisan cache:clear
php artisan config:clear
```

### 7.3 Test Failover
1. Buka SPMB: `http://localhost:3000/admin/gateway`
2. Lihat status kedua gateway
3. Coba stop gateway primary (port 3000)
4. SPMB seharusnya auto-switch ke backup (port 3001 di hosting)

---

## 🧪 LANGKAH 8: Testing & Verification

### 8.1 Test Laravel Application
```bash
# Akses via browser
https://absensi.sekolah.sch.id

# Check logs jika ada error
tail -f /www/wwwroot/absensi.sekolah.sch.id/absensi/storage/logs/laravel.log
```

### 8.2 Test WhatsApp Gateway
```bash
# Check PM2 status
pm2 status

# Check logs
pm2 logs wa-backup-gateway --lines 50

# Test via browser
https://absensi.sekolah.sch.id/wa-backup/

# Test send message (via Laravel)
curl -X POST https://absensi.sekolah.sch.id/wa-backup/send \
  -H "Content-Type: application/json" \
  -d '{
    "phone": "628123456789",
    "message": "Test message dari gateway backup"
  }'
```

### 8.3 Test Failover dari SPMB
1. Buka SPMB Dashboard
2. Stop gateway primary:
   ```bash
   pm2 stop wa-primary-gateway
   ```
3. SPMB auto switch ke backup
4. Kirim test message dari SPMB
5. Message should go through backup gateway

---

## 📊 LANGKAH 9: Monitoring & Maintenance

### 9.1 Monitor PM2 Processes
```bash
# Status
pm2 status

# Logs real-time
pm2 logs

# Monitor resource usage
pm2 monit

# Info detail
pm2 info wa-backup-gateway
```

### 9.2 Auto-restart on Crash
PM2 sudah auto-restart by default, tapi pastikan:
```bash
pm2 restart wa-backup-gateway --watch
pm2 save
```

### 9.3 Setup Log Rotation
```bash
pm2 install pm2-logrotate

# Configure (optional)
pm2 set pm2-logrotate:max_size 10M
pm2 set pm2-logrotate:retain 7
```

### 9.4 Monitoring Script (Cron Job)
Buat script monitoring untuk alert jika gateway down:

```bash
nano /root/check-wa-gateway.sh
```

**Script content:**
```bash
#!/bin/bash

# Check if gateway is running
if pm2 list | grep -q "wa-backup-gateway.*online"; then
    echo "Gateway OK"
else
    echo "Gateway DOWN! Restarting..."
    pm2 restart wa-backup-gateway
    # Optional: send alert email
    echo "WhatsApp Gateway Backup was down and has been restarted at $(date)" | \
        mail -s "Alert: WA Gateway Restarted" admin@sekolah.sch.id
fi
```

Set executable dan add to cron:
```bash
chmod +x /root/check-wa-gateway.sh

# Add to crontab (run every 5 minutes)
crontab -e

# Add line:
*/5 * * * * /root/check-wa-gateway.sh >> /var/log/wa-gateway-check.log 2>&1
```

---

## 🔄 LANGKAH 10: Update & Deployment Selanjutnya

### 10.1 Update Code dari Git
```bash
cd /www/wwwroot/absensi.sekolah.sch.id/absensi

# Pull latest changes
git pull origin main

# Update dependencies
composer install --optimize-autoloader --no-dev
cd whatsapp-server-absensi && npm install && cd ..

# Run migrations
php artisan migrate --force

# Clear & cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Restart gateway
pm2 restart wa-backup-gateway
```

### 10.2 Rollback (Jika Diperlukan)
```bash
# Checkout previous commit
git log --oneline  # lihat commit history
git checkout <commit-hash>

# Atau revert specific commit
git revert <commit-hash>
git push origin main
```

---

## 🚨 Troubleshooting

### Problem 0: Composer Install Error - PHP Version Mismatch
**Error:** `symfony/clock v8.0.8 requires php >=8.4`

**Root Cause:** Laravel 13 membutuhkan PHP 8.4+, tapi server menggunakan PHP 8.3.x

**Solution A - Upgrade PHP (Recommended):**
```bash
# 1. Install PHP 8.4 dari Remi
yum install -y https://rpms.remirepo.net/enterprise/remi-release-8.rpm
yum-config-manager --enable remi-php84
yum install -y php84 php84-php-fpm php84-php-cli php84-php-common \
    php84-php-mysqlnd php84-php-pdo php84-php-xml php84-php-mbstring \
    php84-php-json php84-php-zip php84-php-gd php84-php-curl \
    php84-php-tokenizer php84-php-fileinfo php84-php-bcmath

# 2. Verify installation
php84 --version

# 3. Update aaPanel to use PHP 8.4
# Di aaPanel: Website → Settings → PHP Version → pilih PHP 8.4

# 4. Update composer
composer self-update

# 5. Clear composer cache
composer clear-cache

# 6. Try install again
composer install --optimize-autoloader --no-dev
```

**Solution B - Downgrade ke Laravel 11:**
```bash
# 1. Edit composer.json
nano composer.json

# 2. Ubah line:
#    "laravel/framework": "^13.0"
# Menjadi:
#    "laravel/framework": "^11.0"

# 3. Save dan update
composer update

# 4. Run migrations (might need to adjust)
php artisan migrate --force
```

**Solution C - Force Install with Platform Override (Not Recommended):**
```bash
# Override platform check (temporary fix)
composer config platform.php 8.4.0
composer install --ignore-platform-reqs --optimize-autoloader --no-dev

# Note: Ini bisa menyebabkan runtime error jika ada fitur PHP 8.4 yang digunakan
```

### Problem 1: Gateway Tidak Connect
**Solution:**
```bash
# Check PM2 logs
pm2 logs wa-backup-gateway

# Check if port 3001 is listening
netstat -tlnp | grep 3001

# Restart gateway
pm2 restart wa-backup-gateway

# Delete old session and regenerate QR
rm -rf whatsapp-server-absensi/spmb-wa-session-backup
pm2 restart wa-backup-gateway
```

### Problem 2: Laravel 500 Error
**Solution:**
```bash
# Check logs
tail -f storage/logs/laravel.log

# Check permissions
chown -R www:www /www/wwwroot/absensi.sekolah.sch.id/absensi
chmod -R 775 storage bootstrap/cache

# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

### Problem 3: Database Connection Error
**Solution:**
```bash
# Check database credentials in .env
nano .env

# Test connection
php artisan migrate:status

# Check MySQL service
systemctl status mysql
```

### Problem 4: PM2 Not Auto-starting on Reboot
**Solution:**
```bash
# Re-setup startup script
pm2 unstartup
pm2 startup
# Run the command it gives you
pm2 save
```

---

## 📝 Checklist Deployment

### Pre-Deployment
- [ ] Repository Absensi ready di GitHub
- [ ] Domain sudah pointing ke server IP
- [ ] SSL certificate ready
- [ ] Database credentials prepared
- [ ] .env file configured

### Deployment
- [ ] aaPanel installed dan configured
- [ ] PHP 8.2+, MySQL, Nginx installed
- [ ] Node.js 18+ & PM2 installed
- [ ] Website created di aaPanel
- [ ] SSL enabled
- [ ] Laravel cloned & setup
- [ ] Database migrated & seeded
- [ ] WhatsApp Gateway running with PM2
- [ ] Nginx proxy configured
- [ ] QR code scanned & connected

### Post-Deployment
- [ ] Test Laravel application accessible
- [ ] Test WhatsApp Gateway UI accessible
- [ ] Test send message via gateway
- [ ] Test failover from SPMB
- [ ] Monitoring setup (PM2 + cron)
- [ ] Backup strategy implemented
- [ ] Documentation updated

---

## 🎯 Summary

Setelah mengikuti panduan ini, Anda akan memiliki:

1. ✅ **Laravel Absensi** running di hosting dengan SSL
2. ✅ **WhatsApp Gateway Backup** (port 3001) running 24/7 dengan PM2
3. ✅ **SPMB** dapat auto-failover ke gateway backup jika primary down
4. ✅ **Monitoring & auto-restart** untuk high availability
5. ✅ **Akses via domain** (https://absensi.sekolah.sch.id/wa-backup/)

**Next Steps:**
- Develop fitur Absensi system
- Integrate with existing systems
- Setup backup & disaster recovery
- Monitor performance & optimize

---

📞 **Support:**
- GitHub Issues: https://github.com/muochgack2-glitch/Absensi/issues
- Documentation: Check repo README.md
