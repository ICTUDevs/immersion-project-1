#!/bin/sh

# Set the timezone to Asia/Manila
export TZ=Asia/Manila

# Run the Laravel scheduler
while true; do
  php /var/www/html/artisan generate:qr-codes
  sleep 20  # Sleep for 20 seconds
done