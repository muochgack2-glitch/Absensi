<?php

namespace App\Console\Commands;

use App\Models\Admin;
use App\Models\User;
use App\Notifications\WhatsAppStatusChanged;
use App\Services\WhatsAppService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class MonitorWhatsAppStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wa:monitor {--verbose : Show detailed output}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Monitor WhatsApp Gateway status and send notifications on changes';

    /**
     * Execute the console command.
     */
    public function handle(WhatsAppService $whatsappService)
    {
        if ($this->option('verbose')) {
            $this->info('🔍 Monitoring WhatsApp Gateway status...');
        }

        // Get current status from server
        $response = $whatsappService->getStatus();
        
        if (!$response['success']) {
            if ($this->option('verbose')) {
                $this->error('❌ Failed to get status from server');
            }
            Log::warning('WhatsApp status monitor: Failed to get server status');
            return Command::FAILURE;
        }

        $currentStatus = $response['data']['status'] ?? 'unknown';
        
        // Get previous status from cache
        $previousStatus = Cache::get('wa_gateway_previous_status', 'unknown');

        if ($this->option('verbose')) {
            $this->info("   Previous: {$previousStatus}");
            $this->info("   Current:  {$currentStatus}");
        }

        // Check if status changed
        if ($previousStatus !== $currentStatus && $previousStatus !== 'unknown') {
            $this->info("🔔 Status changed: {$previousStatus} → {$currentStatus}");
            
            // Send notifications to administrators
            $this->sendNotifications($previousStatus, $currentStatus);
            
            // Log the change
            Log::info('WhatsApp status changed', [
                'old_status' => $previousStatus,
                'new_status' => $currentStatus,
                'timestamp' => now()->toDateTimeString(),
            ]);
        } else {
            if ($this->option('verbose')) {
                $this->info('✅ No status change detected');
            }
        }

        // Update cache with current status
        Cache::put('wa_gateway_previous_status', $currentStatus, 3600); // 1 hour

        return Command::SUCCESS;
    }

    /**
     * Send notifications to administrators
     */
    protected function sendNotifications(string $oldStatus, string $newStatus): void
    {
        $this->info('📧 Sending notifications...');

        // Get all administrators
        $admins = Admin::where('role', 'administrator')
            ->orWhere('role', 'admin_wa')
            ->get();

        $sentCount = 0;
        foreach ($admins as $admin) {
            try {
                $admin->notify(new WhatsAppStatusChanged($oldStatus, $newStatus));
                $sentCount++;
            } catch (\Exception $e) {
                Log::error('Failed to send notification to admin', [
                    'admin_id' => $admin->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->info("   ✅ Sent {$sentCount} notification(s)");
    }
}
