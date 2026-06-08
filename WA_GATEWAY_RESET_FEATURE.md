# WA Gateway Auto-Reset Feature

## Masalah Sebelumnya
Ketika WA Gateway disconnect setelah beberapa hari produksi, proses reconnect manual terlalu lama:
1. SSH ke server
2. Hapus folder session di aaPanel
3. Stop PM2
4. Start PM2
5. Save PM2
6. Scan QR ulang

## Solusi Baru
Tombol **"Reset & Reconnect"** di dashboard WA Gateway yang otomatis:
1. Logout dari WhatsApp
2. Hapus session folder
3. Generate QR baru
4. Tampilkan QR di dashboard
5. User tinggal scan ulang

## Implementasi

### 1. Frontend (resources/views/whatsapp/index.blade.php)
- **Tombol Reset**: Ditambahkan di sebelah tombol Refresh
- **Function resetConnection()**: 
  - Call endpoint `/whatsapp/logout` via AJAX
  - Show loading state
  - Wait 3 detik untuk server generate QR
  - Auto refresh status untuk tampilkan QR baru
  - Show success/error alert

### 2. Backend Laravel (app/Http/Controllers/WhatsAppController.php)
- **Method logout()**: 
  - Support AJAX request (return JSON)
  - Support regular request (return redirect)
  - Call WhatsAppService::logout()

### 3. Node.js Server (whatsapp-server/server.js)
- **Endpoint /logout**: SUDAH ADA DAN LENGKAP
  - Set flag manualLogout = true
  - Call sock.logout()
  - Hapus session folder dengan fs.rmSync()
  - Trigger connectToWhatsApp() untuk generate QR baru
  - Return success response

## Cara Penggunaan
1. Buka dashboard WA Gateway
2. Jika status "Disconnected", klik tombol **"Reset & Reconnect"**
3. Konfirmasi reset
4. Tunggu 3 detik
5. QR code baru akan muncul otomatis
6. Scan QR dengan WhatsApp
7. Status berubah menjadi "Connected"

## Keuntungan
- ✅ Tidak perlu SSH ke server
- ✅ Tidak perlu akses aaPanel
- ✅ Tidak perlu command PM2
- ✅ Proses cepat (hanya 3-5 detik)
- ✅ Bisa dilakukan oleh user dengan role admin_wa
- ✅ QR muncul otomatis di dashboard
- ✅ Auto-refresh status setiap 5 detik

## Testing
1. **Test Normal Reset**:
   - Pastikan WA Gateway connected
   - Klik "Reset & Reconnect"
   - Verifikasi QR muncul
   - Scan QR
   - Verifikasi status "Connected"

2. **Test When Already Disconnected**:
   - Matikan WhatsApp di HP
   - Tunggu sampai status "Disconnected"
   - Klik "Reset & Reconnect"
   - Verifikasi QR muncul
   - Scan QR dengan WhatsApp
   - Verifikasi status "Connected"

3. **Test Server Down**:
   - Stop Node.js server
   - Klik "Reset & Reconnect"
   - Verifikasi error message muncul
   - Start Node.js server
   - Klik "Reset & Reconnect" lagi
   - Verifikasi QR muncul

## Catatan Teknis
- Session folder: `whatsapp-server/spmb-wa-session/` (sesuai .env `SESSION_NAME`)
- Timeout: 3 detik sebelum refresh status
- Auto-refresh: Status di-check setiap 5 detik
- QR inline: Muncul otomatis di dashboard ketika status = 'qr'
- Alert auto-dismiss: 5 detik

## Files Modified
1. `resources/views/whatsapp/index.blade.php` - Added reset button & function
2. `app/Http/Controllers/WhatsAppController.php` - Updated logout() to support AJAX
3. `whatsapp-server/server.js` - TIDAK PERLU DIUBAH (sudah ada endpoint logout lengkap)

## Route
```php
Route::post('/whatsapp/logout', [WhatsAppController::class, 'logout'])->name('whatsapp.logout');
```

## Node.js Endpoint
```
POST http://localhost:3000/logout
```
Response:
```json
{
    "success": true,
    "message": "Logged out successfully. Generating new QR code..."
}
```
