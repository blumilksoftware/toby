export COMPOSE_DOCKER_CLI_BUILD = 1
export DOCKER_BUILDKIT = 1

DOCKER_COMPOSE_FILENAME = docker-compose.prod.yml
DOCKER_COMPOSE_APP_CONTAINER = toby-prod-app

DOCKER_EXEC_SCRIPT = docker compose --file ${DOCKER_COMPOSE_FILENAME} exec --workdir /application/environment/prod/deployment/scripts ${DOCKER_COMPOSE_APP_CONTAINER} bash
DOCKER_EXEC_ARTISAN_COMMAND = docker compose --file ${DOCKER_COMPOSE_FILENAME} exec ${DOCKER_COMPOSE_APP_CONTAINER} php artisan

PROD_DOCKER_IMAGE = ghcr.io/blumilksoftware/toby:latest

CURRENT_DIR = $(shell pwd)

prod-deploy: decrypt-prod-env create-deployment-file
	@docker compose --file ${DOCKER_COMPOSE_FILENAME} up --force-recreate --detach && \
	echo "App post deploy actions" && \
	${DOCKER_EXEC_SCRIPT} post-deploy-actions.sh

decrypt-prod-env:
	@docker run \
	--rm \
	--volume ${CURRENT_DIR}:/envs \
	--entrypoint "" \
	--workdir /application \
	${PROD_DOCKER_IMAGE} \
	bash -c "cp /envs/.env.prod.encrypted /application \
		&& php artisan env:decrypt --env prod --key ${PROD_ENV_KEY} \
		&& cp .env.prod /envs/.env.prod \
		&& cp .env.prod /envs/.env"

DEPLOYMENT_DATETIME = $(shell TZ=Europe/Warsaw date --rfc-3339=seconds)

create-deployment-file:
	@echo "\
	DEPLOY_DATE='${DEPLOYMENT_DATETIME}'\
	" > .deployment

.PHONY: prod-deploy decrypt-prod-env create-deployment-file
