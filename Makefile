export COMPOSE_DOCKER_CLI_BUILD = 1
export DOCKER_BUILDKIT = 1

include .env

SHELL := /bin/bash

DOCKER_COMPOSE_FILE = docker-compose.yml
DOCKER_COMPOSE_APP_CONTAINER = php

CURRENT_USER_ID = $(shell id --user)
CURRENT_USER_GROUP_ID = $(shell id --group)
CURRENT_DIR = $(shell pwd)

build:
	@docker compose --file ${DOCKER_COMPOSE_FILE} build --pull

run:
	@docker compose --file ${DOCKER_COMPOSE_FILE} up --detach

stop:
	@docker compose --file ${DOCKER_COMPOSE_FILE} stop

restart: stop run

shell:
	@docker compose --file ${DOCKER_COMPOSE_FILE} exec --user "${CURRENT_USER_ID}:${CURRENT_USER_GROUP_ID}" ${DOCKER_COMPOSE_APP_CONTAINER} bash

test:
	@docker compose --file ${DOCKER_COMPOSE_FILE} exec --user "${CURRENT_USER_ID}:${CURRENT_USER_GROUP_ID}" ${DOCKER_COMPOSE_APP_CONTAINER} composer test

fix:
	@docker compose --file ${DOCKER_COMPOSE_FILE} exec --user "${CURRENT_USER_ID}:${CURRENT_USER_GROUP_ID}" ${DOCKER_COMPOSE_APP_CONTAINER} bash -c 'composer csf'

queue:
	@docker compose --file ${DOCKER_COMPOSE_FILE} exec --user "${CURRENT_USER_ID}:${CURRENT_USER_GROUP_ID}" ${DOCKER_COMPOSE_APP_CONTAINER} php artisan queue:work

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

encrypt-prod-env:
	@docker compose --file ${DOCKER_COMPOSE_FILE} run \
	--rm \
	--no-deps \
	--volume ${CURRENT_DIR}/environment/prod/deployment/prod:/envs \
	--entrypoint "" \
	--workdir /application \
	--user "${CURRENT_USER_ID}:${CURRENT_USER_GROUP_ID}" \
	${DOCKER_COMPOSE_APP_CONTAINER} \
	bash -c "cp /envs/.env.prod /application \
		&& php artisan env:encrypt --env prod --key ${PROD_ENV_KEY} \
		&& mv .env.prod.encrypted /envs \
		&& rm .env.prod"

decrypt-prod-env:
	@docker compose --file ${DOCKER_COMPOSE_FILE} run \
	--rm \
	--no-deps \
	--volume ${CURRENT_DIR}/environment/prod/deployment/prod:/envs \
	--entrypoint "" \
	--workdir /application \
	--user "${CURRENT_USER_ID}:${CURRENT_USER_GROUP_ID}" \
	${DOCKER_COMPOSE_APP_CONTAINER} \
	bash -c "cp /envs/.env.prod.encrypted /application \
		&& php artisan env:decrypt --env prod --key ${PROD_ENV_KEY} \
		&& mv .env.prod /envs \
		&& rm .env.prod.encrypted"

.PHONY: build run stop restart shell test fix queue encrypt-beta-env decrypt-beta-env encrypt-prod-env decrypt-prod-env
