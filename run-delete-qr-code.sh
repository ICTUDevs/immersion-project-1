# Calculate the number of seconds to sleep until 7:30 AM Philippine time
current_time=$(date +%s)
target_time=$(TZ=Asia/Manila date -d "07:30" +%s)
if [ $current_time -gt $target_time ]; then
  target_time=$(TZ=Asia/Manila date -d "tomorrow 07:30" +%s)
fi
sleep_seconds=$((target_time - current_time))

# Sleep until the target time
sleep $sleep_seconds


# Delete the QR codes

while true; do
  php /var/www/html/artisan generate:delete-qr-codes
  sleep 86400 # Sleep for 1 day
done