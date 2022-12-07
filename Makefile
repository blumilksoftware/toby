DOCKER_COMPOSE_BETA_FILENAME=docker-compose.beta.yml
BETA_DOCKER_EXEC=docker compose --file ${DOCKER_COMPOSE_BETA_FILENAME} exec --workdir /application/environment/scripts

DOCKER_COMPOSE_PROD_FILENAME=docker-compose.prod.yml
PROD_DOCKER_EXEC=docker compose --file ${DOCKER_COMPOSE_PROD_FILENAME} exec --workdir /application/environment/scripts

.PHONY: beta-deploy
beta-deploy:
	echo "${BRANCH_NAME}" && \
	git fetch && \
	git checkout --force "${BRANCH_NAME}" && \
	git pull && \
	COMPOSE_DOCKER_CLI_BUILD=1 DOCKER_BUILDKIT=1 docker compose -f ${DOCKER_COMPOSE_BETA_FILENAME} build --pull && \
	docker compose --file ${DOCKER_COMPOSE_BETA_FILENAME} up --detach && \
	${BETA_DOCKER_EXEC} toby-beta-php post-deploy-actions && \
	${BETA_DOCKER_EXEC} toby-beta-worker post-deploy-actions && \
	${BETA_DOCKER_EXEC} toby-beta-scheduler post-deploy-actions

.PHONY: beta-reload-config
beta-reload-config:
	${BETA_DOCKER_EXEC} toby-beta-php bash reload-config.sh && \
	${BETA_DOCKER_EXEC} toby-beta-worker bash reload-config.sh && \
	${BETA_DOCKER_EXEC} toby-beta-scheduler bash reload-config.sh
