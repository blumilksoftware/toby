# Toby

HR software you love to hate

## Local setup

> `dcr` is an alias to `docker-compose run --rm -u "$(id -u):$(id -g)"`

- clone the repository
- initialize `.env` file and customize if needed

      cp .env.example .env

- build containers

      docker-compose build --no-cache --pull

- run containers

      docker-compose up -d

- install composer packages

      dcr php composer install

- generate app key

      dcr php php artisan key:generate

- migrate and seed database

      dcr php php artisan migrate --seed

- build assets

      dcr node npm run dev

## Available containers (local)
- **php** - php and composer stuff
- **node** - npm stuff
- **mysql** - database for local development
- **mailhog** - for emails preview

## Running tests
If xDebug is installed, set environment variable **XDEBUG_MODE=off** to improve performance

      dcr -e XDEBUG_MODE=off php php artisan test


## Code style check
      dcr php php vendor/bin/ecs check
      dcr php composer ecs
      dcr php php vendor/bin/ecs check --fix
      dcr php composer ecsf

## xDebug

To use xDebug you need to set `DOCKER_INSTALL_XDEBUG` to `true` in `.env` file.\
Then rebuild php container `docker-compose up --build -d php`.\
You can also set up xDebug params (see docs https://xdebug.org/docs/all_settings) in `docker/dev/php/php.ini` file:

Default values for xDebug:
```
xdebug.client_host=host.docker.internal
xdebug.client_port=9003
xdebug.mode=debug
xdebug.start_with_request=yes
xdebug.log_level=0
```

### Disable xDebug
it is possible to disable the Xdebug completely by setting the option **xdebug.mode** to **off**,
or by setting the environment variable **XDEBUG_MODE=off**\
See docs (https://xdebug.org/docs/all_settings#mode)

CLI:
```
XDEBUG_MODE=off php artisan test
```
Docker container:
```
docker-compose run --rm -e XDEBUG_MODE=off php php artisan test
```