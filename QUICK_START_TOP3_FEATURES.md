# Quick Start: WA Gateway Top 3 Features

## 🚀 Deployment Checklist (10 Menit)

### ✅ Step 1: Pull Code (30 detik)
```bash
cd /path/to/spmb
git pull origin main
```

### ✅ Step 2: Run Migration (30 detik)
```bash
php artisan migrate
```
Ini akan create table `notifications` untuk notifikasi.

### ✅ Step 3: Clear Cache (30 detik)
```bash
php artisan view:clear
php artisan cache:clear
php artisan route:clear
```

### ✅ Step 4: Setup Cron (2 menit) **PENTING!**
```bash
crontab -e
```

Tambahkan line ini:
```
* * * * * cd /path/to/spmb && php artisan schedule:run >> /dev/null 2>&1
```

Save & exit (`:wq` jika vim)

**Verify:**
```bash
php artisan schedule:list
```

Harus muncul:
- `wa:restart --force` (daily at 3 AM)
- `wa:monitor` (every 5 minutes)

### ✅ Step 5: Configure Email (5 menit)
Edit `.env`:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@domain.com
MAIL_FROM_NAME="SPMB SMK"
```

**Untuk Gmail:**
1. Buat App Password: https://myaccount.google.com/apppasswords
2. Copy password ke `MAIL_PASSWORD`

### ✅ Step 6: Test (2 menit)
```bash
# Test health monitoring
curl http://localhost:3000/health

# Test restart command
php artisan wa:restart

# Test monitor
php artisan wa:monitor --verbose
```

---

## 🎯 What You Get

### 1. Dashboard baru menampilkan:
```
┌─────────────────────────────────────┐
│ 📊 Server Health                    │
│                                     │
│ Uptime: 2d 5h 30m                  │
│ Memory: 120 / 512 MB               │
│ Memory %: 23% (hijau)              │
│ Node: v24.15.0                     │
└─────────────────────────────────────┘
```

### 2. Automatic restart:
- **Setiap hari jam 3 pagi**
- Session preserved (tidak perlu scan QR)
- Downtime hanya 5-10 detik
- Auto-reconnect

### 3. Email notifications:
- **Disconnect alert:** ⚠️ Warning email immediately
- **Reconnect alert:** ✅ Success email
- Dikirim ke semua administrator & admin_wa

---

## 📧 Email Configuration Examples

### Gmail
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=yourname@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
```

### Mailtrap (Testing)
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your-mailtrap-username
MAIL_PASSWORD=your-mailtrap-password
MAIL_ENCRYPTION=tls
```

### SMTP Custom
```env
MAIL_MAILER=smtp
MAIL_HOST=mail.yourdomain.com
MAIL_PORT=587
MAIL_USERNAME=noreply@yourdomain.com
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
```

### Mailgun
```env
MAIL_MAILER=mailgun
MAILGUN_DOMAIN=mg.yourdomain.com
MAILGUN_SECRET=your-mailgun-key
```

---

## 🧪 Testing After Deploy

### Test 1: Health Monitoring (30 detik)
1. Buka `https://your-domain.com/whatsapp`
2. Lihat card "Server Health" di atas
3. Cek metrics: Uptime, Memory, dll
4. Tunggu 30 detik, metrics akan refresh otomatis

**Expected:** ✅ Metrics muncul dan update otomatis

---

### Test 2: Manual Restart (1 menit)
```bash
php artisan wa:restart
```

**Expected output:**
```
🔄 WhatsApp Gateway Server Restart
================================

Checking server status...
   Current status: connected

Are you sure you want to restart the server? (yes/no) [no]:
> yes

🔄 Restarting server...
✅ Server is restarting... Please wait 5-10 seconds.
⏳ Waiting 10 seconds for server to restart...
🔍 Checking server status...
   New status: connected
✅ Server restarted and reconnected successfully!
```

---

### Test 3: Status Monitor (30 detik)
```bash
php artisan wa:monitor --verbose
```

**Expected output:**
```
🔍 Monitoring WhatsApp Gateway status...
   Previous: connected
   Current:  connected
✅ No status change detected
```

---

### Test 4: Scheduled Tasks (30 detik)
```bash
php artisan schedule:list
```

**Expected output:**
```
┌────────────────────────────────────────────────────────────┐
│ 0 3 * * * wa:restart --force ............. Next: 8h 30m   │
│ */5 * * * * wa:monitor .................... Next: 5m       │
└────────────────────────────────────────────────────────────┘
```

---

### Test 5: Email Notification (Manual Trigger)
**Simulasi disconnect:**
```bash
# Stop Node.js
pm2 stop wa-gateway

# Wait 5 minutes for monitor to detect
# Or trigger manually:
php artisan wa:monitor --verbose

# Start Node.js
pm2 start wa-gateway

# Wait 5 minutes or trigger manually:
php artisan wa:monitor --verbose
```

**Expected:** 
- ✅ Email diterima saat disconnect
- ✅ Email diterima saat reconnect
- ✅ Check database: `select * from notifications;`

---

## ⚡ Quick Commands Reference

### Health & Status
```bash
# Check server health
curl http://localhost:3000/health | jq

# Check server status
curl http://localhost:3000/status | jq

# Monitor status (verbose)
php artisan wa:monitor --verbose
```

### Restart
```bash
# Manual restart (with confirmation)
php artisan wa:restart

# Force restart (no confirmation)
php artisan wa:restart --force
```

### Scheduler
```bash
# List scheduled tasks
php artisan schedule:list

# Run scheduler manually (test)
php artisan schedule:run

# Test specific command
php artisan schedule:test
```

### Logs
```bash
# Laravel logs
tail -f storage/logs/laravel.log

# Filter WA Gateway logs
tail -f storage/logs/laravel.log | grep -i "whatsapp"

# Node.js logs
pm2 logs wa-gateway

# PM2 status
pm2 status
```

### Database
```bash
# Check notifications
php artisan tinker
>>> \App\Models\Admin::first()->notifications;

# Clear old notifications (older than 30 days)
>>> \Illuminate\Notifications\DatabaseNotification::where('created_at', '<', now()->subDays(30))->delete();
```

---

## 🔧 Troubleshooting

### Health monitoring tidak muncul
```bash
# 1. Hard refresh browser
Ctrl + Shift + R

# 2. Check Node.js endpoint
curl http://localhost:3000/health

# 3. Restart Node.js jika perlu
pm2 restart wa-gateway
```

### Scheduled tasks tidak jalan
```bash
# 1. Check cron installed
crontab -l

# 2. Check scheduler
php artisan schedule:list

# 3. Run manual to test
php artisan schedule:run

# 4. Check permission
ls -la artisan
chmod +x artisan
```

### Email tidak terkirim
```bash
# 1. Test email config
php artisan tinker
>>> Mail::raw('Test', fn($m) => $m->to('admin@test.com')->subject('Test'));

# 2. Check .env
cat .env | grep MAIL

# 3. Check logs
tail -f storage/logs/laravel.log | grep mail

# 4. For Gmail: Enable "Less secure app access" or use App Password
```

### Notification tidak tersimpan
```bash
# 1. Check table exists
php artisan migrate:status

# 2. Run migration if needed
php artisan migrate

# 3. Check database
mysql -u user -p database
> SELECT * FROM notifications;
```

---

## 📊 Expected Behavior

### Server Health Card
**Update frequency:** Every 30 seconds
**Metrics shown:**
- Uptime (formatted: Xd Xh Xm)
- Memory usage (XX / YY MB)
- Memory percentage (color-coded)
- Node.js version

**Color coding:**
- Green: < 75% memory
- Yellow: 75-90% memory
- Red: > 90% memory

### Auto Restart
**Schedule:** Daily at 3:00 AM
**Duration:** 5-10 seconds
**Impact:** Minimal (off-peak hours)
**Session:** Preserved (no QR needed)
**Reconnect:** Automatic

### Status Notifications
**Check frequency:** Every 5 minutes
**Trigger conditions:**
- Status change detected
- Previous ≠ Current status

**Email sent to:**
- All `administrator` role
- All `admin_wa` role

**Email types:**
- Warning: Connected → Disconnected
- Success: Disconnected → Connected

---

## ✅ Success Indicators

After deployment, verify:
- [x] Health card visible in dashboard
- [x] Metrics updating every 30 seconds
- [x] `php artisan wa:restart` works
- [x] `php artisan schedule:list` shows 2 tasks
- [x] Cron configured (via `crontab -l`)
- [x] Email config in .env
- [x] Test email sent successfully
- [x] Monitor command detects changes
- [x] Notifications stored in database
- [x] Auto-restart scheduled at 3 AM

---

## 🎊 Summary

**Deployment time:** 10 minutes
**Configuration:** Minimal (just .env email)
**Impact:** Zero downtime
**Benefits:** 
- Real-time monitoring ✅
- Automatic maintenance ✅  
- Proactive alerts ✅

**You're done!** 🚀

Monitor for 24 hours to ensure:
- Health metrics updating
- Scheduled tasks running
- Notifications working

---

## 📞 Need Help?

Check full documentation: `WA_GATEWAY_TOP3_FEATURES.md`

Common issues and solutions included in troubleshooting section above.

**Happy monitoring!** 😊
