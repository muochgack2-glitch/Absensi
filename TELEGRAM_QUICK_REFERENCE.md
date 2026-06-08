# 📱 Telegram Feature - Quick Reference Card

## ⚡ QUICK FACTS

**Status:** ✅ COMPLETE & PUSHED TO GITHUB  
**Commit:** `45e1de2`  
**Time to Deploy:** 10-15 minutes  
**Difficulty:** Easy (follow checklist)  

---

## 🎯 WHAT YOU GET

### **Automatic Alerts:**
- 🚨 Instant notification when WA Gateway disconnects
- ✅ Instant notification when reconnected
- 📊 Scheduled monitoring every 5 minutes
- 🔄 Auto-restart daily at 3:00 AM

### **Interactive Buttons:**
- **📊 View Dashboard** - Opens dashboard URL
- **🔄 Restart Server** - Restarts PM2 (no SSH!)
- **🔌 Reset & Reconnect** - Logout & generate QR
- **🔍 Check Status** - Shows current metrics

---

## 🚀 FASTEST SETUP (5 STEPS)

### **1. Create Bot (2 min)**
```
Telegram → @BotFather → /newbot → Copy token
```

### **2. Create Group & Get ID (2 min)**
```
Create group → Add bot → Add @getidsbot → Copy chat_id
```

### **3. Update .env (1 min)**
```env
TELEGRAM_BOT_TOKEN=your_token_here
TELEGRAM_CHAT_ID=your_chat_id_here
```

### **4. Set Webhook (1 min)**
```bash
curl -X POST "https://api.telegram.org/botTOKEN/setWebhook" \
  -d "url=https://yourdomain.com/telegram/webhook"
```

### **5. Test (1 min)**
```bash
php test_telegram_bot.php
php artisan wa:monitor
```

**Done! Check Telegram group for alerts.**

---

## 📋 FILES CHANGED

### **New Files (4):**
```
✅ app/Http/Controllers/TelegramWebhookController.php
✅ test_telegram_bot.php
✅ TELEGRAM_SETUP_GUIDE.md
✅ TELEGRAM_DEPLOYMENT_CHECKLIST.md
```

### **Modified Files (5):**
```
✅ app/Notifications/WhatsAppStatusChanged.php
✅ config/services.php
✅ routes/web.php
✅ bootstrap/app.php
✅ .env.example
```

---

## 🧪 TESTING COMMANDS

```bash
# Test bot credentials
php test_telegram_bot.php

# Test notification manually
php artisan wa:monitor

# Check webhook status
curl "https://api.telegram.org/botTOKEN/getWebhookInfo"

# View scheduled tasks
php artisan schedule:list

# Watch Laravel logs
tail -f storage/logs/laravel.log
```

---

## 🔧 COMMON COMMANDS

### **Webhook Management:**
```bash
# Set webhook
curl -X POST "https://api.telegram.org/botTOKEN/setWebhook" \
  -d "url=https://domain.com/telegram/webhook"

# Check webhook
curl "https://api.telegram.org/botTOKEN/getWebhookInfo"

# Delete webhook
curl -X POST "https://api.telegram.org/botTOKEN/deleteWebhook"
```

### **Artisan Commands:**
```bash
php artisan wa:monitor          # Check status & send alerts
php artisan wa:restart          # Restart server (interactive)
php artisan wa:restart --force  # Restart without prompt
php artisan schedule:list       # View scheduled tasks
php artisan queue:work          # Start queue worker
```

---

## 📊 NOTIFICATION PREVIEW

### **Disconnect Alert:**
```
🚨 WA Gateway Disconnected!

📊 Status Change:
• Previous: Connected ✅
• Current: Disconnected ❌

⏰ Time: 08 Jun 2026 15:30:45

⚠️ Action Required: Check dashboard immediately!

📱 SPMB WhatsApp Gateway
```
**[📊 View Dashboard] [🔄 Restart] [🔌 Reset] [🔍 Status]**

### **Button Response:**
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

## 🐛 TROUBLESHOOTING (FASTEST FIXES)

| Problem | Quick Fix |
|---------|-----------|
| No messages | `php artisan queue:work` |
| Buttons don't work | Set webhook URL |
| Bot not found | Add bot to group |
| Wrong chat_id | Use negative number for groups |
| Config not loaded | `php artisan config:clear` |

---

## 📚 DOCUMENTATION

| File | Use Case |
|------|----------|
| `TELEGRAM_SETUP_GUIDE.md` | **First time setup** (detailed) |
| `TELEGRAM_DEPLOYMENT_CHECKLIST.md` | **Deploy to hosting** (step-by-step) |
| `TELEGRAM_NOTIFICATION_FEATURE.md` | **Technical details** (implementation) |
| `TELEGRAM_QUICK_REFERENCE.md` | **Quick lookup** (this file) |

---

## ✅ DEPLOYMENT CHECKLIST (SHORT)

**On Hosting:**
- [ ] `git pull origin main`
- [ ] `php artisan config:clear`
- [ ] Create bot via @BotFather
- [ ] Create group & get chat_id
- [ ] Update `.env` with credentials
- [ ] `php test_telegram_bot.php` (should pass)
- [ ] Set webhook URL
- [ ] `php artisan wa:monitor` (should send alert)
- [ ] Click button in Telegram (should work)
- [ ] Verify queue worker running

**Total Time:** ~10-15 minutes

---

## 🎯 SUCCESS INDICATORS

✅ Test script shows all passed  
✅ Messages appear in Telegram group  
✅ Buttons respond with messages  
✅ Status button shows current metrics  
✅ No errors in Laravel logs  
✅ Scheduled tasks listed correctly  

---

## 💡 PRO TIPS

1. **Use @getidsbot** - Fastest way to get chat_id
2. **Test locally first** - Run `test_telegram_bot.php`
3. **Check webhook** - Most button issues = webhook not set
4. **Monitor logs** - `tail -f storage/logs/laravel.log`
5. **Queue worker** - Must be running for notifications

---

## 📞 QUICK HELP

**Messages not sending?**  
→ Check queue worker: `ps aux | grep queue:work`

**Buttons not working?**  
→ Check webhook: `curl "https://api.telegram.org/botTOKEN/getWebhookInfo"`

**Need detailed help?**  
→ Read: `TELEGRAM_SETUP_GUIDE.md`

---

**🎉 Feature Complete! Ready to Deploy!**

**Questions?** Check documentation files above.  
**Need help?** Run test script and check logs.

---

**Last Updated:** June 8, 2026  
**Version:** 1.0.0  
**Status:** Production Ready ✅
