#!/usr/bin/env bash
# Redeploy do FeiraemAcao — correr NO SERVIDOR, dentro de /var/www/feiraemacao,
# depois do primeiro deploy manual (ver docs/11-deploy-producao.md).
# Uso: ./deploy/deploy.sh
set -euo pipefail

echo "== A atualizar código (git pull) =="
git pull origin main

echo "== A instalar dependências PHP =="
composer install --no-dev --optimize-autoloader

echo "== A instalar dependências JS e a compilar assets =="
npm ci
npm run build

echo "== A colocar em modo de manutenção =="
php artisan down || true

echo "== Migrations =="
php artisan migrate --force

echo "== A limpar e reconstruir caches =="
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "== A sair do modo de manutenção =="
php artisan up

echo "== A reiniciar a fila e o PHP-FPM =="
sudo systemctl restart feiraemacao-queue
sudo systemctl reload php8.3-fpm

echo "== Concluído =="
