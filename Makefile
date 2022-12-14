DOCKER_COMPOSE_BETA_FILENAME = docker-compose.beta.yml
BETA_DOCKER_EXEC = docker compose --file ${DOCKER_COMPOSE_BETA_FILENAME} exec --workdir /application/environment/scripts

DOCKER_COMPOSE_PROD_FILENAME = docker-compose.prod.yml
PROD_DOCKER_EXEC = docker compose --file ${DOCKER_COMPOSE_PROD_FILENAME} exec --workdir /application/environment/scripts

export COMPOSE_DOCKER_CLI_BUILD = 1
export DOCKER_BUILDKIT = 1

.PHONY: beta-deploy
beta-deploy: create-deployment-file
	docker compose --file ${DOCKER_COMPOSE_BETA_FILENAME} build --pull && \
	docker compose --file ${DOCKER_COMPOSE_BETA_FILENAME} up --detach && \
	echo "App post deploy actions" && \
	${BETA_DOCKER_EXEC} toby-beta-php bash post-deploy-actions.sh && \
	echo "Worker post deploy actions" && \
	${BETA_DOCKER_EXEC} toby-beta-worker bash post-deploy-actions.sh && \
	echo "Scheduler post deploy actions" && \
	${BETA_DOCKER_EXEC} toby-beta-scheduler bash post-deploy-actions.sh

.PHONY: beta-reload-config
beta-reload-config:
	echo "App config reload" && \
	${BETA_DOCKER_EXEC} toby-beta-php bash reload-config.sh && \
	echo "Worker config reload" && \
	${BETA_DOCKER_EXEC} toby-beta-worker bash reload-config.sh && \
	echo "Scheduler config reload" && \
	${BETA_DOCKER_EXEC} toby-beta-scheduler bash reload-config.sh

.PHONY: prod-deploy
prod-deploy: create-deployment-file
	docker compose --file ${DOCKER_COMPOSE_PROD_FILENAME} build --pull && \
	docker compose --file ${DOCKER_COMPOSE_PROD_FILENAME} up --detach && \
	echo "App post deploy actions" && \
	${PROD_DOCKER_EXEC} toby-prod-php bash post-deploy-actions.sh && \
	echo "Worker post deploy actions" && \
	${PROD_DOCKER_EXEC} toby-prod-worker bash post-deploy-actions.sh && \
	echo "Scheduler post deploy actions" && \
	${PROD_DOCKER_EXEC} toby-prod-scheduler bash post-deploy-actions.sh

.PHONY: prod-reload-config
prod-reload-config:
	echo "App config reload" && \
	${PROD_DOCKER_EXEC} toby-prod-php bash reload-config.sh && \
	echo "Worker config reload" && \
	${PROD_DOCKER_EXEC} toby-prod-worker bash reload-config.sh && \
	echo "Scheduler config reload" && \
	${PROD_DOCKER_EXEC} toby-prod-scheduler bash reload-config.sh


DEPLOYMENT_PROJECT_VERSION = $(shell ./environment/scripts/version.sh --long)
DEPLOYMENT_DATETIME = $(shell date --rfc-3339=seconds)

.PHONY: create-deployment-file
create-deployment-file:
	@echo "\
	DEPLOY_DATE='${DEPLOYMENT_DATETIME}'\n\
	DEPLOY_VERSION='${DEPLOYMENT_PROJECT_VERSION}'\
	" > .deployment
