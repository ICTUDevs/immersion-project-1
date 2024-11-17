#!/bin/sh

# Run the Laravel scheduler
while true; do
  php /var/www/html/artisan generate:qr-codes
  sleep 60  # Sleep for 5 seconds
done