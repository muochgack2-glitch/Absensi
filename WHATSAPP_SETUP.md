# WhatsApp Gateway Setup Guide

## 🎯 Fitur WhatsApp Auto-Send

Sistem SPMB sudah dilengkapi dengan fitur auto-send WhatsApp yang akan otomatis mengirim pesan selamat datang ketika calon siswa berhasil mendaftar melalui halaman publik.

## 📋 Prerequisites

### 1. Install WhatsApp Gateway Server

Anda perlu menginstall WhatsApp Gateway Server terlebih dahulu. Pilih salah satu:

#### Option A: Menggunakan whatsapp-web.js (Recommended)
```bash
# Clone repository
git clone https://github.com/pedroslopez/whatsapp-web.js.git
cd whatsapp-web.js

# Install dependencies
npm install

# Jalankan server (port default: 3000)
npm start
```

#### Option B: Menggunakan Baileys
```bash
npm install @whiskeysockets/baileys
```

#### Option C: Menggunakan WA-Automate
```bash
npm install @open-wa/wa-automate
```

### 2. Setup Template WhatsApp

Jalankan seeder untuk membuat template default:

```bash
php artisan db:seed --class=WhatsAppSeeder
```

Template yang akan dibuat:
- ✅ `welcome_registration` - Pesan otomatis setelah pendaftaran berhasil (AUTO)
- 📧 `payment_reminder` - Pengingat pembayaran (MANUAL)
- ✅ `payment_confirmed` - Konfirmasi pembayaran diterima (MANUAL)
- 📅 `test_schedule` - Pemberitahuan jadwal tes (MANUAL)
- 🎉 `acceptance_announcement` - Pengumuman kelulusan (MANUAL)

## ⚙️ Konfigurasi

### 1. Setting WhatsApp Gateway URL

Masuk ke menu **WhatsApp Gateway > Settings** dan atur:

- **WhatsApp Server URL**: `http://localhost:3000` (sesuaikan dengan server Anda)
- **Auto Send Enabled**: `Yes` (aktifkan pengiriman otomatis)
- **Connection Timeout**: `10` detik
- **Retry Attempts**: `3` kali
- **Rate Limit**: `20` pesan per menit

### 2. Hubungkan WhatsApp

1. Masuk ke menu **WhatsApp Gateway > Dashboard**
2. Klik tombol **Connect WhatsApp**
3. Scan QR Code yang muncul dengan aplikasi WhatsApp Anda
4. Status akan berubah menjadi **Connected** jika berhasil

### 3. Verifikasi Template

1. Masuk ke menu **WhatsApp Gateway > Templates**
2. Pastikan template `welcome_registration` ada dan statusnya **Active**
3. Toggle **Auto Send** harus dalam keadaan **ON**

## 🔄 Cara Kerja Auto-Send

### Alur Proses:

1. **Calon siswa mendaftar** melalui halaman publik (`/daftar`)
2. **Sistem menyimpan data** pendaftar dan membuat nomor registrasi
3. **Sistem mengecek:**
   - ✅ Apakah WhatsApp Gateway terhubung?
   - ✅ Apakah auto-send diaktifkan?
   - ✅ Apakah ada nomor HP yang valid?
4. **Sistem mengambil nomor HP** dengan prioritas:
   - **Prioritas 1**: Nomor HP Wali (`no_hp_wali`)
   - **Prioritas 2**: Nomor HP Orang Tua (`no_hp_ortu`)
   - **Prioritas 3**: Nomor HP Siswa (`no_telepon`)
5. **Sistem mengirim pesan** menggunakan template `welcome_registration`
6. **Log tercatat** di menu **WhatsApp Gateway > Logs**

### Isi Pesan Default:

```
Assalamu'alaikum {nama},

Selamat! Pendaftaran Anda telah berhasil.

📋 *Detail Pendaftaran:*
No. Pendaftaran: {no_pendaftaran}
Nama: {nama}
Jurusan: {jurusan}

✅ Silakan login ke portal SPMB untuk melengkapi data dan melakukan pembayaran.

🔗 Portal: {portal_url}

Terima kasih telah mendaftar di {sekolah}.

Wassalamu'alaikum
```

## 🧪 Testing

### Test 1: Test Koneksi
```bash
# Cek status WhatsApp Gateway
curl http://localhost:3000/status
```

### Test 2: Test Kirim Manual
1. Masuk ke menu **WhatsApp Gateway > Send Message**
2. Masukkan nomor HP test
3. Pilih template `welcome_registration`
4. Klik **Send**

### Test 3: Test Auto-Send
1. Buka halaman pendaftaran publik: `http://your-domain/daftar`
2. Isi form pendaftaran dengan nomor HP yang valid
3. Submit form
4. Cek HP, seharusnya menerima pesan otomatis
5. Cek log di menu **WhatsApp Gateway > Logs**

## 🔍 Troubleshooting

### ❌ Pesan tidak terkirim setelah pendaftaran

**Cek 1: Status Koneksi**
- Masuk ke menu **WhatsApp Gateway > Dashboard**
- Status harus **Connected** (hijau)
- Jika **Disconnected**, scan ulang QR code

**Cek 2: Setting Auto-Send**
- Masuk ke menu **WhatsApp Gateway > Settings**
- Pastikan **Auto Send Enabled** = `Yes`

**Cek 3: Template Aktif**
- Masuk ke menu **WhatsApp Gateway > Templates**
- Pastikan template `welcome_registration` statusnya **Active**
- Pastikan toggle **Auto Send** dalam keadaan **ON**

**Cek 4: Nomor HP Valid**
- Data pendaftar harus punya minimal 1 nomor HP terisi
- Format nomor: `08123456789` atau `628123456789`

**Cek 5: Log Error**
```bash
# Cek Laravel log
tail -f storage/logs/laravel.log | grep -i whatsapp
```

### ❌ WhatsApp Gateway tidak connect

**Solusi 1: Restart Gateway Server**
```bash
# Stop server
Ctrl + C

# Start ulang
npm start
```

**Solusi 2: Clear Session**
```bash
# Hapus folder .wwebjs_auth atau session
rm -rf .wwebjs_auth
npm start
```

**Solusi 3: Cek Port**
```bash
# Pastikan port 3000 tidak digunakan aplikasi lain
netstat -tulpn | grep 3000
```

### ❌ Template tidak ditemukan

**Jalankan ulang seeder:**
```bash
php artisan db:seed --class=WhatsAppSeeder --force
```

## 📊 Monitoring

### Cek Statistik Pengiriman
- Masuk ke menu **WhatsApp Gateway > Dashboard**
- Lihat **Statistics**:
  - Total Sent: Total pesan terkirim
  - Success: Pesan berhasil
  - Failed: Pesan gagal
  - Pending: Pesan dalam antrian

### Cek Log Detail
- Masuk ke menu **WhatsApp Gateway > Logs**
- Filter berdasarkan:
  - Status: Success / Failed / Pending
  - Type: auto_registration / manual / broadcast
  - Tanggal
  - Nomor HP

## 🔒 Security Tips

1. **Jangan expose WhatsApp Gateway** ke public internet
2. **Gunakan firewall** untuk membatasi akses ke port 3000
3. **Gunakan HTTPS** untuk komunikasi production
4. **Backup session** WhatsApp secara berkala
5. **Monitor rate limit** untuk menghindari banned dari WhatsApp

## 📞 Support

Jika ada kendala, hubungi tim IT atau cek dokumentasi:
- Laravel Log: `storage/logs/laravel.log`
- WhatsApp Gateway Log: Di console server gateway
- Database Log: Table `whatsapp_logs`

---
**Created**: 2026-06-04
**Version**: 1.0
**Project**: SPMB - Sistem Penerimaan Siswa Baru
