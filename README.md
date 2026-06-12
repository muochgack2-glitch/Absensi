# SPMB & Absensi Monorepo

Repository ini menggunakan strategi **Monorepo dengan Dual Push** untuk mengelola 2 proyek Laravel dalam satu workspace.

## 📁 Struktur Folder

```
SPMB/ (root)
├── app/                    # SPMB Laravel 11 - Aplikasi SPMB
├── whatsapp-server/        # WhatsApp Gateway untuk SPMB (Port 3000)
└── absensi/                # Absensi Laravel 13 - Aplikasi Absensi
    └── whatsapp-server-absensi/  # WhatsApp Gateway untuk Absensi (Port 3001)
```

## 🔄 Git Remotes

Repository ini terhubung ke 2 remote repositories:

- **origin**: https://github.com/muochgack2-glitch/SPMB.git (Repository SPMB)
- **absensi**: https://github.com/muochgack2-glitch/Absensi.git (Repository Absensi)

## 📝 Workflow Push

### Push ke SPMB saja:
```bash
git push origin main
```

### Push ke Absensi saja:
```bash
git push absensi main
```

### Push ke kedua repository:
```bash
git push origin main
git push absensi main
```

**Atau gunakan shortcut:**
```bash
git push --all
```

## 🚀 Deployment

### Deploy SPMB
```bash
git clone https://github.com/muochgack2-glitch/SPMB.git
cd SPMB
# Fokus ke folder root untuk aplikasi SPMB
# Fokus ke whatsapp-server/ untuk gateway SPMB
```

### Deploy Absensi
```bash
git clone https://github.com/muochgack2-glitch/Absensi.git
cd Absensi/absensi
# Fokus ke folder absensi/ untuk aplikasi Absensi
# Fokus ke absensi/whatsapp-server-absensi/ untuk gateway Absensi
```

## ⚙️ Setup Development

### SPMB (Root)
```bash
# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Run migrations
php artisan migrate --seed

# Start development
php artisan serve
npm run dev

# Gateway SPMB (di terminal terpisah)
cd whatsapp-server
npm install
cp .env.example .env
npm start
```

### Absensi
```bash
cd absensi

# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Run migrations
php artisan migrate --seed

# Start development
php artisan serve --port=8001
npm run dev

# Gateway Absensi (di terminal terpisah)
cd whatsapp-server-absensi
npm install
cp .env.example .env
npm start
```

## 📚 Dokumentasi Lengkap

- **Dual Gateway Setup**: [DUAL_GATEWAY_SETUP.md](DUAL_GATEWAY_SETUP.md)
- **Implementation Details**: [DUAL_GATEWAY_IMPLEMENTATION.md](DUAL_GATEWAY_IMPLEMENTATION.md)
- **Deployment Checklist**: [DEPLOYMENT_CHECKLIST.md](DEPLOYMENT_CHECKLIST.md)
- **Absensi System Plan**: [ABSENSI_SYSTEM_PLAN.md](ABSENSI_SYSTEM_PLAN.md)
- **Commit Summary**: [COMMIT_SUMMARY.md](COMMIT_SUMMARY.md)

## 🎯 Kenapa Monorepo?

1. **Development Context**: Semua komunikasi dengan AI assistant (Kiro) tetap dalam satu konteks
2. **Shared Resources**: Gateway backup bisa digunakan bersama sebelum Absensi aktif
3. **Easier Management**: Update dan maintenance dilakukan di satu tempat
4. **Flexible Deployment**: Tetap bisa deploy terpisah sesuai kebutuhan

## ⚠️ Catatan Penting

- **Kedua repository memiliki konten yang sama** (by design untuk monorepo)
- **Deployment fokus berbeda**: SPMB fokus ke root, Absensi fokus ke folder `absensi/`
- **Gateway saat ini**: Port 3001 digunakan sebagai backup SPMB, nanti akan direpurpose untuk Absensi
- **Instruksi Push**: Selalu spesifikasikan repository tujuan saat push

## 🔧 Troubleshooting

### Cek status git:
```bash
git status
git remote -v
```

### Cek commit terakhir:
```bash
git log -1 --oneline
```

### Sinkronisasi dengan remote:
```bash
git fetch origin
git fetch absensi
```

---

**Version**: 1.0.0  
**Last Updated**: 12 Juni 2026  
**Maintainer**: muochgack2-glitch
