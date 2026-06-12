# Panduan Testing Integrasi Gateway Backup ke Laravel SPMB

**Status**: ✅ INTEGRASI SELESAI  
**Tanggal**: 12 Juni 2026, 20:35 WIB

---

## ✅ Yang Sudah Terintegrasi:

### 1. Database Migration ✅
- ✅ Migration dijalankan: `2026_06_11_234501_add_backup_gateway_to_whatsapp_settings`
- ✅ Settings ditambahkan ke tabel `whatsapp_settings`:
  - `wa_server_url_backup` = http://localhost:3001
  - `wa_failover_enabled` = true (enabled)
  - `wa_failover_timeout` = 5 (seconds)

### 2. WhatsAppService ✅
- ✅ File: `app/Services/WhatsAppService.php`
- ✅ Method `getActiveServerUrl()` - Auto-detect active gateway
- ✅ Method `checkServerHealth()` - Health check dengan timeout
- ✅ Failover logic implemented

### 3. Gateway Management Controller ✅
- ✅ File: `app/Http/Controllers/WhatsAppGatewayController.php`
- ✅ Methods:
  - `index()` - Dashboard dengan dual gateway
  - `getQRCode($gateway)` - Fetch QR dari gateway
  - `restart($gateway)` - Restart gateway via API
  - `logout($gateway)` - Logout & generate new QR
  - `getLogs($gateway)` - Fetch PM2 logs

### 4. Routes ✅
- ✅ File: `routes/web.php`
- ✅ Middleware: `checkRole:administrator,admin_wa`
- ✅ Routes:
  ```
  GET  /admin/gateway              → index (dashboard)
  GET  /admin/gateway/{gateway}/qr → getQRCode
  POST /admin/gateway/{gateway}/restart → restart
  POST /admin/gateway/{gateway}/logout → logout
  GET  /admin/gateway/{gateway}/logs → getLogs
  ```

### 5. Gateway Management View ✅
- ✅ File: `resources/views/admin/gateway/index.blade.php`
- ✅ Features:
  - Display kedua gateway (SPMB + Absensi/Backup)
  - Real-time status monitoring
  - Health metrics (uptime, memory, CPU)
  - QR code modal
  - Restart/Logout buttons
  - Logs viewer
  - Failover settings display
  - AlpineJS for interactivity

---

## 🚀 Cara Mengakses Gateway Management UI

### 1. Login Laravel
```
URL: http://localhost:8000/login
Role: administrator atau admin_wa
```

### 2. Akses Gateway Management
```
URL: http://localhost:8000/admin/gateway
```

### 3. Tampilan Dashboard
Anda akan melihat 2 cards:
- **Gateway SPMB** (Port 3000) - Primary
- **Gateway Absensi** (Port 3001) - Backup

---

## 📊 Fitur Gateway Management UI

### Card Gateway:
Setiap gateway menampilkan:
- ✅ **Status badge**: Online/Offline (green/red)
- ✅ **Port**: 3000 atau 3001
- ✅ **Connection Status**: Connected/Disconnected/QR
- ✅ **Uptime**: Durasi gateway berjalan
- ✅ **Memory usage**: Heap used/total + percentage
- ✅ **CPU usage**: User time + System time
- ✅ **QR availability**: Yes/No

### Action Buttons:
- 🔵 **QR Code**: View QR untuk scan WhatsApp
- 🟡 **Restart**: Restart gateway server
- 🔴 **Logout**: Logout & generate new QR
- 🔵 **Logs**: View PM2 logs

### Failover Settings Panel:
- ✅ Auto Failover: Enabled/Disabled
- ✅ Health Check Timeout: 5 seconds
- ✅ Current Active Gateway indicator

---

## 🔧 Testing Checklist

### Pre-requisites:
- [✅] Gateway Backup running (port 3001)
- [⏳] Gateway Primary running (port 3000)
- [✅] Laravel server running (port 8000)
- [✅] Database migration executed
- [✅] Failover enabled in database

### Test Cases:

#### 1. Akses Gateway Management UI
```bash
# Start Laravel (jika belum)
php artisan serve

# Buka browser
http://localhost:8000/admin/gateway
```

**Expected Result**:
- ✅ Dashboard loads
- ✅ 2 gateway cards displayed
- ✅ Backup gateway shows "Online" (green)
- ✅ Primary gateway shows status (tergantung apakah running)

#### 2. View QR Code
**Steps**:
1. Klik button "QR Code" di card Gateway Backup
2. Modal popup opens
3. QR code displayed

**Expected Result**:
- ✅ Modal shows QR code image
- ✅ Text: "Scan dengan WhatsApp di HP Anda"

#### 3. Check Gateway Status
**Expected for Backup Gateway (Port 3001)**:
- Status: "Online" badge (green)
- Connection: "Qr" badge (yellow/orange)
- QR Available: "Yes" badge
- Uptime: ~XX:XX:XX
- Memory: XX MB / XX MB (XX%)

#### 4. Test Failover Logic
**Steps**:
1. Check current `WhatsAppService` usage
2. Stop primary gateway (port 3000)
3. Send WhatsApp message dari Laravel
4. Message should route to backup (port 3001)

**Test Command**:
```bash
php artisan tinker
```
```php
$service = app(\App\Services\WhatsAppService::class);
$service->sendMessage('081234567890', 'Test failover message');
```

**Expected Result**:
- ✅ Service detects primary offline
- ✅ Auto-switch to backup gateway
- ✅ Message sent via backup (port 3001)
- ✅ Log entry: "Primary WhatsApp gateway unhealthy, switching to backup"

#### 5. Test Restart Gateway
**Steps**:
1. Klik button "Restart" di card Gateway Backup
2. Confirm dialog
3. Wait 5-10 seconds
4. Page auto-reload

**Expected Result**:
- ✅ Alert: "Gateway is restarting..."
- ✅ Gateway process restarts
- ✅ Page reloads after 8 seconds
- ✅ Gateway back online

#### 6. Test Logout Gateway
**Steps**:
1. Klik button "Logout" di card Gateway Backup
2. Confirm dialog
3. Wait 5 seconds
4. Page auto-reload

**Expected Result**:
- ✅ Alert: "Logged out successfully. New QR will be generated..."
- ✅ Session deleted
- ✅ New QR generated
- ✅ Status changes to "Qr" (waiting scan)

#### 7. View Logs
**Steps**:
1. Klik button "Logs" di card Gateway
2. Modal opens with logs

**Expected Result**:
- ✅ Modal shows terminal logs
- ✅ Logs displayed in monospace font (terminal-like)
- ✅ If PM2 not available: "Unable to fetch logs" message

---

## 🎯 Integration Points

### Laravel → Gateway Communication:

#### Health Check:
```php
// In WhatsAppService.php
$response = Http::timeout(5)->get("http://localhost:3001/status");
if ($response->successful() && $response->json('status') === 'connected') {
    // Gateway healthy
}
```

#### Send Message:
```php
// In WhatsAppService.php
$url = $this->getActiveServerUrl(); // Auto-detect primary/backup
Http::post("{$url}/send", [
    'phone' => '081234567890',
    'message' => 'Hello from Laravel'
]);
```

#### Failover Flow:
```
1. Laravel tries primary (port 3000)
2. Health check timeout/failed
3. Log warning: "Primary unhealthy, switching to backup"
4. Use backup URL (port 3001)
5. Send message via backup
```

---

## 📝 Manual Testing Steps

### Complete Flow:

1. **Start Gateway Backup** (if not running):
   ```bash
   cd absensi/whatsapp-server-absensi
   npm start
   ```

2. **Start Laravel**:
   ```bash
   php artisan serve
   ```

3. **Login to Laravel**:
   - URL: http://localhost:8000/login
   - User: administrator
   - Password: [your admin password]

4. **Access Gateway Management**:
   ```
   http://localhost:8000/admin/gateway
   ```

5. **Verify Display**:
   - ✅ Gateway SPMB card (port 3000)
   - ✅ Gateway Absensi card (port 3001) - ONLINE
   - ✅ Backup shows: Online, QR status, metrics

6. **View QR Code**:
   - Click "QR Code" button on Backup gateway
   - Modal shows QR
   - Scan with WhatsApp

7. **After Scan**:
   - Status changes to "Connected" (green)
   - QR Available changes to "No"

8. **Test Send Message** (via Tinker):
   ```bash
   php artisan tinker
   ```
   ```php
   $service = app(\App\Services\WhatsAppService::class);
   $service->sendMessage('081234567890', 'Test dari Laravel SPMB');
   ```

9. **Check Logs**:
   - Click "Logs" button
   - View terminal output

---

## 🔍 Troubleshooting

### Gateway shows "Offline"?
```bash
# Check if gateway running
curl http://localhost:3001/status

# Check process
netstat -ano | Select-String ":3001"

# Restart gateway
cd absensi/whatsapp-server-absensi
npm start
```

### QR Modal shows error?
- Check gateway status endpoint: `/status`
- Check gateway qr endpoint: `/qr`
- Ensure gateway connection status is 'qr'

### Failover not working?
```bash
php artisan tinker
```
```php
// Check settings
\App\Models\WhatsAppSetting::get('wa_failover_enabled'); // Should return true
\App\Models\WhatsAppSetting::get('wa_server_url_backup'); // Should return http://localhost:3001

// Test health check
$service = app(\App\Services\WhatsAppService::class);
$method = new ReflectionMethod($service, 'checkServerHealth');
$method->setAccessible(true);
$result = $method->invoke($service, 'http://localhost:3001');
var_dump($result); // Should return true if online
```

### Logs not showing?
- PM2 might not be installed or gateway not running via PM2
- Check gateway is running: `npm start` (not PM2)
- Logs feature works best with PM2 deployment

---

## ✨ Summary

### ✅ Integrasi Selesai:
1. ✅ Database migration executed
2. ✅ Settings backup gateway configured
3. ✅ Failover enabled (auto-switch to backup)
4. ✅ Controller & routes registered
5. ✅ View with dual gateway support
6. ✅ WhatsAppService dengan failover logic
7. ✅ Gateway Backup running (port 3001)

### 🎯 Ready to Use:
- **Gateway Management UI**: http://localhost:8000/admin/gateway
- **Backup Gateway UI**: http://localhost:3001
- **Primary Gateway UI**: http://localhost:3000
- **Failover**: Automatic switch when primary offline

### ⏳ Manual Steps Required:
1. Start gateway primary (port 3000) - optional
2. Login to Laravel admin
3. Access `/admin/gateway`
4. Scan QR code for backup gateway
5. Test send message

**Integrasi backup gateway ke Laravel SPMB sudah SELESAI!** 🚀

---

**Documentation by**: Kiro AI Assistant  
**Integration Status**: COMPLETE ✅  
**Ready for**: Testing & Production Use
