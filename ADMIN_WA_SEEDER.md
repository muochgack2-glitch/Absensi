# Admin WA Seeder Documentation

## Overview
AdminSeeder telah diupdate untuk otomatis membuat user **Admin WhatsApp** dengan role `admin_wa`.

## Default Credentials

Setelah menjalankan seeder, akan ada 3 user default:

### 1. Administrator
- **Email**: `admin@spmb.pgri` (atau sesuai `ADMIN_EMAIL` di .env)
- **Password**: `admin123` (atau sesuai `ADMIN_PASSWORD` di .env)
- **Role**: `administrator`
- **Akses**: Full access ke semua fitur

### 2. Panitia
- **Email**: `panitia@spmb.pgri` (atau sesuai `PANITIA_EMAIL` di .env)
- **Password**: `panitia123` (atau sesuai `PANITIA_PASSWORD` di .env)
- **Role**: `panitia`
- **Akses**: Dashboard, Data Pendaftar, Verifikasi, Laporan

### 3. Admin WhatsApp (NEW!)
- **Email**: `adminwa@spmb.pgri` (atau sesuai `ADMIN_WA_EMAIL` di .env)
- **Password**: `adminwa123` (atau sesuai `ADMIN_WA_PASSWORD` di .env)
- **Role**: `admin_wa`
- **Akses**: Dashboard, WhatsApp Gateway only

## Environment Variables

Tambahkan ke file `.env`:

```env
# Admin WhatsApp Credentials
ADMIN_WA_EMAIL=adminwa@spmb.pgri
ADMIN_WA_PASSWORD=adminwa123
ADMIN_WA_NAME="Admin WhatsApp"
```

## Running the Seeder

### Fresh Installation
```bash
php artisan migrate:fresh --seed
```

### Update Existing Database
```bash
php artisan db:seed --class=AdminSeeder
```

### Run All Seeders
```bash
php artisan db:seed
```

## Seeder Code

File: `database/seeders/AdminSeeder.php`

```php
// Create/Update Admin WhatsApp in Users table (new system)
$adminWaName = env('ADMIN_WA_NAME', 'Admin WhatsApp');
$adminWaPassword = env('ADMIN_WA_PASSWORD', 'adminwa123');
$adminWaEmail = env('ADMIN_WA_EMAIL', 'adminwa@spmb.pgri');

User::updateOrCreate(
    ['email' => $adminWaEmail],
    [
        'name' => $adminWaName,
        'password' => Hash::make($adminWaPassword),
        'role' => 'admin_wa',
        'status' => 'aktif',
        'email_verified_at' => now(),
    ]
);
```

## Features

### 1. UpdateOrCreate
Seeder menggunakan `updateOrCreate()` sehingga:
- ✅ Jika user belum ada → akan dibuat
- ✅ Jika user sudah ada → password dan data akan diupdate
- ✅ Aman dijalankan berulang kali

### 2. Environment-Based
Credentials bisa dikustomisasi via `.env`:
```env
ADMIN_WA_EMAIL=custom@email.com
ADMIN_WA_PASSWORD=custom-password
ADMIN_WA_NAME="Custom Name"
```

### 3. Auto-Verified
Email otomatis terverifikasi dengan `email_verified_at => now()`

## Deployment Steps

### On aaPanel Server:

**1. Update .env file:**
```bash
cd /www/wwwroot/spmb
nano .env
```

Add these lines:
```env
ADMIN_WA_EMAIL=adminwa@spmb.pgri
ADMIN_WA_PASSWORD=YourSecurePassword123!
ADMIN_WA_NAME="Admin WhatsApp SPMB"
```

**2. Pull latest code:**
```bash
git pull origin main
```

**3. Run seeder:**
```bash
php artisan db:seed --class=AdminSeeder
```

**4. Verify:**
```bash
php artisan tinker
>>> \App\Models\User::where('role', 'admin_wa')->first()
```

Expected output:
```
=> App\Models\User {#...
     id: 3,
     name: "Admin WhatsApp SPMB",
     email: "adminwa@spmb.pgri",
     role: "admin_wa",
     status: "aktif",
     ...
   }
```

## Testing Login

**1. Open browser:**
```
https://your-domain.com/login
```

**2. Login with Admin WA credentials:**
- Email: `adminwa@spmb.pgri`
- Password: `adminwa123` (or your custom password)

**3. Verify access:**
- ✅ Should see Dashboard
- ✅ Should see WhatsApp Gateway menu
- ❌ Should NOT see Data Pendaftar
- ❌ Should NOT see Laporan
- ❌ Should NOT see Settings
- ❌ Should NOT see User Management

## Security Recommendations

### Production Environment:

**1. Change default passwords:**
```env
ADMIN_PASSWORD=VerySecurePassword123!@#
PANITIA_PASSWORD=AnotherSecurePass456!@#
ADMIN_WA_PASSWORD=WhatsAppAdminPass789!@#
```

**2. Use strong passwords:**
- Minimal 12 karakter
- Kombinasi huruf besar, kecil, angka, simbol
- Tidak menggunakan kata umum

**3. Change default emails:**
```env
ADMIN_EMAIL=admin@yourdomain.com
PANITIA_EMAIL=panitia@yourdomain.com
ADMIN_WA_EMAIL=wa-admin@yourdomain.com
```

**4. After first login:**
- Login sebagai Administrator
- Buka User Management
- Edit user Admin WA
- Ganti password via UI
- Atau user Admin WA bisa ganti password sendiri

## Troubleshooting

### Issue 1: User tidak terbuat
**Solution:**
```bash
# Check if seeder runs
php artisan db:seed --class=AdminSeeder --verbose

# Check database
php artisan tinker
>>> \App\Models\User::all()
```

### Issue 2: Password tidak bisa login
**Solution:**
```bash
# Re-run seeder untuk reset password
php artisan db:seed --class=AdminSeeder

# Atau manual update via tinker
php artisan tinker
>>> $user = \App\Models\User::where('email', 'adminwa@spmb.pgri')->first()
>>> $user->password = \Hash::make('newpassword123')
>>> $user->save()
```

### Issue 3: Role tidak sesuai
**Solution:**
```bash
php artisan tinker
>>> $user = \App\Models\User::where('email', 'adminwa@spmb.pgri')->first()
>>> $user->role = 'admin_wa'
>>> $user->save()
```

## Files Modified

1. `database/seeders/AdminSeeder.php` - Added Admin WA user creation
2. `.env.example` - Added Admin WA environment variables

## Date
May 31, 2026
