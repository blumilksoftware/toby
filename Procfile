web: vendor/bin/heroku-php-nginx -C environment/prod/nginx.conf public/
release: php artisan config:cache && php artisan route:cache && php artisan migrate --force
worker: php artisan queue:work
