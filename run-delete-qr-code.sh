#!/bin/sh

# Run the Laravel scheduler
while true; do
  php /var/www/html/artisan generate:delete-qr-codes
  sleep 60 # Sleep for 1 day
done