# Technical Design Document

## Overview

The External Broadcast feature extends the SPMB WhatsApp Gateway to support broadcasting messages to external recipients (alumni, manual lists, CSV uploads) while maintaining integration with the existing system architecture.

## Architecture

### High-Level Components

```
┌─────────────────────────────────────────────────────────────┐
│                      Broadcast UI (Blade)                   │
│  ┌──────────────┐              ┌──────────────────────┐    │
│  │  Data SPMB   │              │  Data Eksternal      │    │
│  │     Tab      │              │      Tab             │    │
│  └──────────────┘              └──────────────────────┘    │
│                                  ├─ CSV Upload              │
│                                  └─ Manual Input            │
└───────────────────────┬─────────────────────────────────────┘
                        │
                        ▼
┌─────────────────────────────────────────────────────────────┐
│             WhatsAppController                              │
│  ┌──────────────────────────────────────────────────────┐  │
│  │  broadcastExternalPage()                             │  │
│  │  parseExternalRecipients()                           │  │
│  │  sendExternalBroadcast()                             │  │
│  └──────────────────────────────────────────────────────┘  │
└───────────┬─────────────────────────────────────────────────┘
            │
            ▼
┌─────────────────────────────────────────────────────────────┐
│           ExternalBroadcastService                          │
│  ┌──────────────────────────────────────────────────────┐  │
│  │  createBatch()                                       │  │
│  │  parseCSV()                                          │  │
│  │  parseManualInput()                                  │  │
│  │  detectDuplicates()                                  │  │
│  │  normalizePhone()                                    │  │
│  └──────────────────────────────────────────────────────┘  │
└───────────┬─────────────────────────────────────────────────┘
            │
            ▼
┌─────────────────────────────────────────────────────────────┐
│          WhatsAppService (Existing)                         │
│  Reused for message sending                                 │
└───────────┬─────────────────────────────────────────────────┘
            │
            ▼
┌─────────────────────────────────────────────────────────────┐
│                    Database Layer                           │
│  ┌────────────────┐  ┌─────────────────────────────────┐   │
│  │ whatsapp_logs  │  │ external_broadcast_batches      │   │
│  │ (modified)     │  │ (new)                           │   │
│  └────────────────┘  └─────────────────────────────────┘   │
│  ┌─────────────────────────────────────────────────────┐   │
│  │ external_broadcast_recipients (new)                 │   │
│  └─────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────┘
```

## Database Schema

### Modified Table: `whatsapp_logs`

```sql
ALTER TABLE whatsapp_logs ADD COLUMN external_batch_id BIGINT UNSIGNED NULL;
ALTER TABLE whatsapp_logs ADD INDEX idx_external_batch_id (external_batch_id);
ALTER TABLE whatsapp_logs ADD CONSTRAINT fk_external_batch 
    FOREIGN KEY (external_batch_id) 
    REFERENCES external_broadcast_batches(id) 
    ON DELETE SET NULL;
```

### New Table: `external_broadcast_batches`

```sql
CREATE TABLE external_broadcast_batches (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    batch_name VARCHAR(255) NOT NULL,
    description TEXT NULL,
    total_recipients INT UNSIGNED NOT NULL DEFAULT 0,
    total_sent INT UNSIGNED NOT NULL DEFAULT 0,
    total_failed INT UNSIGNED NOT NULL DEFAULT 0,
    status ENUM('pending', 'in_progress', 'completed', 'failed') NOT NULL DEFAULT 'pending',
    source_type ENUM('csv', 'manual') NOT NULL,
    source_file VARCHAR(255) NULL,
    created_by BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    completed_at TIMESTAMP NULL,
    INDEX idx_created_by (created_by),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
);
```

### New Table: `external_broadcast_recipients`

```sql
CREATE TABLE external_broadcast_recipients (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    batch_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    phone_normalized VARCHAR(20) NOT NULL,
    notes TEXT NULL,
    is_duplicate_spmb BOOLEAN NOT NULL DEFAULT FALSE,
    matched_pendaftar_id BIGINT UNSIGNED NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (batch_id) REFERENCES external_broadcast_batches(id) ON DELETE CASCADE,
    INDEX idx_phone_normalized (phone_normalized),
    INDEX idx_batch_id (batch_id),
    INDEX idx_is_duplicate (is_duplicate_spmb)
);
```

## API Endpoints

### 1. External Broadcast Page
- **Route**: `GET /whatsapp/broadcast/external`
- **Controller**: `WhatsAppController@externalBroadcastPage`
- **Returns**: Blade view with templates

### 2. Parse and Preview Recipients
- **Route**: `POST /whatsapp/broadcast/external/parse`
- **Controller**: `WhatsAppController@parseExternalRecipients`
- **Request**:
```json
{
  "source_type": "csv|manual",
  "csv_file": "(file upload)",
  "manual_input": "string",
  "batch_name": "string"
}
```
- **Response**:
```json
{
  "success": true,
  "batch_id": 123,
  "recipients": [...],
  "preview": [...],
  "duplicates_count": 5,
  "total_count": 100
}
```

### 3. Send External Broadcast
- **Route**: `POST /whatsapp/broadcast/external/send`
- **Controller**: `WhatsAppController@sendExternalBroadcast`
- **Request**:
```json
{
  "batch_id": 123,
  "message": "string",
  "template_id": 1
}
```
- **Response**:
```json
{
  "success": true,
  "message": "Broadcast started",
  "batch_id": 123,
  "total": 100
}
```

### 4. Phone List External Tab
- **Route**: `GET /whatsapp/phone-list?tab=external`
- **Controller**: `WhatsAppController@phoneList`
- **Returns**: Paginated external recipients with duplicate badges

### 5. External Recipient Messages
- **Route**: `GET /whatsapp/external/{id}/messages`
- **Controller**: `WhatsAppController@getExternalMessages`
- **Returns**: Message history for external recipient

## Core Algorithms

### Phone Number Normalization

```php
class ExternalBroadcastService
{
    public function normalizePhone(string $phone): ?string
    {
        // Remove non-numeric characters
        $cleaned = preg_replace('/[^0-9]/', '', $phone);
        
        // Convert to 62xxx format
        if (substr($cleaned, 0, 1) === '0') {
            $cleaned = '62' . substr($cleaned, 1);
        } elseif (substr($cleaned, 0, 1) === '8') {
            $cleaned = '62' . $cleaned;
        } elseif (!str_starts_with($cleaned, '62')) {
            return null; // Invalid format
        }
        
        // Validate length
        if (strlen($cleaned) < 10 || strlen($cleaned) > 15) {
            return null;
        }
        
        return $cleaned;
    }
}
```

### Duplicate Detection

```php
public function detectDuplicates(array $recipients): array
{
    $normalizedPhones = array_map(function($r) {
        return $this->normalizePhone($r['phone']);
    }, $recipients);
    
    // Batch query to SPMB database
    $duplicates = DB::table('pendaftar')
        ->whereIn('no_hp_wali', $normalizedPhones)
        ->orWhereIn('no_hp_ortu', $normalizedPhones)
        ->orWhereIn('no_telepon', $normalizedPhones)
        ->select('id_pendaftar', 'no_hp_wali', 'no_hp_ortu', 'no_telepon')
        ->get();
    
    // Map duplicates to recipients
    foreach ($recipients as &$recipient) {
        $normalized = $this->normalizePhone($recipient['phone']);
        foreach ($duplicates as $dup) {
            if ($dup->no_hp_wali === $normalized || 
                $dup->no_hp_ortu === $normalized || 
                $dup->no_telepon === $normalized) {
                $recipient['is_duplicate'] = true;
                $recipient['matched_pendaftar_id'] = $dup->id_pendaftar;
                break;
            }
        }
    }
    
    return $recipients;
}
```

### CSV Parser

```php
public function parseCSV(UploadedFile $file): array
{
    $errors = [];
    $recipients = [];
    
    $handle = fopen($file->path(), 'r');
    $header = fgetcsv($handle);
    
    // Validate header
    if (!in_array('name', $header) || !in_array('phone', $header)) {
        throw new \Exception('CSV must have "name" and "phone" columns');
    }
    
    $row = 1;
    while (($data = fgetcsv($handle)) !== false) {
        $row++;
        $record = array_combine($header, $data);
        
        // Validate
        if (empty($record['name']) || empty($record['phone'])) {
            $errors[] = "Row {$row}: Name and phone are required";
            continue;
        }
        
        $normalized = $this->normalizePhone($record['phone']);
        if (!$normalized) {
            $errors[] = "Row {$row}: Invalid phone number format";
            continue;
        }
        
        $recipients[] = [
            'name' => $record['name'],
            'phone' => $record['phone'],
            'phone_normalized' => $normalized,
            'notes' => $record['notes'] ?? null
        ];
    }
    
    fclose($handle);
    
    if (!empty($errors)) {
        throw new ValidationException($errors);
    }
    
    return $recipients;
}
```

## Components and Interfaces

### ExternalBroadcastService

Primary service for external broadcast operations.

**Methods**:
- `createBatch(string $name, string $type, int $userId): ExternalBroadcastBatch`
- `parseCSV(UploadedFile $file): array`
- `parseManualInput(string $input): array`
- `detectDuplicates(array $recipients): array`
- `normalizePhone(string $phone): ?string`
- `saveFecipients(int $batchId, array $recipients): void`

### WhatsAppController (Extended)

**New Methods**:
- `externalBroadcastPage(): View`
- `parseExternalRecipients(Request $request): JsonResponse`
- `sendExternalBroadcast(Request $request): JsonResponse`
- `getExternalMessages(int $id): JsonResponse`

**Modified Methods**:
- `phoneList(Request $request)`: Add external tab handling

## Data Models

### ExternalBroadcastBatch

**Attributes**:
- id: bigint (PK)
- batch_name: string
- description: text (nullable)
- total_recipients: int
- total_sent: int
- total_failed: int
- status: enum (pending, in_progress, completed, failed)
- source_type: enum (csv, manual)
- source_file: string (nullable)
- created_by: bigint (FK to users)
- created_at, updated_at, completed_at: timestamps

**Relationships**:
- hasMany: ExternalBroadcastRecipient
- hasMany: WhatsAppLog
- belongsTo: User (creator)

### ExternalBroadcastRecipient

**Attributes**:
- id: bigint (PK)
- batch_id: bigint (FK)
- name: string
- phone: string (original)
- phone_normalized: string
- notes: text (nullable)
- is_duplicate_spmb: boolean
- matched_pendaftar_id: bigint (nullable, FK to pendaftar)
- created_at, updated_at: timestamps

**Relationships**:
- belongsTo: ExternalBroadcastBatch
- belongsTo: Pendaftar (matched)

### WhatsAppLog (Modified)

**New Attribute**:
- external_batch_id: bigint (nullable, FK)

**New Relationship**:
- belongsTo: ExternalBroadcastBatch

## Correctness Properties

### Property 1: Phone Number Normalization Consistency
- For any phone P, normalizePhone(P) must always return the same result
- Normalized phones must match regex: /^62[0-9]{9,13}$/

### Property 2: Duplicate Detection Accuracy
- If phone P exists in SPMB database, detectDuplicates must flag it
- Duplicate detection must not produce false negatives
- Detection time must be O(n) where n is number of recipients

### Property 3: Batch Atomicity
- Creating a batch with recipients must be atomic (transaction)
- If any recipient fails validation, entire batch creation must roll back
- Batch status transitions must be sequential: pending → in_progress → completed/failed

### Property 4: Message Log Integrity
- Every sent message must have exactly one WhatsAppLog entry
- external_batch_id must match the originating batch
- Log timestamps must be monotonically increasing within a batch

### Property 5: Data Consistency
- total_sent + total_failed must equal number of WhatsAppLog entries for the batch
- Recipient count in batch must match number of ExternalBroadcastRecipient records

## Error Handling

### CSV Parsing Errors
- **Invalid Header**: Return validation error with required column names
- **Empty Row**: Skip row, log warning, continue processing
- **Invalid Phone**: Collect error with row number, continue parsing, return all errors at end
- **File Too Large**: Return 413 error before processing

### Duplicate Detection Errors
- **Database Connection Fail**: Retry 3 times with exponential backoff, then fail gracefully
- **Timeout**: Set timeout to 10s, return partial results with warning

### Broadcast Send Errors
- **WhatsApp Gateway Offline**: Check status before starting, prevent broadcast if offline
- **Individual Send Failure**: Log error, increment failed count, continue with remaining recipients
- **Rate Limit Exceeded**: Implement exponential backoff, respect 20 msg/min limit

### Validation Errors
- **Empty Batch Name**: Return 422 with specific error message
- **Duplicate Batch Name**: Check last 30 days, return 422 if exists
- **No Recipients**: Return 422 before creating batch
- **Invalid Permission**: Return 403 with role requirement message

### Database Errors
- **Transaction Failure**: Roll back all changes, return 500 with safe error message
- **Foreign Key Violation**: Return 422 with relationship error
- **Deadlock**: Retry transaction up to 3 times



## UI Components

### Broadcast Page Tab Structure

```blade
{{-- resources/views/whatsapp/broadcast.blade.php --}}

<ul class="nav nav-tabs mb-4" role="tablist">
    <li class="nav-item">
        <a class="nav-link active" data-bs-toggle="tab" href="#spmb-tab">
            <i class="fas fa-users me-2"></i>Data SPMB
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" href="#external-tab">
            <i class="fas fa-file-upload me-2"></i>Data Eksternal
        </a>
    </li>
</ul>

<div class="tab-content">
    <!-- SPMB Tab (existing) -->
    <div class="tab-pane fade show active" id="spmb-tab">
        <!-- Existing SPMB broadcast form -->
    </div>
    
    <!-- External Tab (new) -->
    <div class="tab-pane fade" id="external-tab">
        <div class="row">
            <div class="col-lg-8">
                <!-- Data Source Selection -->
                <div class="btn-group w-100 mb-3" role="group">
                    <input type="radio" class="btn-check" name="sourceType" id="csvUpload" value="csv" checked>
                    <label class="btn btn-outline-primary" for="csvUpload">
                        <i class="fas fa-file-csv me-2"></i>Upload CSV
                    </label>
                    
                    <input type="radio" class="btn-check" name="sourceType" id="manualInput" value="manual">
                    <label class="btn btn-outline-primary" for="manualInput">
                        <i class="fas fa-keyboard me-2"></i>Input Manual
                    </label>
                </div>
                
                <!-- CSV Upload Section -->
                <div id="csvSection">
                    <input type="file" class="form-control" accept=".csv" />
                    <small>Format: name,phone,notes (header required)</small>
                </div>
                
                <!-- Manual Input Section -->
                <div id="manualSection" style="display:none;">
                    <textarea class="form-control" rows="10" 
                        placeholder="Format: phone|name|notes (satu per baris)&#10;Contoh:&#10;081234567890|Ahmad|Alumni 2020&#10;082345678901"></textarea>
                </div>
                
                <!-- Batch Name -->
                <div class="mt-3">
                    <input type="text" class="form-control" placeholder="Nama Batch (contoh: Broadcast Alumni 2024)" />
                </div>
                
                <!-- Preview Recipients -->
                <div id="previewSection" class="mt-3" style="display:none;">
                    <h6>Preview Penerima</h6>
                    <div class="alert alert-info">
                        <strong>Total:</strong> <span id="totalCount">0</span><br>
                        <strong>Duplikat dengan SPMB:</strong> <span id="dupCount">0</span>
                    </div>
                    <table class="table table-sm">
                        <!-- Preview table -->
                    </table>
                </div>
                
                <!-- Message Template & Text -->
                <div class="mt-3">
                    <select class="form-select" id="templateSelect">
                        <option value="">-- Pilih Template --</option>
                        @foreach($templates as $template)
                        <option value="{{ $template->id }}">{{ $template->label }}</option>
                        @endforeach
                    </select>
                    
                    <textarea class="form-control mt-2" rows="8" 
                        placeholder="Ketik pesan...&#10;&#10;Variabel: {nama} {phone}"></textarea>
                </div>
                
                <!-- Send Button -->
                <button type="button" class="btn btn-primary btn-lg w-100 mt-3">
                    <i class="fas fa-paper-plane me-2"></i>Kirim Broadcast
                </button>
            </div>
            
            <div class="col-lg-4">
                <!-- Info Cards -->
                <div class="card">
                    <div class="card-body">
                        <h6>Variabel Tersedia</h6>
                        <code>{nama}</code> - Nama penerima<br>
                        <code>{phone}</code> - Nomor HP
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
```

### Phone List External Tab

```blade
{{-- resources/views/whatsapp/phone-list.blade.php --}}

<ul class="nav nav-tabs mb-3">
    <li class="nav-item">
        <a class="nav-link {{ $activeTab == 'all' ? 'active' : '' }}" 
           href="?tab=all">Semua</a>
    </li>
    <!-- ... existing tabs ... -->
    <li class="nav-item">
        <a class="nav-link {{ $activeTab == 'external' ? 'active' : '' }}" 
           href="?tab=external">
            📤 Eksternal
            <span class="badge bg-secondary">{{ $externalCount }}</span>
        </a>
    </li>
</ul>

@if($activeTab == 'external')
<div class="mb-3">
    <div class="form-check form-check-inline">
        <input class="form-check-input" type="checkbox" id="showDuplicatesOnly">
        <label class="form-check-label" for="showDuplicatesOnly">
            Tampilkan duplikat saja
        </label>
    </div>
</div>

<table class="table">
    <thead>
        <tr>
            <th>Nama</th>
            <th>Nomor HP</th>
            <th>Batch</th>
            <th>Pesan</th>
            <th>Terakhir</th>
        </tr>
    </thead>
    <tbody>
        @foreach($externalRecipients as $recipient)
        <tr>
            <td>{{ $recipient->name }}</td>
            <td>
                {{ $recipient->phone }}
                @if($recipient->is_duplicate_spmb)
                    <span class="badge bg-warning text-dark" 
                          data-bs-toggle="tooltip" 
                          title="Juga ada di SPMB">
                        🔄 Duplikat
                    </span>
                @endif
            </td>
            <td>{{ $recipient->batch->batch_name }}</td>
            <td>
                <button class="btn btn-sm btn-outline-primary" 
                        onclick="viewMessages({{ $recipient->id }})">
                    <i class="fas fa-eye"></i>
                </button>
            </td>
            <td>{{ $recipient->last_message_date }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif
```

## Data Flow

### External Broadcast Flow

```
1. User selects "Data Eksternal" tab
   ↓
2. User uploads CSV or enters manual data
   ↓
3. System parses and validates input
   ↓
4. System normalizes phone numbers
   ↓
5. System detects duplicates with SPMB database
   ↓
6. System creates ExternalBroadcastBatch record
   ↓
7. System creates ExternalBroadcastRecipient records
   ↓
8. System shows preview with duplicate indicators
   ↓
9. User confirms and enters message
   ↓
10. System sends via WhatsAppService (existing)
    ↓
11. System creates WhatsAppLog entries with external_batch_id
    ↓
12. System updates batch status and counts
```

## Models

### ExternalBroadcastBatch Model

```php
namespace App\Models;

class ExternalBroadcastBatch extends Model
{
    protected $fillable = [
        'batch_name', 'description', 'total_recipients',
        'total_sent', 'total_failed', 'status',
        'source_type', 'source_file', 'created_by'
    ];
    
    public function recipients()
    {
        return $this->hasMany(ExternalBroadcastRecipient::class, 'batch_id');
    }
    
    public function logs()
    {
        return $this->hasMany(WhatsAppLog::class, 'external_batch_id');
    }
    
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    
    public function markAsInProgress()
    {
        $this->update(['status' => 'in_progress']);
    }
    
    public function markAsCompleted()
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now()
        ]);
    }
    
    public function incrementSent()
    {
        $this->increment('total_sent');
    }
    
    public function incrementFailed()
    {
        $this->increment('total_failed');
    }
}
```

### ExternalBroadcastRecipient Model

```php
namespace App\Models;

class ExternalBroadcastRecipient extends Model
{
    protected $fillable = [
        'batch_id', 'name', 'phone', 'phone_normalized',
        'notes', 'is_duplicate_spmb', 'matched_pendaftar_id'
    ];
    
    protected $casts = [
        'is_duplicate_spmb' => 'boolean'
    ];
    
    public function batch()
    {
        return $this->belongsTo(ExternalBroadcastBatch::class, 'batch_id');
    }
    
    public function matchedPendaftar()
    {
        return $this->belongsTo(Pendaftar::class, 'matched_pendaftar_id', 'id_pendaftar');
    }
    
    public function messages()
    {
        return WhatsAppLog::where('phone', $this->phone_normalized)
            ->where('external_batch_id', $this->batch_id)
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
```

## Security Considerations

1. **Authorization**: Middleware `CheckRole:admin_wa` on all external broadcast routes
2. **CSRF Protection**: All POST requests require CSRF token
3. **Input Sanitization**: HTML::purify() on all text inputs
4. **File Upload**: Validate MIME type, size limit 2MB, store in non-public directory
5. **SQL Injection**: Use Eloquent ORM and parameterized queries
6. **Rate Limiting**: Apply Laravel rate limiter on external broadcast endpoints

## Performance Optimizations

1. **Batch Processing**: Process CSV in chunks of 500 records
2. **Database Indexing**: Indexes on phone_normalized, batch_id, external_batch_id
3. **Query Optimization**: Use eager loading for relationships
4. **Caching**: Cache duplicate detection results for 5 minutes
5. **Async Processing**: Consider queue for broadcasts >100 recipients
6. **Pagination**: 20 records per page on Phone List

## Testing Strategy

1. **Unit Tests**: Phone normalization, duplicate detection algorithms
2. **Integration Tests**: CSV parsing, batch creation, message sending flow
3. **Feature Tests**: Full external broadcast workflow end-to-end
4. **Performance Tests**: 1000 recipient batch processing under 5 seconds
5. **Security Tests**: Authorization, CSRF, input validation

## Migration Plan

1. Run database migrations for new tables
2. Seed test data for external broadcast
3. Deploy controller and service changes
4. Update Blade views with new tabs
5. Test on staging with sample CSV
6. Deploy to production during low-traffic period
7. Monitor logs for errors in first 24 hours

