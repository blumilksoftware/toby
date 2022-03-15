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
* APP_DEBUG=false
* APP_ENV=production
* APP_KEY=
* APP_NAME="Toby HR application"
* APP_URL=
* ASSET_URL=
* BROADCAST_DRIVER=log
* CACHE_DRIVER=redis
* DATABASE_URL=
* DB_CONNECTION=pgsql
* DB_DATABASE=
* DB_HOST=
* DB_PORT=
* DB_USER=
* FILESYSTEM_DISK=local
* GOOGLE_CALENDAR_ID=
* GOOGLE_CLIENT_ID=
* GOOGLE_CLIENT_SECRET=
* GOOGLE_CREDENTIALS=
* GOOGLE_REDIRECT=
* LOG_CHANNEL=errorlog
* LOG_LEVEL=info
* MAIL_MAILER=log
* QUEUE_CONNECTION=redis
* REDIS_URL=
* SESSION_DRIVER=redis
* SESSION_LIFETIME=120