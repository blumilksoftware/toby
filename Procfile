web: vendor/bin/heroku-php-nginx -C nginx.conf public/
release: php artisan migrate --force && php artisan db:seed --class=DemoSeeder --force && php artisan cache:clear && php artisan config:cache