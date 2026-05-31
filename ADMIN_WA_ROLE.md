# Feature: Admin WhatsApp Role

## Overview
Menambahkan role baru **"Admin WA"** yang hanya memiliki akses ke:
1. **Dashboard** - Melihat statistik umum
2. **WhatsApp Gateway** - Semua fitur WhatsApp (kirim pesan, broadcast, logs, templates, settings)

Role ini **TIDAK** memiliki akses ke:
- Data Pendaftar
- Verifikasi Daftar Ulang
- Laporan & Cetak
- Pengaturan Sistem
- Manajemen User

## Use Case
Untuk user yang hanya bertugas mengelola komunikasi WhatsApp dengan calon siswa, tanpa perlu akses ke data pendaftaran atau pengaturan sistem.

## Implementation

### 1. User Model (`app/Models/User.php`)
Menambahkan helper methods:

```php
/**
 * Check if user is admin WhatsApp
 */
public function isAdminWA(): bool
{
    return $this->role === 'admin_wa';
}

/**
 * Check if user can access WhatsApp features
 */
public function canAccessWhatsApp(): bool
{
    return in_array($this->role, ['administrator', 'admin_wa']);
}
```

### 2. Routes (`routes/web.php`)
Menambahkan role-based middleware:

**Pendaftar & Reports** - Hanya untuk Administrator dan Panitia:
```php
Route::middleware(['checkRole:administrator,panitia'])->group(function () {
    // Pendaftar routes
    // Reports routes
});
```

**WhatsApp Gateway** - Untuk Administrator dan Admin WA:
```php
Route::middleware(['checkRole:administrator,admin_wa'])->prefix('whatsapp')->name('whatsapp.')->group(function () {
    // WhatsApp routes
});
```

### 3. Sidebar (`resources/views/partials/admin-sidebar.blade.php`)
Menu visibility berdasarkan role:

```blade
{{-- Dashboard - Semua role --}}
<li class="nav-item">
    <a href="{{ route('dashboard') }}">Dashboard</a>
</li>

{{-- Pendaftar, Verifikasi, Laporan - Hanya Administrator & Panitia --}}
@if(auth()->check() && (auth()->user()->isAdministrator() || auth()->user()->isPanitia()))
    <li class="nav-item">
        <a href="{{ route('pendaftar.index') }}">Data Pendaftar</a>
    </li>
    <li class="nav-item">
        <a href="{{ route('report.index') }}">Laporan</a>
    </li>
@endif

{{-- WhatsApp Gateway - Administrator & Admin WA --}}
@if(auth()->check() && auth()->user()->canAccessWhatsApp())
    <li class="nav-item has-submenu">
        <a href="#">WhatsApp Gateway</a>
        {{-- Submenu items --}}
    </li>
@endif

{{-- Settings & User Management - Hanya Administrator --}}
@if(auth()->check() && auth()->user()->isAdministrator())
    <li class="nav-item">
        <a href="{{ route('settings.index') }}">Pengaturan Sistem</a>
    </li>
    <li class="nav-item">
        <a href="{{ route('users.index') }}">Manajemen User</a>
    </li>
@endif
```

### 4. User Management Controller (`app/Http/Controllers/UserManagementController.php`)
Menambahkan `admin_wa` ke roles array:

```php
$roles = ['administrator', 'panitia', 'admin_wa'];

// Validation rules
'role' => 'required|in:administrator,panitia,admin_wa',
```

### 5. User Forms
**Create Form** (`resources/views/users/create.blade.php`):
```blade
<option value="administrator">🛡️ Administrator - Akses penuh</option>
<option value="admin_wa">📱 Admin WhatsApp - Dashboard & WA Gateway</option>
<option value="panitia">👔 Panitia - Akses terbatas</option>
```

**Edit Form** (`resources/views/users/edit.blade.php`):
Same as create form.

**Index View** (`resources/views/users/index.blade.php`):
```blade
@if($user->role === 'administrator')
    <span class="badge bg-danger">
        <i class="fas fa-user-shield me-1"></i>Administrator
    </span>
@elseif($user->role === 'admin_wa')
    <span class="badge bg-success">
        <i class="fab fa-whatsapp me-1"></i>Admin WA
    </span>
@else
    <span class="badge bg-info">
        <i class="fas fa-user-tie me-1"></i>Panitia
    </span>
@endif
```

## Role Comparison

| Feature | Administrator | Panitia | Admin WA |
|---------|--------------|---------|----------|
| Dashboard | ✅ | ✅ | ✅ |
| Data Pendaftar | ✅ | ✅ | ❌ |
| Verifikasi Daftar Ulang | ✅ | ✅ | ❌ |
| Laporan & Cetak | ✅ | ✅ | ❌ |
| WhatsApp Gateway | ✅ | ❌ | ✅ |
| Pengaturan Sistem | ✅ | ❌ | ❌ |
| Manajemen User | ✅ | ❌ | ❌ |

## Usage

### Membuat User Admin WA Baru

1. Login sebagai **Administrator**
2. Buka **Manajemen User**
3. Klik **Tambah Pengguna**
4. Isi form:
   - Nama: `Admin WhatsApp`
   - Email: `adminwa@example.com`
   - Password: `********`
   - **Role: Admin WhatsApp - Dashboard & WA Gateway**
   - Status: Aktif
5. Klik **Simpan Pengguna**

### Login sebagai Admin WA

1. Login dengan email dan password Admin WA
2. Setelah login, sidebar hanya menampilkan:
   - 🏠 Dashboard
   - 📱 WhatsApp Gateway (dengan 7 submenu)
   - 🚪 Logout

### Testing Access Control

**Test 1: Akses Dashboard** ✅
```
GET /dashboard
Expected: 200 OK
```

**Test 2: Akses WhatsApp Gateway** ✅
```
GET /whatsapp
Expected: 200 OK
```

**Test 3: Akses Data Pendaftar** ❌
```
GET /pendaftar
Expected: 403 Forbidden
```

**Test 4: Akses Laporan** ❌
```
GET /laporan
Expected: 403 Forbidden
```

**Test 5: Akses Settings** ❌
```
GET /settings
Expected: 403 Forbidden
```

**Test 6: Akses User Management** ❌
```
GET /users
Expected: 403 Forbidden
```

## Security Notes

1. **Middleware Protection**: Semua routes dilindungi dengan `checkRole` middleware
2. **Sidebar Visibility**: Menu yang tidak boleh diakses tidak ditampilkan di sidebar
3. **Direct URL Access**: Jika Admin WA mencoba akses URL langsung (e.g., `/pendaftar`), akan mendapat error 403 Forbidden
4. **Role Validation**: Role divalidasi di controller level dengan `in:administrator,panitia,admin_wa`

## Database

Tidak perlu migration baru. Role `admin_wa` disimpan di kolom `role` yang sudah ada di tabel `users`.

```sql
-- Example user dengan role admin_wa
INSERT INTO users (name, email, password, role, status, email_verified_at, created_at, updated_at)
VALUES ('Admin WhatsApp', 'adminwa@example.com', '$2y$12$...', 'admin_wa', 'aktif', NOW(), NOW(), NOW());
```

## Files Modified

1. `app/Models/User.php` - Added `isAdminWA()` and `canAccessWhatsApp()` methods
2. `routes/web.php` - Added role-based middleware groups
3. `resources/views/partials/admin-sidebar.blade.php` - Added conditional menu visibility
4. `app/Http/Controllers/UserManagementController.php` - Added `admin_wa` to roles array and validation
5. `resources/views/users/create.blade.php` - Added admin_wa option
6. `resources/views/users/edit.blade.php` - Added admin_wa option
7. `resources/views/users/index.blade.php` - Added admin_wa badge display

## Date
May 31, 2026
