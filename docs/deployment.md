# Deployment

## Prasyarat

| Item | Nilai |
|------|-------|
| VPS Host (SSH) | `tencentcloud` (43.153.196.198) |
| Remote path | `/var/www/iunikeb.com.my/public_html` |
| Branch | `main` |
| SSH key | `~/.ssh/tencentcloud` |
| DB | SQLite (`database/database.sqlite`) |

## Cara Deploy

```bash
./deploy.sh
```

Script akan minta mesej commit, then push → ssh → deploy remotely.

## Apa `deploy.sh` buat (step-by-step)

1. **Git commit & push** ke GitHub
2. **SSH ke VPS** (`tencentcloud`)
3. **Bersihkan** `public/hot` (tinggalan Vite dev)
4. **Backup database** → `database/database.sqlite.bak`
5. **Git reset --hard origin/main** (sync dengan GitHub)
6. **Pulihkan database** dari backup (kalau file DB kosong)
7. `composer install --no-dev --optimize-autoloader`
8. `npm ci && npm run build`
9. `php artisan migrate --force`
10. `php artisan db:seed --force` (idempotent)
11. **Cache**: `optimize:clear`, `config:cache`, `route:cache`
12. `php artisan storage:link --force`
13. **Permissions**: chown + chmod database/storage

## Sebelum Deploy

- [ ] Code dah tested (lint, typecheck, etc.)
- [ ] Semua migration dah tested
- [ ] Commit message clear & deskriptif
- [ ] Pastikan internet stable (deploy depend on GitHub)

## Rollback

```bash
# 1. SSH manually
ssh tencentcloud

# 2. Reset ke commit sebelumnya
cd /var/www/iunikeb.com.my/public_html
git reset --hard HEAD~1

# 3. Build semula
npm ci && npm run build
php artisan migrate --force
php artisan optimize:clear && php artisan config:cache && php artisan route:cache

# 4. Restore database kalau perlu
mv database/database.sqlite.bak database/database.sqlite
chown www-data:www-data database/database.sqlite
chmod 664 database/database.sqlite
```

## Troubleshooting

| Masalah | Sebab / Solution |
|---------|-----------------|
| `npm ci` gagal | `node_modules` corrupt. Ssh & padam `node_modules`, then re-run `./deploy.sh` |
| DB kosong lepas deploy | Script auto-restore dari `.bak`. Kalau tiada backup, DB baru akan terhasil lepas migrate |
| Permission denied | Ssh manually & jalankan `chown -R admin:admin .` + `chmod 775 storage bootstrap/cache` |
| 500 error lepas deploy | Check `storage/logs/laravel.log`. Mungkin env issue — pastikan `.env` ada di VPS |

## Env Variables

Pastikan `.env` di VPS ada semua required vars. Jangan commit `.env` ke GitHub. Kalau ada env baru, update manual kat VPS lepas deploy.

## First-time Setup

Kalau kali pertama deploy kat VPS baru:

```bash
# Clone repo
git clone https://github.com/.../iunikeb.git /var/www/iunikeb.com.my/public_html

# Setup env
cp .env.example .env
# — edit .env with production values —

# Install deps & build
composer install --no-dev --optimize-autoloader
npm ci && npm run build
php artisan key:generate
php artisan storage:link

# Setup permissions
chown -R admin:admin .
chown www-data:www-data database/database.sqlite
chmod 664 database/database.sqlite
chmod -R 775 storage bootstrap/cache

# Migrate & seed
php artisan migrate --force
php artisan db:seed --force

# Cache
php artisan config:cache
php artisan route:cache
```
