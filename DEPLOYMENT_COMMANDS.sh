#!/bin/bash
# 🚀 Quick Deployment Commands untuk External Broadcast Feature

echo "=========================================="
echo "  EXTERNAL BROADCAST - DEPLOYMENT SCRIPT"
echo "=========================================="
echo ""

# 1. Pull latest code
echo "📥 Step 1: Pulling latest code..."
git pull origin main

echo ""
echo "✅ Code updated!"
echo ""

# 2. Run migrations
echo "🗄️  Step 2: Running database migrations..."
php artisan migrate --force

echo ""
echo "✅ Migrations completed!"
echo ""

# 3. Clear all caches
echo "🧹 Step 3: Clearing caches..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

echo ""
echo "✅ Caches cleared!"
echo ""

# 4. Optimize for production (optional)
echo "⚡ Step 4: Optimizing for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo ""
echo "✅ Optimization complete!"
echo ""

# 5. Check migrations status
echo "📊 Step 5: Migration status..."
php artisan migrate:status

echo ""
echo ""
echo "=========================================="
echo "  ✅ DEPLOYMENT COMPLETE!"
echo "=========================================="
echo ""
echo "🔍 Next steps:"
echo "1. Visit /whatsapp/broadcast to test"
echo "2. Check tab 'Data Eksternal'"
echo "3. Upload CSV or input manual"
echo "4. Send test broadcast"
echo ""
echo "📝 For detailed testing guide, see:"
echo "   EXTERNAL_BROADCAST_TESTING_GUIDE.md"
echo ""
