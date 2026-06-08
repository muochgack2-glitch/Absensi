#!/bin/bash
# Clear Cache Script for Production Hosting

echo "🧹 Clearing Laravel Cache..."

# Clear application cache
php artisan cache:clear
echo "✅ Application cache cleared"

# Clear config cache
php artisan config:clear
echo "✅ Config cache cleared"

# Clear route cache
php artisan route:clear
echo "✅ Route cache cleared"

# Clear view cache
php artisan view:clear
echo "✅ View cache cleared"

# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
echo "✅ Production optimization complete"

echo ""
echo "✅ All cache cleared and optimized!"
echo "📝 Remember to hard refresh browser (Ctrl+Shift+R or Cmd+Shift+R)"
