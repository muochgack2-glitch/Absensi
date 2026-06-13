# 📱 Panduan WhatsApp Gateway Management

## 🎯 Untuk Apa Menu Ini?
Menu ini untuk **monitoring dan kontrol 2 gateway WhatsApp** yang bekerja secara otomatis dengan sistem failover (backup).

---

## 🚀 Akses Menu
1. Login ke sistem admin
2. Klik menu **"Gateway Management"** di dashboard WhatsApp

---

## 📊 Informasi di Halaman

### 1️⃣ **Status Real-Time**
- **Auto-refresh setiap 10 detik** - Status gateway update otomatis
- **Last updated** - Kapan terakhir data di-refresh
- **Tombol Refresh All** - Refresh manual jika perlu

### 2️⃣ **Card Gateway (2 Kartu)**

#### 🟢 **Gateway SPMB** (Primary - Port 3000)
- **Warna Hijau** = Online dan siap digunakan ✅
- **Warna Merah** = Offline atau bermasalah ❌

#### 🟡 **Gateway Absensi** (Backup - Port 3001)
- Saat ini sebagai **backup SPMB**
- Nanti akan digunakan untuk sistem Absensi

---

## 🔧 Tombol-Tombol di Setiap Gateway

### 📱 **QR Code**
**Untuk apa?**
- Menautkan WhatsApp Anda ke gateway
- QR code hanya muncul jika gateway **belum terhubung**

**Kapan digunakan?**
- Pertama kali setup gateway
- Setelah logout
- Jika koneksi terputus dan perlu scan ulang

**Cara pakai:**
1. Klik tombol **QR Code**
2. Modal akan muncul dengan QR code
3. Buka WhatsApp di HP → Menu (⋮) → **Perangkat Tertaut**
4. Tap **Tautkan Perangkat**
5. Scan QR code di layar
6. Tunggu sampai status berubah jadi **Connected**

---

### 🔄 **Restart**
**Untuk apa?**
- Restart Node.js process gateway
- Session WhatsApp **TETAP TERSIMPAN**
- **TIDAK perlu scan QR ulang**

**Kapan digunakan?**
- Gateway lambat atau hang
- Memory usage tinggi
- Gateway tidak merespon tapi masih online

**Yang terjadi:**
- Server restart (5-10 detik offline)
- Koneksi WhatsApp reconnect otomatis
- Tidak kehilangan session

---

### 🚪 **Logout**
**Untuk apa?**
- Memutuskan koneksi WhatsApp
- Generate QR code baru

**Kapan digunakan?**
- Ingin ganti nomor WhatsApp
- Koneksi bermasalah dan perlu fresh start
- QR tidak muncul padahal status disconnected

**Yang terjadi:**
- Session WhatsApp dihapus
- Gateway generate QR baru
- **HARUS scan QR ulang**

---

### 📄 **Logs**
**Untuk apa?**
- Melihat log PM2 gateway
- Debug jika ada error

**Kapan digunakan?**
- Gateway error dan perlu investigasi
- Ingin lihat aktivitas gateway
- Admin/IT troubleshooting

---

### 🔴 **Reset & Reconnect** (Tombol Merah Besar)
**Untuk apa?**
- **Hard reset**: Restart PM2 + Hapus session + Generate QR baru
- Solusi paling drastis

**Kapan digunakan?**
- Gateway stuck dan restart biasa tidak berhasil
- Session corrupt
- Semua cara lain sudah dicoba
- **TERAKHIR** yang dilakukan

**Yang terjadi:**
- PM2 restart
- Session dihapus total
- QR baru di-generate
- Gateway offline 10-15 detik
- **HARUS scan QR ulang**

---

## 🔀 Auto Failover

### **Toggle Switch**
- **ON** = Jika primary down, otomatis pindah ke backup
- **OFF** = Hanya pakai primary, tidak ada failover

### **Kapan digunakan?**
- **ON (Recommended)**: Sistem produksi, butuh reliability tinggi
- **OFF**: Testing, maintenance, debugging

---

## 🎬 Skenario Penggunaan

### ✅ **Scenario 1: Setup Pertama Kali**
1. Buka Gateway Management
2. Klik **QR Code** di Gateway SPMB
3. Scan dengan WhatsApp
4. Tunggu status jadi **Connected**
5. Ulangi untuk Gateway Absensi (backup)
6. Aktifkan **Auto Failover**

---

### ⚠️ **Scenario 2: Gateway Lambat/Hang**
1. Cek **Memory Usage** di card
2. Jika tinggi (>80%), klik **Restart**
3. Tunggu 10 detik
4. Status kembali online tanpa scan QR

---

### 🔴 **Scenario 3: Gateway Disconnected**
1. Cek status: Jika **QR Available**, scan ulang
2. Jika QR tidak muncul:
   - Coba **Logout** dulu
   - Tunggu 5 detik
   - Klik **QR Code**
   - Scan QR baru
3. Jika masih gagal:
   - Klik **Reset & Reconnect**
   - Tunggu 15 detik
   - Scan QR baru

---

### 🆘 **Scenario 4: Gateway Totally Stuck**
**Urutan troubleshooting:**
1. **Restart** → tunggu 10 detik
2. Jika masih stuck → **Logout** → scan QR
3. Jika masih stuck → **Reset & Reconnect** → scan QR
4. Jika masih stuck → Hubungi IT/Admin server

---

## ⚡ Tips Penting

### ✅ **DO (Lakukan)**
- Gunakan **Restart** untuk masalah ringan
- Aktifkan **Auto Failover** di produksi
- Cek **Logs** jika ada error
- Tunggu sampai proses selesai sebelum action lagi

### ❌ **DON'T (Jangan)**
- Jangan spam tombol (tunggu modal selesai)
- Jangan **Reset & Reconnect** untuk masalah ringan
- Jangan matikan failover tanpa alasan jelas
- Jangan restart kedua gateway bersamaan

---

## 📈 Monitoring Metrics

### **Health Metrics di Card:**
- **Uptime**: Berapa lama gateway jalan tanpa restart
- **Memory**: Penggunaan RAM (80%+ = perlu restart)
- **QR Status**: Available/Not Needed
- **Connection Status**: Connected/Disconnected/QR

### **Status Badge:**
- 🟢 **Connected** = Siap kirim pesan
- 🟡 **QR** = Perlu scan QR
- 🔴 **Disconnected** = Bermasalah

---

## 🔔 Notifikasi & Alert

### **Real-time Monitoring Badge:**
- Badge **"Real-time monitoring"** di atas = sistem aktif monitoring
- Status auto-update setiap 10 detik
- Tidak perlu refresh manual

---

## 💡 FAQ Singkat

**Q: Kapan scan QR code?**
A: Saat pertama setup, setelah logout, atau setelah reset & reconnect.

**Q: Beda Restart vs Logout?**
A: Restart = refresh server, Logout = putus koneksi WA.

**Q: Beda Logout vs Reset & Reconnect?**
A: Logout = putus WA saja, Reset = restart PM2 + putus WA.

**Q: Kenapa ada 2 gateway?**
A: Untuk backup/failover, jika primary down, backup ambil alih.

**Q: Kapan pakai Reset & Reconnect?**
A: Hanya jika restart dan logout tidak berhasil.

---

## 📞 Butuh Bantuan?
Jika masalah tidak terselesaikan setelah **Reset & Reconnect**, hubungi IT Support atau Admin Server.

---

**Dibuat:** {{ date('d M Y') }}  
**Versi:** 1.0
