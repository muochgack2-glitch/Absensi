<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WhatsAppLog extends Model
{
    use HasFactory;

    protected $table = 'whatsapp_logs';

    protected $fillable = [
        'phone',
        'message',
        'status',
        'type',
        'pendaftar_id',
        'template_id',
        'sent_by',
        'external_batch_id',
        'error_message',
        'sent_at',
        'metadata',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'metadata' => 'array',
    ];

    /**
     * Relasi ke Pendaftar
     */
    public function pendaftar(): BelongsTo
    {
        return $this->belongsTo(Pendaftar::class, 'pendaftar_id', 'id_pendaftar');
    }

    /**
     * Relasi ke WhatsApp Template
     */
    public function template(): BelongsTo
    {
        return $this->belongsTo(WhatsAppTemplate::class, 'template_id');
    }

    /**
     * Relasi ke User (yang mengirim)
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sent_by');
    }

    /**
     * Relasi ke External Broadcast Batch
     */
    public function externalBatch(): BelongsTo
    {
        return $this->belongsTo(ExternalBroadcastBatch::class, 'external_batch_id');
    }

    /**
     * Scope untuk filter berdasarkan status
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope untuk filter berdasarkan type
     */
    public function scopeType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope untuk pesan yang berhasil terkirim
     */
    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    /**
     * Scope untuk pesan yang gagal
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope untuk pesan pending
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope untuk pesan hari ini
     */
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    /**
     * Mark message as sent
     */
    public function markAsSent($metadata = null)
    {
        $this->update([
            'status' => 'sent',
            'sent_at' => now(),
            'metadata' => $metadata,
        ]);
    }

    /**
     * Mark message as failed
     */
    public function markAsFailed($errorMessage, $metadata = null)
    {
        $this->update([
            'status' => 'failed',
            'error_message' => $errorMessage,
            'metadata' => $metadata,
        ]);
    }

    /**
     * Get formatted phone number
     */
    public function getFormattedPhoneAttribute()
    {
        return $this->phone;
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'sent' => 'success',
            'failed' => 'danger',
            'pending' => 'warning',
            default => 'secondary',
        };
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'sent' => 'Terkirim',
            'failed' => 'Gagal',
            'pending' => 'Pending',
            default => 'Unknown',
        };
    }

    /**
     * Get type label
     */
    public function getTypeLabelAttribute()
    {
        return match($this->type) {
            'manual' => 'Manual',
            'auto_registration' => 'Auto Registrasi',
            'broadcast' => 'Broadcast',
            'external_broadcast' => 'Broadcast Eksternal',
            'reminder' => 'Pengingat',
            default => ucfirst($this->type),
        };
    }
}
