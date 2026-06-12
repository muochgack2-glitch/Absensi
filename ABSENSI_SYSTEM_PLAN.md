# 📋 Rencana Sistem Absensi Siswa

**Status:** Planning (Untuk implementasi nanti)

## 🎯 Target

Sistem absensi siswa SMK dengan WhatsApp Gateway terintegrasi.

## 🛠️ Tech Stack

- **Backend:** Laravel 11
- **Frontend:** Blade + Livewire
- **Gateway:** whatsapp-server-absensi (port 3001)
- **Database:** MySQL

## 📱 Fitur Utama

### 1. Absen via WhatsApp
- Siswa kirim "ABSEN MASUK" / "ABSEN PULANG"
- Gateway detect keyword → record absensi
- Reply otomatis konfirmasi
- Notifikasi ke orang tua

### 2. QR Code Scan
- QR di pintu masuk sekolah
- Siswa scan dengan HP
- GPS validation (dalam radius sekolah)
- Real-time record

### 3. GPS/Location Based
- Validasi lokasi siswa (radius 100-500m dari sekolah)
- Flag "di luar zona" jika tidak sesuai
- Record lat/lng per absensi

### 4. Reminder Otomatis
- 06:30 → Reminder absen masuk
- 07:15 → Reminder ke yang belum absen
- 14:00 → Reminder absen pulang
- 15:00 → Reminder ke yang belum absen pulang

### 5. Notifikasi Orang Tua
Auto kirim WA ke orang tua:
```
[ABSENSI SMK]
Ananda: Budi Santoso
Kelas: XII RPL 1
Absen Masuk: 07:15 WIB
Lokasi: SMK Negeri 1
Status: ✅ Hadir
```

## 🗄️ Database Tables

```sql
students
- id, nis, nama, kelas_id, no_hp, no_hp_ortu

classes
- id, nama_kelas, tingkat, jurusan, wali_kelas_id

attendances
- id, student_id, date, type (masuk/pulang)
- check_in_time, check_in_method (wa/qr/manual)
- check_in_lat, check_in_lng
- status (hadir/izin/sakit/alpha/terlambat)

attendance_settings
- school_lat, school_lng, radius
- check_in_start, check_in_end
- late_tolerance

qr_tokens
- id, token, expired_at, used_count
```

## 🎨 UI Pages (Livewire)

### Admin/Guru:
1. Dashboard - Real-time attendance
2. Data Siswa - CRUD siswa
3. Rekap Absensi - Filter & export
4. QR Generator - Generate QR per hari
5. WhatsApp Gateway - Status & settings
6. Settings - Konfigurasi lokasi & jam

### Siswa (Mobile-First):
1. Absen QR Code - Scan dengan kamera
2. Riwayat Absensi - List kehadiran

## 📱 WhatsApp Integration

### Incoming Messages:
```php
if (stripos($message, 'ABSEN MASUK') !== false) {
    $attendance->checkIn([...]);
    $wa->send($phone, "✅ Absen masuk tercatat");
    $wa->send($ortu, "Ananda sudah sampai sekolah");
}
```

### Broadcast Reminder:
```php
$belumAbsen = Student::whereDoesntHave('attendances')->get();
foreach ($belumAbsen as $siswa) {
    $wa->send($siswa->no_hp, "Jangan lupa absen ya!");
}
```

## 🚀 Development Roadmap

### FASE 1: Setup Gateway (1 hari) ✅
- Setup folder whatsapp-server-absensi
- PM2 port 3001
- Scan QR dengan nomor WA Absensi

### FASE 2: Laravel Absensi Core (3-4 hari)
- Setup Laravel project
- Database migration
- Models & relationships
- Authentication

### FASE 3: Fitur Absensi (3-4 hari)
- QR Code scan (Livewire)
- GPS validation
- Status logic (hadir/terlambat/alpha)
- Rekap & laporan

### FASE 4: WhatsApp Integration (2-3 hari)
- AbsensiWhatsAppService
- Webhook incoming messages
- Absen via WA
- Notifikasi orang tua
- Reminder scheduler

### FASE 5: Dashboard & UI (2-3 hari)
- Real-time dashboard
- Grafik & statistik
- Mobile responsive
- Print & export

### FASE 6: Testing & Deploy (1-2 hari)
- Testing
- Deploy ke aaPanel
- Training
- Go live!

**Total estimasi: 12-17 hari development**

## 💰 Resource Projection

```
Laravel SPMB:        ~300 MB RAM
Laravel Absensi:     ~300 MB RAM
Gateway SPMB:        ~200 MB RAM
Gateway Absensi:     ~200 MB RAM
MySQL:               ~400 MB RAM
Nginx/PHP-FPM:       ~300 MB RAM
─────────────────────────────────
Total:               ~1.7 GB RAM
Free:                ~4.1 GB RAM ✅
```

Server 6GB RAM cukup untuk semua!

## 📝 Notes

- Gateway absensi (3001) saat ini dipakai sebagai **backup SPMB**
- Saat aplikasi Absensi ready, tinggal re-configure ke Absensi
- **Tidak perlu scan QR ulang!**
- Session tetap tersimpan di folder `spmb-wa-session-absensi`

## 📞 Contact

Untuk diskusi lebih lanjut tentang implementasi Absensi.
