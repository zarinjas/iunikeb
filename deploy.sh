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

  echo "--- Bersihkan merge conflict lama & pull ---"
  git merge --abort 2>/dev/null || true
  git checkout --theirs database/database.sqlite 2>/dev/null || true
  git clean -fd database/database.sqlite 2>/dev/null || true
  git stash push -m "auto-stash-sebelum-deploy" 2>/dev/null || true
  git pull origin $BRANCH
  git stash pop 2>/dev/null || true

  echo "--- Install PHP deps ---"
  composer install --no-dev --optimize-autoloader 2>/dev/null || true

  echo "--- Build frontend ---"
  npm ci && npm run build

  echo "--- Migration ---"
  php artisan migrate --force

  echo "--- Cache ---"
  php artisan optimize:clear
  php artisan config:cache
  php artisan route:cache

  echo "--- Storage link ---"
  php artisan storage:link --force

  echo "--- Final permissions ---"
  chown -R admin:admin .
  chmod -R 775 storage bootstrap/cache

  echo "✓ Deploy selesai!"
EOF
