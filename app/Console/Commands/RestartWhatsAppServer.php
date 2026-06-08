<?php

namespace App\Console\Commands;

use App\Services\WhatsAppService;
use Illuminate\Console\Command;

class RestartWhatsAppServer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wa:restart {--force : Force restart without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Restart WhatsApp Gateway server';

    /**
     * Execute the console command.
     */
    public function handle(WhatsAppService $whatsappService)
    {
        $this->info('🔄 WhatsApp Gateway Server Restart');
        $this->info('================================');
        $this->newLine();

        // Check server status first
        $this->info('Checking server status...');
        $status = $whatsappService->getStatus();
        
        if (!$status['success']) {
            $this->error('❌ Server not responding!');
            $this->error('   Make sure Node.js server is running.');
            return Command::FAILURE;
        }

        $currentStatus = $status['data']['status'] ?? 'unknown';
        $this->info("   Current status: {$currentStatus}");
        $this->newLine();

        // Confirmation
        if (!$this->option('force')) {
            if (!$this->confirm('Are you sure you want to restart the server?')) {
                $this->info('❌ Restart cancelled.');
                return Command::SUCCESS;
            }
        }

        // Restart server
        $this->info('🔄 Restarting server...');
        $result = $whatsappService->restart();

        if ($result['success']) {
            $this->info('✅ ' . $result['message']);
            $this->info('⏳ Waiting 10 seconds for server to restart...');
            
            // Wait for restart
            sleep(10);
            
            // Check status after restart
            $this->info('🔍 Checking server status...');
            $newStatus = $whatsappService->getStatus();
            
            if ($newStatus['success']) {
                $status = $newStatus['data']['status'] ?? 'unknown';
                $this->info("   New status: {$status}");
                
                if ($status === 'connected') {
                    $this->info('✅ Server restarted and reconnected successfully!');
                    return Command::SUCCESS;
                } else {
                    $this->warn('⚠️  Server restarted but not connected yet.');
                    $this->info('   Status: ' . $status);
                    return Command::SUCCESS;
                }
            } else {
                $this->error('❌ Failed to check status after restart.');
                return Command::FAILURE;
            }
        } else {
            $this->error('❌ Failed to restart server');
            $this->error('   ' . $result['message']);
            return Command::FAILURE;
        }
    }
}
