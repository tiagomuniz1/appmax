#!/usr/bin/env bash
set -e

echo "Setting up test environment..."

# loads .env.testing variables
if [ ! -f .env.testing ]; then
  echo ".env.testing not found"
  exit 1
fi

set -a
source .env.testing
set +a

echo "Waiting for MySQL to be ready..."

until docker compose exec -T mysql mysqladmin ping \
  -h"$DB_HOST" -P"$DB_PORT" \
  -u"$DB_USERNAME" -p"$DB_PASSWORD" --silent; do
  sleep 2
done

echo "Creating database (if not exists yet)..."

docker compose exec -T mysql \
  mysql -h"$DB_HOST" -P"$DB_PORT" \
  -u"$DB_USERNAME" -p"$DB_PASSWORD" \
  -e "CREATE DATABASE IF NOT EXISTS \`${DB_DATABASE}\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

echo "Running migrations..."

docker compose exec -T app \
  php artisan migrate --env=testing --force

echo "Done!"
