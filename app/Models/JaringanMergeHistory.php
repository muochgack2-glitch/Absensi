<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JaringanMergeHistory extends Model
{
    use HasFactory;
    
    protected $table = 'jaringan_merge_history';
    
    protected $fillable = [
        'merge_type',
        'from_jaringan',
        'to_jaringan',
        'affected_count',
        'pendaftar_ids',
        'merged_by',
        'merged_by_name',
        'merged_by_role',
        'is_undone',
        'undone_at',
        'undone_by',
        'undone_by_name',
        'undone_by_role',
    ];
    
    protected $casts = [
        'pendaftar_ids' => 'array',
        'is_undone' => 'boolean',
        'undone_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    public function mergedByUser()
    {
        return $this->belongsTo(User::class, 'merged_by');
    }
    
    public function undoneByUser()
    {
        return $this->belongsTo(User::class, 'undone_by');
    }
}
