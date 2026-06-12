# ✅ Deployment Checklist: Dual WhatsApp Gateway

## 📋 Pre-Deployment (Local)

- [x] Folder `whatsapp-server-absensi` created
- [x] Migration created
- [x] `WhatsAppService.php` updated with failover
- [x] `WhatsAppGatewayController.php` created
- [x] Gateway management view created
- [x] Routes added
- [x] Documentation created
- [ ] **Git commit & push**

## 🚀 Deployment Steps (Server)

### 1. Pull Changes
```bash
cd /www/wwwroot/your-domain.com
git pull origin main
```

### 2. Setup Gateway Absensi
```bash
cd whatsapp-server-absensi

# Create .env
cp .env.example .env

# Verify .env settings
cat .env
# Should show:
# PORT=3001
# HOST=0.0.0.0
# SESSION_NAME=spmb-wa-session-absensi

# Install dependencies
npm install

# Start with PM2
pm2 start server.js --name wa-gateway-absensi
pm2 save
pm2 startup
```

### 3. Run Migration
```bash
cd ..
php artisan migrate

# Verify settings added
php artisan tinker
>>> \App\Models\WhatsAppSetting::whereIn('key', ['wa_server_url_backup', 'wa_failover_enabled'])->get();
>>> exit
```

### 4. Clear Cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### 5. Verify PM2
```bash
pm2 list

# Should show:
# ┌─────┬─────────────────────┬─────┬────────┐
# │ id  │ name                │ ... │ status │
# ├─────┼─────────────────────┼─────┼────────┤
# │ 0   │ wa-gateway-spmb     │ ... │ online │
# │ 1   │ wa-gateway-absensi  │ ... │ online │
# └─────┴─────────────────────┴─────┴────────┘
```

### 6. Test Endpoints
```bash
# Test Primary (3000)
curl http://localhost:3000/status
# Should return: {"success":true,"status":"..."}

# Test Backup (3001)
curl http://localhost:3001/status
# Should return: {"success":true,"status":"..."}
```

## 🔐 Scan QR Codes

### Via Web UI (Recommended):
1. Open: `https://your-domain.com/admin/gateway`
2. Login as Administrator atau Admin WA
3. **Gateway SPMB (Primary):**
   - Click "QR Code" button
   - Scan dengan HP nomor SPMB (08123...)
   - Wait for "Connected" status
4. **Gateway Absensi (Backup):**
   - Click "QR Code" button
   - Scan dengan HP nomor Absensi (08987...)
   - Wait for "Connected" status

### Via CLI (Alternative):
```bash
# Get QR for Primary
curl http://localhost:3000/qr

# Get QR for Backup
curl http://localhost:3001/qr
```

## ✅ Testing

### Test 1: Gateway Status UI
- [ ] Open `/admin/gateway`
- [ ] See both gateways with status
- [ ] Green "Online" badge visible
- [ ] Uptime & memory stats visible

### Test 2: QR Code View
- [ ] Click "QR Code" button on SPMB gateway
- [ ] Modal opens with QR code image
- [ ] QR code is scannable
- [ ] Click "QR Code" button on Absensi gateway
- [ ] Modal opens with different QR code

### Test 3: Send Test Message (Primary)
```bash
cd whatsapp-server
curl -X POST http://localhost:3000/send \
  -H "Content-Type: application/json" \
  -d '{"phone":"YOUR_TEST_NUMBER","message":"Test dari gateway primary"}'
```
- [ ] Message received on WhatsApp

### Test 4: Failover Test
```bash
# Stop primary
pm2 stop wa-gateway-spmb

# Try send via Laravel (should auto-use backup)
# Via WhatsApp UI or API

# Check logs
tail -f storage/logs/laravel.log
# Should see: "Primary gateway unhealthy, switching to backup"

# Start primary again
pm2 start wa-gateway-spmb

# Try send again (should auto-use primary)
```
- [ ] Message sent via backup when primary down
- [ ] Message sent via primary when back online
- [ ] Logs show failover messages

### Test 5: Gateway Management Features
- [ ] Click "Restart" button → Gateway restarts successfully
- [ ] Click "Logs" button → PM2 logs displayed
- [ ] Click "Logout" button → Logout successful, new QR generated

### Test 6: Permissions
- [ ] Login as Administrator → Can access `/admin/gateway`
- [ ] Login as Admin WA → Can access `/admin/gateway`
- [ ] Login as Panitia → Cannot access (403/redirect)

## 📊 Monitoring Post-Deployment

### Check Resource Usage:
```bash
# PM2 monitoring
pm2 monit

# Server resources
htop
# or
free -h
df -h
```

Expected:
- Gateway SPMB: ~200 MB RAM
- Gateway Absensi: ~200 MB RAM
- Total increase: ~400 MB RAM
- Server still has 4+ GB free

### Check Logs:
```bash
# Gateway logs
pm2 logs wa-gateway-spmb --lines 20
pm2 logs wa-gateway-absensi --lines 20

# Laravel logs
tail -f storage/logs/laravel.log
```

### Health Check:
```bash
# Every 5 minutes for first hour
watch -n 300 'curl -s http://localhost:3000/status && curl -s http://localhost:3001/status'
```

## 🚨 Rollback Plan (If Issues)

```bash
# Stop new gateway
pm2 stop wa-gateway-absensi
pm2 delete wa-gateway-absensi

# Rollback migration
php artisan migrate:rollback --step=1

# Revert code
git log --oneline
git revert <commit-hash>
git push origin main

# Clear cache
php artisan cache:clear
php artisan config:clear
```

## ✅ Success Criteria

- [x] Both gateways running in PM2
- [x] Both gateways showing "Connected" status
- [x] Gateway management UI accessible
- [x] QR codes visible and scannable
- [x] Test messages sent successfully
- [x] Failover working (backup used when primary down)
- [x] Logs showing proper failover messages
- [x] Resource usage within acceptable limits
- [x] No errors in Laravel logs
- [x] PM2 processes stable (no restarts)

## 📞 Post-Deployment

### Communication:
1. Inform admins about new Gateway Management UI
2. Share URL: `/admin/gateway`
3. Brief demo of features
4. Share documentation links

### Monitor for 24 Hours:
- Check PM2 status every hour
- Check Laravel logs for errors
- Monitor resource usage
- Test sending messages periodically

### After 24 Hours:
- Review logs for any issues
- Adjust failover timeout if needed
- Update documentation based on feedback

## 🎉 Deployment Complete!

Gateway dual setup dengan UI management berhasil di-deploy! 🚀
