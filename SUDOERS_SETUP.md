# Sudoers Setup untuk PM2 Access

## 🎯 Tujuan
Mengizinkan web server user (`www`) menjalankan PM2 commands sebagai `root` tanpa password.

## ⚠️ Kenapa Perlu Sudo?

PM2 process `whatsapp-server` berjalan di user `root`, tapi web server PHP berjalan sebagai user `www`.

**Tanpa sudo:**
```bash
# As www user
pm2 jlist  # Returns [] (empty) - tidak bisa lihat process root
```

**Dengan sudo:**
```bash
# As www user with sudo
sudo -u root pm2 jlist  # Returns process list ✅
```

---

## 📝 Setup Steps

### 1. Backup sudoers (Safety First!)
```bash
sudo cp /etc/sudoers /etc/sudoers.backup
```

### 2. Edit sudoers menggunakan visudo
```bash
sudo visudo
```

**PENTING:** Selalu gunakan `visudo`, jangan edit `/etc/sudoers` langsung!

### 3. Add Permission Line

Scroll ke baris paling bawah file, tambahkan:

```bash
# Allow www user to run PM2 as root without password
www ALL=(root) NOPASSWD: /usr/bin/pm2
```

**Penjelasan:**
- `www` = user web server
- `ALL` = dari host manapun
- `(root)` = bisa run sebagai root
- `NOPASSWD` = tidak perlu password
- `/usr/bin/pm2` = hanya PM2 command (tidak command lain)

### 4. Save & Exit
- **Tekan:** `Ctrl+O` (Save)
- **Tekan:** `Enter` (Confirm)
- **Tekan:** `Ctrl+X` (Exit)

visudo akan otomatis validasi syntax. Jika ada error, akan muncul peringatan.

### 5. Test Permission

```bash
# Switch to www user
su - www

# Test PM2 with sudo (should work without password)
sudo -u root /usr/bin/pm2 jlist

# Should return JSON with process list
# Exit back to root
exit
```

---

## ✅ Verification

### Test 1: Via Shell
```bash
sudo -u www sudo -u root /usr/bin/pm2 jlist
```

**Expected:** JSON output dengan whatsapp-server process

### Test 2: Via Web (test-shell.php)
```
Refresh: https://spmb.smkpgriblora.sch.id/test-shell.php
```

**Expected:**
- PM2 jlist (as root via sudo): `[{...}]` dengan data process

### Test 3: Via Dashboard
```
Buka: https://spmb.smkpgriblora.sch.id/whatsapp
Scroll ke: Auto-Healing Diagnostics panel
Click: Refresh Diagnostics
```

**Expected:** Panel loads dengan process info, tidak stuck di "Loading..."

---

## 🔐 Security Considerations

### ✅ What's Safe:
- **Specific binary path:** `/usr/bin/pm2` (tidak bisa run command lain)
- **NOPASSWD hanya untuk PM2:** Limited scope
- **Web server sudah terisolasi:** www user sudah restricted
- **Commands hardcoded:** Tidak ada user input di PM2 commands

### ⚠️ What to Monitor:
- Review sudoers changes secara berkala
- Monitor `auth.log` untuk sudo usage
- Track UserActivityLog di Laravel

### 🛡️ Additional Hardening (Optional):
```bash
# Limit PM2 subcommands yang bisa dijalankan
www ALL=(root) NOPASSWD: /usr/bin/pm2 jlist, /usr/bin/pm2 restart *, /usr/bin/pm2 delete *, /usr/bin/pm2 start *, /usr/bin/pm2 logs *, /usr/bin/pm2 flush *
```

Ini lebih specific, hanya PM2 subcommands yang diperlukan.

---

## 🚨 Troubleshooting

### Problem: "sudo: no tty present and no askpass program specified"
**Solution:** Pastikan `NOPASSWD` ada di sudoers entry

### Problem: "www is not in the sudoers file"
**Solution:** Entry belum ditambahkan atau typo. Re-check dengan `visudo`

### Problem: "sudo: /usr/bin/pm2: command not found"
**Solution:** 
```bash
# Check PM2 path
which pm2  # Should return /usr/bin/pm2

# If different, use actual path in sudoers
```

### Problem: "sorry, you must have a tty to run sudo"
**Solution:** Add this line di sudoers (sebelum www entry):
```bash
Defaults:www !requiretty
```

---

## 📊 Alternative: PM2 Startup as www User

Alternatif lain (lebih kompleks): Jalankan PM2 sebagai user `www` dari awal.

### Pros:
- ✅ Tidak perlu sudo
- ✅ Lebih clean permission-wise

### Cons:
- ❌ Perlu setup ulang PM2
- ❌ Perlu restart semua processes
- ❌ `www` user limited shell access
- ❌ PM2 tidak persist setelah reboot (kecuali setup PM2 startup)

**Untuk sekarang, sudo approach lebih praktis.**

---

## 📝 Sudoers Final Check

Setelah setup, file `/etc/sudoers` seharusnya punya line seperti ini:

```bash
# ... (sudoers default content)

# Allow www user to run PM2 as root without password
www ALL=(root) NOPASSWD: /usr/bin/pm2
```

Atau yang lebih specific:

```bash
# PM2 management for WhatsApp Gateway
www ALL=(root) NOPASSWD: /usr/bin/pm2 jlist
www ALL=(root) NOPASSWD: /usr/bin/pm2 restart whatsapp-server
www ALL=(root) NOPASSWD: /usr/bin/pm2 delete whatsapp-server
www ALL=(root) NOPASSWD: /usr/bin/pm2 start *
www ALL=(root) NOPASSWD: /usr/bin/pm2 logs whatsapp-server *
www ALL=(root) NOPASSWD: /usr/bin/pm2 flush whatsapp-server
```

---

## 🎓 References

- [Sudoers Manual](https://www.sudo.ws/docs/man/1.8.15/sudoers.man/)
- [PM2 Documentation](https://pm2.keymetrics.io/docs/usage/quick-start/)
- [Linux Privilege Escalation Best Practices](https://wiki.archlinux.org/title/Sudo)

---

**Version:** 1.0.0  
**Last Updated:** 9 Juni 2026  
**Author:** Kiro AI Assistant
