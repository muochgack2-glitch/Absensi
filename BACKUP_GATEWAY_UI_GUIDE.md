# Panduan UI WhatsApp Gateway Backup

**Status**: ✅ UI READY & ACCESSIBLE  
**Last Updated**: 12 Juni 2026, 20:20 WIB

## 🌐 Akses UI Gateway Backup

### URL Akses:
```
http://localhost:3001
```

### Fitur UI:
1. **📊 Connection Status** - Real-time status gateway
2. **📷 QR Code Scanner** - Scan untuk connect WhatsApp
3. **💬 Send Test Message** - Testing kirim pesan

## 📱 Tampilan UI

### Header
```
📱 SPMB WhatsApp Gateway - BACKUP
Testing Panel - Port 3001 (Backup Gateway)
```

### Card 1: Connection Status
- Status badge (Connected/Disconnected/Waiting QR Scan)
- Status detail
- QR availability
- Reconnect attempts
- Last update timestamp
- **Auto-refresh setiap 5 detik**

### Card 2: QR Code Scanner
- Display QR code untuk scan
- Tombol "🔄 Refresh QR Code"
- Auto-refresh saat status = 'qr'

### Card 3: Send Test Message
- Input phone number (format: 08xxx atau 628xxx)
- Input message (textarea)
- Tombol "📤 Send Message"
- Result notification (success/error)

## 🎨 Design Features

### Styling
- **Background**: Linear gradient (purple theme)
- **Layout**: Responsive grid (auto-fit, min 350px)
- **Cards**: White background, rounded corners, shadow
- **Buttons**: Gradient purple, hover effect
- **Status badges**: Color-coded (green/red/yellow)

### Interactive Elements
- Loading spinner saat fetch data
- Hover animations pada buttons
- Auto-refresh status & QR
- Form validation
- Alert notifications

## 🔧 Technical Details

### API Integration
```javascript
// Auto-detect current port (works for both 3000 and 3001)
const API_URL = window.location.origin;
```

### Endpoints Used
- `GET /status` - Connection status (refresh setiap 5 detik)
- `GET /qr` - Get QR code
- `POST /send` - Send message

### Auto-Refresh Logic
```javascript
// Load on page load
loadStatus();
loadQR();

// Auto-refresh every 5 seconds
setInterval(loadStatus, 5000);

// Auto-refresh QR if status is 'qr'
if (data.status === 'qr' && data.qrAvailable) {
    loadQR();
}
```

## 📋 Testing Checklist

### UI Access ✅
- [✅] Browser dapat akses http://localhost:3001
- [✅] UI loads dengan header "BACKUP"
- [✅] Port 3001 ditampilkan di header
- [✅] Dynamic API_URL menggunakan window.location.origin

### Functionality ✅
- [✅] Status card auto-refresh setiap 5 detik
- [✅] QR code card display QR
- [✅] Refresh QR button works
- [⏳] Send message form (after QR scan)

## 🚀 Cara Menggunakan

### 1. Start Gateway
```bash
cd absensi/whatsapp-server-absensi
npm start
```

### 2. Buka Browser
```
http://localhost:3001
```

### 3. Scan QR Code
- Lihat di card "QR Code Scanner"
- Scan dengan WhatsApp (nomor berbeda dari gateway primary)
- Status akan berubah dari "Waiting QR Scan" → "Connected ✓"

### 4. Test Send Message
Setelah connected:
- Masukkan nomor phone (contoh: 081234567890)
- Ketik message
- Klik "📤 Send Message"
- Cek hasil di bawah form

## 🔄 Perbedaan dengan Gateway Primary

### Gateway Primary (Port 3000)
- Header: "SPMB WhatsApp Gateway"
- Session: `spmb-wa-session`
- Purpose: Main gateway untuk SPMB

### Gateway Backup (Port 3001) ✅
- Header: "SPMB WhatsApp Gateway - BACKUP"
- Subtitle: "Port 3001 (Backup Gateway)"
- Session: `spmb-wa-session-backup`
- Purpose: Backup/Failover untuk SPMB

## 📊 Status Badges

### Status: Connected ✓
- **Color**: Green (#d4edda)
- **Meaning**: WhatsApp tersambung dan siap kirim pesan

### Status: Waiting QR Scan
- **Color**: Yellow (#fff3cd)
- **Meaning**: QR code tersedia, perlu di-scan

### Status: Disconnected
- **Color**: Red (#f8d7da)
- **Meaning**: Tidak tersambung, cek server/connection

## 🎯 Integration dengan Laravel

Gateway backup ini sudah terintegrasi dengan Laravel SPMB:

### Laravel Gateway Management UI
```
URL: /admin/gateway
```

Fitur Laravel UI:
- View status BOTH gateways (primary + backup)
- Display QR code dari Laravel
- Restart gateway via API
- Logout & generate new QR
- View logs
- Monitor health metrics

### Workflow
1. **Admin access**: `/admin/gateway`
2. **View both gateways**: Primary (3000) + Backup (3001)
3. **Monitor status**: Real-time status kedua gateway
4. **Failover automatic**: Laravel switch ke backup jika primary down

## ⚠️ Catatan Penting

1. **Port 3001**: Pastikan tidak digunakan aplikasi lain
2. **Nomor WhatsApp**: Harus berbeda dari gateway primary
3. **Browser**: Modern browser (Chrome, Firefox, Edge)
4. **Network**: Gateway listen di 0.0.0.0 (external access)
5. **Auto-refresh**: Status update otomatis setiap 5 detik

## 🔍 Troubleshooting

### UI tidak load?
```bash
# Check if gateway running
curl http://localhost:3001/status

# Check process
netstat -ano | Select-String ":3001"
```

### QR tidak muncul?
- Tunggu 5-10 detik setelah start gateway
- Klik tombol "🔄 Refresh QR Code"
- Check status endpoint: /status

### Send message failed?
- Pastikan status = "Connected ✓"
- Check nomor format (08xxx atau 628xxx)
- Check console browser untuk error details

## 📦 File Locations

- **UI File**: `absensi/whatsapp-server-absensi/public/index.html`
- **Server**: `absensi/whatsapp-server-absensi/server.js`
- **Config**: `absensi/whatsapp-server-absensi/.env`

## ✨ Updates Applied

- ✅ Dynamic API_URL (auto-detect port)
- ✅ Header shows "BACKUP"
- ✅ Subtitle shows "Port 3001 (Backup Gateway)"
- ✅ Title updated: "SPMB WhatsApp Gateway BACKUP - Port 3001"

---

**UI Status**: READY ✅  
**Access**: http://localhost:3001  
**Purpose**: Testing & monitoring backup gateway
