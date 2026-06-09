# Auto-Healing Dashboard - WhatsApp Gateway

## Overview
Auto-Healing Dashboard adalah fitur diagnostik otomatis yang mendeteksi dan memperbaiki masalah WhatsApp Gateway secara otomatis. Fitur ini membantu administrator untuk:
- Mendeteksi masalah server secara real-time
- Memperbaiki masalah umum dengan satu klik
- Melihat log error dari PM2
- Melacak history perbaikan

## Features Implemented

### 1. **Diagnostics Panel**
Panel diagnostik yang menampilkan status kesehatan server dan issues yang terdeteksi.

**Lokasi:** WhatsApp Gateway Dashboard (paling atas)

**Fitur:**
- Real-time detection of issues
- Color-coded status badges (Red: Error, Yellow: Warning, Green: Healthy)
- Auto-refresh every 60 seconds
- Manual refresh button

### 2. **Auto-Fix Functionality**
Sistem perbaikan otomatis yang dapat menangani berbagai masalah umum.

**Issues yang dapat diperbaiki otomatis:**
- `PROCESS_NOT_FOUND` - PM2 process tidak ditemukan → Start new process
- `IMPORT_PATH_ERROR` - Error import path → Delete & restart with correct config
- `CRASH_LOOP` - High restart count (>10) → Flush logs & restart
- `PROCESS_STOPPED` - Process in stopped/errored state → Restart process

**Issues yang terdeteksi (manual fix):**
- `HIGH_MEMORY` - Memory usage > 500 MB → Recommendation: restart server

**Rate Limiting:**
- Maximum 3 auto-fixes per hour per user
- Prevents abuse and excessive server restarts

### 3. **Error Log Viewer**
Collapsible panel untuk melihat error logs dari PM2.

**Fitur:**
- Display last 100 lines from PM2 error log
- Monospace font for better readability
- Dark theme for log viewing
- Auto-load on collapse expand
- Scrollable container (max height: 400px)

### 4. **Fix History**
Tabel yang menampilkan 10 fix terakhir yang dilakukan.

**Informasi yang ditampilkan:**
- Timestamp (when fix was applied)
- User name (who triggered the fix)
- Issues fixed (list of fixed issues)
- Status (success/failed count with badges)

**Data Storage:**
- Stored in Laravel cache
- Retained for 7 days
- Maximum 10 entries displayed

## Technical Implementation

### Backend (Controller Methods)

#### `diagnostics()` - GET /whatsapp/diagnostics
**Purpose:** Get PM2 process status and detect issues

**Process:**
1. Execute `pm2 jlist` to get process list
2. Find `whatsapp-server` process
3. Check for common issues:
   - Process not found
   - High restart count (crash loop)
   - High memory usage
   - Process stopped/errored
   - Import path errors in logs
4. Return diagnostics data with issues array

**Response:**
```json
{
  "success": true,
  "data": {
    "process": { ... }, // PM2 process data
    "issues": [
      {
        "type": "error|warning",
        "code": "ISSUE_CODE",
        "title": "Issue Title",
        "description": "Issue description",
        "auto_fixable": true|false
      }
    ],
    "recommendations": [],
    "fix_history": [...],
    "timestamp": "2024-01-01T00:00:00Z"
  }
}
```

#### `autoFix()` - POST /whatsapp/auto-fix
**Purpose:** Automatically fix detected issues

**Rate Limiting:** 3 requests per hour

**Process:**
1. Check rate limit (max 3 fixes per hour)
2. Get current diagnostics
3. Iterate through auto-fixable issues
4. Apply appropriate fix based on issue code:
   - `PROCESS_NOT_FOUND`: `pm2 start server.js --name whatsapp-server`
   - `IMPORT_PATH_ERROR`: `pm2 delete whatsapp-server && pm2 start server.js`
   - `CRASH_LOOP`: `pm2 flush whatsapp-server && pm2 restart whatsapp-server`
   - `PROCESS_STOPPED`: `pm2 restart whatsapp-server`
5. Update fix counter and timestamp
6. Save to fix history (cache)
7. Log activity to `user_activity_logs` table

**Response:**
```json
{
  "success": true,
  "message": "Auto-fix applied successfully. Fixed 2 issue(s).",
  "data": {
    "fixed": [...],
    "failed": [...]
  }
}
```

#### `getErrorLogs()` - GET /whatsapp/error-logs
**Purpose:** Fetch PM2 error logs

**Process:**
1. Execute `pm2 logs whatsapp-server --err --lines 100 --nostream`
2. Return raw log output

**Response:**
```json
{
  "success": true,
  "data": {
    "logs": "...",
    "timestamp": "2024-01-01T00:00:00Z"
  }
}
```

### Frontend (JavaScript Functions)

#### `loadDiagnostics()`
- Fetch diagnostics from backend
- Update UI with issues and fix history
- Called on page load and every 60 seconds

#### `refreshDiagnostics()`
- Manual refresh trigger
- Shows loading state
- Calls `loadDiagnostics()`

#### `updateDiagnosticsUI(data)`
- Updates diagnostics panel UI
- Shows "All Systems Healthy" if no issues
- Lists issues with badges and descriptions
- Updates fix history table

#### `runAutoFix()`
- Triggered by "Auto-Fix Issues" button
- Shows confirmation dialog
- POST to `/whatsapp/auto-fix`
- Displays success/error message
- Reloads diagnostics after 3 seconds

#### `loadErrorLogs()`
- Fetches error logs from backend
- Displays in monospace dark theme
- Auto-escapes HTML for security

### Routes
```php
Route::get('/diagnostics', [WhatsAppController::class, 'diagnostics'])
    ->name('whatsapp.diagnostics');

Route::post('/auto-fix', [WhatsAppController::class, 'autoFix'])
    ->name('whatsapp.auto-fix')
    ->middleware('throttle:3,60'); // Max 3x per hour

Route::get('/error-logs', [WhatsAppController::class, 'getErrorLogs'])
    ->name('whatsapp.error-logs');
```

## UI Components

### Diagnostics Panel
**Bootstrap Classes:**
- `card border-0 shadow-sm border-start border-5 border-primary` - Card styling
- `bg-gradient-primary text-white` - Header styling
- Responsive button groups

### Status Badges
- **Error:** `alert-danger` with `fa-times-circle` icon
- **Warning:** `alert-warning` with `fa-exclamation-triangle` icon
- **Success:** `alert-success` with `fa-check-circle` icon
- **Auto-fixable:** `badge bg-success` with `fa-magic` icon
- **Manual fix:** `badge bg-secondary` with `fa-ban` icon

### Error Log Viewer
- Collapsible Bootstrap collapse component
- Dark card body (`bg-dark text-light`)
- Monospace font family
- Max height: 400px with vertical scroll

### Fix History Table
- Responsive Bootstrap table
- Small table (`table-sm table-hover`)
- Light header (`table-light`)
- Success/danger badges for status

## Security Considerations

1. **Rate Limiting**
   - Auto-fix limited to 3 times per hour
   - Prevents abuse and excessive server restarts

2. **CSRF Protection**
   - All POST requests include CSRF token
   - Laravel middleware validates tokens

3. **HTML Escaping**
   - Error logs are escaped before display
   - Prevents XSS attacks from log content

4. **Role-Based Access**
   - Middleware: `checkRole:administrator,admin_wa`
   - Only authorized users can access diagnostics

5. **Shell Command Safety**
   - Commands are hardcoded (no user input)
   - Working directory is controlled
   - Error output is captured and logged

## Usage Guide

### For Administrators

**1. View Diagnostics**
- Navigate to WhatsApp Gateway dashboard
- Diagnostics panel is at the top
- Auto-refreshes every 60 seconds

**2. Manual Refresh**
- Click "Refresh" button in diagnostics panel
- Useful after making manual changes

**3. Auto-Fix Issues**
- Review detected issues
- Click "Auto-Fix Issues" button
- Confirm the action
- Wait for completion (3-10 seconds)
- Check fix history for results

**4. View Error Logs**
- Click "View Error Logs" button
- Collapsible panel will expand
- Scroll through last 100 lines
- Look for patterns or recurring errors

**5. Review Fix History**
- Check "Fix History" table at bottom
- See who applied fixes and when
- Review which issues were fixed
- Monitor for recurring problems

### Troubleshooting

**Issue: Auto-fix button disabled**
- **Cause:** Rate limit reached (3 fixes per hour)
- **Solution:** Wait for the hour window to reset

**Issue: No issues detected but server not working**
- **Cause:** PM2 process not properly registered
- **Solution:** Manually check PM2 status: `pm2 list`

**Issue: Error logs not loading**
- **Cause:** PM2 not installed or path issue
- **Solution:** Verify PM2 installation: `pm2 --version`

**Issue: Auto-fix failed**
- **Cause:** Permission issues or PM2 not responding
- **Solution:** Manually restart via SSH: `pm2 restart whatsapp-server`

## Best Practices

1. **Monitor Regularly**
   - Check diagnostics panel daily
   - Review fix history weekly
   - Look for patterns in issues

2. **Don't Over-Fix**
   - Avoid clicking auto-fix repeatedly
   - Let server stabilize after fix (10+ seconds)
   - Check status before running another fix

3. **Review Logs**
   - When issues persist, check error logs
   - Look for root causes, not just symptoms
   - Consider manual intervention if needed

4. **Track Memory Usage**
   - Monitor "Server Health" card
   - If memory consistently high, consider upgrading server
   - Schedule regular restarts during low-traffic periods

5. **Document Patterns**
   - If same issue occurs repeatedly, investigate
   - May indicate code bug or infrastructure issue
   - Contact developer for persistent problems

## Future Enhancements

### Potential Improvements
- [ ] Email notifications on critical issues
- [ ] Telegram bot notifications
- [ ] Auto-fix scheduling (e.g., restart at 3 AM if high memory)
- [ ] Historical trends chart (issues over time)
- [ ] Export diagnostics report as PDF
- [ ] Integration with server monitoring tools
- [ ] Advanced diagnostics (network, disk, CPU)
- [ ] Predictive analysis (detect issues before they occur)

### Known Limitations
- Only detects PM2-related issues
- Cannot fix code-level bugs
- Rate limiting may delay urgent fixes
- Requires PM2 CLI access from PHP
- Windows compatibility may vary

## Testing Checklist

- [x] Diagnostics panel loads on page load
- [x] Issues are displayed correctly
- [x] Auto-fix button triggers POST request
- [x] Rate limiting works (max 3 per hour)
- [x] Error logs load in collapsible panel
- [x] Fix history displays correctly
- [x] CSRF token is included in requests
- [x] Badges display with correct colors
- [x] Responsive design works on mobile
- [x] Real-time updates work (60s interval)

## Related Files

### Modified Files
1. `app/Http/Controllers/WhatsAppController.php`
   - Added `diagnostics()` method
   - Added `autoFix()` method
   - Added `getErrorLogs()` method

2. `routes/web.php`
   - Added `/whatsapp/diagnostics` route
   - Added `/whatsapp/auto-fix` route (with throttle)
   - Added `/whatsapp/error-logs` route

3. `resources/views/whatsapp/index.blade.php`
   - Added Auto-Healing Diagnostics Panel (HTML)
   - Added JavaScript functions for diagnostics
   - Added error log viewer component
   - Added fix history table

### Dependencies
- Laravel Cache (for fix history storage)
- PM2 CLI (for process management)
- Bootstrap 5 (for UI components)
- Font Awesome (for icons)

## Support

For issues or questions:
1. Check error logs first
2. Review fix history for patterns
3. Consult Laravel logs: `storage/logs/laravel.log`
4. Check PM2 logs: `pm2 logs whatsapp-server`
5. Contact system administrator if issue persists

---

**Version:** 1.0.0  
**Last Updated:** 2024-01-01  
**Author:** Kiro AI Assistant  
**License:** Internal Use Only
