export COMPOSE_DOCKER_CLI_BUILD = 1
export DOCKER_BUILDKIT = 1

DOCKER_COMPOSE_FILENAME = docker-compose.beta.yml
DOCKER_COMPOSE_APP_CONTAINER = toby-beta-app

DOCKER_EXEC_SCRIPT = docker compose --file ${DOCKER_COMPOSE_FILENAME} exec --workdir /application/environment/prod/deployment/scripts ${DOCKER_COMPOSE_APP_CONTAINER} bash
DOCKER_EXEC_ARTISAN_COMMAND = docker compose --file ${DOCKER_COMPOSE_FILENAME} exec ${DOCKER_COMPOSE_APP_CONTAINER} php artisan

BETA_DOCKER_IMAGE = ghcr.io/blumilksoftware/toby:beta

CURRENT_DIR = $(shell pwd)

beta-artisan:
	echo "Running: php artisan ${ARTISAN_ARGS}" && \
	${DOCKER_EXEC_ARTISAN_COMMAND} ${ARTISAN_ARGS}

beta-deploy: decrypt-beta-env create-deployment-file
	@docker compose --file ${DOCKER_COMPOSE_FILENAME} up --force-recreate --detach && \
	echo "App post deploy actions" && \
	${DOCKER_EXEC_SCRIPT} post-deploy-actions.sh

decrypt-beta-env:
	@docker run \
	--rm \
	--volume ${CURRENT_DIR}:/envs \
	--entrypoint "" \
	--workdir /application \
	${BETA_DOCKER_IMAGE} \
	bash -c "cp /envs/.env.beta.encrypted /application \
		&& php artisan env:decrypt --env beta --key ${BETA_ENV_KEY} \
		&& cp .env.beta /envs/.env.beta \
		&& cp .env.beta /envs/.env"

DEPLOYMENT_DATETIME = $(shell TZ=Europe/Warsaw date --rfc-3339=seconds)

create-deployment-file:
	@echo "\
	DEPLOY_DATE='${DEPLOYMENT_DATETIME}'\
	" > .deployment

.PHONY: beta-artisan beta-deploy decrypt-beta-env create-deployment-file
