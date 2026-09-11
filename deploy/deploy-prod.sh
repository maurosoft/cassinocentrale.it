#!/usr/bin/env bash
# ============================================================
#  Script di messa online (deploy) del B&B Cassino Centrale
#  Da lanciare sul server Hetzner, dentro la cartella del sito:
#     bash deploy/deploy-prod.sh
# ============================================================
set -e

echo "==> 1/7 Scarico gli ultimi aggiornamenti da GitHub..."
git pull origin main

echo "==> 2/7 Installo/aggiorno le dipendenze PHP (Composer)..."
composer install --no-dev --optimize-autoloader

echo "==> 3/7 Grafica compilata (se hai Node la ricompilo, altrimenti uso quella inclusa nel progetto)..."
if command -v npm >/dev/null 2>&1; then
    npm ci && npm run build
else
    echo "    Node non presente: uso la grafica gia' compilata (public/build). OK."
fi

echo "==> 4/7 Aggiorno il database (migrazioni)..."
php artisan migrate --force

echo "==> 5/7 Collego la cartella delle immagini caricate..."
php artisan storage:link || true

echo "==> 6/7 Pulisco e ricreo le cache..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "==> 7/7 Fatto! Sito aggiornato."
echo "    (Se usi le code/coda, ricordati di riavviare il worker.)"
