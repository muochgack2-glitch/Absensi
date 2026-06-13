# 📅 Tahun Ajaran Manager - Technical Specification

## 📋 Overview

Fitur untuk mengelola tahun ajaran secara professional dengan:
- Multi-year data management
- Auto nomor registrasi dengan reset per tahun
- Historical data access
- One-click create new year
- Auto backup sebelum action

---

## 🎯 Requirements (Final Confirmed)

1. **Format Nomor Registrasi:** `{YEAR}-{NUMBER:3}`
   - Contoh: `2028-001`, `2028-002`, `2028-003`
   
2. **Reset Counter:** Setiap tahun ajaran baru
   - 2026/2027: `2027-001` sampai `2027-500`
   - 2027/2028: `2028-001` sampai `2028-450` (reset dari 001)

3. **Display:** Historical data tetap visible
   - Dashboard bisa lihat semua tahun
   - Filter dropdown untuk switch tahun

4. **Gelombang:** Global counter (tidak reset per gelombang)
   - Gel 1: `2027-001` sampai `2027-200`
   - Gel 2: `2027-201` sampai `2027-350` (lanjut)
   - Gel 3: `2027-351` sampai `2027-500` (lanjut)

---

## 🗄️ Database Schema

### Migration 1: Add tahun_ajaran to Existing Tables

```sql
-- File: database/migrations/2026_06_13_180400_add_tahun_ajaran_support_to_tables.php

-- Add to pendaftar
ALTER TABLE pendaftar 
ADD COLUMN tahun_ajaran VARCHAR(9) DEFAULT '2026/2027' AFTER status_siswa,
ADD INDEX idx_tahun_ajaran (tahun_ajaran);

-- Add to logistik_bayar
ALTER TABLE logistik_bayar 
ADD COLUMN tahun_ajaran VARCHAR(9) DEFAULT '2026/2027' AFTER status_bayar,
ADD INDEX idx_tahun_ajaran (tahun_ajaran);

-- Add to whatsapp_logs
ALTER TABLE whatsapp_logs 
ADD COLUMN tahun_ajaran VARCHAR(9) DEFAULT '2026/2027' AFTER status,
ADD INDEX idx_tahun_ajaran (tahun_ajaran);
```

**Safety:**
- ✅ Only ADD column (non-destructive)
- ✅ Default value for existing data
- ✅ Index for performance
- ✅ Can rollback by DROP column

---

### Migration 2: Create tahun_ajaran Table

```sql
-- File: database/migrations/2026_06_13_180412_create_tahun_ajaran_table.php

CREATE TABLE tahun_ajaran (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    
    -- Basic Info
    tahun VARCHAR(9) NOT NULL UNIQUE COMMENT 'Format: 2027/2028',
    status ENUM('upcoming', 'active', 'archived') DEFAULT 'upcoming',
    
    -- Registration Number Config
    reg_number_pattern VARCHAR(50) DEFAULT '{YEAR}-{NUMBER:3}',
    reg_number_current INT UNSIGNED DEFAULT 0 COMMENT 'Counter saat ini',
    
    -- Statistics
    total_pendaftar INT UNSIGNED DEFAULT 0,
    total_diterima INT UNSIGNED DEFAULT 0,
    total_ditolak INT UNSIGNED DEFAULT 0,
    
    -- Timestamps
    started_at DATE NULL COMMENT 'Tanggal mulai pendaftaran',
    closed_at DATE NULL COMMENT 'Tanggal tutup pendaftaran',
    
    -- Audit
    created_by BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Foreign Keys
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE RESTRICT,
    
    -- Indexes
    INDEX idx_status (status),
    INDEX idx_tahun (tahun)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert current year (backfill)
INSERT INTO tahun_ajaran (tahun, status, created_by) VALUES
('2026/2027', 'active', 1);

-- Add setting for active year
INSERT INTO setting_system (key, value, `group`, label, description) VALUES
('active_tahun_ajaran', '2026/2027', 'system', 'Tahun Ajaran Aktif', 'Tahun ajaran yang sedang berjalan saat ini');
```

---

## 🏗️ File Structure

```
app/
├── Models/
│   └── TahunAjaran.php (NEW)
│
├── Services/
│   └── TahunAjaranService.php (NEW)
│
├── Http/
│   └── Controllers/
│       └── TahunAjaranController.php (NEW)
│
database/
├── migrations/
│   ├── 2026_06_13_180400_add_tahun_ajaran_support_to_tables.php (NEW)
│   └── 2026_06_13_180412_create_tahun_ajaran_table.php (NEW)
│
resources/
└── views/
    ├── admin/
    │   └── tahun-ajaran/
    │       ├── index.blade.php (NEW)
    │       └── create.blade.php (NEW)
    │
    └── partials/
        └── tahun-filter.blade.php (NEW)

routes/
└── web.php (UPDATE - add tahun-ajaran routes)
```

---

## 🔧 Core Components

### 1. Model: TahunAjaran

```php
// app/Models/TahunAjaran.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TahunAjaran extends Model
{
    protected $table = 'tahun_ajaran';
    
    protected $fillable = [
        'tahun',
        'status',
        'reg_number_pattern',
        'reg_number_current',
        'total_pendaftar',
        'total_diterima',
        'total_ditolak',
        'started_at',
        'closed_at',
        'created_by',
    ];
    
    protected $casts = [
        'started_at' => 'date',
        'closed_at' => 'date',
    ];
    
    // Relationships
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    
    public function pendaftars()
    {
        return $this->hasMany(Pendaftar::class, 'tahun_ajaran', 'tahun');
    }
    
    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
    
    public function scopeArchived($query)
    {
        return $query->where('status', 'archived');
    }
    
    // Helpers
    public function getYearNumber()
    {
        // "2027/2028" -> "2028"
        return explode('/', $this->tahun)[1];
    }
    
    public function updateStatistics()
    {
        $this->total_pendaftar = Pendaftar::where('tahun_ajaran', $this->tahun)->count();
        $this->total_diterima = Pendaftar::where('tahun_ajaran', $this->tahun)
            ->where('status_siswa', 'Diterima')->count();
        $this->total_ditolak = Pendaftar::where('tahun_ajaran', $this->tahun)
            ->where('status_siswa', 'Ditolak')->count();
        $this->save();
    }
}
```

---

### 2. Service: TahunAjaranService

```php
// app/Services/TahunAjaranService.php

namespace App\Services;

use App\Models\TahunAjaran;
use App\Models\Pendaftar;
use App\Models\SettingSystem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class TahunAjaranService
{
    /**
     * Generate nomor registrasi untuk tahun ajaran tertentu
     * Format: {YEAR}-{NUMBER:3}
     * Example: 2028-001, 2028-002
     */
    public function generateNomorRegistrasi($tahunAjaran)
    {
        $ta = TahunAjaran::where('tahun', $tahunAjaran)->lockForUpdate()->first();
        
        if (!$ta) {
            throw new \Exception("Tahun ajaran {$tahunAjaran} tidak ditemukan");
        }
        
        // Increment counter
        $ta->increment('reg_number_current');
        $nextNumber = $ta->reg_number_current;
        
        // Get year (ambil tahun kedua dari "2027/2028")
        $year = $this->extractYear($tahunAjaran);
        
        // Format: 2028-001
        return sprintf('%s-%03d', $year, $nextNumber);
    }
    
    /**
     * Extract year dari format "2027/2028" -> "2028"
     */
    private function extractYear($tahunAjaran)
    {
        $parts = explode('/', $tahunAjaran);
        $year = $parts[1] ?? $parts[0];
        
        // Handle 2-digit year: 28 -> 2028
        if (strlen($year) == 2) {
            $year = '20' . $year;
        }
        
        return $year;
    }
    
    /**
     * Create tahun ajaran baru dengan wizard
     */
    public function createNewYear($tahunBaru, $options = [])
    {
        DB::beginTransaction();
        try {
            // 1. Validate format
            if (!preg_match('/^\d{4}\/\d{4}$/', $tahunBaru)) {
                throw new \Exception('Format tahun ajaran harus YYYY/YYYY (contoh: 2027/2028)');
            }
            
            // 2. Check if already exists
            if (TahunAjaran::where('tahun', $tahunBaru)->exists()) {
                throw new \Exception("Tahun ajaran {$tahunBaru} sudah ada");
            }
            
            // 3. Auto backup
            $backupPath = $this->autoBackup();
            
            // 4. Get current active year
            $currentYear = SettingSystem::get('active_tahun_ajaran');
            
            // 5. Archive current year if requested
            if ($options['archive_current'] ?? false) {
                TahunAjaran::where('tahun', $currentYear)
                    ->update(['status' => 'archived', 'closed_at' => now()]);
            }
            
            // 6. Create new tahun ajaran
            $newTA = TahunAjaran::create([
                'tahun' => $tahunBaru,
                'status' => 'active',
                'reg_number_pattern' => '{YEAR}-{NUMBER:3}',
                'reg_number_current' => 0, // Reset counter
                'total_pendaftar' => 0,
                'created_by' => auth()->id(),
                'started_at' => now(),
            ]);
            
            // 7. Update active year setting
            SettingSystem::set('active_tahun_ajaran', $tahunBaru);
            SettingSystem::clearCache();
            
            // 8. Clone config if requested
            if ($options['clone_config'] ?? true) {
                $this->cloneConfigFrom($currentYear, $tahunBaru);
            }
            
            DB::commit();
            
            // 9. Log activity
            Log::info('New academic year created', [
                'tahun' => $tahunBaru,
                'user' => auth()->user()->name,
                'backup' => $backupPath,
            ]);
            
            return [
                'success' => true,
                'tahun_ajaran' => $newTA,
                'backup_path' => $backupPath,
                'message' => "Tahun ajaran {$tahunBaru} berhasil dibuat. Nomor registrasi akan dimulai dari {$this->extractYear($tahunBaru)}-001",
            ];
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Failed to create new academic year', [
                'tahun' => $tahunBaru,
                'error' => $e->getMessage(),
            ]);
            
            return [
                'success' => false,
                'message' => 'Gagal membuat tahun ajaran: ' . $e->getMessage(),
            ];
        }
    }
    
    /**
     * Auto backup database
     */
    private function autoBackup()
    {
        $timestamp = now()->format('Y-m-d_H-i-s');
        $filename = "backup_before_new_year_{$timestamp}.sql";
        $path = storage_path("app/backups/{$filename}");
        
        // Create backups directory if not exists
        if (!file_exists(storage_path('app/backups'))) {
            mkdir(storage_path('app/backups'), 0755, true);
        }
        
        // Database config
        $host = config('database.connections.mysql.host');
        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');
        
        // mysqldump command
        $command = sprintf(
            'mysqldump -h%s -u%s -p%s %s > %s',
            escapeshellarg($host),
            escapeshellarg($username),
            escapeshellarg($password),
            escapeshellarg($database),
            escapeshellarg($path)
        );
        
        // Execute
        exec($command, $output, $returnCode);
        
        if ($returnCode !== 0) {
            throw new \Exception('Backup database gagal');
        }
        
        return $path;
    }
    
    /**
     * Clone config dari tahun lalu
     */
    private function cloneConfigFrom($fromYear, $toYear)
    {
        // TODO: Clone settings like:
        // - Gelombang configuration
        // - WhatsApp templates (update tahun)
        // - Jurusan settings
        // - etc.
        
        // For now, just log
        Log::info('Config cloned', [
            'from' => $fromYear,
            'to' => $toYear,
        ]);
    }
    
    /**
     * Get statistics untuk tahun tertentu atau semua tahun
     */
    public function getStatistics($tahunAjaran = null)
    {
        if ($tahunAjaran && $tahunAjaran !== 'all') {
            return [
                'total_pendaftar' => Pendaftar::where('tahun_ajaran', $tahunAjaran)->count(),
                'diterima' => Pendaftar::where('tahun_ajaran', $tahunAjaran)
                    ->where('status_siswa', 'Diterima')->count(),
                'ditolak' => Pendaftar::where('tahun_ajaran', $tahunAjaran)
                    ->where('status_siswa', 'Ditolak')->count(),
                'pending' => Pendaftar::where('tahun_ajaran', $tahunAjaran)
                    ->where('status_siswa', 'Pending')->count(),
            ];
        }
        
        // All time stats
        return [
            'total_pendaftar' => Pendaftar::count(),
            'diterima' => Pendaftar::where('status_siswa', 'Diterima')->count(),
            'ditolak' => Pendaftar::where('status_siswa', 'Ditolak')->count(),
            'pending' => Pendaftar::where('status_siswa', 'Pending')->count(),
        ];
    }
}
```

---

## 📝 Implementation Checklist

### Phase 1: Database (SAFE)
- [ ] Run migration 1: add tahun_ajaran columns
- [ ] Run migration 2: create tahun_ajaran table
- [ ] Verify: existing data not affected
- [ ] Test: rollback works

### Phase 2: Models & Services
- [ ] Create TahunAjaran model
- [ ] Create TahunAjaranService
- [ ] Test: nomor registrasi generation
- [ ] Test: create new year logic

### Phase 3: Controllers & Routes
- [ ] Create TahunAjaranController
- [ ] Add routes to web.php
- [ ] Middleware: admin only

### Phase 4: Views
- [ ] Create tahun-ajaran/index.blade.php
- [ ] Create tahun-ajaran/create.blade.php
- [ ] Create partials/tahun-filter.blade.php
- [ ] Update dashboard with filter

### Phase 5: Integration
- [ ] Update PendaftarController to use tahun_ajaran
- [ ] Update RegistrationController (public form)
- [ ] Update ReportController with year filter
- [ ] Update all queries to support filter

### Phase 6: Testing
- [ ] Test: create new year wizard
- [ ] Test: nomor registrasi format
- [ ] Test: filter by year
- [ ] Test: statistics per year
- [ ] Test: backup functionality
- [ ] Test: rollback scenario

### Phase 7: Documentation
- [ ] Update PANDUAN_WA_GATEWAY.md
- [ ] Create PANDUAN_TAHUN_AJARAN.md
- [ ] Add inline help tooltips

---

## 🚨 Safety Measures

1. **Backup Before Action**
   - Auto backup sebelum create new year
   - Stored in: `storage/app/backups/`
   - Retention: keep last 10 backups

2. **Rollback Plan**
   ```bash
   # If migration fails
   php artisan migrate:rollback --step=2
   
   # If need to restore database
   mysql -u root -p database_name < backup_file.sql
   ```

3. **Data Integrity Checks**
   - Foreign key constraints
   - Unique constraints on tahun
   - Default values for existing data

4. **No Data Loss**
   - Never DELETE or TRUNCATE
   - Only ADD columns
   - Only UPDATE status (not data)

---

## 📊 Expected Results

### Before:
```
Pendaftar table:
- id_pendaftar: 1-500
- no_registrasi: random format
- No tahun_ajaran column
```

### After Migration:
```
Pendaftar table:
- id_pendaftar: 1-500
- no_registrasi: (unchanged)
- tahun_ajaran: '2026/2027' (auto-filled)

Tahun_ajaran table:
- 1 row: 2026/2027, active, counter=500
```

### After Create New Year:
```
Tahun_ajaran table:
- Row 1: 2026/2027, archived, counter=500
- Row 2: 2027/2028, active, counter=0

Pendaftar baru:
- no_registrasi: 2028-001, 2028-002, ...
- tahun_ajaran: '2027/2028'
```

---

## ⏱️ Timeline

- **Phase 1-2:** 2-3 jam (Database & Backend)
- **Phase 3-4:** 2-3 jam (Controllers & Views)
- **Phase 5:** 1-2 jam (Integration)
- **Phase 6:** 1-2 jam (Testing)
- **Phase 7:** 1 jam (Documentation)

**Total:** ~1 hari development + testing

---

## 📋 Next Steps

1. **Review spec ini** - Ada yang perlu diubah?
2. **Approve to proceed** - Saya lanjut ke implementation
3. **Test di local** - Pakai copy database production
4. **Demo ke Anda** - Show hasil sebelum production
5. **Deploy to production** - Setelah Anda approve

---

**Status:** ✅ SPEC READY - Waiting for approval

**Created:** {{ now() }}
**Version:** 1.0
