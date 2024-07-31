## Xdebug configuration guide
Xdebug comes pre-installed and activated by default to enhance your development experience.

### Adjusting Xdebug installation
If you choose not to install Xdebug, simply set the `DOCKER_INSTALL_XDEBUG` flag to `false` in your `.env` file. After this change, you will need to rebuild your application's container using the commands `make build` and `make run`.

For fine-tuning Xdebug's behavior, configuration parameters can be adjusted in the `docker/dev/app/php.ini` file. Comprehensive details on available settings can be found in the [Xdebug documentation](https://xdebug.org/docs/all_settings).

### Disabling Xdebug
For scenarios where Xdebug is not required, it can be completely deactivated in two ways:
* by setting the `xdebug.mode` directive to `off` in your PHP configuration file
* through specifying the environment variable `XDEBUG_MODE=off`

This adjustment is particularly useful for optimizing performance during specific tasks, such as testing. Detailed information on the mode setting is available in the [Xdebug documentation](https://xdebug.org/docs/all_settings#mode).

#### CLI
To disable Xdebug for CLI operations, use the following command:
```
XDEBUG_MODE=off php artisan test
```

#### Docker container
When running tests within a Docker container, disable Xdebug like so:
```
docker compose run --rm -e XDEBUG_MODE=off php php artisan test
```
