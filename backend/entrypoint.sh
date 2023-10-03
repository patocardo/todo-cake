#!/bin/bash

# Set permissions
chown -R www-data:www-data /app/tmp
chmod -R 775 /app/tmp

# Start php-fpm in the background
php-fpm &

# Keep container running
tail -f /dev/null
