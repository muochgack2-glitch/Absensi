# Auto-Healing Dashboard - Code Reference

## Quick Code Snippets

### 1. Controller Method Signatures

```php
// In WhatsAppController.php

/**
 * Get PM2 diagnostics
 */
public function diagnostics()
{
    // Returns JSON with process data, issues, recommendations, fix_history
}

/**
 * Auto-fix detected issues
 */
public function autoFix()
{
    // Rate limited: 3 per hour
    // Returns JSON with fixed and failed issues
}

/**
 * Get error logs from PM2
 */
public function getErrorLogs()
{
    // Returns last 100 lines of error logs
}
```

### 2. Route Definitions

```php
// In routes/web.php (inside whatsapp middleware group)

// Diagnostics & Auto-Fix
Route::get('/diagnostics', [\App\Http\Controllers\WhatsAppController::class, 'diagnostics'])
    ->name('whatsapp.diagnostics');
    
Route::post('/auto-fix', [\App\Http\Controllers\WhatsAppController::class, 'autoFix'])
    ->name('whatsapp.auto-fix')
    ->middleware('throttle:3,60'); // Max 3x per hour
    
Route::get('/error-logs', [\App\Http\Controllers\WhatsAppController::class, 'getErrorLogs'])
    ->name('whatsapp.error-logs');
```

### 3. JavaScript Function Calls

```javascript
// Load diagnostics on page load
document.addEventListener('DOMContentLoaded', function() {
    loadDiagnostics();
    setInterval(loadDiagnostics, 60000); // Auto-refresh every 60s
});

// Manual refresh
refreshDiagnostics();

// Run auto-fix
runAutoFix();

// Load error logs
loadErrorLogs();
```

### 4. API Response Formats

#### Diagnostics Response
```json
{
  "success": true,
  "data": {
    "process": {
      "name": "whatsapp-server",
      "pm2_env": { ... },
      "monit": { "memory": 123456, "cpu": 5 }
    },
    "issues": [
      {
        "type": "error",
        "code": "PROCESS_NOT_FOUND",
        "title": "PM2 Process Not Found",
        "description": "WhatsApp server process tidak ditemukan di PM2",
        "auto_fixable": true
      }
    ],
    "recommendations": [],
    "fix_history": [],
    "timestamp": "2024-01-01T00:00:00Z"
  }
}
```

#### Auto-Fix Response
```json
{
  "success": true,
  "message": "Auto-fix applied successfully. Fixed 2 issue(s).",
  "data": {
    "fixed": [
      {
        "code": "CRASH_LOOP",
        "title": "High Restart Count",
        "fix": "Flushed logs and restarted process"
      }
    ],
    "failed": []
  }
}
```

#### Error Logs Response
```json
{
  "success": true,
  "data": {
    "logs": "[PM2] Error: ...\n[PM2] Stack trace: ...",
    "timestamp": "2024-01-01T00:00:00Z"
  }
}
```

### 5. PM2 Commands Used

```bash
# Get process list (JSON format)
pm2 jlist

# Get error logs (last 100 lines, no streaming)
pm2 logs whatsapp-server --err --lines 100 --nostream

# Start new process
cd /path/to/whatsapp-server && pm2 start server.js --name whatsapp-server

# Delete process
pm2 delete whatsapp-server

# Restart process
pm2 restart whatsapp-server

# Flush logs
pm2 flush whatsapp-server
```

### 6. Cache Keys Used

```php
// Rate limiting
cache()->get('wa_autofix_count'); // Int: number of fixes
cache()->get('wa_autofix_timestamp'); // Carbon: last fix timestamp

// Fix history
cache()->get('wa_diagnostics_fix_history'); // Array: last 10 fixes

// Store examples
cache()->put('wa_autofix_count', 1, 3600); // 1 hour
cache()->put('wa_diagnostics_fix_history', $history, 86400 * 7); // 7 days
```

### 7. HTML Structure

```html
<!-- Diagnostics Panel -->
<div class="card border-0 shadow-sm border-start border-5 border-primary">
    <div class="card-header bg-gradient-primary text-white">
        <!-- Header with buttons -->
    </div>
    <div class="card-body">
        <!-- Diagnostics Status -->
        <div id="diagnosticsStatus">...</div>
        
        <!-- Issues Panel -->
        <div id="issuesPanel">
            <div id="issuesList">...</div>
            
            <!-- Error Log Viewer -->
            <div class="collapse" id="errorLogCollapse">
                <div class="card-body bg-dark text-light">
                    <div id="errorLogContent">...</div>
                </div>
            </div>
        </div>
        
        <!-- Fix History -->
        <div id="fixHistoryPanel">
            <table id="fixHistoryTable">...</table>
        </div>
    </div>
</div>
```

### 8. CSS Classes Used

```css
/* Panel styling */
.border-start.border-5.border-primary
.bg-gradient-primary.text-white

/* Alert boxes */
.alert-success  /* Green - healthy */
.alert-danger   /* Red - error */
.alert-warning  /* Yellow - warning */

/* Badges */
.badge.bg-success  /* Green - auto-fixable */
.badge.bg-danger   /* Red - error status */
.badge.bg-secondary /* Gray - manual fix */

/* Log viewer */
.bg-dark.text-light
font-family: monospace
max-height: 400px
overflow-y: auto
```

### 9. Issue Detection Logic

```php
// Process not found
if (!$process) {
    $issues[] = [
        'type' => 'error',
        'code' => 'PROCESS_NOT_FOUND',
        'title' => 'PM2 Process Not Found',
        'description' => 'WhatsApp server process tidak ditemukan di PM2',
        'auto_fixable' => true
    ];
}

// Crash loop detection
$restarts = $pm2['restart_time'] ?? 0;
if ($restarts > 10) {
    $issues[] = [
        'type' => 'warning',
        'code' => 'CRASH_LOOP',
        'title' => 'High Restart Count',
        'description' => "Server telah restart {$restarts} kali.",
        'auto_fixable' => true
    ];
}

// Memory check
$memory = $monit['memory'] ?? 0;
$memoryMB = round($memory / 1024 / 1024, 2);
if ($memoryMB > 500) {
    $issues[] = [
        'type' => 'warning',
        'code' => 'HIGH_MEMORY',
        'title' => 'High Memory Usage',
        'description' => "Memory usage tinggi: {$memoryMB} MB.",
        'auto_fixable' => false
    ];
}

// Process status check
$status = $pm2['status'] ?? 'unknown';
if ($status === 'stopped' || $status === 'errored') {
    $issues[] = [
        'type' => 'error',
        'code' => 'PROCESS_STOPPED',
        'title' => 'Process Stopped',
        'description' => "Server dalam status: {$status}",
        'auto_fixable' => true
    ];
}

// Import path error check
$errorLog = shell_exec('pm2 logs whatsapp-server --err --lines 50 --nostream 2>&1');
if ($errorLog && str_contains($errorLog, 'ERR_UNSUPPORTED_DIR_IMPORT')) {
    $issues[] = [
        'type' => 'error',
        'code' => 'IMPORT_PATH_ERROR',
        'title' => 'Import Path Error',
        'description' => 'Ditemukan error import path di logs.',
        'auto_fixable' => true
    ];
}
```

### 10. Fix Application Logic

```php
switch ($issue['code']) {
    case 'PROCESS_NOT_FOUND':
        $output = shell_exec('cd ' . base_path('../whatsapp-server') . ' && pm2 start server.js --name whatsapp-server 2>&1');
        $fixed = true;
        $fixMessage = 'Started new PM2 process';
        break;

    case 'IMPORT_PATH_ERROR':
        shell_exec('pm2 delete whatsapp-server 2>&1');
        sleep(2);
        $output = shell_exec('cd ' . base_path('../whatsapp-server') . ' && pm2 start server.js --name whatsapp-server 2>&1');
        $fixed = true;
        $fixMessage = 'Deleted and restarted process with correct configuration';
        break;

    case 'CRASH_LOOP':
        shell_exec('pm2 flush whatsapp-server 2>&1');
        sleep(1);
        shell_exec('pm2 restart whatsapp-server 2>&1');
        $fixed = true;
        $fixMessage = 'Flushed logs and restarted process';
        break;

    case 'PROCESS_STOPPED':
        shell_exec('pm2 restart whatsapp-server 2>&1');
        $fixed = true;
        $fixMessage = 'Restarted stopped process';
        break;
}
```

### 11. Rate Limiting Implementation

```php
// Check rate limit
$fixCount = cache()->get('wa_autofix_count', 0);
$fixTimestamp = cache()->get('wa_autofix_timestamp');

if ($fixCount >= 3 && $fixTimestamp && now()->diffInHours($fixTimestamp) < 1) {
    return response()->json([
        'success' => false,
        'message' => 'Rate limit exceeded. Maximum 3 auto-fixes per hour. Please wait.'
    ], 429);
}

// Update rate limit
if (!empty($fixedIssues)) {
    cache()->put('wa_autofix_count', $fixCount + 1, 3600);
    cache()->put('wa_autofix_timestamp', now(), 3600);
}
```

### 12. Fix History Storage

```php
// Add to fix history
$fixHistory = cache()->get('wa_diagnostics_fix_history', []);
$fixHistory[] = [
    'timestamp' => now()->toIso8601String(),
    'user_id' => auth()->id(),
    'user_name' => auth()->user()->name,
    'fixed_issues' => $fixedIssues,
    'failed_issues' => $failedIssues
];
cache()->put('wa_diagnostics_fix_history', $fixHistory, 86400 * 7); // 7 days
```

### 13. Activity Logging

```php
UserActivityLog::create([
    'user_id' => auth()->id(),
    'action' => 'wa_auto_fix',
    'description' => 'Applied auto-fix for ' . count($fixedIssues) . ' issue(s)',
    'ip_address' => request()->ip(),
    'user_agent' => request()->userAgent(),
]);
```

### 14. JavaScript AJAX Calls

```javascript
// Fetch diagnostics
fetch('{{ route("whatsapp.diagnostics") }}')
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateDiagnosticsUI(data.data);
        }
    });

// Run auto-fix
fetch('{{ route("whatsapp.auto-fix") }}', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
    }
})
.then(response => response.json())
.then(data => {
    if (data.success) {
        showAlert('success', data.message);
        setTimeout(loadDiagnostics, 3000);
    }
});

// Fetch error logs
fetch('{{ route("whatsapp.error-logs") }}')
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            errorLogContent.innerHTML = `<pre>${escapeHtml(data.data.logs)}</pre>`;
        }
    });
```

### 15. UI Update Functions

```javascript
// Update diagnostics UI
function updateDiagnosticsUI(data) {
    const issues = data.issues || [];
    
    if (issues.length === 0) {
        // Show success state
        diagnosticsStatus.innerHTML = `
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                All Systems Healthy
            </div>
        `;
    } else {
        // Show issues
        let issuesHtml = '';
        issues.forEach(issue => {
            const badgeClass = issue.type === 'error' ? 'danger' : 'warning';
            issuesHtml += `
                <div class="alert alert-${badgeClass}">
                    <h6>${issue.title}</h6>
                    <p>${issue.description}</p>
                    ${issue.auto_fixable ? '<span class="badge bg-success">Auto-fixable</span>' : ''}
                </div>
            `;
        });
        document.getElementById('issuesList').innerHTML = issuesHtml;
    }
}
```

### 16. Testing Commands

```bash
# Test diagnostics endpoint
curl -X GET http://localhost/whatsapp/diagnostics \
  -H "Cookie: laravel_session=..."

# Test auto-fix endpoint
curl -X POST http://localhost/whatsapp/auto-fix \
  -H "Cookie: laravel_session=..." \
  -H "X-CSRF-TOKEN: ..."

# Test error logs endpoint
curl -X GET http://localhost/whatsapp/error-logs \
  -H "Cookie: laravel_session=..."

# Test PM2 from PHP
php artisan tinker
>>> shell_exec('pm2 jlist');

# Test rate limiting
# Run auto-fix 3 times, 4th should fail with 429
```

### 17. Debugging Tips

```javascript
// Enable debug logging
console.log('Diagnostics data:', data);
console.log('Issues found:', data.data.issues);
console.log('Fix history:', data.data.fix_history);

// Check network requests
// Open browser DevTools → Network tab
// Filter by: diagnostics, auto-fix, error-logs

// Check console for errors
// Open browser DevTools → Console tab
// Look for red error messages
```

### 18. Common Errors & Solutions

```php
// Error: "shell_exec() is disabled"
// Solution: Enable shell_exec in php.ini
// Remove from disable_functions list

// Error: "pm2: command not found"
// Solution: Install PM2 globally
npm install -g pm2

// Error: "ENOENT: no such file or directory"
// Solution: Check whatsapp-server path
// Verify base_path('../whatsapp-server') exists

// Error: "Rate limit exceeded"
// Solution: Wait 1 hour or clear cache
cache()->forget('wa_autofix_count');
cache()->forget('wa_autofix_timestamp');
```

### 19. Performance Optimization

```php
// Cache PM2 process list for 30 seconds
$cacheKey = 'wa_pm2_processes';
$processes = cache()->remember($cacheKey, 30, function() {
    $output = shell_exec('pm2 jlist 2>&1');
    return json_decode($output, true);
});

// Async log loading
// Load logs only when collapse is expanded
// Use lazy loading for fix history

// Debounce manual refresh
// Prevent spam clicking refresh button
let refreshTimeout;
function refreshDiagnostics() {
    clearTimeout(refreshTimeout);
    refreshTimeout = setTimeout(loadDiagnostics, 500);
}
```

### 20. Security Best Practices

```php
// 1. Sanitize shell commands
$processName = 'whatsapp-server'; // Hardcoded, not from user input

// 2. Escape output
echo htmlspecialchars($errorLog);

// 3. Validate user permissions
if (!auth()->check() || !auth()->user()->hasRole(['administrator', 'admin_wa'])) {
    abort(403);
}

// 4. Rate limiting
->middleware('throttle:3,60')

// 5. CSRF protection
@csrf token in forms

// 6. Activity logging
UserActivityLog::create([...]);

// 7. Don't expose sensitive data
// Don't return full error stack traces to frontend
```

---

## Quick Reference Card

### Endpoints
- `GET /whatsapp/diagnostics` - Get diagnostics
- `POST /whatsapp/auto-fix` - Run auto-fix (3/hour)
- `GET /whatsapp/error-logs` - Get error logs

### Functions
- `loadDiagnostics()` - Fetch & display
- `runAutoFix()` - Trigger auto-fix
- `loadErrorLogs()` - Show logs

### Issue Codes
- `PROCESS_NOT_FOUND` - Start new
- `IMPORT_PATH_ERROR` - Delete & restart
- `CRASH_LOOP` - Flush & restart
- `PROCESS_STOPPED` - Restart
- `HIGH_MEMORY` - Manual fix

### Cache Keys
- `wa_autofix_count` - Rate limit counter
- `wa_autofix_timestamp` - Rate limit timestamp
- `wa_diagnostics_fix_history` - Fix history array

---

**Version:** 1.0.0  
**Last Updated:** 2024-01-01  
**Author:** Kiro AI Assistant
