# Implementation Guide: Phone List with Message Tracking Tabs

## 🎯 Status: IN PROGRESS - Backend 50% Complete

## ✅ COMPLETED:

### 1. Controller Update (`WhatsAppController.php`)
- ✅ Updated `phoneList()` method with tab filtering
- ✅ Added tab persistence via session
- ✅ Added message status tracking
- ✅ Added WhatsAppLog relationship loading

## ⏳ REMAINING:

### 2. Add Helper Methods to WhatsAppController

Add these methods at the end of `WhatsAppController.php` (before the closing `}`):

```php
    /**
     * Get message status for a pendaftar
     */
    private function getMessageStatus(Pendaftar $pendaftar): array
    {
        $totalMessages = $pendaftar->whatsappLogs->count();
        $sentMessages = $pendaftar->whatsappLogs->where('status', 'sent')->count();
        $failedMessages = $pendaftar->whatsappLogs->where('status', 'failed')->count();
        $pendingMessages = $pendaftar->whatsappLogs->where('status', 'pending')->count();
        
        // Get last message
        $lastMessage = $pendaftar->whatsappLogs->sortByDesc('created_at')->first();
        
        // Determine status
        if ($totalMessages == 0) {
            $status = 'not-sent';
            $label = 'Belum Dikirim';
            $badge = 'secondary';
            $icon = '🔵';
        } elseif ($lastMessage && $lastMessage->status == 'failed') {
            $status = 'failed';
            $label = 'Gagal';
            $badge = 'danger';
            $icon = '❌';
        } elseif ($sent Messages > 0) {
            $status = 'sent';
            $label = "Terkirim ({$sentMessages}x)";
            $badge = 'success';
            $icon = '✅';
        } elseif ($pendingMessages > 0) {
            $status = 'pending';
            $label = "Pending ({$pendingMessages}x)";
            $badge = 'warning';
            $icon = '⏳';
        } else {
            $status = 'unknown';
            $label = 'Unknown';
            $badge = 'secondary';
            $icon = '❓';
        }
        
        return [
            'status' => $status,
            'label' => $label,
            'badge' => $badge,
            'icon' => $icon,
            'total' => $totalMessages,
            'sent' => $sentMessages,
            'failed' => $failedMessages,
            'pending' => $pendingMessages,
            'last_message' => $lastMessage ? [
                'date' => $lastMessage->created_at->format('d M Y, H:i'),
                'template' => $lastMessage->template->label ?? 'Manual',
                'status' => $lastMessage->status
            ] : null
        ];
    }
    
    /**
     * Get message statistics
     */
    private function getMessageStatistics(): array
    {
        $totalSent = WhatsAppLog::where('status', 'sent')->count();
        $totalFailed = WhatsAppLog::where('status', 'failed')->count();
        $totalPending = WhatsAppLog::where('status', 'pending')->count();
        $total = WhatsAppLog::count();
        
        $successRate = $total > 0 ? round(($totalSent / $total) * 100, 1) : 0;
        
        // Today's messages
        $todaySent = WhatsAppLog::where('status', 'sent')
            ->whereDate('created_at', today())
            ->count();
        
        return [
            'total_sent' => $totalSent,
            'total_failed' => $totalFailed,
            'total_pending' => $totalPending,
            'success_rate' => $successRate,
            'today_sent' => $todaySent
        ];
    }
    
    /**
     * Get tab counts
     */
    private function getTabCounts(): array
    {
        $total = Pendaftar::count();
        
        $sent = Pendaftar::whereHas('whatsappLogs', function($q) {
            $q->where('status', 'sent');
        })->count();
        
        $notSent = Pendaftar::whereDoesntHave('whatsappLogs')->count();
        
        $failed = Pendaftar::whereHas('whatsappLogs', function($q) {
            $q->where('status', 'failed')
              ->whereRaw('id = (SELECT MAX(id) FROM whatsapp_logs WHERE pendaftar_id = pendaftars.id_pendaftar)');
        })->count();
        
        $noPhone = Pendaftar::where(function($q) {
            $q->whereNull('no_hp_wali')
              ->whereNull('no_hp_ortu')
              ->whereNull('no_telepon');
        })->orWhere(function($q) {
            $q->where('no_hp_wali', '')
              ->where('no_hp_ortu', '')
              ->where('no_telepon', '');
        })->count();
        
        return [
            'all' => $total,
            'sent' => $sent,
            'not-sent' => $notSent,
            'failed' => $failed,
            'no-phone' => $noPhone
        ];
    }
```

### 3. Update Pendaftar Model

Add relationship to `whatsappLogs` in `app/Models/Pendaftar.php`:

```php
public function whatsappLogs()
{
    return $this->hasMany(WhatsAppLog::class, 'pendaftar_id', 'id_pendaftar');
}
```

### 4. Update WhatsAppLog Model

Add relationship back to Pendaftar in `app/Models/WhatsAppLog.php`:

```php
public function pendaftar()
{
    return $this->belongsTo(Pendaftar::class, 'pendaftar_id', 'id_pendaftar');
}
```

### 5. Update View (MAJOR TASK)

Update `resources/views/whatsapp/phone-list.blade.php`:

#### A. Add Tab Navigation (After page header, before filters)

```blade
<!-- Message Status Tabs -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-0">
        <ul class="nav nav-tabs nav-fill border-0" role="tablist" style="background: var(--bg-secondary);">
            <li class="nav-item">
                <a class="nav-link {{ $activeTab == 'all' ? 'active' : '' }}" 
                   href="{{ route('whatsapp.phone-list', array_merge(request()->except('tab'), ['tab' => 'all'])) }}">
                    📱 Semua
                    <span class="badge bg-primary ms-2">{{ $tabCounts['all'] }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $activeTab == 'sent' ? 'active' : '' }}" 
                   href="{{ route('whatsapp.phone-list', array_merge(request()->except('tab'), ['tab' => 'sent'])) }}">
                    ✅ Terkirim
                    <span class="badge bg-success ms-2">{{ $tabCounts['sent'] }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $activeTab == 'not-sent' ? 'active' : '' }}" 
                   href="{{ route('whatsapp.phone-list', array_merge(request()->except('tab'), ['tab' => 'not-sent'])) }}">
                    🔵 Belum Dikirim
                    <span class="badge bg-secondary ms-2">{{ $tabCounts['not-sent'] }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $activeTab == 'failed' ? 'active' : '' }}" 
                   href="{{ route('whatsapp.phone-list', array_merge(request()->except('tab'), ['tab' => 'failed'])) }}">
                    ❌ Gagal
                    <span class="badge bg-danger ms-2">{{ $tabCounts['failed'] }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $activeTab == 'no-phone' ? 'active' : '' }}" 
                   href="{{ route('whatsapp.phone-list', array_merge(request()->except('tab'), ['tab' => 'no-phone'])) }}">
                    📵 Tidak Ada Nomor
                    <span class="badge bg-dark ms-2">{{ $tabCounts['no-phone'] }}</span>
                </a>
            </li>
        </ul>
    </div>
</div>
```

#### B. Add Message Statistics Cards (After existing statistics, before table)

```blade
<!-- Message Statistics -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="rounded-circle bg-success bg-opacity-10 p-3">
                            <i class="fas fa-check-circle fa-2x text-success"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Sudah Terkirim</h6>
                        <h3 class="mb-0">{{ $messageStats['total_sent'] }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="rounded-circle bg-danger bg-opacity-10 p-3">
                            <i class="fas fa-times-circle fa-2x text-danger"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Gagal Terkirim</h6>
                        <h3 class="mb-0">{{ $messageStats['total_failed'] }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="rounded-circle bg-info bg-opacity-10 p-3">
                            <i class="fas fa-percentage fa-2x text-info"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Success Rate</h6>
                        <h3 class="mb-0">{{ $messageStats['success_rate'] }}%</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="rounded-circle bg-primary bg-opacity-10 p-3">
                            <i class="fas fa-calendar-day fa-2x text-primary"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Hari Ini</h6>
                        <h3 class="mb-0">{{ $messageStats['today_sent'] }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
```

#### C. Add Status Pesan Column in Table

Add after "Tipe" column header:
```blade
<th>Status Pesan</th>
```

Add after "Tipe" column data:
```blade
<td>
    @php $msgStatus = $pendaftar->message_status; @endphp
    <span class="badge bg-{{ $msgStatus['badge'] }}" title="{{ $msgStatus['last_message']['date'] ?? '' }}">
        {{ $msgStatus['icon'] }} {{ $msgStatus['label'] }}
    </span>
</td>
```

## 🎯 NEXT STEPS:

1. ✅ Add helper methods to WhatsAppController
2. ✅ Update Pendaftar model relationship
3. ✅ Update WhatsAppLog model relationship  
4. ✅ Update view with tabs and new column
5. ✅ Test all tabs
6. ✅ Test message status display
7. ✅ Commit and push

## ⏱️ ESTIMATED TIME: 30-45 minutes

## 📝 FILES TO MODIFY:

- `app/Http/Controllers/WhatsAppController.php` - Add 3 helper methods
- `app/Models/Pendaftar.php` - Add whatsappLogs relationship
- `app/Models/WhatsAppLog.php` - Add pendaftar relationship  
- `resources/views/whatsapp/phone-list.blade.php` - Add tabs, stats, column

Mau saya lanjutkan implement atau mau break dulu?
