#!/bin/bash
set -e

if [ -f .env ]; then
  DOMAIN=$(grep -E '^APP_DOMAIN=' .env | cut -d '=' -f2 | tr -d '"' | tr -d "'")
fi

if [ -z "$DOMAIN" ]; then
  echo "ERROR: APP_DOMAIN not found in .env"
  exit 1
fi

EMAIL="admin@$DOMAIN"
STAGING=0 # set to 1 for testing to avoid rate limits

echo "### Creating certbot volumes..."
docker compose up -d --no-deps certbot 2>/dev/null || true
docker compose down certbot 2>/dev/null || true

echo "### Creating dummy certificate for $DOMAIN..."
docker compose run --rm --entrypoint "\
  mkdir -p /etc/letsencrypt/live/$DOMAIN" certbot

docker compose run --rm --entrypoint "\
  openssl req -x509 -nodes -newkey rsa:4096 -days 1 \
    -keyout /etc/letsencrypt/live/$DOMAIN/privkey.pem \
    -out /etc/letsencrypt/live/$DOMAIN/fullchain.pem \
    -subj '/CN=localhost'" certbot

echo "### Starting nginx..."
docker compose up -d nginx

echo "### Deleting dummy certificate..."
docker compose run --rm --entrypoint "\
  rm -rf /etc/letsencrypt/live/$DOMAIN && \
  rm -rf /etc/letsencrypt/archive/$DOMAIN && \
  rm -rf /etc/letsencrypt/renewal/$DOMAIN.conf" certbot

echo "### Requesting Let's Encrypt certificate for $DOMAIN..."
if [ $STAGING != "0" ]; then
  STAGING_ARG="--staging"
fi

docker compose run --rm --entrypoint "\
  certbot certonly --webroot -w /var/www/certbot \
    ${STAGING_ARG:-} \
    --email $EMAIL \
    --rsa-key-size 4096 \
    --agree-tos \
    --no-eff-email \
    --force-renewal \
    -d $DOMAIN" certbot

echo "### Reloading nginx..."
docker compose exec nginx nginx -s reload

echo "### Done! SSL certificate installed for $DOMAIN"
