![He's watching you](toby.png)

# Toby

> HR software you love to hate

## Architecture
Directory structure little differs from a standard Laravel tree. We decided to refactor main `app` directory to better suit our needs. All classes are grouped in four major categories:
* `app/Architecture` for all framework-related stuff like service providers, exception handler and more;
* `app/Domain` for all framework-agnostic services related to the business logic of the application;
* `app/Eloquent` for all database/ORM-related classes like models, observers and scopes;
* `app/Infrastructure` for entry points to the application: CLI, HTTP and async ones.

## Local setup
- run `sh setup` or:

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

- generate storage link

      dcr php php artisan storage:link

- migrate and seed database

      dcr php php artisan migrate --seed

- install npm packages

      dcr node npm install

- build assets

      dcr node npm run dev

- place Google credentials here: `/google-credentials.json` ([how to obtain the credentials](https://github.com/spatie/laravel-google-calendar#how-to-obtain-the-credentials-to-communicate-with-google-calendar))

For now, you can login to the application only via Google. To do this, fill your e-mail address in field `LOCAL_EMAIL_FOR_LOGIN_VIA_GOOGLE` in `.env` file. Next step is to create `GOOGLE_CLIENT_ID` and `GOOGLE_CLIENT_SECRET` - this can be done via Google Developers Console.

### Available containers (local)

- **web** - nginx HTTP server
- **php** - php and composer stuff
- **node** - npm stuff
- **pgsql** - database for local development
- **mailhog** - for emails preview

### Running tests

If xDebug is installed, set environment variable **XDEBUG_MODE=off** to improve performance

      dcr -e XDEBUG_MODE=off php php artisan test

To run test in Cypress, install Cypress locally 

      npm install cypress

After that run Cypress

      cypress open

### Code style check

      dcr php composer cs
      dcr php composer csf
      dcr node npm run lint
      dcr node npm run lintf

### xDebug

* To use xDebug you need to set `DOCKER_INSTALL_XDEBUG` to `true` in `.env` file.
* Then rebuild php container `docker-compose up --build -d php`.
* You can also set up xDebug params (see docs https://xdebug.org/docs/all_settings) in `docker/dev/php/php.ini` file:

Default values for xDebug:

```
xdebug.client_host=host.docker.internal
xdebug.client_port=9003
xdebug.mode=debug
xdebug.start_with_request=yes
xdebug.log_level=0
```

#### Disable xDebug

* It is possible to disable the Xdebug completely by setting the option **xdebug.mode** to **off**, or by setting the environment variable **XDEBUG_MODE=off**.
* See docs: (https://xdebug.org/docs/all_settings#mode)

CLI:

```
XDEBUG_MODE=off php artisan test
```

Docker container:

```
docker-compose run --rm -e XDEBUG_MODE=off php php artisan test
```
