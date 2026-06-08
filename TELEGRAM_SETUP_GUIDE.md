# 📱 Telegram Alert Setup Guide
## WhatsApp Gateway Status Notifications with Inline Buttons

---

## 🎯 Overview
This system sends **automated Telegram notifications** when WhatsApp Gateway status changes (disconnected/reconnected), with **inline action buttons** for quick response.

### ✨ Features
- 🚨 **Instant alerts** when WA Gateway disconnects
- ✅ **Reconnection notifications** 
- 📊 **Interactive inline buttons**:
  - View Dashboard
  - Restart Server
  - Reset & Reconnect
  - Check Status
- 📱 **Group broadcast** - all admins get notified in one group
- ⚡ **No manual SSH needed** - control everything from Telegram

---

## 🚀 Setup Steps

### **Step 1: Create Telegram Bot**

1. **Open Telegram** and search for `@BotFather`
2. **Start chat** and send: `/newbot`
3. **Follow prompts**:
   ```
   Name: SPMB WhatsApp Gateway Bot
   Username: spmb_wa_gateway_bot (must end with "bot")
   ```
4. **Copy Bot Token** (looks like: `1234567890:ABCdefGHIjklMNOpqrsTUVwxyz`)
   - ⚠️ **SAVE THIS TOKEN** - you'll need it later

**Example:**
```
Done! Congratulations on your new bot. You will find it at t.me/spmb_wa_gateway_bot
Use this token to access the HTTP API:
1234567890:ABCdefGHIjklMNOpqrsTUVwxyz

For a description of the Bot API, see this page: https://core.telegram.org/bots/api
```

---

### **Step 2: Create Telegram Group**

1. **Create new group** in Telegram
   - Group name: `SPMB WA Gateway Alerts` (or any name)
2. **Add your bot** to the group:
   - Tap group name → Add members → Search for your bot
   - Add the bot to group
3. **Make bot admin** (optional, but recommended):
   - Tap group name → Edit → Administrators → Add Admin
   - Select your bot

---

### **Step 3: Get Chat ID**

There are **3 methods** to get your group's chat_id:

#### **Method 1: Using @getidsbot (Easiest)**
1. Add `@getidsbot` to your group
2. It will automatically show the group's chat_id
3. Copy the number (e.g., `-1001234567890`)
4. Remove `@getidsbot` from group (optional)

#### **Method 2: Using @userinfobot**
1. Add `@userinfobot` to your group
2. Send any message in the group
3. Bot will reply with chat info including chat_id
4. Copy the chat_id
5. Remove the bot (optional)

#### **Method 3: Manual API Call**
1. Send a test message in your group
2. Open browser and visit:
   ```
   https://api.telegram.org/bot<YOUR_BOT_TOKEN>/getUpdates
   ```
   Replace `<YOUR_BOT_TOKEN>` with your actual bot token
3. Look for `"chat":{"id":-1001234567890}` in the JSON response
4. Copy the negative number (e.g., `-1001234567890`)

---

### **Step 4: Configure Laravel Application**

1. **Open `.env` file** in your project root
2. **Add Telegram configuration**:
   ```env
   # Telegram Configuration
   TELEGRAM_BOT_TOKEN=1234567890:ABCdefGHIjklMNOpqrsTUVwxyz
   TELEGRAM_CHAT_ID=-1001234567890
   ```
   - Replace with YOUR actual bot token and chat_id
   - ⚠️ **Chat ID must include the minus sign** if it's negative

3. **Save the file**

---

### **Step 5: Set Telegram Webhook URL**

The webhook allows Telegram to send button clicks back to your server.

#### **Production Server:**
Run this command to set webhook:
```bash
curl -X POST "https://api.telegram.org/bot<YOUR_BOT_TOKEN>/setWebhook" \
  -d "url=https://yourdomain.com/telegram/webhook"
```

**Example:**
```bash
curl -X POST "https://api.telegram.org/bot1234567890:ABCdefGHIjklMNOpqrsTUVwxyz/setWebhook" \
  -d "url=https://spmb.example.com/telegram/webhook"
```

#### **Check webhook status:**
```bash
curl "https://api.telegram.org/bot<YOUR_BOT_TOKEN>/getWebhookInfo"
```

**Expected response:**
```json
{
  "ok": true,
  "result": {
    "url": "https://spmb.example.com/telegram/webhook",
    "has_custom_certificate": false,
    "pending_update_count": 0
  }
}
```

---

### **Step 6: Test Notifications**

#### **Test 1: Send Test Notification**
You can manually test by running the monitor command:
```bash
php artisan wa:monitor
```

This will:
- Check WhatsApp Gateway status
- Send notification if status changed
- Show result in console

#### **Test 2: Force Status Change**
To test the notification system:

1. **Stop WhatsApp Node.js server** (to trigger disconnect notification):
   ```bash
   pm2 stop whatsapp-gateway
   ```
   
2. **Wait for monitor** (runs every 5 minutes) OR run manually:
   ```bash
   php artisan wa:monitor
   ```

3. **Check Telegram group** - you should receive:
   - 🚨 Disconnect notification with action buttons

4. **Restart server** to test reconnect notification:
   ```bash
   pm2 start whatsapp-gateway
   ```

5. **Run monitor again**:
   ```bash
   php artisan wa:monitor
   ```

6. **Check Telegram** - you should receive:
   - ✅ Reconnected notification

---

### **Step 7: Test Inline Buttons**

After receiving a notification in Telegram:

1. **Tap "🔍 Check Status" button**
   - Bot should reply with current status
   - Shows uptime, memory usage, connection status

2. **Tap "📊 View Dashboard" button**
   - Opens dashboard URL in browser

3. **When disconnected, tap "🔄 Restart Server" button**
   - Bot should reply: "Restart command sent"
   - WhatsApp server restarts automatically
   - Wait 10 seconds, check status again

4. **When disconnected, tap "🔌 Reset & Reconnect" button**
   - Bot should reply: "Logout successful, generating QR"
   - You'll need to scan QR code from dashboard

---

## 📋 Command Reference

### **Artisan Commands**

```bash
# Monitor WhatsApp status manually
php artisan wa:monitor

# Restart WhatsApp server (interactive)
php artisan wa:restart

# Restart WhatsApp server (force, no prompt)
php artisan wa:restart --force

# Check scheduled tasks
php artisan schedule:list
```

### **Scheduled Tasks**

The following tasks run automatically:

| Command | Schedule | Description |
|---------|----------|-------------|
| `wa:monitor` | Every 5 minutes | Check WA status and send alerts |
| `wa:restart` | Daily at 3:00 AM | Auto-restart WA server |

---

## 🔧 Troubleshooting

### **Problem: Bot doesn't send messages**

**Check:**
1. ✅ Bot token is correct in `.env`
2. ✅ Chat ID is correct (including minus sign)
3. ✅ Bot is member of the group
4. ✅ Laravel queue is running: `php artisan queue:work`

**Test connection manually:**
```bash
curl -X POST "https://api.telegram.org/bot<YOUR_BOT_TOKEN>/sendMessage" \
  -d "chat_id=<YOUR_CHAT_ID>" \
  -d "text=Test message from bot"
```

If this works, your credentials are correct.

---

### **Problem: Buttons don't work**

**Check:**
1. ✅ Webhook URL is set correctly
2. ✅ URL is HTTPS (not HTTP)
3. ✅ Server is accessible from internet
4. ✅ Route is registered in `routes/web.php`
5. ✅ CSRF exception is configured in `bootstrap/app.php`

**Check webhook status:**
```bash
curl "https://api.telegram.org/bot<YOUR_BOT_TOKEN>/getWebhookInfo"
```

**Check Laravel logs:**
```bash
tail -f storage/logs/laravel.log
```

---

### **Problem: "Webhook not set" error**

**Solution:**
Run the webhook setup command again:
```bash
curl -X POST "https://api.telegram.org/bot<YOUR_BOT_TOKEN>/setWebhook" \
  -d "url=https://yourdomain.com/telegram/webhook"
```

---

### **Problem: Getting old/duplicate messages**

**Clear pending updates:**
```bash
curl -X POST "https://api.telegram.org/bot<YOUR_BOT_TOKEN>/setWebhook" \
  -d "url=https://yourdomain.com/telegram/webhook" \
  -d "drop_pending_updates=true"
```

---

### **Problem: Notifications not sent on status change**

**Check:**
1. ✅ Monitor command is scheduled: `php artisan schedule:list`
2. ✅ Cron is running: `* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1`
3. ✅ Check logs: `tail -f storage/logs/laravel.log`

**Test manually:**
```bash
php artisan wa:monitor
```

---

## 📊 Notification Examples

### **Disconnect Notification:**
```
🚨 WA Gateway Disconnected!

📊 Status Change:
• Previous: Connected ✅
• Current: Disconnected ❌

⏰ Time: 08 Jun 2026 15:30:45

⚠️ Action Required: Check the dashboard immediately!

📱 SPMB WhatsApp Gateway
```
**Buttons:** View Dashboard | Restart Server | Reset & Reconnect | Check Status

---

### **Reconnect Notification:**
```
✅ WA Gateway Reconnected!

📊 Status Change:
• Previous: Disconnected ❌
• Current: Connected ✅

⏰ Time: 08 Jun 2026 15:32:10

✨ Server has automatically reconnected.

📱 SPMB WhatsApp Gateway
```
**Buttons:** View Dashboard | Check Status

---

### **Check Status Response:**
```
🔍 Current Status

📊 Connection: connected ✅
🔌 QR Available: No
🔄 Reconnect Attempts: 0

📊 Server Health:
• Uptime: 2d 5h 30m
• Memory: 245/512 MB (47%)

⏰ Checked: 15:30:45
```

---

## 🔐 Security Notes

1. **Keep bot token secret**
   - Never commit `.env` file to git
   - Use `.env.example` as template only

2. **Restrict group access**
   - Only add trusted admins to notification group
   - Bot commands are accessible to all group members

3. **HTTPS required**
   - Telegram webhooks ONLY work with HTTPS
   - Use SSL certificate (Let's Encrypt recommended)

4. **Rate limiting**
   - Restart command: Max 2x per hour
   - Prevents abuse and accidental spam

---

## 🎨 Customization

### **Change notification message:**
Edit: `app/Notifications/WhatsAppStatusChanged.php`
- Method: `buildTelegramMessage()`

### **Add/remove buttons:**
Edit: `app/Notifications/WhatsAppStatusChanged.php`
- Method: `buildInlineKeyboard()`

### **Handle new button actions:**
Edit: `app/Http/Controllers/TelegramWebhookController.php`
- Method: `handle()`
- Add new case in match statement

### **Change monitoring frequency:**
Edit: `routes/console.php`
- Change `everyFiveMinutes()` to desired frequency

---

## 📚 Additional Resources

- [Telegram Bot API Documentation](https://core.telegram.org/bots/api)
- [Laravel Notifications](https://laravel.com/docs/notifications)
- [Laravel Task Scheduling](https://laravel.com/docs/scheduling)

---

## ✅ Setup Checklist

- [ ] Created bot via @BotFather
- [ ] Got bot token
- [ ] Created Telegram group
- [ ] Added bot to group
- [ ] Got group chat_id
- [ ] Updated `.env` with credentials
- [ ] Set webhook URL
- [ ] Tested notification sending
- [ ] Tested inline buttons
- [ ] Verified scheduled tasks
- [ ] Confirmed queue is running

---

**🎉 Setup Complete!**

Your WhatsApp Gateway alerts are now live. You'll receive instant Telegram notifications with action buttons whenever the gateway status changes.

**Need help?** Check the troubleshooting section or review Laravel logs.
