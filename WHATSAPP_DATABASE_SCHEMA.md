# WhatsApp Gateway - Database Schema

Dokumentasi struktur database untuk fitur WhatsApp Gateway di sistem SPMB.

## 📊 Tabel Database

### 1. `whatsapp_logs`
Menyimpan log semua pesan WhatsApp yang dikirim melalui sistem.

| Column | Type | Description |
|--------|------|-------------|
| `id` | bigint | Primary key |
| `phone` | varchar(20) | Nomor HP tujuan (indexed) |
| `message` | text | Isi pesan yang dikirim |
| `status` | enum | Status pengiriman: `pending`, `sent`, `failed` (indexed) |
| `type` | varchar(50) | Tipe pesan: `manual`, `auto_registration`, `broadcast`, `reminder` (indexed) |
| `pendaftar_id` | bigint | Foreign key ke tabel `pendaftars` (nullable) |
| `template_id` | bigint | Foreign key ke tabel `whatsapp_templates` (nullable) |
| `sent_by` | bigint | Foreign key ke tabel `users` - user yang mengirim (nullable) |
| `error_message` | text | Pesan error jika gagal kirim (nullable) |
| `sent_at` | timestamp | Waktu pesan terkirim (nullable, indexed) |
| `metadata` | json | Data tambahan (response API, dll) (nullable) |
| `created_at` | timestamp | Waktu record dibuat (indexed) |
| `updated_at` | timestamp | Waktu record diupdate |

**Indexes:**
- `phone` - untuk query berdasarkan nomor HP
- `status` - untuk filter status pengiriman
- `type` - untuk filter tipe pesan
- `sent_at` - untuk query berdasarkan waktu kirim
- `created_at` - untuk query berdasarkan waktu dibuat

**Relations:**
- `belongsTo` → `Pendaftar` (pendaftar_id)
- `belongsTo` → `WhatsAppTemplate` (template_id)
- `belongsTo` → `User` (sent_by)

---

### 2. `whatsapp_templates`
Menyimpan template pesan WhatsApp yang dapat digunakan berulang kali.

| Column | Type | Description |
|--------|------|-------------|
| `id` | bigint | Primary key |
| `name` | varchar(100) | Nama unik template (e.g., `welcome_message`) (unique) |
| `label` | varchar(200) | Label untuk tampilan admin UI |
| `message` | text | Template pesan (support variables: `{nama}`, `{no_pendaftaran}`, dll) |
| `description` | text | Deskripsi template (nullable) |
| `type` | enum | Kategori: `registration`, `payment`, `reminder`, `notification`, `custom` (indexed) |
| `is_active` | boolean | Status aktif/nonaktif (default: true, indexed) |
| `auto_send` | boolean | Kirim otomatis atau manual (default: false, indexed) |
| `variables` | json | List variabel yang tersedia (nullable) |
| `usage_count` | integer | Jumlah kali template digunakan (default: 0) |
| `last_used_at` | timestamp | Terakhir kali template digunakan (nullable) |
| `created_at` | timestamp | Waktu record dibuat |
| `updated_at` | timestamp | Waktu record diupdate |

**Indexes:**
- `type` - untuk filter berdasarkan kategori
- `is_active` - untuk query template aktif
- `auto_send` - untuk query template auto send

**Relations:**
- `hasMany` → `WhatsAppLog` (template_id)

**Template Variables:**
Template mendukung variabel dinamis yang akan di-replace saat pengiriman:
- `{nama}` - Nama lengkap pendaftar
- `{no_pendaftaran}` - Nomor pendaftaran
- `{jurusan}` - Nama jurusan yang dipilih
- `{portal_url}` - URL portal SPMB
- `{sekolah}` - Nama sekolah
- `{tanggal_tes}` - Tanggal tes (untuk template jadwal)
- `{waktu_tes}` - Waktu tes (untuk template jadwal)
- `{tempat_tes}` - Lokasi tes (untuk template jadwal)

**Contoh Template:**
```
Assalamu'alaikum {nama},

Selamat! Pendaftaran Anda telah berhasil.

📋 Detail Pendaftaran:
No. Pendaftaran: {no_pendaftaran}
Nama: {nama}
Jurusan: {jurusan}

✅ Silakan login ke portal SPMB untuk melengkapi data.

🔗 Portal: {portal_url}

Terima kasih telah mendaftar di {sekolah}.
```

---

### 3. `whatsapp_settings`
Menyimpan konfigurasi sistem WhatsApp Gateway.

| Column | Type | Description |
|--------|------|-------------|
| `id` | bigint | Primary key |
| `key` | varchar(100) | Setting key (unique) |
| `value` | text | Setting value (nullable) |
| `type` | varchar(50) | Tipe data: `string`, `boolean`, `integer`, `json` (default: `string`) |
| `group` | varchar(50) | Group setting: `general`, `connection`, `notification`, `advanced` (indexed) |
| `label` | varchar(200) | Label untuk tampilan admin UI |
| `description` | text | Deskripsi setting (nullable) |
| `is_public` | boolean | Apakah bisa diakses tanpa auth (default: false) |
| `created_at` | timestamp | Waktu record dibuat |
| `updated_at` | timestamp | Waktu record diupdate |

**Indexes:**
- `group` - untuk query berdasarkan group

**Default Settings:**

| Key | Value | Type | Group | Description |
|-----|-------|------|-------|-------------|
| `wa_server_url` | `http://localhost:3000` | string | connection | URL endpoint WhatsApp Gateway server |
| `wa_auto_send_enabled` | `true` | boolean | general | Aktifkan pengiriman otomatis saat pendaftaran |
| `wa_timeout` | `10` | integer | connection | Timeout koneksi ke WhatsApp server (detik) |
| `wa_retry_attempts` | `3` | integer | connection | Jumlah percobaan ulang jika gagal kirim |
| `wa_rate_limit` | `20` | integer | advanced | Maksimal pesan per menit |
| `wa_log_retention_days` | `90` | integer | advanced | Berapa lama log disimpan (hari) |

---

## 🔄 Relasi Antar Tabel

```
whatsapp_logs
├── belongsTo → pendaftars (pendaftar_id)
├── belongsTo → whatsapp_templates (template_id)
└── belongsTo → users (sent_by)

whatsapp_templates
└── hasMany → whatsapp_logs (template_id)

whatsapp_settings
└── (no relations)
```

---

## 📝 Model Methods

### WhatsAppLog Model

**Scopes:**
- `status($status)` - Filter berdasarkan status
- `type($type)` - Filter berdasarkan type
- `sent()` - Pesan yang berhasil terkirim
- `failed()` - Pesan yang gagal
- `pending()` - Pesan pending
- `today()` - Pesan hari ini

**Methods:**
- `markAsSent($metadata)` - Mark pesan sebagai terkirim
- `markAsFailed($errorMessage, $metadata)` - Mark pesan sebagai gagal
- `getFormattedPhoneAttribute()` - Get nomor HP terformat
- `getStatusColorAttribute()` - Get warna badge status
- `getStatusLabelAttribute()` - Get label status
- `getTypeLabelAttribute()` - Get label type

**Usage Example:**
```php
// Get all sent messages today
$sentToday = WhatsAppLog::sent()->today()->get();

// Mark message as sent
$log->markAsSent(['response' => 'success']);

// Mark message as failed
$log->markAsFailed('Connection timeout');
```

---

### WhatsAppTemplate Model

**Scopes:**
- `active()` - Template yang aktif
- `autoSend()` - Template auto send
- `type($type)` - Filter berdasarkan type

**Methods:**
- `parse(array $data)` - Parse template dengan data
- `incrementUsage()` - Increment usage count
- `getAvailableVariables()` - Get list variabel yang tersedia
- `isUsed()` - Check apakah template pernah digunakan
- `getPreview()` - Get preview dengan sample data
- `getTypeColorAttribute()` - Get warna badge type
- `getTypeLabelAttribute()` - Get label type

**Usage Example:**
```php
// Get active registration templates
$templates = WhatsAppTemplate::active()->type('registration')->get();

// Parse template with data
$message = $template->parse([
    'nama' => 'John Doe',
    'no_pendaftaran' => 'REG-2026-001',
    'jurusan' => 'TKJ',
]);

// Increment usage
$template->incrementUsage();

// Get preview
$preview = $template->getPreview();
```

---

### WhatsAppSetting Model

**Scopes:**
- `group($group)` - Filter berdasarkan group
- `public()` - Setting yang public

**Static Methods:**
- `get($key, $default)` - Get setting value (with cache)
- `set($key, $value)` - Set setting value
- `getByGroup($group)` - Get all settings by group
- `clearCache()` - Clear all settings cache
- `getServerUrl()` - Get WhatsApp server URL
- `isAutoSendEnabled()` - Check if auto send enabled
- `getTimeout()` - Get connection timeout
- `getRetryAttempts()` - Get retry attempts
- `getRateLimit()` - Get rate limit
- `getLogRetentionDays()` - Get log retention days

**Methods:**
- `getFormattedValueAttribute()` - Get formatted value untuk display
- `getGroupColorAttribute()` - Get warna badge group
- `getGroupLabelAttribute()` - Get label group

**Usage Example:**
```php
// Get setting value
$serverUrl = WhatsAppSetting::getServerUrl();
$autoSend = WhatsAppSetting::isAutoSendEnabled();

// Set setting value
WhatsAppSetting::set('wa_auto_send_enabled', true);

// Get all connection settings
$connectionSettings = WhatsAppSetting::getByGroup('connection');

// Clear cache
WhatsAppSetting::clearCache();
```

---

## 🚀 Migration & Seeding

### Run Migrations:
```bash
php artisan migrate
```

### Run Seeder:
```bash
php artisan db:seed --class=WhatsAppSeeder
```

Seeder akan mengisi:
- **6 default settings** (server URL, auto send, timeout, dll)
- **5 template pesan** (welcome, payment reminder, payment confirmed, test schedule, acceptance)

### Rollback:
```bash
php artisan migrate:rollback --step=3
```

---

## 📈 Query Examples

### Get statistics
```php
// Total messages sent today
$sentToday = WhatsAppLog::sent()->today()->count();

// Failed messages in last 7 days
$failedWeek = WhatsAppLog::failed()
    ->where('created_at', '>=', now()->subDays(7))
    ->count();

// Most used template
$mostUsed = WhatsAppTemplate::orderBy('usage_count', 'desc')->first();

// Messages by type
$byType = WhatsAppLog::selectRaw('type, count(*) as total')
    ->groupBy('type')
    ->get();
```

### Get logs with relations
```php
// Get logs with pendaftar and template
$logs = WhatsAppLog::with(['pendaftar', 'template', 'sender'])
    ->latest()
    ->paginate(20);

// Get template with usage stats
$template = WhatsAppTemplate::with(['logs' => function($query) {
    $query->where('created_at', '>=', now()->subDays(30));
}])->find($id);
```

---

## 🔒 Security Notes

1. **Phone Number Validation**: Selalu validasi format nomor HP sebelum menyimpan
2. **Rate Limiting**: Gunakan setting `wa_rate_limit` untuk mencegah spam
3. **Log Retention**: Otomatis hapus log lama sesuai `wa_log_retention_days`
4. **Template Sanitization**: Sanitize input saat membuat/edit template
5. **Access Control**: Hanya admin yang bisa akses WhatsApp settings

---

## 📊 Performance Optimization

1. **Indexes**: Semua kolom yang sering di-query sudah di-index
2. **Caching**: WhatsAppSetting menggunakan cache (1 hour)
3. **Eager Loading**: Gunakan `with()` untuk load relations
4. **Pagination**: Selalu gunakan pagination untuk list logs
5. **Queue**: Pertimbangkan gunakan queue untuk bulk sending

---

## 🔄 Maintenance Tasks

### Clean old logs (via Artisan command - to be created)
```bash
php artisan whatsapp:clean-logs
```

### Reset template usage stats
```php
WhatsAppTemplate::query()->update([
    'usage_count' => 0,
    'last_used_at' => null
]);
```

### Backup logs before cleanup
```bash
php artisan whatsapp:backup-logs --days=90
```

---

## 📚 Next Steps

1. ✅ Database migrations created
2. ✅ Models created with relations and methods
3. ✅ Seeder created with default data
4. ⏳ Create WhatsAppService class
5. ⏳ Create WhatsAppController
6. ⏳ Create admin UI views
7. ⏳ Integrate with RegistrationController
8. ⏳ Create Artisan commands for maintenance

---

**Created:** 2026-05-31  
**Version:** 1.0.0  
**Author:** SPMB Development Team
