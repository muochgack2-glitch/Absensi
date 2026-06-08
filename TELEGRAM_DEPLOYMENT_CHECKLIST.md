# 📋 Telegram Feature - Deployment Checklist

## ✅ Status: PUSHED TO GITHUB - READY FOR HOSTING DEPLOYMENT

**Commit:** `45e1de2` - feat: Telegram notifications with inline buttons for WA Gateway monitoring

---

## 🚀 DEPLOYMENT STEPS (ON HOSTING)

### **Step 1: Pull Latest Code**
```bash
cd /path/to/project
git pull origin main
```

### **Step 2: Clear Laravel Cache**
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
```

### **Step 3: Create Telegram Bot**

**In Telegram:**
1. Search for `@BotFather`
2. Send: `/newbot`
3. Follow prompts:
   - Bot name: `SPMB WhatsApp Gateway Bot`
   - Username: `spmb_wa_gateway_bot` (must end with "bot")
4. **COPY THE TOKEN** (looks like: `1234567890:ABCdefGHIjklMNOpqrsTUVwxyz`)

### **Step 4: Create Telegram Group**

1. Create new group in Telegram
   - Name: `SPMB WA Gateway Alerts`
2. Add the bot to the group
3. Make bot admin (optional)

### **Step 5: Get Chat ID**

**Method A: Using @getidsbot (Recommended)**
1. Add `@getidsbot` to your group
2. Copy the chat_id (e.g., `-1001234567890`)
3. Remove `@getidsbot` from group

**Method B: Manual API Call**
1. Send a message in your group
2. Open in browser:
   ```
   https://api.telegram.org/bot<YOUR_BOT_TOKEN>/getUpdates
   ```
3. Find `"chat":{"id":-1001234567890}`
4. Copy the negative number

### **Step 6: Update .env File**

**On hosting server:**
```bash
nano .env
```

**Add these lines:**
```env
# Telegram Configuration
TELEGRAM_BOT_TOKEN=1234567890:ABCdefGHIjklMNOpqrsTUVwxyz
TELEGRAM_CHAT_ID=-1001234567890
```

Replace with YOUR actual values!

**Save and exit:** `Ctrl+X`, `Y`, `Enter`

### **Step 7: Clear Config Cache Again**
```bash
php artisan config:clear
```

### **Step 8: Test Telegram Configuration**

**Run test script:**
```bash
php test_telegram_bot.php
```

**Expected output:**
```
✅ PASSED: Bot is valid
✅ PASSED: Message sent successfully
✅ PASSED: Message with buttons sent
```

**Check your Telegram group** - you should see 2 test messages.

### **Step 9: Set Webhook URL**

**Replace with your actual domain:**
```bash
curl -X POST "https://api.telegram.org/bot<YOUR_BOT_TOKEN>/setWebhook" \
  -d "url=https://yourdomain.com/telegram/webhook"
```

**Example:**
```bash
curl -X POST "https://api.telegram.org/bot1234567890:ABCdefGHIjklMNOpqrsTUVwxyz/setWebhook" \
  -d "url=https://spmb.smkpgriblora.sch.id/telegram/webhook"
```

**Expected response:**
```json
{"ok":true,"result":true,"description":"Webhook was set"}
```

### **Step 10: Verify Webhook**

```bash
curl "https://api.telegram.org/bot<YOUR_BOT_TOKEN>/getWebhookInfo"
```

**Should show:**
```json
{
  "ok": true,
  "result": {
    "url": "https://yourdomain.com/telegram/webhook",
    "has_custom_certificate": false,
    "pending_update_count": 0
  }
}
```

### **Step 11: Test Monitoring**

**Run manually:**
```bash
php artisan wa:monitor
```

**Check Telegram group** - you should receive a notification if status changed.

### **Step 12: Verify Scheduled Tasks**

```bash
php artisan schedule:list
```

**Should show:**
```
0 3 * * *  php artisan wa:restart --force ........... Next Due: 1 day from now
*/5 * * * * php artisan wa:monitor ................. Next Due: 4 minutes from now
```

### **Step 13: Test Inline Buttons**

**In Telegram group:**
1. Wait for a notification OR manually trigger one
2. Click **"🔍 Check Status"** button
3. You should get a reply with current status
4. Try other buttons (restart, reset) if needed

### **Step 14: Verify Queue Worker**

**Check if queue worker is running:**
```bash
ps aux | grep queue:work
```

**If not running, start it:**
```bash
php artisan queue:work --daemon &
```

**Or use supervisor (recommended for production).**

---

## ✅ VERIFICATION CHECKLIST

**Before going live, verify:**

- [ ] Code pulled from GitHub successfully
- [ ] Laravel cache cleared
- [ ] Telegram bot created via @BotFather
- [ ] Bot token obtained and saved
- [ ] Telegram group created
- [ ] Bot added to group as admin
- [ ] Chat ID obtained
- [ ] `.env` updated with bot token and chat_id
- [ ] Config cache cleared after .env update
- [ ] Test script passed (`php test_telegram_bot.php`)
- [ ] Test messages received in Telegram group
- [ ] Webhook URL set successfully
- [ ] Webhook verified with getWebhookInfo
- [ ] Manual monitor test passed (`php artisan wa:monitor`)
- [ ] Scheduled tasks listed correctly
- [ ] Inline buttons work (click and get response)
- [ ] Queue worker is running
- [ ] Cron is configured for scheduled tasks

---

## 🧪 TESTING SCENARIOS

### **Test 1: Disconnect Notification**
```bash
# Stop WA server
pm2 stop whatsapp-gateway

# Trigger monitor
php artisan wa:monitor

# Check Telegram - should get disconnect alert
```

### **Test 2: Reconnect Notification**
```bash
# Start WA server
pm2 start whatsapp-gateway

# Wait 10 seconds, then monitor
sleep 10
php artisan wa:monitor

# Check Telegram - should get reconnect alert
```

### **Test 3: Check Status Button**
```
1. Click "🔍 Check Status" button in any notification
2. Should get reply with current status and health metrics
```

### **Test 4: Restart Server Button**
```
1. Stop WA server first: pm2 stop whatsapp-gateway
2. Wait for disconnect notification
3. Click "🔄 Restart Server" button
4. Should get reply: "Restart command sent..."
5. Check server: pm2 status whatsapp-gateway (should be online)
```

### **Test 5: Reset Connection Button**
```
1. Click "🔌 Reset & Reconnect" button
2. Should get reply: "Logout successful, generating QR..."
3. Check dashboard - should show new QR code
```

---

## 🐛 TROUBLESHOOTING

### **Problem: Test script fails**

**Check:**
```bash
# Verify bot token
echo $TELEGRAM_BOT_TOKEN

# If empty, token not loaded
php artisan config:clear
php test_telegram_bot.php
```

### **Problem: No messages in Telegram**

**Debug:**
```bash
# Check Laravel logs
tail -f storage/logs/laravel.log

# Check if queue worker is running
ps aux | grep queue:work

# Start queue worker if not running
php artisan queue:work --verbose
```

### **Problem: Buttons don't work**

**Fix:**
```bash
# Verify webhook is set
curl "https://api.telegram.org/bot<TOKEN>/getWebhookInfo"

# If webhook missing, set it
curl -X POST "https://api.telegram.org/bot<TOKEN>/setWebhook" \
  -d "url=https://yourdomain.com/telegram/webhook"

# Check Laravel logs when clicking button
tail -f storage/logs/laravel.log
```

### **Problem: Webhook errors**

**Check:**
```bash
# Verify webhook URL is accessible
curl https://yourdomain.com/telegram/webhook

# Should return: 404 or method not allowed (not 500 error)

# Check webhook info for errors
curl "https://api.telegram.org/bot<TOKEN>/getWebhookInfo"
```

---

## 📊 MONITORING (After Deployment)

### **Day 1: Check Every Hour**
- Monitor Telegram group for notifications
- Check Laravel logs: `tail -f storage/logs/laravel.log`
- Verify buttons work
- Check queue worker is running

### **Day 2-7: Check Daily**
- Review notification frequency
- Verify scheduled tasks run (3 AM restart)
- Check for any errors in logs
- Confirm buttons still work

### **Week 2+: Check Weekly**
- Review notification history
- Check uptime metrics
- Verify no queue job failures
- Confirm auto-restart works

---

## 📚 DOCUMENTATION REFERENCES

| Document | Purpose |
|----------|---------|
| `TELEGRAM_SETUP_GUIDE.md` | Complete step-by-step setup guide |
| `TELEGRAM_NOTIFICATION_FEATURE.md` | Feature implementation details |
| `test_telegram_bot.php` | Test script for verification |
| `WA_GATEWAY_TOP3_FEATURES.md` | Original feature proposal |

---

## 🎯 SUCCESS CRITERIA

**Feature is working correctly when:**

✅ **Disconnect alerts arrive instantly** in Telegram group  
✅ **Reconnect alerts arrive** after server recovery  
✅ **All inline buttons work** and return responses  
✅ **Check Status shows** current metrics  
✅ **Restart Server button** restarts PM2 successfully  
✅ **Reset Connection button** generates new QR  
✅ **Dashboard button** opens correct URL  
✅ **Scheduled monitor runs** every 5 minutes automatically  
✅ **Scheduled restart runs** daily at 3 AM  
✅ **No errors** in Laravel logs  
✅ **Queue worker processes** jobs without failures  

---

## 🚀 DEPLOYMENT COMPLETE!

Once all checklist items are verified, the feature is **production-ready**.

**Questions?**
- Review: `TELEGRAM_SETUP_GUIDE.md`
- Test: `php test_telegram_bot.php`
- Debug: `tail -f storage/logs/laravel.log`

---

**Last Updated:** June 8, 2026  
**Commit:** 45e1de2  
**Status:** ✅ Ready for Production Deployment
