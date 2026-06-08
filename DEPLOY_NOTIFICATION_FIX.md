# 🚀 Deploy Telegram Notification Fix

## ❌ Masalah yang Ditemukan
1. **Config Telegram tidak terload** - Bot token dan Chat ID kosong di `.env`
2. **SSL certificate error di lokal** - Normal untuk Windows development environment

## ✅ Solusi
Telegram credentials harus diisi di file `.env` hosting

---

## 📋 LANGKAH DEPLOYMENT DI HOSTING

### 1. Pull Update Terbaru
```bash
cd /path/to/spmb
git pull origin main
```

### 2. Isi Telegram Credentials di .env
```bash
nano .env
# atau
vim .env
```

**Tambahkan/update baris berikut:**
```env
TELEGRAM_BOT_TOKEN=8874333362:AAFKA7x_T6p_BwPTNRV450wqD4gQ6qA7STg
TELEGRAM_CHAT_ID=-1003914556507
```

**PENTING:** Pastikan tidak ada spasi sebelum/sesudah nilai!

### 3. Clear All Cache
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
```

### 4. Test Direct API Call (Harus Berhasil!)
```bash
php test_notification.php
```

**Expected Output:**
```
=== TESTING TELEGRAM NOTIFICATION ===

1. Checking Telegram Config:
   Bot Token: 8874333362...
   Chat ID: -1003914556507

2. Testing Direct Telegram API Call:
   ✅ Direct API call SUCCESS!

3. Testing Admin Notification:
   ✅ Notification method called!

=== TEST COMPLETE ===
```

**Cek Telegram group, harus ada 2 pesan:**
1. Pesan dari direct API call
2. Pesan dari notification (dengan buttons)

### 5. Test Monitoring Command

**A. Simpan status "connected":**
```bash
# Pastikan WA server running dulu
pm2 status whatsapp-server

php artisan wa:monitor
```

Output:
```
🔍 Monitoring WhatsApp Gateway status...
   Previous: unknown
   Current:  connected
✅ No status change detected
```

**B. Stop WA server dan test disconnect:**
```bash
pm2 stop whatsapp-server

php artisan wa:monitor
```

Output:
```
🔍 Monitoring WhatsApp Gateway status...
⚠️  Cannot connect to server, assuming disconnected
   Previous: connected
   Current:  disconnected
🔔 Status changed: connected → disconnected
📧 Sending notifications...
   ✅ Sent 1 notification(s)
```

**Cek Telegram - harus ada notifikasi disconnect dengan buttons!**

**C. Start WA server dan test reconnect:**
```bash
pm2 start whatsapp-server

sleep 5  # tunggu 5 detik

php artisan wa:monitor
```

Output:
```
🔍 Monitoring WhatsApp Gateway status...
   Previous: disconnected
   Current:  connected
🔔 Status changed: disconnected → connected
📧 Sending notifications...
   ✅ Sent 1 notification(s)
```

**Cek Telegram - harus ada notifikasi reconnect!**

### 6. Setup Cron Job (Auto-monitor Every 5 Minutes)
```bash
crontab -e
```

**Tambahkan baris ini:**
```cron
# Monitor WA Gateway status every 5 minutes
*/5 * * * * cd /path/to/spmb && php artisan wa:monitor >> /dev/null 2>&1
```

**Ganti `/path/to/spmb` dengan path sebenarnya!**

Contoh:
```cron
*/5 * * * * cd /home/user/public_html/spmb && php artisan wa:monitor >> /dev/null 2>&1
```

Save dan exit (`:wq` di vim, atau `Ctrl+X` lalu `Y` di nano)

### 7. Verify Cron Job
```bash
crontab -l
```

Harus muncul entry monitoring.

---

## 🧪 Test Semua Buttons di Telegram

Setelah notifikasi masuk, test semua inline buttons:

1. **📊 View Dashboard** - Buka URL dashboard (harus buka https://spmb.smkpgriblora.sch.id/whatsapp)
2. **🔄 Restart Server** - Restart Node.js server (Bot reply: "Server restart initiated")
3. **🔌 Reset & Reconnect** - Logout dan reconnect (Bot reply: "Reset initiated")
4. **🔍 Check Status** - Cek status real-time (Bot reply dengan status detail)

---

## 📝 Checklist

- [ ] Git pull berhasil
- [ ] `.env` sudah diisi dengan TELEGRAM_BOT_TOKEN dan TELEGRAM_CHAT_ID
- [ ] Cache sudah di-clear
- [ ] Test script berhasil (2 pesan masuk ke Telegram)
- [ ] Monitoring command detect disconnect (notifikasi masuk)
- [ ] Monitoring command detect reconnect (notifikasi masuk)
- [ ] Cron job sudah disetup
- [ ] Semua buttons di Telegram berfungsi

---

## 🐛 Troubleshooting

### Notifikasi tidak masuk:
1. Cek `.env` - pastikan TELEGRAM_BOT_TOKEN dan TELEGRAM_CHAT_ID terisi
2. Run: `php artisan config:clear`
3. Cek log: `tail -f storage/logs/laravel.log`

### Button tidak respond:
1. Pastikan webhook sudah diset: `https://spmb.smkpgriblora.sch.id/telegram/webhook`
2. Cek route: `php artisan route:list | grep telegram`
3. Cek controller: `app/Http/Controllers/TelegramWebhookController.php`

### Monitoring command gagal:
1. Cek WA server running: `pm2 status whatsapp-server`
2. Cek endpoint: `curl http://localhost:3000/status`
3. Cek previous status: `php artisan tinker` lalu `Cache::get('wa_gateway_previous_status')`

---

## 🎯 Commits
- `edce673` - fix: send Telegram notification directly without custom channel
- `f06fc74` - fix: treat unreachable WA server as disconnected in monitor command

---

## 📞 Support
Jika masih ada masalah, cek file log di:
- Laravel: `storage/logs/laravel.log`
- PM2: `pm2 logs whatsapp-server`
