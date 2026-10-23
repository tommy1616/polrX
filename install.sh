#!/usr/bin/env bash
set -euo pipefail

cd "$(dirname "$0")"

echo "========================================================"
echo "  polrX — Bootstrap & Interactive Installer"
echo "========================================================"

if ! command -v php >/dev/null 2>&1; then
    echo "[ERROR] PHP is not installed or not in PATH."
    exit 1
fi

if [ ! -d "vendor" ]; then
    if ! command -v composer >/dev/null 2>&1; then
        echo "[ERROR] Composer is required to install dependencies."
        exit 1
    fi
    echo "-> Installing Composer dependencies..."
    composer install --no-dev -o
fi

mkdir -p storage/framework/{cache,sessions,views} storage/logs bootstrap/cache
chmod -R ug+rwX storage bootstrap/cache 2>/dev/null || true

php artisan polrx:install "$@"
