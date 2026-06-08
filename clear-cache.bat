@echo off
REM Clear Cache Script for Production Hosting (Windows)

echo 🧹 Clearing Laravel Cache...
echo.

REM Clear application cache
php artisan cache:clear
echo ✅ Application cache cleared

REM Clear config cache
php artisan config:clear
echo ✅ Config cache cleared

REM Clear route cache
php artisan route:clear
echo ✅ Route cache cleared

REM Clear view cache
php artisan view:clear
echo ✅ View cache cleared

REM Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
echo ✅ Production optimization complete

echo.
echo ✅ All cache cleared and optimized!
echo 📝 Remember to hard refresh browser (Ctrl+Shift+R)
pause
