#!/bin/bash
set -e

BRANCH="main"
VPS_HOST="tencentcloud"
REMOTE_PATH="/var/www/iunikeb.com.my/public_html"

echo "=== 1. Push ke GitHub ==="
read -p "Mesej commit: " MSG
git add .
git commit -m "$MSG" || echo "Tiada perubahan baru"
git push origin "$BRANCH"

echo "=== 2. Deploy kat VPS ==="
ssh "$VPS_HOST" << EOF
  set -e
  cd $REMOTE_PATH
  git config --global --add safe.directory $REMOTE_PATH

  echo "--- Bersihkan hot file ---"
  rm -f public/hot

  echo "--- Backup uploaded files ---"
  BACKUP_DIR="/tmp/deploy-storage-backup"
  rm -rf "$BACKUP_DIR"
  mkdir -p "$BACKUP_DIR"
  if [ -d storage/app/public ]; then
    rsync -a storage/app/public/ "$BACKUP_DIR/" --exclude='.gitignore' 2>/dev/null || true
    FILE_COUNT=$(find "$BACKUP_DIR" -type f 2>/dev/null | wc -l)
    echo "  ✓ Backup selesai: $FILE_COUNT fail disimpan"
  else
    echo "  ⚠️ storage/app/public tidak wujud, backup dilangkau"
  fi

  echo "--- Backup database ---"
  cp database/database.sqlite database/database.sqlite.bak 2>/dev/null || true

  echo "--- Sync dengan GitHub (reset-hard) ---"
  git merge --abort 2>/dev/null || true
  git stash push -m "auto-stash-sebelum-deploy" 2>/dev/null || true
  git fetch origin $BRANCH
  git reset --hard origin/$BRANCH
  git stash pop 2>/dev/null || true

  echo "--- Pulihkan database jika backup ada ---"
  if [ -f database/database.sqlite.bak ] && [ ! -s database/database.sqlite ]; then
    mv database/database.sqlite.bak database/database.sqlite
    echo "  Database dipulihkan dari backup."
  else
    rm -f database/database.sqlite.bak
  fi

  echo "--- Install PHP deps ---"
  composer install --no-dev --optimize-autoloader 2>/dev/null || true

  echo "--- Build frontend ---"
  npm ci && npm run build
  rm -f public/hot

  echo "--- Migration ---"
  php artisan migrate --force

  echo "--- Seed data ---"
  php artisan db:seed --force 2>&1 || echo "  ⚠️ Seeder ada isu (kemungkinan data dah wujud, bukan error)"
  echo "  ✓ Seed selesai"

  echo "--- Cache ---"
  php artisan optimize:clear
  php artisan config:cache
  php artisan route:cache

  echo "--- Storage link ---"
  php artisan storage:link --force

  echo "--- Restore uploaded files ---"
  if [ -d "$BACKUP_DIR" ] && [ "$(find "$BACKUP_DIR" -type f 2>/dev/null | wc -l)" -gt 0 ]; then
    rsync -a "$BACKUP_DIR/" storage/app/public/ 2>/dev/null || true
    FILE_COUNT=$(find "$BACKUP_DIR" -type f 2>/dev/null | wc -l)
    echo "  ✓ Restore selesai: $FILE_COUNT fail dipulihkan"
  else
    echo "  ⚠️ Tiada backup untuk diretore"
  fi
  rm -rf "$BACKUP_DIR" 2>/dev/null || true

  echo "--- Final permissions ---"
  chown -R admin:admin .
  chown www-data:www-data database/database.sqlite
  chmod 664 database/database.sqlite
  chmod -R 775 storage bootstrap/cache

  echo "✓ Deploy selesai!"
EOF
