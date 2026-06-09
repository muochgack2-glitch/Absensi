# 🔧 Auto-Healing Dashboard - Quick Guide

## What is Auto-Healing?
Auto-Healing Dashboard adalah sistem diagnostik otomatis yang mendeteksi dan memperbaiki masalah WhatsApp Gateway secara otomatis tanpa perlu intervensi manual.

## 🎯 Key Features

### 1️⃣ **Real-Time Diagnostics**
- ✅ Deteksi otomatis masalah server
- ✅ Update setiap 60 detik
- ✅ Status badge berwarna (Merah/Kuning/Hijau)

### 2️⃣ **One-Click Auto-Fix**
- ✅ Perbaiki masalah dengan 1 klik
- ✅ Support untuk 4 jenis masalah umum
- ✅ Rate limit: Max 3x per jam

### 3️⃣ **Error Log Viewer**
- ✅ Lihat 100 baris log error terbaru
- ✅ Tampilan dark theme
- ✅ Collapsible (bisa disembunyikan)

### 4️⃣ **Fix History**
- ✅ Lacak 10 perbaikan terakhir
- ✅ Timestamp & user yang melakukan fix
- ✅ Status success/failed

## 🚀 Quick Start

### Cara Menggunakan:

1. **Buka Dashboard**
   ```
   WhatsApp Gateway → Dashboard
   ```
   Panel diagnostik muncul di bagian atas.

2. **Cek Status**
   - 🟢 **All Systems Healthy** = Tidak ada masalah
   - 🔴 **Issues Detected** = Ada masalah yang perlu diperbaiki

3. **Auto-Fix (jika ada issue)**
   ```
   Klik tombol "Auto-Fix Issues"
   → Konfirmasi
   → Tunggu 3-10 detik
   → Selesai!
   ```

4. **Lihat Error Log** (opsional)
   ```
   Klik "View Error Logs"
   → Panel expand
   → Scroll untuk lihat log
   ```

5. **Review History**
   - Lihat tabel "Fix History" di bawah
   - Cek siapa yang fix dan kapan

## 🔍 Issue Types yang Auto-Fixable

| Issue Code | Deskripsi | Auto-Fix Action |
|------------|-----------|----------------|
| `PROCESS_NOT_FOUND` | PM2 process tidak ditemukan | Start new process |
| `IMPORT_PATH_ERROR` | Error import path di logs | Delete & restart |
| `CRASH_LOOP` | Restart count > 10 | Flush logs & restart |
| `PROCESS_STOPPED` | Process stopped/errored | Restart process |
| `HIGH_MEMORY` | Memory > 500 MB | ⚠️ Manual: Restart recommended |

## ⚡ Shortcut Commands

### Refresh Diagnostics
```
Klik "Refresh" button → Update manual
```

### Force Refresh All
```
Tekan F5 → Reload page
```

### View Logs
```
Klik "View Error Logs" → Expand panel
```

## ⚠️ Important Notes

### Rate Limiting
- **Max 3 auto-fix per jam** per user
- Cegah abuse dan restart berlebihan
- Tunggu 1 jam jika limit tercapai

### Best Time to Fix
- ✅ Pagi hari (sebelum jam sibuk)
- ✅ Saat traffic rendah
- ❌ Hindari saat jam sibuk (09:00-15:00)

### When to Use Auto-Fix
- ✅ Status "Disconnected" terus menerus
- ✅ QR Code tidak muncul
- ✅ Server hang/lambat
- ✅ Error di logs yang berulang

### When NOT to Use Auto-Fix
- ❌ Server sedang kirim broadcast
- ❌ Baru restart < 5 menit lalu
- ❌ Tidak ada masalah terdeteksi

## 🐛 Troubleshooting

### Problem: "Rate limit exceeded"
**Solution:** Tunggu 1 jam atau hubungi administrator

### Problem: Auto-fix tidak berhasil
**Solution:** 
1. Cek error logs untuk detail
2. Coba restart manual via button "Restart Server"
3. Hubungi administrator jika masih gagal

### Problem: Diagnostics tidak load
**Solution:**
1. Refresh page (F5)
2. Cek koneksi internet
3. Clear browser cache

### Problem: Error logs kosong
**Solution:**
1. Artinya tidak ada error (good!)
2. Atau PM2 belum generate log
3. Tunggu beberapa menit dan coba lagi

## 📊 Understanding Status Badges

### Issue Type Badges
- 🔴 **Error** (Red) = Masalah serius, perlu fix segera
- 🟡 **Warning** (Yellow) = Masalah minor, monitor saja
- 🟢 **Healthy** (Green) = Semua normal

### Fix Status Badges
- 🟢 **Auto-fixable** = Bisa diperbaiki otomatis
- ⚪ **Manual fix required** = Perlu perbaikan manual

### History Status
- 🟢 **N fixed** = Jumlah issue yang berhasil diperbaiki
- 🔴 **N failed** = Jumlah issue yang gagal diperbaiki

## 💡 Pro Tips

1. **Monitor Daily**
   - Cek diagnostics setiap pagi
   - Review fix history setiap minggu

2. **Pattern Recognition**
   - Jika issue yang sama muncul terus → Ada masalah lebih dalam
   - Hubungi developer untuk investigasi

3. **Memory Management**
   - Monitor "Memory %" di Server Health card
   - Jika > 90% terus menerus → Perlu upgrade server

4. **Log Analysis**
   - Error yang sama berulang → Catat pattern nya
   - Share ke developer untuk fix permanent

5. **Preventive Action**
   - Restart server 1x seminggu (scheduled)
   - Waktu ideal: Minggu malam 02:00 WIB
   - Cegah crash loop dan memory leak

## 📞 Need Help?

### Level 1: Self-Service
1. Cek dokumentasi ini
2. Lihat error logs
3. Coba auto-fix

### Level 2: Manual Action
1. Gunakan "Restart Server" button
2. Gunakan "Reset & Reconnect" button
3. Clear browser cache

### Level 3: Contact Admin
- Email: admin@sekolah.com
- WhatsApp: 08xx-xxxx-xxxx
- Include: Screenshot + error log

## 🎓 Training Video
_(Coming soon - Link to video tutorial)_

---

**Quick Access:**
- Dashboard: `/whatsapp`
- Full Documentation: `AUTO_HEALING_DASHBOARD.md`
- Support: Contact Administrator

**Version:** 1.0.0  
**Last Updated:** 2024-01-01
