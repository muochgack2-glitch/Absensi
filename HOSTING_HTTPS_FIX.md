# Fix Mixed Content Error - HTTPS Configuration

## Problem
Dashboard loaded via HTTPS but API requests use HTTP, causing "Mixed Content" error:
```
Mixed Content: The page at 'https://spmb.smkpgriblora.sch.id/dashboard' was loaded over HTTPS, 
but requested an insecure resource 'http://spmb.smkpgriblora.sch.id/laporan/stats'. 
This request has been blocked; the content must be served over HTTPS.
```

## Solution Applied

### 1. Force HTTPS in AppServiceProvider
File: `app/Providers/AppServiceProvider.php`

Added code to force HTTPS scheme in production:
```php
public function boot(): void
{
    // Force HTTPS in production
    if ($this->app->environment('production')) {
        \URL::forceScheme('https');
    }
}
```

### 2. Configure .env in Hosting

Update your `.env` file in hosting with these settings:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://spmb.smkpgriblora.sch.id

# Other settings...
```

**Important:**
- `APP_ENV=production` - Must be "production" to enable HTTPS forcing
- `APP_DEBUG=false` - Disable debug mode in production
- `APP_URL=https://...` - Must use HTTPS protocol

### 3. Clear Cache in Hosting

After updating `.env`, run these commands in hosting terminal:

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

Or use single command:
```bash
php artisan optimize:clear
```

### 4. Verify HTTPS Configuration

1. Check if `.env` has correct settings
2. Verify SSL certificate is installed and active
3. Test dashboard page - all API calls should use HTTPS
4. Check browser console - no more "Mixed Content" errors

## Additional Notes

### If Using Reverse Proxy (Nginx/Apache)

Add this to `app/Http/Middleware/TrustProxies.php`:

```php
protected $proxies = '*';

protected $headers = 
    Request::HEADER_X_FORWARDED_FOR |
    Request::HEADER_X_FORWARDED_HOST |
    Request::HEADER_X_FORWARDED_PORT |
    Request::HEADER_X_FORWARDED_PROTO;
```

### If Still Getting HTTP URLs

Add to `.htaccess` (if using Apache):
```apache
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

Or add to Nginx config:
```nginx
if ($scheme != "https") {
    return 301 https://$host$request_uri;
}
```

## Testing

After deployment:
1. Open dashboard: `https://spmb.smkpgriblora.sch.id/dashboard`
2. Open browser console (F12)
3. Check Network tab - all requests should use HTTPS
4. Statistics table should load without errors

## Troubleshooting

**If still getting Mixed Content error:**
1. Clear browser cache (Ctrl+Shift+Delete)
2. Hard refresh page (Ctrl+F5)
3. Check `.env` file has `APP_ENV=production`
4. Run `php artisan config:clear` again

**If getting 500 error:**
1. Check `storage/logs/laravel.log`
2. Verify file permissions (755 for folders, 644 for files)
3. Check database connection

**If statistics still not loading:**
1. Check browser console for specific error
2. Test API directly: `https://spmb.smkpgriblora.sch.id/laporan/stats`
3. Verify user is logged in (session valid)
