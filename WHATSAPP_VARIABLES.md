# Variabel Template WhatsApp

## Daftar Variabel yang Tersedia

Semua variabel template WhatsApp di sistem SPMB mengikuti standar yang sama. Berikut adalah variabel yang tersedia untuk digunakan dalam template pesan:

### Data Pendaftar

| Variabel | Deskripsi | Contoh Output |
|----------|-----------|---------------|
| `{nama}` atau `{nama_lengkap}` | Nama lengkap pendaftar | "John Doe" |
| `{no_pendaftaran}` atau `{no_registrasi}` | Nomor registrasi pendaftar | "SPMB-2026-0001" |
| `{nisn}` | Nomor Induk Siswa Nasional | "1234567890" |
| `{jurusan}` | Nama jurusan pilihan | "Teknik Komputer dan Jaringan" |
| `{asal_sekolah}` | Nama sekolah asal | "SMP Negeri 1 Jakarta" |
| `{gelombang}` | Gelombang pendaftaran | "1" |

### Informasi Sistem

| Variabel | Deskripsi | Contoh Output |
|----------|-----------|---------------|
| `{sekolah}` | Nama sekolah (dari settings) | "SMK PGRI BLORA" |
| `{portal_url}` | URL portal SPMB | "https://spmb.smegandiarasa.sch.id" |
| `{tanggal}` | Tanggal hari ini | "08-06-2026" |
| `{tahun}` | Tahun sekarang | "2026" |

## Contoh Penggunaan

### Template Welcome Registration

```
Assalamu'alaikum {nama},

Selamat! Pendaftaran Anda di {sekolah} telah berhasil.

📋 Detail Pendaftaran:
- Nomor Registrasi: {no_registrasi}
- NISN: {nisn}
- Jurusan: {jurusan}
- Gelombang: {gelombang}

Silakan akses portal kami di {portal_url} untuk melengkapi data dan melihat informasi selanjutnya.

Terima kasih atas kepercayaan Anda.

{sekolah}
Tanggal: {tanggal}
```

### Template Reminder

```
Hai {nama},

Ini adalah pengingat untuk melengkapi data pendaftaran Anda.

Nomor Registrasi: {no_registrasi}
Portal: {portal_url}

Segera lengkapi data Anda sebelum batas waktu berakhir.

Salam,
{sekolah}
```

### Template Payment Confirmation

```
Terima kasih {nama}!

Pembayaran pendaftaran Anda telah kami terima.

Detail:
- No. Registrasi: {no_registrasi}
- Jurusan: {jurusan}
- Tanggal: {tanggal}

Silakan tunggu konfirmasi lebih lanjut melalui portal {portal_url}

{sekolah}
```

## Penggunaan di Sistem

### 1. Auto-Send saat Pendaftaran

File: `app/Http/Controllers/RegistrationController.php`

Variabel yang tersedia:
- `{nama}`
- `{no_registrasi}`
- `{jurusan}`
- `{nisn}`
- `{asal_sekolah}`
- `{gelombang}`
- `{portal_url}`
- `{sekolah}`
- `{tanggal}`
- `{tahun}`

### 2. Broadcast Manual

File: `app/Http/Controllers/WhatsAppController.php` method `sendBroadcast()`

Variabel yang tersedia (sama dengan auto-send):
- `{nama}`
- `{no_registrasi}`
- `{jurusan}`
- `{nisn}`
- `{asal_sekolah}`
- `{portal_url}`
- `{sekolah}`
- `{tanggal}`
- `{tahun}`

### 3. Broadcast dari Phone List

File: `app/Http/Controllers/WhatsAppController.php` method `sendBulkBroadcast()`

Variabel yang tersedia (sama dengan broadcast manual).

## Cara Menambah Variabel Baru

Jika Anda ingin menambahkan variabel baru, ikuti langkah berikut:

### 1. Update Method `replaceMessageVariables()`

File: `app/Http/Controllers/WhatsAppController.php`

```php
private function replaceMessageVariables(string $message, array $data): string
{
    $settings = SettingSystem::instance()->toSettingsArray();
    
    $replacements = [
        '{nama}' => $data['name'] ?? '',
        // ... variabel lain ...
        '{variabel_baru}' => $data['variabel_baru'] ?? '', // Tambahkan di sini
    ];

    return str_replace(array_keys($replacements), array_values($replacements), $message);
}
```

### 2. Update Data yang Dikirim

Di setiap tempat yang memanggil `replaceMessageVariables()` atau `sendWithTemplate()`, tambahkan data baru:

```php
$data = [
    'name' => $pendaftar->nama_lengkap,
    // ... data lain ...
    'variabel_baru' => $pendaftar->field_baru, // Tambahkan data
];
```

### 3. Update Preview di Model

File: `app/Models/WhatsAppTemplate.php` method `getPreview()`

```php
$sampleData = [
    'nama' => 'John Doe',
    // ... data lain ...
    'variabel_baru' => 'Contoh Data', // Tambahkan contoh
];
```

### 4. Update Dokumentasi UI

File: `resources/views/whatsapp/broadcast.blade.php`

Tambahkan di card "Variabel Template":

```html
<code>{variabel_baru}</code> - Deskripsi variabel<br>
```

## Best Practices

1. **Gunakan nama variabel yang konsisten**: Pilih antara underscore (`no_registrasi`) atau camelCase, jangan campur.

2. **Provide fallback values**: Selalu sediakan nilai default untuk variabel yang mungkin kosong.
   ```php
   '{sekolah}' => $settings['school_name'] ?? 'SMK PGRI BLORA',
   ```

3. **Format yang konsisten**: Gunakan format tanggal yang sama di semua tempat.
   ```php
   '{tanggal}' => now()->format('d-m-Y'),
   ```

4. **Test template preview**: Selalu test preview template sebelum digunakan untuk broadcast.

5. **Dokumentasi**: Update dokumentasi ini setiap kali menambah variabel baru.

## Troubleshooting

### Variabel Tidak Terganti

**Problem**: Variabel masih muncul sebagai `{nama}` di pesan yang diterima.

**Solusi**:
1. Pastikan variabel ditulis dengan benar (case-sensitive)
2. Pastikan data tersebut dikirim ke method parse/replace
3. Check logs untuk melihat data apa yang sebenarnya dikirim

### Variabel Menampilkan Data Lama

**Problem**: `{sekolah}` menampilkan "SPMB SMK PGRI BLORA" bukan "SMK PGRI BLORA"

**Solusi**:
1. Pastikan menggunakan `SettingSystem::instance()->toSettingsArray()` bukan `config('app.name')`
2. Clear config cache: `php artisan config:clear`
3. Update setting di halaman Pengaturan Sistem

### Preview Template Tidak Sesuai

**Problem**: Preview template tidak menampilkan contoh data yang benar.

**Solusi**:
1. Update method `getPreview()` di `WhatsAppTemplate.php`
2. Pastikan sample data sesuai dengan variabel yang tersedia
3. Clear view cache: `php artisan view:clear`

## Referensi File

1. **Controller**: `app/Http/Controllers/WhatsAppController.php`
2. **Service**: `app/Services/WhatsAppService.php`
3. **Model**: `app/Models/WhatsAppTemplate.php`
4. **Registration**: `app/Http/Controllers/RegistrationController.php`
5. **View**: `resources/views/whatsapp/broadcast.blade.php`

## Update History

- **2026-06-08**: Initial documentation
- **2026-06-08**: Changed `{sekolah}` from `config('app.name')` to `SettingSystem` school_name
- **2026-06-08**: Added more variables: `{nisn}`, `{asal_sekolah}`, `{tahun}`
- **2026-06-08**: Standardized variable names (support both underscore variants)
