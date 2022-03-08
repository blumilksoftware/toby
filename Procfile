web: vendor/bin/heroku-php-nginx -C nginx.conf public/
release: php artisan migrate && php artisan db:seed --class=DemoSeeder && php artisan cache:clear && php artisan config:cache