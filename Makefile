DOCKER_COMPOSE_BETA_FILENAME = docker-compose.beta.yml
BETA_DOCKER_EXEC = docker compose --file ${DOCKER_COMPOSE_BETA_FILENAME} exec --workdir /application/environment/scripts

DOCKER_COMPOSE_PROD_FILENAME = docker-compose.prod.yml
PROD_DOCKER_EXEC = docker compose --file ${DOCKER_COMPOSE_PROD_FILENAME} exec --workdir /application/environment/scripts

export COMPOSE_DOCKER_CLI_BUILD = 1
export DOCKER_BUILDKIT = 1

beta-artisan:
	echo "Running: php artisan ${ARTISAN_ARGS}" && \
	docker compose --file ${DOCKER_COMPOSE_BETA_FILENAME} exec toby-beta-app php artisan ${ARTISAN_ARGS}

beta-deploy: create-deployment-file
	docker compose --file ${DOCKER_COMPOSE_BETA_FILENAME} up --force-recreate --detach && \
	echo "App post deploy actions" && \
	${BETA_DOCKER_EXEC} toby-beta-app bash post-deploy-actions.sh

beta-reload-config:
	echo "App config reload" && \
	${BETA_DOCKER_EXEC} toby-beta-app bash reload-config.sh

prod-deploy: create-deployment-file
	docker compose --file ${DOCKER_COMPOSE_PROD_FILENAME} up --force-recreate --detach && \
	echo "App post deploy actions" && \
	${PROD_DOCKER_EXEC} toby-prod-app bash post-deploy-actions.sh

prod-reload-config:
	echo "App config reload" && \
	${PROD_DOCKER_EXEC} toby-prod-app bash reload-config.sh


DEPLOYMENT_PROJECT_VERSION = $(shell ./environment/scripts/version.sh --long)
DEPLOYMENT_DATETIME = $(shell TZ=Europe/Warsaw date --rfc-3339=seconds)

create-deployment-file:
	@echo "\
	DEPLOY_DATE='${DEPLOYMENT_DATETIME}'\n\
	DEPLOY_VERSION='${DEPLOYMENT_PROJECT_VERSION}'\
	" > .deployment

.PHONY: beta-deploy beta-reload-config prod-deploy prod-reload-config create-deployment-file beta-artisan
