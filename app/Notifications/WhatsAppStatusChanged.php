<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppStatusChanged extends Notification
{
    use Queueable;

    protected $oldStatus;
    protected $newStatus;
    protected $timestamp;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $oldStatus, string $newStatus)
    {
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
        $this->timestamp = now();
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        // Send directly without using Laravel channels
        $this->sendToTelegram();
        return [];
    }

    /**
     * Send Telegram notification directly
     */
    protected function sendToTelegram(): bool
    {
        $token = config('services.telegram.bot_token');
        $chatId = config('services.telegram.chat_id');

        if (!$token || !$chatId) {
            Log::warning('Telegram bot token or chat ID not configured');
            return false;
        }

        $message = $this->buildTelegramMessage();
        $keyboard = $this->buildInlineKeyboard();

        try {
            $response = Http::post("https://api.telegram.org/bot{$token}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'Markdown',
                'reply_markup' => json_encode(['inline_keyboard' => $keyboard]),
                'disable_web_page_preview' => true,
            ]);

            if ($response->successful()) {
                Log::info('Telegram notification sent successfully', [
                    'old_status' => $this->oldStatus,
                    'new_status' => $this->newStatus,
                ]);
                return true;
            } else {
                Log::error('Failed to send Telegram notification', [
                    'response' => $response->json(),
                ]);
                return false;
            }
        } catch (\Exception $e) {
            Log::error('Telegram notification error', [
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Build Telegram message with Markdown formatting
     */
    protected function buildTelegramMessage(): string
    {
        $isDisconnected = $this->newStatus === 'disconnected';
        $isReconnected = $this->oldStatus === 'disconnected' && $this->newStatus === 'connected';

        // Header with emoji
        if ($isDisconnected) {
            $header = "🚨 *WA Gateway Disconnected!*";
        } elseif ($isReconnected) {
            $header = "✅ *WA Gateway Reconnected!*";
        } else {
            $header = "ℹ️ *WA Gateway Status Changed*";
        }

        // Status change
        $statusLine = "📊 *Status Change:*\n";
        $statusLine .= "• Previous: `{$this->formatStatus($this->oldStatus)}` {$this->getStatusEmoji($this->oldStatus)}\n";
        $statusLine .= "• Current: `{$this->formatStatus($this->newStatus)}` {$this->getStatusEmoji($this->newStatus)}";

        // Timestamp
        $timeLine = "\n⏰ *Time:* " . $this->timestamp->format('d M Y H:i:s');

        // Action message
        if ($isDisconnected) {
            $actionLine = "\n\n⚠️ _Action Required: Check the dashboard immediately!_";
        } elseif ($isReconnected) {
            $actionLine = "\n\n✨ _Server has automatically reconnected._";
        } else {
            $actionLine = "";
        }

        // Footer
        $footer = "\n\n📱 *SPMB WhatsApp Gateway*";

        return $header . "\n\n" . $statusLine . $timeLine . $actionLine . $footer;
    }

    /**
     * Build inline keyboard with action buttons
     */
    protected function buildInlineKeyboard(): array
    {
        $dashboardUrl = url('/whatsapp');
        $isDisconnected = $this->newStatus === 'disconnected';

        $keyboard = [];

        // Row 1: Dashboard button (always show)
        $keyboard[] = [
            [
                'text' => '📊 View Dashboard',
                'url' => $dashboardUrl,
            ],
        ];

        // Row 2: Action buttons (conditional)
        if ($isDisconnected) {
            // Show restart and reset buttons when disconnected
            $keyboard[] = [
                [
                    'text' => '🔄 Restart Server',
                    'callback_data' => 'restart_server',
                ],
                [
                    'text' => '🔌 Reset & Reconnect',
                    'callback_data' => 'reset_connection',
                ],
            ];
        }

        // Row 3: Check status button
        $keyboard[] = [
            [
                'text' => '🔍 Check Status',
                'callback_data' => 'check_status',
            ],
        ];

        return $keyboard;
    }

    /**
     * Get status change message
     */
    protected function getStatusMessage(): string
    {
        if ($this->newStatus === 'disconnected') {
            return '⚠️ WhatsApp Gateway has been disconnected!';
        }

        if ($this->oldStatus === 'disconnected' && $this->newStatus === 'connected') {
            return '✅ WhatsApp Gateway has reconnected successfully!';
        }

        return "WhatsApp Gateway status changed from {$this->formatStatus($this->oldStatus)} to {$this->formatStatus($this->newStatus)}.";
    }

    /**
     * Format status for display
     */
    protected function formatStatus(string $status): string
    {
        return match($status) {
            'connected' => 'Connected',
            'disconnected' => 'Disconnected',
            'qr' => 'Waiting QR Scan',
            default => ucfirst($status),
        };
    }

    /**
     * Get emoji for status
     */
    protected function getStatusEmoji(string $status): string
    {
        return match($status) {
            'connected' => '✅',
            'disconnected' => '❌',
            'qr' => '📱',
            default => 'ℹ️',
        };
    }

    /**
     * Get the array representation of the notification (for database)
     */
    public function toArray(object $notifiable): array
    {
        return [
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'message' => $this->getStatusMessage(),
            'timestamp' => $this->timestamp->toDateTimeString(),
        ];
    }
}

