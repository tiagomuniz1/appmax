#!/usr/bin/env bash
set -e

echo "building containers..."
docker compose down
docker compose build --no-cache
docker compose up -d

echo "Waiting for mysql set up..."
until docker compose exec -T mysql mysqladmin ping -h "localhost" --silent; do
  sleep 2
done

echo "Installing PHP dependencies (composer install)..."
docker compose exec app composer install

# Generates APP_KEY if it does not exists
if ! docker compose exec app php artisan key:status >/dev/null 2>&1; then
  echo "Generating APP_KEY..."
  docker compose exec app php artisan key:generate
else
  echo "APP_KEY already exists."
fi

echo "Running migrations..."
docker compose exec app php artisan migrate --force

echo ""
echo "Done!"
echo "Access: http://localhost:8000"
echo ""
echo "To start the server in the container:"
echo "docker compose exec app php artisan serve --host=0.0.0.0 --port=8000"
