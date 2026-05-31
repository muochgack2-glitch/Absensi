# WhatsApp Auto-Reconnect After Logout - Fix Documentation

## Problem Description

Setelah logout dari WhatsApp Gateway, sistem tidak otomatis generate QR code baru. User harus restart PM2 secara manual untuk mendapatkan QR code baru.

### Error yang Muncul:
```
Max reconnect attempts reached. Please restart the server.
```

## Root Cause Analysis

1. **Reconnect Counter Tidak Di-reset**: Saat `manualLogout = true`, counter `reconnectAttempts` tidak di-reset, sehingga jika sudah ada attempt sebelumnya, bisa langsung mencapai limit (5 attempts)

2. **Timing Issue**: Session folder dihapus terlalu cepat sebelum socket benar-benar disconnect

3. **Socket State**: Socket tidak di-set ke `null` setelah logout, menyebabkan state tidak konsisten

## Solution Implemented

### 1. Reset Reconnect Counter pada Manual Logout

**File**: `whatsapp-server/server.js` (lines 70-95)

```javascript
if (connection === 'close') {
    const shouldReconnect = (lastDisconnect?.error?.output?.statusCode !== DisconnectReason.loggedOut) || manualLogout;
    connectionState = 'disconnected';
    
    if (shouldReconnect) {
        // Reset reconnect attempts jika manual logout
        if (manualLogout) {
            reconnectAttempts = 0;
            logger.info('Manual logout detected - resetting reconnect counter');
        }
        
        if (reconnectAttempts < MAX_RECONNECT_ATTEMPTS) {
            reconnectAttempts++;
            logger.info(`Reconnecting... Attempt ${reconnectAttempts}/${MAX_RECONNECT_ATTEMPTS}`);
            setTimeout(connectToWhatsApp, RECONNECT_INTERVAL);
        }
    }
}
```

**Perubahan**:
- Counter `reconnectAttempts` di-reset ke 0 saat `manualLogout = true`
- Memastikan reconnect logic berjalan dengan counter yang fresh

### 2. Improved Logout Endpoint

**File**: `whatsapp-server/server.js` (lines 307-360)

```javascript
app.post('/logout', async (req, res) => {
    try {
        if (sock) {
            logger.info('Logout requested - preparing to disconnect and generate new QR...');
            
            // Set flag sebelum logout
            manualLogout = true;
            reconnectAttempts = 0;
            
            // Logout dari WhatsApp
            try {
                await sock.logout();
                logger.info('Successfully logged out from WhatsApp');
            } catch (logoutError) {
                logger.warn('Logout error (might be already disconnected):', logoutError.message);
            }
            
            // Update state
            connectionState = 'disconnected';
            qrCodeData = null;
            sock = null; // Set socket ke null
            
            // Tunggu sebentar sebelum hapus session
            await new Promise(resolve => setTimeout(resolve, 1000));
            
            // Hapus session folder
            if (fs.existsSync(sessionPath)) {
                fs.rmSync(sessionPath, { recursive: true, force: true });
                logger.info('Session folder deleted successfully');
            }
            
            // Tunggu lagi sebelum reconnect
            await new Promise(resolve => setTimeout(resolve, 2000));
            
            // Trigger reconnect untuk generate QR baru
            logger.info('Starting reconnection to generate new QR code...');
            connectToWhatsApp();
            
            res.json({
                success: true,
                message: 'Logged out successfully. Generating new QR code...'
            });
        }
    } catch (error) {
        logger.error('Failed to logout:', error);
        manualLogout = false;
        res.status(500).json({
            success: false,
            message: 'Failed to logout',
            error: error.message
        });
    }
});
```

**Perubahan**:
1. **Better Error Handling**: Logout error di-catch (karena socket mungkin sudah disconnect)
2. **Socket State Reset**: `sock = null` untuk clear state
3. **Timing Delays**: 
   - 1 detik sebelum hapus session folder
   - 2 detik sebelum reconnect
4. **Manual Reconnect Trigger**: Explicit call `connectToWhatsApp()` setelah cleanup
5. **Better Logging**: Log setiap step untuk debugging

### 3. Reset Flag saat Connected

**File**: `whatsapp-server/server.js` (lines 96-102)

```javascript
else if (connection === 'open') {
    connectionState = 'connected';
    reconnectAttempts = 0;
    manualLogout = false; // Reset flag saat berhasil connect
    qrCodeData = null;
    logger.info('WhatsApp connection established successfully!');
}
```

**Perubahan**:
- Reset `manualLogout` flag saat berhasil connect
- Memastikan flag tidak "stuck" di true

## Testing Steps

### 1. Deploy ke aaPanel

```bash
# Di server aaPanel
cd /www/wwwroot/spmb

# Pull changes dari GitHub
git pull origin main

# Restart PM2
pm2 restart spmb-wa-gateway

# Monitor logs
pm2 logs spmb-wa-gateway --lines 50
```

### 2. Test Logout Flow

1. **Pastikan WhatsApp Connected**:
   - Buka dashboard WhatsApp Gateway
   - Status harus "Connected" (badge hijau)

2. **Trigger Logout**:
   - Buka menu "Settings" di WhatsApp Gateway
   - Scroll ke bawah ke "Danger Zone"
   - Klik tombol "Logout WhatsApp"
   - Konfirmasi logout

3. **Monitor Logs** (di terminal server):
   ```bash
   pm2 logs spmb-wa-gateway --lines 30
   ```

4. **Expected Log Output**:
   ```
   [INFO] Logout requested - preparing to disconnect and generate new QR...
   [INFO] Successfully logged out from WhatsApp
   [INFO] Session folder deleted successfully
   [INFO] Starting reconnection to generate new QR code...
   [INFO] Manual logout detected - resetting reconnect counter
   [INFO] Reconnecting... Attempt 1/5
   [INFO] QR Code generated, scan with WhatsApp
   ```

5. **Verify Dashboard**:
   - Kembali ke dashboard WhatsApp Gateway
   - Status harus berubah ke "Waiting QR Scan" (badge kuning)
   - QR code harus muncul otomatis di dashboard (dalam 5-10 detik)
   - **TIDAK PERLU** restart PM2 manual

### 3. Test QR Code Display

1. **Auto-Display**:
   - QR code harus muncul otomatis di dashboard
   - Refresh halaman jika perlu (auto-refresh setiap 5 detik)

2. **Manual Refresh**:
   - Klik tombol "Refresh QR" di bawah QR code
   - QR code harus update

3. **Scan QR**:
   - Scan QR code dengan WhatsApp
   - Status harus berubah ke "Connected"
   - QR code section harus hilang otomatis

## Troubleshooting

### Issue 1: QR Code Tidak Muncul Setelah Logout

**Symptoms**:
- Status stuck di "Disconnected"
- Tidak ada QR code yang muncul

**Solution**:
```bash
# Check PM2 logs
pm2 logs spmb-wa-gateway --lines 50

# Jika ada error, restart PM2
pm2 restart spmb-wa-gateway

# Monitor logs lagi
pm2 logs spmb-wa-gateway
```

### Issue 2: Max Reconnect Attempts Error

**Symptoms**:
```
[ERROR] Max reconnect attempts reached. Please restart the server.
```

**Solution**:
```bash
# Restart PM2
pm2 restart spmb-wa-gateway

# Atau stop dan start ulang
pm2 stop spmb-wa-gateway
pm2 start spmb-wa-gateway

# Monitor logs
pm2 logs spmb-wa-gateway --lines 30
```

### Issue 3: Session Folder Tidak Terhapus

**Symptoms**:
- QR code tidak generate
- Log menunjukkan "Failed to delete session folder"

**Solution**:
```bash
# Hapus session folder manual
cd /www/wwwroot/spmb/whatsapp-server
rm -rf spmb-wa-session

# Restart PM2
pm2 restart spmb-wa-gateway
```

### Issue 4: Dashboard Tidak Auto-Refresh

**Symptoms**:
- Status tidak update otomatis
- QR code tidak muncul meskipun server sudah generate

**Solution**:
- Refresh halaman browser (F5)
- Clear browser cache (Ctrl+Shift+R)
- Check browser console untuk error JavaScript

## Configuration

### Environment Variables

**File**: `whatsapp-server/.env`

```env
# Reconnect settings
MAX_RECONNECT_ATTEMPTS=5
RECONNECT_INTERVAL=5000

# Session settings
SESSION_NAME=spmb-wa-session

# Server settings
HOST=0.0.0.0
PORT=3000

# Logging
LOG_LEVEL=info
```

**Penjelasan**:
- `MAX_RECONNECT_ATTEMPTS`: Maksimal percobaan reconnect (default: 5)
- `RECONNECT_INTERVAL`: Delay antar reconnect dalam ms (default: 5000 = 5 detik)
- `SESSION_NAME`: Nama folder session WhatsApp
- `LOG_LEVEL`: Level logging (debug, info, warn, error)

## Expected Behavior

### Normal Flow (Setelah Fix)

1. **User klik Logout** → Server receive logout request
2. **Set Flags** → `manualLogout = true`, `reconnectAttempts = 0`
3. **Logout WhatsApp** → Call `sock.logout()`
4. **Update State** → `connectionState = 'disconnected'`, `sock = null`
5. **Wait 1s** → Delay untuk ensure socket closed
6. **Delete Session** → Hapus folder `spmb-wa-session/`
7. **Wait 2s** → Delay untuk ensure cleanup complete
8. **Reconnect** → Call `connectToWhatsApp()`
9. **Generate QR** → Baileys generate QR code baru
10. **Update UI** → Dashboard auto-refresh dan tampilkan QR
11. **User Scan** → Scan QR dengan WhatsApp
12. **Connected** → Status berubah ke "Connected", QR hilang

### Timeline

```
T+0s    : User klik logout
T+1s    : Session folder deleted
T+3s    : Reconnect triggered
T+5s    : QR code generated
T+5-10s : Dashboard auto-refresh, QR muncul
```

## Files Modified

1. **whatsapp-server/server.js**
   - Lines 70-102: Connection update handler (reset counter logic)
   - Lines 307-360: Logout endpoint (improved cleanup & timing)

## Testing Checklist

- [ ] Pull latest code dari GitHub
- [ ] Restart PM2 service
- [ ] WhatsApp connected (scan QR pertama kali)
- [ ] Dashboard menampilkan status "Connected"
- [ ] Klik logout dari Settings
- [ ] Monitor PM2 logs (tidak ada error)
- [ ] Dashboard auto-refresh (5 detik)
- [ ] QR code muncul otomatis (tanpa restart PM2)
- [ ] Scan QR code baru
- [ ] Status berubah ke "Connected"
- [ ] QR section hilang otomatis

## Success Criteria

✅ **Fix berhasil jika**:
1. Setelah logout, QR code muncul otomatis dalam 5-10 detik
2. Tidak perlu restart PM2 manual
3. Tidak ada error "Max reconnect attempts" di logs
4. Dashboard auto-refresh dan tampilkan QR
5. User bisa scan QR dan connect kembali

## Notes

- **Auto-refresh interval**: Dashboard refresh status setiap 5 detik
- **Reconnect delay**: 5 detik antar reconnect attempt
- **Session cleanup**: Folder session dihapus otomatis saat logout
- **Max attempts**: 5 kali reconnect attempt (bisa diubah di .env)
- **Logging**: Semua step di-log untuk debugging

## Support

Jika masih ada issue setelah fix ini:

1. **Check PM2 logs**: `pm2 logs spmb-wa-gateway --lines 100`
2. **Check session folder**: `ls -la /www/wwwroot/spmb/whatsapp-server/spmb-wa-session`
3. **Check browser console**: F12 → Console tab
4. **Restart PM2**: `pm2 restart spmb-wa-gateway`
5. **Check .env config**: Pastikan semua variable sudah benar

---

**Last Updated**: 2026-05-31
**Version**: 1.1.0
**Status**: Ready for Testing
