<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\WhatsAppSetting;

class WhatsAppGatewayController extends Controller
{
    /**
     * Get gateway configurations (dynamic from database)
     */
    protected function getGateways()
    {
        return [
            'spmb' => [
                'name' => 'Gateway SPMB',
                'url' => WhatsAppSetting::get('wa_server_url', 'http://localhost:3000'),
                'purpose' => 'Primary - SPMB System',
            ],
            'absensi' => [
                'name' => 'Gateway Absensi',
                'url' => WhatsAppSetting::get('wa_server_url_backup', 'http://localhost:3001'),
                'purpose' => 'Backup SPMB / Future: Absensi System',
            ],
        ];
    }

    /**
     * Show gateway management dashboard
     */
    public function index()
    {
        $gateways = $this->getGateways();
        $statuses = [];
        
        foreach ($gateways as $key => $gateway) {
            try {
                $response = Http::timeout(5)->get("{$gateway['url']}/status");
                $health = Http::timeout(5)->get("{$gateway['url']}/health");
                
                $statuses[$key] = [
                    'info' => $gateway,
                    'status' => $response->successful() ? $response->json() : null,
                    'health' => $health->successful() ? $health->json() : null,
                    'online' => $response->successful(),
                ];
            } catch (\Exception $e) {
                $statuses[$key] = [
                    'info' => $gateway,
                    'status' => null,
                    'health' => null,
                    'online' => false,
                    'error' => $e->getMessage(),
                ];
            }
        }
        
        // Failover settings
        $failoverSettings = [
            'enabled' => WhatsAppSetting::get('wa_failover_enabled', false),
            'timeout' => WhatsAppSetting::get('wa_failover_timeout', 5),
        ];
        
        return view('admin.gateway.index', compact('statuses', 'failoverSettings'));
    }

    /**
     * Get QR code from gateway
     */
    public function getQRCode($gateway)
    {
        $gateways = $this->getGateways();
        $url = $gateways[$gateway]['url'] ?? null;
        if (!$url) {
            return response()->json([
                'success' => false,
                'message' => 'Gateway not found',
            ], 404);
        }
        
        try {
            $response = Http::timeout(10)->get("{$url}/qr");
            
            if ($response->successful() && $response->json('success')) {
                return response()->json([
                    'success' => true,
                    'qr' => $response->json('qr'),
                    'message' => $response->json('message'),
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => $response->json('message', 'QR not available'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch QR: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Restart gateway
     */
    public function restart($gateway)
    {
        $gateways = $this->getGateways();
        $url = $gateways[$gateway]['url'] ?? null;
        if (!$url) {
            return response()->json([
                'success' => false,
                'message' => 'Gateway not found',
            ], 404);
        }
        
        try {
            $response = Http::timeout(10)->post("{$url}/restart");
            
            Log::info('Gateway restart requested', [
                'gateway' => $gateway,
                'url' => $url,
                'user' => auth()->user()->name ?? 'Unknown',
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Gateway is restarting... Please wait 5-10 seconds.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to restart: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Logout from gateway
     */
    public function logout($gateway)
    {
        $gateways = $this->getGateways();
        $url = $gateways[$gateway]['url'] ?? null;
        if (!$url) {
            return response()->json([
                'success' => false,
                'message' => 'Gateway not found',
            ], 404);
        }
        
        try {
            $response = Http::timeout(10)->post("{$url}/logout");
            
            Log::info('Gateway logout requested', [
                'gateway' => $gateway,
                'url' => $url,
                'user' => auth()->user()->name ?? 'Unknown',
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Logged out successfully. New QR will be generated...',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to logout: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get gateway logs
     */
    public function getLogs($gateway)
    {
        $processName = $gateway === 'spmb' ? 'wa-gateway-spmb' : 'wa-gateway-absensi';
        
        try {
            // Try to get PM2 logs (this works if PM2 is accessible)
            $logs = shell_exec("pm2 logs {$processName} --lines 50 --nostream 2>&1");
            
            if (empty($logs)) {
                $logs = "Unable to fetch logs. PM2 might not be accessible or process not found.";
            }
            
            return response()->json([
                'success' => true,
                'logs' => $logs,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch logs: ' . $e->getMessage(),
                'logs' => "Error: " . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Toggle failover setting
     */
    public function toggleFailover(Request $request)
    {
        try {
            $enabled = $request->input('enabled', false);
            
            WhatsAppSetting::set('wa_failover_enabled', $enabled);
            
            // Clear cache
            WhatsAppSetting::clearCache();
            
            Log::info('Failover setting changed', [
                'enabled' => $enabled,
                'user' => auth()->user()->name ?? 'Unknown',
            ]);
            
            return response()->json([
                'success' => true,
                'message' => $enabled ? 'Auto failover enabled' : 'Auto failover disabled',
                'enabled' => $enabled,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to toggle failover: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Reset & Reconnect gateway (Hard reset with PM2 restart + session cleanup)
     */
    public function resetGateway($gateway)
    {
        $processName = $gateway === 'spmb' ? 'wa-gateway-spmb' : 'wa-gateway-absensi';
        $sessionName = $gateway === 'spmb' ? 'spmb-wa-session' : 'absensi-wa-session';
        $gateways = $this->getGateways();
        $gatewayInfo = $gateways[$gateway] ?? null;
        
        if (!$gatewayInfo) {
            return response()->json([
                'success' => false,
                'message' => 'Gateway not found',
            ], 404);
        }
        
        try {
            Log::info('Gateway hard reset requested', [
                'gateway' => $gateway,
                'process' => $processName,
                'user' => auth()->user()->name ?? 'Unknown',
            ]);
            
            // Step 1: Logout via API first (to clean state)
            try {
                Http::timeout(5)->post("{$gatewayInfo['url']}/logout");
            } catch (\Exception $e) {
                Log::warning('Logout API call failed (continuing with reset)', [
                    'error' => $e->getMessage()
                ]);
            }
            
            // Step 2: Restart PM2 process
            $restartOutput = shell_exec("pm2 restart {$processName} 2>&1");
            
            Log::info('PM2 restart executed', [
                'process' => $processName,
                'output' => $restartOutput,
            ]);
            
            // Step 3: Wait a bit for process to restart
            sleep(2);
            
            // Step 4: Delete session folder via API endpoint
            // The gateway server will handle session cleanup on next connection attempt
            
            return response()->json([
                'success' => true,
                'message' => 'Gateway reset successfully. Please wait 10-15 seconds for reconnection, then click "Lihat QR" to get new QR code.',
                'process' => $processName,
            ]);
            
        } catch (\Exception $e) {
            Log::error('Gateway reset failed', [
                'gateway' => $gateway,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to reset gateway: ' . $e->getMessage(),
            ], 500);
        }
    }
}
