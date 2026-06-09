#!/bin/bash
# Fix PM2 Sudo Access for www user
# Run as root: bash fix-pm2-sudo.sh

echo "🔧 Fixing PM2 Sudo Access..."

# Backup current sudoers
cp /etc/sudoers /etc/sudoers.backup.$(date +%Y%m%d_%H%M%S)
echo "✅ Sudoers backed up"

# Check if www PM2 entry already exists
if grep -q "www.*pm2" /etc/sudoers; then
    echo "⚠️  PM2 entry already exists in sudoers"
    echo "Current entry:"
    grep "www.*pm2" /etc/sudoers
else
    echo "➕ Adding PM2 sudo entry for www user..."
    
    # Add sudo entry using visudo (safer)
    echo "# Allow www user to run PM2 as root without password" | sudo EDITOR='tee -a' visudo > /dev/null
    echo "Defaults:www !requiretty" | sudo EDITOR='tee -a' visudo > /dev/null
    echo "www ALL=(root) NOPASSWD: /usr/bin/pm2" | sudo EDITOR='tee -a' visudo > /dev/null
    
    echo "✅ Sudoers entry added"
fi

# Test sudo access
echo ""
echo "🧪 Testing sudo access..."
echo "Test 1: PM2 version as www user:"
sudo -u www sudo -u root /usr/bin/pm2 -v

echo ""
echo "Test 2: PM2 list as www user:"
sudo -u www sudo -u root /usr/bin/pm2 list

echo ""
echo "Test 3: PM2 jlist as www user (JSON):"
sudo -u www sudo -u root /usr/bin/pm2 jlist | head -20

echo ""
echo "✅ Setup complete!"
echo "📋 Next steps:"
echo "   1. Check if tests above show PM2 data"
echo "   2. Refresh dashboard diagnostics"
echo "   3. Should show 'Healthy' status"
