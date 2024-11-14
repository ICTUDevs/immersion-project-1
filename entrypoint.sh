#!/bin/sh

# Start php-fpm in the background
php-fpm &

# Start supervisord
/usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf