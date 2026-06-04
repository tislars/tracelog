#!/usr/bin/env bash
# ──────────────────────────────────────────────────────────────────────────────
# seed.sh — Install WordPress and seed demo content via WP-CLI in Docker.
#
# Run from the project root:
#   bash apps/cms/bin/seed.sh
#   make db-seed
# ──────────────────────────────────────────────────────────────────────────────
set -euo pipefail

DC="docker compose -f apps/cms/docker-compose.yml"
WP="$DC exec -T -w /var/www/html php wp"

# ── Wait for DB ───────────────────────────────────────────────────────────────

printf "Waiting for database"
until $DC exec -T db mysqladmin ping -h localhost -uroot -prootpassword --silent 2>/dev/null; do
  printf "."
  sleep 2
done
echo " ready"

# ── Install WordPress ─────────────────────────────────────────────────────────

if $WP core is-installed 2>/dev/null; then
  echo "WordPress already installed, skipping core install."
else
  echo "Installing WordPress..."
  $WP core install \
    --url="http://localhost:8080" \
    --title="Headless WP PoC" \
    --admin_user="admin" \
    --admin_password="admin" \
    --admin_email="admin@example.com" \
    --skip-email
  echo "WordPress installed."
fi

# ── Activate plugins ──────────────────────────────────────────────────────────

echo "Activating plugins..."
$WP plugin activate --all 2>&1 || true

# ── Seed content ──────────────────────────────────────────────────────────────

echo "Seeding content..."
$WP eval-file /var/www/html/bin/seed-content.php

echo ""
echo "  WordPress:  http://localhost:8080"
echo "  Admin:      http://localhost:8080/wp/wp-admin"
echo "  Login:      admin / admin"
echo ""
