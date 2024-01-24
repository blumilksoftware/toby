export COMPOSE_DOCKER_CLI_BUILD = 1
export DOCKER_BUILDKIT = 1

MAKEFLAGS += --no-print-directory

include .env

SHELL := /bin/bash

DOCKER_COMPOSE_FILE = docker-compose.yml
DOCKER_COMPOSE_APP_CONTAINER = app
DOCKER_COMPOSE_DATABASE_CONTAINER = database

CURRENT_USER_ID = $(shell id --user)
CURRENT_USER_GROUP_ID = $(shell id --group)
CURRENT_DIR = $(shell pwd)

DATABASE_USERNAME=toby
TEST_DATABASE_NAME=toby-test

init: check-env-file
	@$(MAKE) build \
    && $(MAKE) run \
	&& docker compose --file ${DOCKER_COMPOSE_FILE} exec --user "${CURRENT_USER_ID}:${CURRENT_USER_GROUP_ID}" ${DOCKER_COMPOSE_APP_CONTAINER} bash "./environment/dev/scripts/init.sh" \
	&& $(MAKE) create-test-db

check-env-file:
	@if [ ! -f ".env" ]; then \
	  echo "Create .env file and adjust." ;\
	  exit 1;\
	fi; \

build:
	@docker compose --file ${DOCKER_COMPOSE_FILE} build --pull

run:
	@docker compose --file ${DOCKER_COMPOSE_FILE} up --detach \
	&& sleep 2 && make unit-reload-config

stop:
	@docker compose --file ${DOCKER_COMPOSE_FILE} stop

restart: stop run

shell:
	@docker compose --file ${DOCKER_COMPOSE_FILE} exec --user "${CURRENT_USER_ID}:${CURRENT_USER_GROUP_ID}" ${DOCKER_COMPOSE_APP_CONTAINER} bash

shell-root:
	@docker compose --file ${DOCKER_COMPOSE_FILE} exec ${DOCKER_COMPOSE_APP_CONTAINER} bash

test:
	@docker compose --file ${DOCKER_COMPOSE_FILE} exec --user "${CURRENT_USER_ID}:${CURRENT_USER_GROUP_ID}" ${DOCKER_COMPOSE_APP_CONTAINER} composer test

fix:
	@docker compose --file ${DOCKER_COMPOSE_FILE} exec --user "${CURRENT_USER_ID}:${CURRENT_USER_GROUP_ID}" ${DOCKER_COMPOSE_APP_CONTAINER} bash -c 'composer csf'
	@docker compose --file ${DOCKER_COMPOSE_FILE} exec --user "${CURRENT_USER_ID}:${CURRENT_USER_GROUP_ID}" ${DOCKER_COMPOSE_APP_CONTAINER} npm run lintf

queue:
	@docker compose --file ${DOCKER_COMPOSE_FILE} exec --user "${CURRENT_USER_ID}:${CURRENT_USER_GROUP_ID}" ${DOCKER_COMPOSE_APP_CONTAINER} php artisan queue:work

dev:
	@docker compose --file ${DOCKER_COMPOSE_FILE} exec --user "${CURRENT_USER_ID}:${CURRENT_USER_GROUP_ID}" ${DOCKER_COMPOSE_APP_CONTAINER} npm run dev

create-test-db:
	@docker compose --file ${DOCKER_COMPOSE_FILE} exec ${DOCKER_COMPOSE_DATABASE_CONTAINER} bash -c 'createdb --username=${DATABASE_USERNAME} ${TEST_DATABASE_NAME} &> /dev/null && echo "Created database for tests (${TEST_DATABASE_NAME})." || echo "Database for tests (${TEST_DATABASE_NAME}) exists."'

unit-reload-config:
	@echo "Reloading Nginx Unit config..." \
	&& docker compose --file ${DOCKER_COMPOSE_FILE} exec ${DOCKER_COMPOSE_APP_CONTAINER} curl -X PUT --data-binary @/application/environment/dev/app/nginx-unit.config.json --unix-socket /var/run/control.unit.sock http://localhost/config

encrypt-beta-secrets:
	@$(MAKE) encrypt-secrets SECRETS_ENV=beta

decrypt-beta-secrets:
	@$(MAKE) decrypt-secrets SECRETS_ENV=beta AGE_SECRET_KEY=${SOPS_AGE_BETA_SECRET_KEY}

encrypt-prod-secrets:
	@$(MAKE) encrypt-secrets SECRETS_ENV=prod

decrypt-prod-secrets:
	@$(MAKE) decrypt-secrets SECRETS_ENV=prod AGE_SECRET_KEY=${SOPS_AGE_PROD_SECRET_KEY}

decrypt-secrets:
	@docker compose --file ${DOCKER_COMPOSE_FILE} exec --user "${CURRENT_USER_ID}:${CURRENT_USER_GROUP_ID}" --env SOPS_AGE_KEY=${AGE_SECRET_KEY} ${DOCKER_COMPOSE_APP_CONTAINER} \
		bash -c "echo 'Decrypting ${SECRETS_ENV} secrets' \
			&& cd ./environment/prod/deployment/${SECRETS_ENV} \
			&& sops --decrypt --input-type=dotenv --output-type=dotenv --output .env.${SECRETS_ENV}.secrets.decrypted .env.${SECRETS_ENV}.secrets \
			&& echo 'Done'"

encrypt-secrets:
	@docker compose --file ${DOCKER_COMPOSE_FILE} exec --user "${CURRENT_USER_ID}:${CURRENT_USER_GROUP_ID}" ${DOCKER_COMPOSE_APP_CONTAINER} \
		bash -c "echo 'Encrypting ${SECRETS_ENV} secrets' \
			&& cd ./environment/prod/deployment/${SECRETS_ENV} \
			&& sops --encrypt --input-type=dotenv --output-type=dotenv --output .env.${SECRETS_ENV}.secrets .env.${SECRETS_ENV}.secrets.decrypted \
			&& echo 'Done'"

.PHONY: init check-env-file build run stop restart shell shell-root test fix queue dev create-test-db encrypt-beta-secrets decrypt-beta-secrets encrypt-prod-secrets decrypt-prod-secrets
