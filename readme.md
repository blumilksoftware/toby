![He's watching you](toby.png)

# @blumilksoftware/toby
> HR software you love to hate. :)

## Local development
### Prerequisites
- make
- docker and docker compose v2

### Setup
Run following commands:
```
cp .env.example .env
make init
make shell
  # inside container
  npm run dev
```

Next, place google credentials in `/google-credentials.json` (check [how to obtain the credentials](https://github.com/spatie/laravel-google-calendar#how-to-obtain-the-credentials-to-communicate-with-google-calendar)).

Application will be running under [localhost:8751](localhost:8751) and [http://toby.blumilk.localhost/](http://toby.blumilk.localhost/) in Blumilk traefik environment. If you don't have a Blumilk traefik environment set up yet, follow the instructions from this [repository](https://github.com/blumilksoftware/environment).

### Available commands

| Command           | Task                               |
|:------------------|:-----------------------------------|
| `make shell`      | Runs application shell             |
| `make shell-root` | Runs application shell as root     |
| `make queue`      | Runs queue worker                  |
| `make test`       | Runs test suite                    |
| `make dev`        | Runs development mode for frontend |
| `make cs`         | Runs codestyle checks              |
| `make fix`        | Runs codestyle fixers              |

### Docker image

App images will be accessible under the following tags:

Beta:
- `registry.blumilk.pl/internal-public/toby:beta`

Prod:
- `registry.blumilk.pl/internal-public/toby:latest`
- `registry.blumilk.pl/internal-public/toby:v1.2.3` (depends on releases/tags)


### Further reading
* [Xdebug configuration](./readme.xdebug.md)
* [sops configuration](./readme.sops.md)
