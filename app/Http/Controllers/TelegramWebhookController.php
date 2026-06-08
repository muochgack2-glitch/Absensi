<?php

namespace App\Http\Controllers;

use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramWebhookController extends Controller
{
    protected WhatsAppService $whatsappService;

    public function __construct(WhatsAppService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
    }

    /**
     * Handle Telegram webhook (button callbacks)
     */
    public function handle(Request $request)
    {
        // DEBUG: Log webhook received
        Log::info('=== TELEGRAM WEBHOOK RECEIVED ===', [
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'headers' => $request->headers->all(),
            'body' => $request->all(),
        ]);
        
        $token = config('services.telegram.bot_token');
        
        if (!$token) {
            return response()->json(['error' => 'Bot token not configured'], 500);
        }

        // Get callback query from Telegram
        $callbackQuery = $request->input('callback_query');
        
        if (!$callbackQuery) {
            return response()->json(['ok' => true]);
        }

        $chatId = $callbackQuery['message']['chat']['id'];
        $messageId = $callbackQuery['message']['message_id'];
        $callbackData = $callbackQuery['callback_data'];
        $callbackId = $callbackQuery['id'];

        Log::info('Telegram callback received', [
            'callback_data' => $callbackData,
            'chat_id' => $chatId,
        ]);

        // Answer callback query first (remove loading state)
        $this->answerCallbackQuery($callbackId, 'Processing...');

        // Handle different actions
        $response = match($callbackData) {
            'restart_server' => $this->handleRestartServer($chatId, $messageId),
            'reset_connection' => $this->handleResetConnection($chatId, $messageId),
            'check_status' => $this->handleCheckStatus($chatId, $messageId),
            default => ['text' => '❓ Unknown action'],
        };

        // Send response message
        $this->sendMessage($chatId, $response['text'], $response['reply_to'] ?? $messageId);

        return response()->json(['ok' => true]);
    }

    /**
     * Handle restart server action
     */
    protected function handleRestartServer(string $chatId, int $messageId): array
    {
        $result = $this->whatsappService->restart();

        if ($result['success']) {
            return [
                'text' => "🔄 *Server Restarting...*\n\n" .
                         "✅ Restart command sent successfully.\n" .
                         "⏳ Please wait 10 seconds for server to reconnect.\n\n" .
                         "_Check status in a moment._",
                'reply_to' => $messageId,
            ];
        } else {
            return [
                'text' => "❌ *Restart Failed*\n\n" .
                         "Error: " . ($result['message'] ?? 'Unknown error') . "\n\n" .
                         "_Please check the dashboard or contact admin._",
                'reply_to' => $messageId,
            ];
        }
    }

    /**
     * Handle reset connection action
     */
    protected function handleResetConnection(string $chatId, int $messageId): array
    {
        $result = $this->whatsappService->logout();

        if ($result['success']) {
            return [
                'text' => "🔌 *Connection Reset Initiated*\n\n" .
                         "✅ Logout successful.\n" .
                         "🔄 Generating new QR code...\n\n" .
                         "⚠️ _You need to scan QR code again._\n" .
                         "📱 Please check the dashboard.",
                'reply_to' => $messageId,
            ];
        } else {
            return [
                'text' => "❌ *Reset Failed*\n\n" .
                         "Error: " . ($result['message'] ?? 'Unknown error') . "\n\n" .
                         "_Please check the dashboard or contact admin._",
                'reply_to' => $messageId,
            ];
        }
    }

    /**
     * Handle check status action
     */
    protected function handleCheckStatus(string $chatId, int $messageId): array
    {
        $status = $this->whatsappService->getStatus();
        $health = $this->whatsappService->getHealth();

        if (!$status['success']) {
            return [
                'text' => "❌ *Server Not Responding*\n\n" .
                         "Cannot connect to WhatsApp Gateway server.\n" .
                         "_Please check if Node.js server is running._",
                'reply_to' => $messageId,
            ];
        }

        $currentStatus = $status['data']['status'] ?? 'unknown';
        $qrAvailable = $status['data']['qrAvailable'] ? 'Yes' : 'No';
        $reconnectAttempts = $status['data']['reconnectAttempts'] ?? 0;

        // Get health metrics if available
        $healthInfo = "";
        if ($health['success'] && isset($health['data'])) {
            $uptime = $this->formatUptime($health['data']['uptime']);
            $memoryPercent = $health['data']['memory']['percentage'] ?? 0;
            $memoryUsed = $health['data']['memory']['heapUsed'] ?? 0;
            $memoryTotal = $health['data']['memory']['heapTotal'] ?? 0;

            $healthInfo = "\n📊 *Server Health:*\n" .
                         "• Uptime: `{$uptime}`\n" .
                         "• Memory: `{$memoryUsed}/{$memoryTotal} MB ({$memoryPercent}%)`\n";
        }

        $statusEmoji = match($currentStatus) {
            'connected' => '✅',
            'disconnected' => '❌',
            'qr' => '📱',
            default => 'ℹ️',
        };

        return [
            'text' => "🔍 *Current Status*\n\n" .
                     "📊 *Connection:* `{$currentStatus}` {$statusEmoji}\n" .
                     "🔌 *QR Available:* {$qrAvailable}\n" .
                     "🔄 *Reconnect Attempts:* {$reconnectAttempts}\n" .
                     $healthInfo .
                     "\n⏰ *Checked:* " . now()->format('H:i:s'),
            'reply_to' => $messageId,
        ];
    }

    /**
     * Format uptime seconds to readable string
     */
    protected function formatUptime(int $seconds): string
    {
        $days = floor($seconds / 86400);
        $hours = floor(($seconds % 86400) / 3600);
        $minutes = floor(($seconds % 3600) / 60);

        if ($days > 0) {
            return "{$days}d {$hours}h {$minutes}m";
        } elseif ($hours > 0) {
            return "{$hours}h {$minutes}m";
        } else {
            return "{$minutes}m";
        }
    }

    /**
     * Answer callback query to remove loading state
     */
    protected function answerCallbackQuery(string $callbackId, string $text = ''): void
    {
        $token = config('services.telegram.bot_token');
        
        Http::post("https://api.telegram.org/bot{$token}/answerCallbackQuery", [
            'callback_query_id' => $callbackId,
            'text' => $text,
        ]);
    }

    /**
     * Send message to Telegram
     */
    protected function sendMessage(string $chatId, string $text, ?int $replyTo = null): void
    {
        $token = config('services.telegram.bot_token');
        
        $payload = [
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'Markdown',
        ];

        if ($replyTo) {
            $payload['reply_to_message_id'] = $replyTo;
        }

        Http::post("https://api.telegram.org/bot{$token}/sendMessage", $payload);
    }
}
