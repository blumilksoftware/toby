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

### Prerequisites
- make
- docker and docker compose v2

```
cp .env.example .env
make init
make shell
  # inside container
  npm run dev
```

- place google credentials here: `/google-credentials.json` ([how to obtain the credentials](https://github.com/spatie/laravel-google-calendar#how-to-obtain-the-credentials-to-communicate-with-google-calendar))

_Check **Makefile** for available commands._

### Available containers (local)

- **app** - nginx HTTP server + php-fpm + node
- **database** - Postgres database for local development
- **mailpit** - for emails preview
- **selenium** - for automated tests
- **redis** - for session/cache store

### Shell in app container

```shell
make shell
```
```shell
make shell-root
```

### Queue worker

```shell
make queue
```

### Running tests

```shell
make test
```

### Code style check
```shell
make cs
```
```shell
make fix
```
---
### Xdebug

Xdebug is enabled and installed by default.

You can set `DOCKER_INSTALL_XDEBUG` to `false` in `.env` file, to not install it.\
Then rebuild app container `make build && make run`.
* You can also set up xDebug params (see docs https://xdebug.org/docs/all_settings) in `docker/dev/app/php.ini` file:

#### Disable Xdebug

* It is possible to disable the Xdebug completely by setting the option **xdebug.mode** to **off**, or by setting the environment variable **XDEBUG_MODE=off**.
* See docs: (https://xdebug.org/docs/all_settings#mode)

CLI:

```
XDEBUG_MODE=off php artisan test
```

Docker container:

```
docker compose run --rm -e XDEBUG_MODE=off php php artisan test
```

---
### Encrypt/Decrypt envs
- sops, age and age-keygen are bundled in the app container
- for decryption secret key is used, which is defined in the `.env` file
- for encryption public key is used, which is defined in the `.sops.yaml` file
- age public key and secret key depends on each other

**Beta**
- set `SOPS_AGE_BETA_SECRET_KEY` in `.env` file, which will be used to decrypt.

decrypt:
```shell
# this will decrypt .env.beta.secrets and create/override .env.beta.secrets.decrypted file
make decrypt-beta-secrets
```

encrypt:
```shell
# this will encrypt .env.beta.secrets.decrypted file and create/override .env.beta.secrets file
make encrypt-beta-secrets
```
Files are in `./environment/prod/deployment/beta`

---
**Production**
- set `SOPS_AGE_PROD_SECRET_KEY` in `.env` file, which will be used to decrypt.

decrypt:
```shell
# this will decrypt .env.prod.secrets and create/override .env.prod.secrets.decrypted file
make decrypt-prod-secrets
```

encrypt:
```shell
# this will encrypt .env.prod.secrets.decrypted file and create/override .env.prod.secrets file
make encrypt-prod-secrets
```
Files are in `./environment/prod/deployment/prod`
