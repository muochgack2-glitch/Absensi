<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExternalBroadcastBatch extends Model
{
    protected $fillable = [
        'batch_name',
        'description',
        'total_recipients',
        'total_sent',
        'total_failed',
        'status',
        'source_type',
        'source_file',
        'created_by',
        'completed_at'
    ];

    protected $casts = [
        'total_recipients' => 'integer',
        'total_sent' => 'integer',
        'total_failed' => 'integer',
        'completed_at' => 'datetime',
    ];

    /**
     * Get all recipients for this batch
     */
    public function recipients()
    {
        return $this->hasMany(ExternalBroadcastRecipient::class, 'batch_id');
    }

    /**
     * Get all WhatsApp logs for this batch
     */
    public function logs()
    {
        return $this->hasMany(WhatsAppLog::class, 'external_batch_id');
    }

    /**
     * Get the user who created this batch
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Mark batch as in progress
     */
    public function markAsInProgress()
    {
        $this->update(['status' => 'in_progress']);
    }

    /**
     * Mark batch as completed
     */
    public function markAsCompleted()
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now()
        ]);
    }

    /**
     * Mark batch as failed
     */
    public function markAsFailed()
    {
        $this->update([
            'status' => 'failed',
            'completed_at' => now()
        ]);
    }

    /**
     * Increment sent counter
     */
    public function incrementSent()
    {
        $this->increment('total_sent');
    }

    /**
     * Increment failed counter
     */
    public function incrementFailed()
    {
        $this->increment('total_failed');
    }

    /**
     * Get success rate percentage
     */
    public function getSuccessRateAttribute()
    {
        if ($this->total_recipients == 0) {
            return 0;
        }
        return round(($this->total_sent / $this->total_recipients) * 100, 1);
    }

    /**
     * Get status badge class for UI
     */
    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'pending' => 'bg-secondary',
            'in_progress' => 'bg-primary',
            'completed' => 'bg-success',
            'failed' => 'bg-danger',
            default => 'bg-secondary'
        };
    }
}
