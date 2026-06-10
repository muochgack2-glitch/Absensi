<?php

namespace App\Console\Commands;

use App\Models\WhatsAppLog;
use App\Models\Pendaftar;
use Illuminate\Console\Command;

class UpdateWhatsAppLogPendaftarId extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'whatsapp:update-log-pendaftar-id {--dry-run : Run without updating database}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update pendaftar_id for WhatsApp logs that are NULL by matching phone numbers';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        
        if ($isDryRun) {
            $this->info('🔍 Running in DRY-RUN mode (no changes will be made)');
            $this->newLine();
        }

        $this->info('📊 Fetching WhatsApp logs with NULL pendaftar_id...');
        
        $logs = WhatsAppLog::whereNull('pendaftar_id')
            ->orderBy('created_at', 'desc')
            ->get();
        
        if ($logs->isEmpty()) {
            $this->info('✅ No logs found with NULL pendaftar_id. All good!');
            return 0;
        }

        $this->info("Found {$logs->count()} logs to process");
        $this->newLine();

        $updated = 0;
        $notFound = 0;
        $bar = $this->output->createProgressBar($logs->count());
        $bar->start();

        foreach ($logs as $log) {
            // Cari pendaftar berdasarkan nomor HP
            $pendaftar = Pendaftar::where(function($query) use ($log) {
                $query->where('no_hp_wali', $log->phone)
                      ->orWhere('no_hp_ortu', $log->phone)
                      ->orWhere('no_telepon', $log->phone);
            })->first();
            
            if ($pendaftar) {
                if (!$isDryRun) {
                    $log->update(['pendaftar_id' => $pendaftar->id_pendaftar]);
                }
                $updated++;
            } else {
                $notFound++;
            }
            
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        // Summary
        $this->info('📈 Summary:');
        $this->table(
            ['Status', 'Count'],
            [
                ['Total Processed', $logs->count()],
                ['✅ Matched & Updated', $updated],
                ['❌ No Match Found', $notFound],
            ]
        );

        if ($isDryRun) {
            $this->newLine();
            $this->warn('⚠️  This was a DRY-RUN. No changes were made to the database.');
            $this->info('💡 Run without --dry-run to apply changes: php artisan whatsapp:update-log-pendaftar-id');
        } else {
            $this->newLine();
            $this->info('✅ Database updated successfully!');
            
            if ($notFound > 0) {
                $this->newLine();
                $this->warn("⚠️  {$notFound} logs could not be matched (custom numbers or deleted pendaftar)");
            }
        }

        return 0;
    }
}
