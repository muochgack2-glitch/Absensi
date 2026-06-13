<?php

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
     * Format: SPMB-{YEAR}-{NUMBER:4}
     * Example: SPMB-2026-0001, SPMB-2026-0002
     * 
     * IMPORTANT: Menggunakan database locking untuk prevent race condition
     */
    public function generateNomorRegistrasi($tahunAjaran)
    {
        return DB::transaction(function () use ($tahunAjaran) {
            $ta = TahunAjaran::where('tahun', $tahunAjaran)
                ->lockForUpdate()
                ->first();
            
            if (!$ta) {
                throw new \Exception("Tahun ajaran {$tahunAjaran} tidak ditemukan");
            }
            
            // Increment counter
            $ta->increment('reg_number_current');
            $nextNumber = $ta->reg_number_current;
            
            // Get year (ambil tahun pertama dari "2026/2027")
            $year = $this->extractYear($tahunAjaran);
            
            // Format: SPMB-2026-0001
            return sprintf('SPMB-%s-%04d', $year, $nextNumber);
        });
    }
    
    /**
     * Extract year dari format "2026/2027" -> "2026" (tahun pertama)
     */
    private function extractYear($tahunAjaran)
    {
        $parts = explode('/', $tahunAjaran);
        $year = $parts[0]; // Ambil tahun pertama
        
        // Handle 2-digit year: 26 -> 2026
        if (strlen($year) == 2) {
            $year = '20' . $year;
        }
        
        return $year;
    }
    
    /**
     * Get active tahun ajaran
     */
    public function getActiveTahunAjaran()
    {
        $activeTahun = SettingSystem::get('active_tahun_ajaran', '2026/2027');
        return TahunAjaran::where('tahun', $activeTahun)->first();
    }
    
    /**
     * Create tahun ajaran baru dengan wizard
     * 
     * Options:
     * - archive_current: Archive current active year (default: true)
     * - clone_config: Clone settings from current year (default: true)
     * - auto_backup: Create database backup (default: true)
     * - started_at: Custom start date (default: now)
     * - closed_at: Custom end date (default: null)
     */
    public function createNewYear($tahunBaru, $options = [])
    {
        // Default options
        $options = array_merge([
            'archive_current' => true,
            'clone_config' => true,
            'auto_backup' => true,
            'started_at' => now(),
            'closed_at' => null,
        ], $options);
        
        DB::beginTransaction();
        try {
            // 1. Validate format
            if (!preg_match('/^\d{4}\/\d{4}$/', $tahunBaru)) {
                throw new \Exception('Format tahun ajaran harus YYYY/YYYY (contoh: 2027/2028)');
            }
            
            // 2. Validate year sequence
            $parts = explode('/', $tahunBaru);
            if ((int)$parts[1] !== (int)$parts[0] + 1) {
                throw new \Exception('Tahun kedua harus berurutan setelah tahun pertama (contoh: 2027/2028)');
            }
            
            // 3. Check if already exists
            if (TahunAjaran::where('tahun', $tahunBaru)->exists()) {
                throw new \Exception("Tahun ajaran {$tahunBaru} sudah ada");
            }
            
            // 4. Auto backup
            $backupPath = null;
            if ($options['auto_backup']) {
                try {
                    $backupPath = $this->autoBackup();
                } catch (\Exception $e) {
                    Log::warning('Backup failed but continuing', ['error' => $e->getMessage()]);
                }
            }
            
            // 5. Get current active year
            $currentYear = SettingSystem::get('active_tahun_ajaran', '2026/2027');
            $currentTA = TahunAjaran::where('tahun', $currentYear)->first();
            
            // 6. Archive current year if requested
            if ($options['archive_current'] && $currentTA) {
                $currentTA->archive();
                
                Log::info('Archived previous academic year', [
                    'tahun' => $currentYear,
                    'total_pendaftar' => $currentTA->total_pendaftar,
                ]);
            }
            
            // 7. Create new tahun ajaran
            $newTA = TahunAjaran::create([
                'tahun' => $tahunBaru,
                'status' => 'active',
                'reg_number_pattern' => '{YEAR}-{NUMBER:3}',
                'reg_number_current' => 0, // Reset counter
                'total_pendaftar' => 0,
                'total_diterima' => 0,
                'total_belum_daftar_ulang' => 0,
                'created_by' => auth()->id(),
                'started_at' => $options['started_at'],
                'closed_at' => $options['closed_at'],
            ]);
            
            // 8. Update active year setting
            SettingSystem::set('active_tahun_ajaran', $tahunBaru);
            SettingSystem::clearCache();
            
            // 9. Clone config if requested
            if ($options['clone_config'] && $currentTA) {
                $this->cloneConfigFrom($currentYear, $tahunBaru);
            }
            
            DB::commit();
            
            // 10. Log activity
            Log::info('New academic year created', [
                'tahun' => $tahunBaru,
                'user' => auth()->user()->name ?? 'System',
                'backup' => $backupPath,
                'archived_previous' => $options['archive_current'],
            ]);
            
            return [
                'success' => true,
                'tahun_ajaran' => $newTA,
                'backup_path' => $backupPath,
                'message' => "✅ Tahun ajaran {$tahunBaru} berhasil dibuat!\n\nNomor registrasi akan dimulai dari SPMB-{$this->extractYear($tahunBaru)}-0001",
            ];
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Failed to create new academic year', [
                'tahun' => $tahunBaru,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return [
                'success' => false,
                'message' => 'Gagal membuat tahun ajaran: ' . $e->getMessage(),
            ];
        }
    }
    
    /**
     * Auto backup database sebelum create new year
     */
    private function autoBackup()
    {
        $timestamp = now()->format('Y-m-d_His');
        $filename = "backup_before_new_year_{$timestamp}.sql";
        $backupDir = storage_path('app/backups');
        $path = "{$backupDir}/{$filename}";
        
        // Create backups directory if not exists
        if (!file_exists($backupDir)) {
            mkdir($backupDir, 0755, true);
        }
        
        // Database config
        $host = config('database.connections.mysql.host');
        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');
        
        // mysqldump command (Windows compatible)
        $command = sprintf(
            'mysqldump --host=%s --user=%s --password=%s %s > "%s" 2>&1',
            escapeshellarg($host),
            escapeshellarg($username),
            escapeshellarg($password),
            escapeshellarg($database),
            $path
        );
        
        // Execute
        exec($command, $output, $returnCode);
        
        if ($returnCode !== 0) {
            throw new \Exception('Backup database gagal: ' . implode("\n", $output));
        }
        
        // Verify backup file exists and not empty
        if (!file_exists($path) || filesize($path) < 1000) {
            throw new \Exception('Backup file tidak valid atau kosong');
        }
        
        // Cleanup old backups (keep last 10)
        $this->cleanupOldBackups($backupDir, 10);
        
        return $path;
    }
    
    /**
     * Cleanup old backups, keep only N latest
     */
    private function cleanupOldBackups($backupDir, $keepCount = 10)
    {
        $files = glob($backupDir . '/backup_before_new_year_*.sql');
        
        if (count($files) <= $keepCount) {
            return;
        }
        
        // Sort by modification time, oldest first
        usort($files, function($a, $b) {
            return filemtime($a) - filemtime($b);
        });
        
        // Delete oldest files
        $toDelete = array_slice($files, 0, count($files) - $keepCount);
        foreach ($toDelete as $file) {
            @unlink($file);
        }
    }
    
    /**
     * Clone config dari tahun lalu ke tahun baru
     */
    private function cloneConfigFrom($fromYear, $toYear)
    {
        // TODO: Implement config cloning
        // Things to clone:
        // - WhatsApp message templates (update placeholders with new year)
        // - Jurusan settings (if any year-specific settings)
        // - Payment amounts (if stored per year)
        // - Gelombang configuration
        
        Log::info('Config cloning placeholder', [
            'from' => $fromYear,
            'to' => $toYear,
            'note' => 'Config cloning belum diimplementasi, skip',
        ]);
        
        return true;
    }
    
    /**
     * Get statistics untuk tahun tertentu atau semua tahun
     */
    public function getStatistics($tahunAjaran = null)
    {
        if ($tahunAjaran && $tahunAjaran !== 'all') {
            $query = Pendaftar::where('tahun_ajaran', $tahunAjaran);
            
            return [
                'total_pendaftar' => $query->count(),
                'diterima' => (clone $query)->where('status_siswa', 'Diterima')->count(),
                'belum_daftar_ulang' => (clone $query)->where('status_siswa', 'Belum Daftar Ulang')->count(),
                'pending' => (clone $query)->where('status_siswa', 'Pending')->count(),
                'ditolak' => (clone $query)->where('status_siswa', 'Ditolak')->count(),
            ];
        }
        
        // All time stats
        return [
            'total_pendaftar' => Pendaftar::count(),
            'diterima' => Pendaftar::where('status_siswa', 'Diterima')->count(),
            'belum_daftar_ulang' => Pendaftar::where('status_siswa', 'Belum Daftar Ulang')->count(),
            'pending' => Pendaftar::where('status_siswa', 'Pending')->count(),
            'ditolak' => Pendaftar::where('status_siswa', 'Ditolak')->count(),
        ];
    }
    
    /**
     * Get list tahun ajaran untuk dropdown
     */
    public function getTahunAjaranList()
    {
        return TahunAjaran::orderBy('tahun', 'desc')->get();
    }
    
    /**
     * Sync statistics untuk semua tahun ajaran
     */
    public function syncAllStatistics()
    {
        $updated = 0;
        
        foreach (TahunAjaran::all() as $ta) {
            $ta->updateStatistics();
            $updated++;
        }
        
        return $updated;
    }
}
