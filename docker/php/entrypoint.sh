#!/usr/bin/env bash
set -euo pipefail

APP_DIR="/var/www/html"
HASH_FILE="${APP_DIR}/storage/.deps-hash"
cd "${APP_DIR}"

log() {
    echo "[entrypoint] $1"
}

# -----------------------------------------------------------------
# 1. Tunggu MySQL siap menerima koneksi sebelum lanjut apa pun.
#    Tanpa ini, container app bisa start lebih dulu dari MySQL
#    dan bikin composer/artisan yang butuh DB gagal.
# -----------------------------------------------------------------
if [ -n "${DB_HOST:-}" ]; then
    log "Menunggu MySQL (${DB_HOST}:${DB_PORT:-3306}) siap..."
    tries=0
    until mysqladmin ping -h"${DB_HOST}" -P"${DB_PORT:-3306}" \
            -u"${DB_USERNAME}" -p"${DB_PASSWORD}" --silent 2>/dev/null; do
        tries=$((tries + 1))
        if [ "${tries}" -ge 30 ]; then
            log "MySQL tidak siap setelah 60 detik, tetap lanjut (cek log mysql)."
            break
        fi
        sleep 2
    done
    log "MySQL siap (atau timeout, lanjut)."
fi

# -----------------------------------------------------------------
# 2. Install dependency PHP & JS hanya jika lock file berubah.
#    Hash disimpan di storage/ (ikut bind mount, jadi persist).
# -----------------------------------------------------------------
mkdir -p "$(dirname "${HASH_FILE}")"
CURRENT_HASH="$(cat composer.lock package-lock.json 2>/dev/null | md5sum | awk '{print $1}')"
PREVIOUS_HASH="$(cat "${HASH_FILE}" 2>/dev/null || echo '')"

if [ "${CURRENT_HASH}" != "${PREVIOUS_HASH}" ] || [ ! -d vendor ] || [ ! -d public/build ]; then
    log "Dependency berubah (atau belum ada) - menjalankan composer install & npm build..."

    composer install --no-interaction --prefer-dist --optimize-autoloader

    if [ -f package.json ]; then
        npm ci --no-audit --no-fund
        npm run build
    fi

    echo "${CURRENT_HASH}" > "${HASH_FILE}"
else
    log "Dependency tidak berubah, skip composer/npm install."
fi

# -----------------------------------------------------------------
# 3. Pastikan .env ada. Kalau belum ada (misal fresh clone di VM),
#    salin dari .env.example lalu beri peringatan untuk diisi.
# -----------------------------------------------------------------
if [ ! -f .env ]; then
    log "PERINGATAN: .env tidak ditemukan, menyalin dari .env.example."
    log "Silakan sesuaikan .env (lihat bagian .env.docker di dokumentasi) lalu restart container."
    cp .env.example .env
fi

# -----------------------------------------------------------------
# 4. Generate APP_KEY jika masih kosong.
# -----------------------------------------------------------------
if ! grep -q "^APP_KEY=base64:" .env 2>/dev/null; then
    log "APP_KEY belum diset, generate key baru..."
    php artisan key:generate --force
fi

# -----------------------------------------------------------------
# 5. Rapikan folder storage/public agar bisa jadi symlink yang benar.
#    Kasus khusus project ini: public/storage sempat berupa folder
#    ASLI (bukan symlink) karena dibuat manual di Windows. Kalau
#    dibiarkan, `storage:link` akan gagal karena target sudah ada.
# -----------------------------------------------------------------
if [ -d public/storage ] && [ ! -L public/storage ]; then
    log "public/storage terdeteksi sebagai folder biasa (bukan symlink)."
    log "Memindahkan isinya ke storage/app/public lalu mengganti dengan symlink..."
    cp -rn public/storage/. storage/app/public/ 2>/dev/null || true
    rm -rf public/storage
fi

if [ ! -e public/storage ]; then
    php artisan storage:link || log "storage:link gagal, cek permission."
fi

# -----------------------------------------------------------------
# 6. Pastikan permission storage & bootstrap/cache benar untuk
#    www-data (proses PHP-FPM jalan sebagai www-data).
# -----------------------------------------------------------------
mkdir -p storage/framework/{cache,sessions,testing,views} storage/logs bootstrap/cache
chmod -R ug+rwX storage bootstrap/cache

log "Siap. Menjalankan: $*"
exec "$@"
