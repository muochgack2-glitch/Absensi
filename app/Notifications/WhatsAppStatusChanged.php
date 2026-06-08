<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WhatsAppStatusChanged extends Notification implements ShouldQueue
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
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $isDisconnected = $this->newStatus === 'disconnected';
        $isReconnected = $this->oldStatus === 'disconnected' && $this->newStatus === 'connected';

        return (new MailMessage)
            ->subject($isDisconnected ? '⚠️ WA Gateway Disconnected' : '✅ WA Gateway Status Changed')
            ->greeting('Hello!')
            ->line($this->getStatusMessage())
            ->line('Status Details:')
            ->line("• Previous Status: **{$this->formatStatus($this->oldStatus)}**")
            ->line("• Current Status: **{$this->formatStatus($this->newStatus)}**")
            ->line("• Timestamp: {$this->timestamp->format('d M Y H:i:s')}")
            ->when($isDisconnected, function ($mail) {
                return $mail
                    ->line('⚠️ **Action Required:** Please check the WhatsApp Gateway dashboard.')
                    ->action('Open Dashboard', url('/whatsapp'));
            })
            ->when($isReconnected, function ($mail) {
                return $mail
                    ->line('✅ The server has automatically reconnected.')
                    ->action('View Dashboard', url('/whatsapp'));
            })
            ->line('Thank you for using ' . config('app.name'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
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
            'connected' => 'Connected ✅',
            'disconnected' => 'Disconnected ❌',
            'qr' => 'Waiting QR Scan 📱',
            default => ucfirst($status),
        };
    }
}
