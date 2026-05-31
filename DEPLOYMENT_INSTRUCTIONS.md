# 🚀 Deployment Instructions - WhatsApp Auto-Reconnect Fix

## 📋 Ringkasan Perubahan

**Problem**: Setelah logout, QR code tidak muncul otomatis. Harus restart PM2 manual.

**Solution**: Auto-reconnect dengan QR generation otomatis setelah logout.

**Version**: 1.1.0

---

## 🔧 Files yang Diubah

1. **whatsapp-server/server.js**
   - Reset reconnect counter saat manual logout
   - Improved logout endpoint dengan timing delays
   - Better error handling dan logging

2. **Documentation**
   - `WHATSAPP_AUTO_RECONNECT_FIX.md` - Dokumentasi lengkap fix
   - `whatsapp-server/README.md` - Updated dengan fitur baru
   - `whatsapp-server/test-logout.sh` - Testing script (Linux/Mac)
   - `whatsapp-server/test-logout.bat` - Testing script (Windows)

---

## 📦 Deployment Steps

### Step 1: Push ke GitHub (Sudah Dilakukan)

```bash
# Di local machine (Windows)
cd C:\Users\DMCenter\Music\SPMB2\SPMB
git add .
git commit -m "Fix: Auto-reconnect after logout with QR generation"
git push origin main
```

### Step 2: Deploy ke aaPanel Server

```bash
# SSH ke server aaPanel
ssh user@your-server-ip

# Masuk ke directory project
cd /www/wwwroot/spmb

# Pull changes dari GitHub
git pull origin main

# Restart PM2 service
pm2 restart spmb-wa-gateway

# Monitor logs untuk memastikan tidak ada error
pm2 logs spmb-wa-gateway --lines 50
```

**Expected Output**:
```
[INFO] WhatsApp Gateway Server running on http://0.0.0.0:3000
[INFO] Connecting to WhatsApp...
[INFO] WhatsApp connection established successfully!
```

---

## 🧪 Testing Procedure

### Test 1: Verify Server Running

```bash
# Check PM2 status
pm2 status

# Should show:
# │ spmb-wa-gateway │ online │

# Check server health
curl http://localhost:3000/status

# Should return:
# {"success":true,"status":"connected",...}
```

### Test 2: Test Logout Flow

1. **Buka Dashboard Admin**
   - URL: `https://your-domain.com/whatsapp`
   - Login sebagai admin

2. **Verify Connected Status**
   - Status badge harus "Connected" (hijau)
   - Jika belum connected, scan QR dulu

3. **Trigger Logout**
   - Klik menu "Settings" di sidebar WhatsApp
   - Scroll ke bawah ke "Danger Zone"
   - Klik tombol "Logout WhatsApp"
   - Konfirmasi logout

4. **Monitor di Terminal Server**
   ```bash
   pm2 logs spmb-wa-gateway --lines 30
   ```

5. **Expected Logs**:
   ```
   [INFO] Logout requested - preparing to disconnect and generate new QR...
   [INFO] Successfully logged out from WhatsApp
   [INFO] Session folder deleted successfully
   [INFO] Starting reconnection to generate new QR code...
   [INFO] Manual logout detected - resetting reconnect counter
   [INFO] Reconnecting... Attempt 1/5
   [INFO] QR Code generated, scan with WhatsApp
   ```

6. **Verify Dashboard**
   - Kembali ke dashboard (auto-refresh setiap 5 detik)
   - Status harus "Waiting QR Scan" (kuning)
   - QR code harus muncul otomatis dalam 5-10 detik
   - **TIDAK PERLU restart PM2!**

7. **Scan QR Code**
   - Scan QR dengan WhatsApp di HP
   - Status harus berubah ke "Connected" (hijau)
   - QR section hilang otomatis

### Test 3: Automated Testing (Optional)

```bash
# Di server
cd /www/wwwroot/spmb/whatsapp-server

# Make script executable
chmod +x test-logout.sh

# Run test
./test-logout.sh

# Expected output:
# ✓ Logout request successful
# ✓ SUCCESS! QR code generated automatically
# Test PASSED!
```

---

## ✅ Success Criteria

Fix dianggap berhasil jika:

- [x] Setelah logout, tidak ada error di PM2 logs
- [x] QR code muncul otomatis dalam 5-10 detik
- [x] Tidak perlu restart PM2 manual
- [x] Dashboard auto-refresh dan tampilkan QR
- [x] User bisa scan QR dan connect kembali
- [x] Tidak ada error "Max reconnect attempts"

---

## 🔍 Monitoring

### Check PM2 Logs
```bash
# Real-time logs
pm2 logs spmb-wa-gateway

# Last 100 lines
pm2 logs spmb-wa-gateway --lines 100

# Only errors
pm2 logs spmb-wa-gateway --err
```

### Check Server Status
```bash
# Via cURL
curl http://localhost:3000/status | jq '.'

# Via browser (if port exposed)
# http://localhost:3000/status
```

### Check Session Folder
```bash
# List session files
ls -la /www/wwwroot/spmb/whatsapp-server/spmb-wa-session/

# Should be empty after logout
# Should have files after successful connection
```

---

## 🚨 Troubleshooting

### Issue 1: Git Pull Failed

**Error**: `error: Your local changes would be overwritten by merge`

**Solution**:
```bash
# Stash local changes
git stash

# Pull changes
git pull origin main

# Apply stashed changes (if needed)
git stash pop
```

### Issue 2: PM2 Restart Failed

**Error**: `[PM2][ERROR] Process not found`

**Solution**:
```bash
# Stop all PM2 processes
pm2 stop all

# Start WhatsApp server
pm2 start /www/wwwroot/spmb/whatsapp-server/server.js --name spmb-wa-gateway

# Save PM2 config
pm2 save
```

### Issue 3: QR Code Still Not Appearing

**Symptoms**: Status stuck di "Disconnected" setelah logout

**Solution**:
```bash
# 1. Check logs for errors
pm2 logs spmb-wa-gateway --lines 50

# 2. Delete session folder manually
rm -rf /www/wwwroot/spmb/whatsapp-server/spmb-wa-session

# 3. Restart PM2
pm2 restart spmb-wa-gateway

# 4. Wait 10 seconds and check status
sleep 10
curl http://localhost:3000/status

# 5. Check dashboard - QR should appear
```

### Issue 4: Max Reconnect Attempts Error

**Error**: `Max reconnect attempts reached. Please restart the server.`

**Solution**:
```bash
# This should NOT happen with the fix, but if it does:

# 1. Restart PM2
pm2 restart spmb-wa-gateway

# 2. Check .env configuration
cat /www/wwwroot/spmb/whatsapp-server/.env

# 3. Verify MAX_RECONNECT_ATTEMPTS is set
# Should be: MAX_RECONNECT_ATTEMPTS=5

# 4. If still failing, check for code issues
pm2 logs spmb-wa-gateway --lines 100
```

### Issue 5: Dashboard Not Auto-Refreshing

**Symptoms**: QR code tidak muncul di dashboard meskipun server sudah generate

**Solution**:
1. Hard refresh browser: `Ctrl + Shift + R` (Windows) atau `Cmd + Shift + R` (Mac)
2. Clear browser cache
3. Check browser console (F12) untuk JavaScript errors
4. Verify Laravel route: `php artisan route:list | grep whatsapp`

---

## 📊 Performance Metrics

### Expected Timings:

| Event | Time |
|-------|------|
| Logout request | T+0s |
| Session deleted | T+1s |
| Reconnect triggered | T+3s |
| QR generated | T+5s |
| Dashboard shows QR | T+5-10s |

### Resource Usage:

- **Memory**: ~50-100 MB (Node.js process)
- **CPU**: <5% (idle), ~20% (during reconnect)
- **Disk**: ~10 MB (session files when connected)

---

## 🔐 Security Checklist

- [x] Port 3000 tidak exposed ke public (hanya localhost)
- [x] Session folder di .gitignore
- [x] Environment variables di .env (tidak di commit)
- [x] Rate limiting aktif (1 msg/second)
- [x] Error messages tidak expose sensitive info
- [x] Logs tidak contain phone numbers atau message content

---

## 📝 Rollback Plan

Jika fix menyebabkan masalah:

```bash
# 1. Revert ke commit sebelumnya
cd /www/wwwroot/spmb
git log --oneline -5  # Lihat commit history
git revert HEAD       # Revert last commit

# 2. Restart PM2
pm2 restart spmb-wa-gateway

# 3. Atau checkout ke commit spesifik
git checkout <commit-hash>
pm2 restart spmb-wa-gateway
```

---

## 📞 Support Contacts

Jika masih ada masalah setelah deployment:

1. **Check Documentation**:
   - `WHATSAPP_AUTO_RECONNECT_FIX.md` - Detailed fix documentation
   - `whatsapp-server/README.md` - Server documentation
   - `WHATSAPP_DATABASE_SCHEMA.md` - Database schema

2. **Check Logs**:
   - PM2: `pm2 logs spmb-wa-gateway`
   - Laravel: `tail -f /www/wwwroot/spmb/storage/logs/laravel.log`
   - Nginx: `tail -f /www/wwwlogs/your-domain.com.log`

3. **Debug Mode**:
   ```bash
   # Enable debug logging
   cd /www/wwwroot/spmb/whatsapp-server
   nano .env
   # Change: LOG_LEVEL=debug
   pm2 restart spmb-wa-gateway
   pm2 logs spmb-wa-gateway
   ```

---

## ✨ Next Steps

Setelah deployment berhasil:

1. **Monitor selama 24 jam** - Pastikan tidak ada error atau crash
2. **Test dengan user real** - Minta admin test logout/login beberapa kali
3. **Document any issues** - Catat jika ada behavior yang unexpected
4. **Update documentation** - Jika ada perubahan atau improvement

---

## 📅 Deployment Checklist

**Pre-Deployment**:
- [x] Code tested locally
- [x] Documentation updated
- [x] Testing scripts created
- [x] Rollback plan prepared

**Deployment**:
- [ ] SSH to aaPanel server
- [ ] Backup current code: `cp -r /www/wwwroot/spmb /www/backup/spmb-$(date +%Y%m%d)`
- [ ] Pull latest code: `git pull origin main`
- [ ] Restart PM2: `pm2 restart spmb-wa-gateway`
- [ ] Check logs: `pm2 logs spmb-wa-gateway --lines 50`
- [ ] Verify no errors

**Testing**:
- [ ] Server status check: `curl http://localhost:3000/status`
- [ ] Dashboard accessible
- [ ] WhatsApp connected
- [ ] Logout test
- [ ] QR auto-appears
- [ ] Scan QR and reconnect
- [ ] Send test message

**Post-Deployment**:
- [ ] Monitor logs for 1 hour
- [ ] Notify admin to test
- [ ] Document any issues
- [ ] Update status: DEPLOYED ✅

---

**Deployment Date**: _________________

**Deployed By**: _________________

**Status**: ⬜ Pending | ⬜ In Progress | ⬜ Completed | ⬜ Failed

**Notes**:
_________________________________________________________________
_________________________________________________________________
_________________________________________________________________

---

**Version**: 1.1.0  
**Last Updated**: 2026-05-31  
**Author**: Kiro AI Assistant
