# 🧪 Panduan Testing External Broadcast di Hosting

## 📋 Checklist Pre-Testing

### 1. Pull Latest Code di Hosting
```bash
cd /path/to/your/project
git pull origin main
```

### 2. Run Database Migrations
```bash
php artisan migrate
```

**Migrations yang akan dijalankan:**
- ✅ `add_external_batch_id_to_whatsapp_logs_table`
- ✅ `create_external_broadcast_batches_table`
- ✅ `create_external_broadcast_recipients_table`

### 3. Clear Cache
```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
```

### 4. Set Permissions (jika perlu)
```bash
chmod -R 775 storage bootstrap/cache
```

---

## 🎯 Skenario Testing

### **TEST 1: Akses Halaman Broadcast dengan Tab Baru**

**URL:** `/whatsapp/broadcast`

**Expected Result:**
- ✅ Ada 2 tabs: "Data SPMB" dan "Data Eksternal"
- ✅ Tab "Data SPMB" adalah existing broadcast page (tidak berubah)
- ✅ Tab "Data Eksternal" menampilkan form baru

**Screenshot Points:**
- Tab navigation
- Form external broadcast

---

### **TEST 2: Upload CSV & Parse Recipients**

**Langkah:**
1. Buka tab "Data Eksternal"
2. Isi "Nama Batch": `Test CSV Alumni 2024`
3. Pilih "Upload CSV"
4. Upload file CSV dengan format:
   ```csv
   name,phone,notes
   Budi Santoso,081234567890,Alumni 2023
   Siti Aminah,082345678901,Orang Tua Siswa
   ```
5. Klik "Parse & Preview Recipients"

**Expected Result:**
- ✅ Preview table muncul dengan 2 recipients
- ✅ Total count: 2
- ✅ Duplicate count: tergantung data (bisa 0 jika nomor unik)
- ✅ Phone numbers ternormalisasi ke format 62xxx

**Possible Issues & Solutions:**
- ❌ Error "File not found": Check file upload permissions di `storage/`
- ❌ Error "Invalid CSV": Pastikan CSV menggunakan header `name,phone,notes`
- ❌ Error "Batch name sudah digunakan": Ganti nama batch

---

### **TEST 3: Input Manual & Parse**

**Langkah:**
1. Buat batch baru dengan nama berbeda: `Test Manual Input`
2. Pilih "Input Manual"
3. Input data di textarea:
   ```
   083456789012|Ahmad Rizki|Guru
   084567890123
   085678901234|Dewi Lestari|Staff
   ```
4. Klik "Parse & Preview Recipients"

**Expected Result:**
- ✅ 3 recipients ter-parse
- ✅ Nomor tanpa nama akan menggunakan "External Contact" sebagai default

---

### **TEST 4: Deteksi Duplikat dengan Database SPMB**

**Langkah:**
1. Ambil 1 nomor HP yang **sudah ada** di database pendaftar (dari tabel `pendaftar`)
2. Buat batch baru dengan nomor tersebut di CSV atau manual input
3. Parse recipients

**Expected Result:**
- ✅ Recipient dengan nomor duplikat menampilkan badge **🔄 Duplikat**
- ✅ Duplicate count bertambah
- ✅ Pesan tetap bisa dikirim (tidak diblock)

**Database Check:**
```sql
-- Cek nomor yang ter-flag duplikat
SELECT * FROM external_broadcast_recipients 
WHERE is_duplicate_spmb = 1 
ORDER BY created_at DESC;

-- Cek matched pendaftar
SELECT ebr.name, ebr.phone, p.nama_lengkap, p.no_hp_wali
FROM external_broadcast_recipients ebr
LEFT JOIN pendaftar p ON ebr.matched_pendaftar_id = p.id_pendaftar
WHERE ebr.is_duplicate_spmb = 1;
```

---

### **TEST 5: Send Broadcast**

**Langkah:**
1. Setelah parse recipients berhasil
2. Pilih template (opsional) atau tulis pesan manual
3. Gunakan variabel: `Halo {nama}, nomor HP Anda {phone} telah terdaftar.`
4. Klik "Kirim Broadcast"

**Expected Result:**
- ✅ Loading indicator muncul
- ✅ Alert success dengan summary: Total/Berhasil/Gagal
- ✅ Messages tercatat di `whatsapp_logs` dengan `external_batch_id`

**Database Check:**
```sql
-- Cek batch yang baru dibuat
SELECT * FROM external_broadcast_batches 
ORDER BY created_at DESC LIMIT 5;

-- Cek log pesan
SELECT * FROM whatsapp_logs 
WHERE external_batch_id IS NOT NULL 
ORDER BY created_at DESC LIMIT 10;

-- Cek status batch
SELECT 
    id, batch_name, status, 
    total_recipients, total_sent, total_failed,
    created_at, completed_at
FROM external_broadcast_batches 
WHERE status = 'completed'
ORDER BY created_at DESC;
```

---

### **TEST 6: Tab Eksternal di Phone List**

**URL:** `/whatsapp/phone-list?tab=external`

**Expected Result:**
- ✅ Tab "🌐 Eksternal" muncul di tab bar
- ✅ Badge menampilkan jumlah external recipients
- ✅ Table menampilkan recipients dengan kolom: Nama, Nomor HP, Batch, Notes, Pesan, Terakhir
- ✅ Duplicate badge (🔄) muncul untuk recipients yang duplikat

**Test Filter:**
- Centang "Tampilkan hanya duplikat SPMB" → hanya show recipients dengan flag duplikat

---

### **TEST 7: View Message History (External)**

**Langkah:**
1. Di tab Eksternal phone list
2. Klik button "Lihat" di kolom Pesan
3. Modal muncul dengan riwayat pesan

**Expected Result:**
- ✅ Modal menampilkan detail recipient (nama, phone, batch)
- ✅ Status duplikat ditampilkan
- ✅ Jika duplikat, ada link ke pendaftar SPMB
- ✅ Riwayat pesan ditampilkan dengan status (Terkirim/Gagal/Pending)

---

### **TEST 8: Template Variable Warning**

**Langkah:**
1. Di tab Data Eksternal
2. Pilih template yang menggunakan variabel SPMB: `{no_registrasi}`, `{jurusan}`, `{nisn}`
3. Perhatikan warning

**Expected Result:**
- ✅ Alert warning muncul: "Template ini menggunakan variabel SPMB yang tidak tersedia untuk data eksternal"
- ✅ List variabel yang bermasalah ditampilkan

---

## 🔍 Database Verification Queries

### Check All External Batches
```sql
SELECT 
    id, batch_name, source_type, status,
    total_recipients, total_sent, total_failed,
    created_at, completed_at
FROM external_broadcast_batches
ORDER BY created_at DESC;
```

### Check Recipients per Batch
```sql
SELECT 
    ebr.id, ebr.name, ebr.phone, ebr.is_duplicate_spmb,
    ebb.batch_name, ebb.status
FROM external_broadcast_recipients ebr
JOIN external_broadcast_batches ebb ON ebr.batch_id = ebb.id
ORDER BY ebr.created_at DESC
LIMIT 20;
```

### Check Message Logs with External Batch
```sql
SELECT 
    wl.id, wl.phone, wl.message, wl.status, wl.type,
    wl.external_batch_id, wl.created_at,
    ebb.batch_name
FROM whatsapp_logs wl
LEFT JOIN external_broadcast_batches ebb ON wl.external_batch_id = ebb.id
WHERE wl.external_batch_id IS NOT NULL
ORDER BY wl.created_at DESC
LIMIT 20;
```

### Count Statistics
```sql
-- Total batches per status
SELECT status, COUNT(*) as total 
FROM external_broadcast_batches 
GROUP BY status;

-- Total recipients vs duplicates
SELECT 
    COUNT(*) as total_recipients,
    SUM(CASE WHEN is_duplicate_spmb = 1 THEN 1 ELSE 0 END) as duplicates,
    SUM(CASE WHEN is_duplicate_spmb = 0 THEN 1 ELSE 0 END) as unique_recipients
FROM external_broadcast_recipients;

-- Messages sent per batch
SELECT 
    ebb.batch_name,
    COUNT(wl.id) as messages_sent
FROM external_broadcast_batches ebb
LEFT JOIN whatsapp_logs wl ON wl.external_batch_id = ebb.id
GROUP BY ebb.id, ebb.batch_name
ORDER BY ebb.created_at DESC;
```

---

## ⚠️ Common Issues & Troubleshooting

### Issue 1: Migration Error
**Error:** `SQLSTATE[42S01]: Base table or view already exists`
**Solution:** Table sudah ada. Skip atau rollback dulu.
```bash
php artisan migrate:status
```

### Issue 2: Route Not Found
**Error:** `404 Not Found` saat akses `/whatsapp/broadcast/external`
**Solution:**
```bash
php artisan route:clear
php artisan config:clear
php artisan route:list | grep external
```

### Issue 3: CSV Parse Error
**Error:** `"Invalid CSV format"`
**Solution:** 
- Pastikan CSV menggunakan encoding UTF-8
- Header harus tepat: `name,phone,notes`
- Cek apakah ada karakter aneh atau BOM

### Issue 4: Permission Denied
**Error:** `"Unable to write file"`
**Solution:**
```bash
chmod -R 775 storage
chown -R www-data:www-data storage
```

### Issue 5: WhatsApp Gateway Not Connected
**Error:** `"WhatsApp Gateway tidak terhubung"`
**Solution:**
- Cek status WhatsApp Gateway di dashboard
- Pastikan QR code sudah di-scan
- Restart gateway jika perlu

---

## 📸 Screenshots to Capture

Untuk dokumentasi, ambil screenshot dari:
1. ✅ Tab navigation di broadcast page
2. ✅ Form external broadcast dengan CSV upload
3. ✅ Preview recipients dengan duplicate badge
4. ✅ Success message setelah send broadcast
5. ✅ Tab Eksternal di phone list
6. ✅ External recipients table dengan duplikat
7. ✅ Modal message history untuk external recipient
8. ✅ Template variable warning

---

## ✅ Testing Checklist

- [ ] Pull latest code dari git
- [ ] Run migrations berhasil
- [ ] Clear all cache
- [ ] Akses halaman broadcast - tab muncul
- [ ] Test CSV upload & parse
- [ ] Test manual input & parse
- [ ] Test deteksi duplikat
- [ ] Send broadcast berhasil
- [ ] Check database - batch created
- [ ] Check database - logs created
- [ ] Tab eksternal di phone list berfungsi
- [ ] Filter duplikat berfungsi
- [ ] View message history modal berfungsi
- [ ] Template variable warning muncul

---

## 🎉 Success Criteria

Fitur dianggap berhasil jika:
- ✅ Semua 8 test scenarios berjalan tanpa error
- ✅ Database records tercreate dengan benar
- ✅ WhatsApp messages terkirim
- ✅ Duplicate detection berfungsi
- ✅ UI/UX responsive dan user-friendly

---

## 📞 Need Help?

Jika ada error atau pertanyaan saat testing:
1. Screenshot error message
2. Check Laravel logs: `storage/logs/laravel.log`
3. Check browser console untuk JavaScript errors
4. Jalankan query verification di atas

Happy Testing! 🚀
