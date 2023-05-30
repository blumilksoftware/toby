export COMPOSE_DOCKER_CLI_BUILD = 1
export DOCKER_BUILDKIT = 1

include .env

SHELL := /bin/bash

DOCKER_COMPOSE_FILE = docker-compose.yml
DOCKER_COMPOSE_APP_CONTAINER = php

DOCKER_COMPOSE_PROD_FILENAME = docker-compose.prod.yml
PROD_DOCKER_EXEC = docker compose --file ${DOCKER_COMPOSE_PROD_FILENAME} exec --workdir /application/environment/scripts

CURRENT_USER_ID = $(shell id --user)
CURRENT_USER_GROUP_ID = $(shell id --group)
CURRENT_DIR = $(shell pwd)

prod-deploy:
	docker compose --file ${DOCKER_COMPOSE_PROD_FILENAME} up --force-recreate --detach && \
	echo "App post deploy actions" && \
	${PROD_DOCKER_EXEC} toby-prod-app bash post-deploy-actions.sh

prod-reload-config:
	echo "App config reload" && \
	${PROD_DOCKER_EXEC} toby-prod-app bash reload-config.sh

shell:
	@docker compose --file ${DOCKER_COMPOSE_FILE} exec --user "${CURRENT_USER_ID}:${CURRENT_USER_GROUP_ID}" ${DOCKER_COMPOSE_APP_CONTAINER} bash

encrypt-beta-env:
	@docker compose --file ${DOCKER_COMPOSE_FILE} run \
	--rm \
	--no-deps \
	--volume ${CURRENT_DIR}/environment/prod/deployment/beta:/envs \
	--entrypoint "" \
	--workdir /application \
	--user "${CURRENT_USER_ID}:${CURRENT_USER_GROUP_ID}" \
	${DOCKER_COMPOSE_APP_CONTAINER} \
	bash -c "cp /envs/.env.beta /application \
		&& php artisan env:encrypt --env beta --key ${BETA_ENV_KEY} \
		&& mv .env.beta.encrypted /envs \
		&& rm .env.beta"

decrypt-beta-env:
	@docker compose --file ${DOCKER_COMPOSE_FILE} run \
	--rm \
	--no-deps \
	--volume ${CURRENT_DIR}/environment/prod/deployment/beta:/envs \
	--entrypoint "" \
	--workdir /application \
	--user "${CURRENT_USER_ID}:${CURRENT_USER_GROUP_ID}" \
	${DOCKER_COMPOSE_APP_CONTAINER} \
	bash -c "cp /envs/.env.beta.encrypted /application \
		&& php artisan env:decrypt --env beta --key ${BETA_ENV_KEY} \
		&& mv .env.beta /envs \
		&& rm .env.beta.encrypted"

.PHONY: prod-deploy prod-reload-config beta-artisan
