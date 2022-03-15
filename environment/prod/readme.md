# Heroku deployment

## Addons

* Heroku Postgres
* Heroku Redis
    
## Dynos

* web (app)
* worker (queue worker)

## Buildpacks
* heroku/php
* heroku/nodejs
* https://github.com/buyersight/heroku-google-application-credentials-buildpack.git

## Config vars
* APP_DEBUG
* APP_ENV
* APP_KEY
* APP_NAME
* APP_URL
* ASSET_URL
* BROADCAST_DRIVER
* CACHE_DRIVER
* DATABASE_URL
* DB_CONNECTION
* DB_DATABASE
* DB_HOST
* DB_PORT
* DB_USER
* FILESYSTEM_DISK
* GOOGLE_CALENDAR_ID
* GOOGLE_CLIENT_ID
* GOOGLE_CLIENT_SECRET
* GOOGLE_CREDENTIALS
* GOOGLE_REDIRECT
* LOG_CHANNEL
* LOG_LEVEL
* MAIL_MAILER
* QUEUE_CONNECTION
* REDIS_URL
* SESSION_DRIVER
* SESSION_LIFETIME