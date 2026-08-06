# Fix Vite Manifest Error on Production

## Error
```
Unable to locate file in Vite manifest: resources/js/sidebar.js
```

## Root Cause
The production server build didn't include `sidebar.js` and `navbar.js` in the Vite manifest. This happens when:
1. Files weren't present during build
2. Build cache is stale
3. Git pull didn't include the JS files

## Solution

Run these commands on **production server** (`root@aapanel-lxc:/www/wwwroot/absensi/Absensi#`):

```bash
# 1. Verify files exist
ls -la resources/js/sidebar.js
ls -la resources/js/navbar.js

# 2. If files are missing, pull again
git pull origin main

# 3. Clear npm cache and node_modules
rm -rf node_modules package-lock.json
npm cache clean --force

# 4. Reinstall dependencies
npm install

# 5. Clean build directory
rm -rf public/build

# 6. Rebuild assets
npm run build

# 7. Verify manifest includes the files
cat public/build/manifest.json | grep sidebar
cat public/build/manifest.json | grep navbar

# 8. Clear Laravel caches
php artisan view:clear
php artisan config:clear
php artisan cache:clear
php artisan optimize:clear

# 9. Restart web server (if using PHP-FPM)
# For aaPanel, you may need to restart through the panel
# Or: systemctl restart php8.3-fpm
# Or: service nginx restart
```

## Verify Fix

After running the commands:
1. Visit: https://absensi.smkpgrilora.sch.id/attendance/dashboard
2. Check browser console for errors
3. Test sidebar toggle functionality
4. Test navbar scroll behavior

## Expected manifest.json entries

After successful build, you should see entries like:
```json
{
  "resources/js/sidebar.js": {
    "file": "assets/sidebar-xxxxx.js",
    "src": "resources/js/sidebar.js"
  },
  "resources/js/navbar.js": {
    "file": "assets/navbar-xxxxx.js",
    "src": "resources/js/navbar.js"
  }
}
```

## Prevention

Always run on production after git pull:
```bash
npm install && npm run build && php artisan optimize:clear
```

---

**Status**: Awaiting execution on production server
**Last Updated**: 2026-08-06
