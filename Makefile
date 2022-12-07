DOCKER_COMPOSE_BETA_FILENAME=docker-compose.beta.yml
BETA_DOCKER_EXEC=docker compose --file ${DOCKER_COMPOSE_BETA_FILENAME} exec --workdir /application/environment/scripts

DOCKER_COMPOSE_PROD_FILENAME=docker-compose.prod.yml
PROD_DOCKER_EXEC=docker compose --file ${DOCKER_COMPOSE_PROD_FILENAME} exec --workdir /application/environment/scripts

.PHONY: beta-deploy
beta-deploy:
	git fetch && \
	git checkout --force "${BRANCH_NAME}" && \
	git pull && \
	COMPOSE_DOCKER_CLI_BUILD=1 DOCKER_BUILDKIT=1 docker compose --file ${DOCKER_COMPOSE_BETA_FILENAME} build --pull && \
	docker compose --file ${DOCKER_COMPOSE_BETA_FILENAME} up --detach && \
	${BETA_DOCKER_EXEC} toby-beta-php bash post-deploy-actions.sh && \
	${BETA_DOCKER_EXEC} toby-beta-worker bash post-deploy-actions.sh && \
	${BETA_DOCKER_EXEC} toby-beta-scheduler bash post-deploy-actions.sh

.PHONY: beta-reload-config
beta-reload-config:
	${BETA_DOCKER_EXEC} toby-beta-php bash reload-config.sh && \
	${BETA_DOCKER_EXEC} toby-beta-worker bash reload-config.sh && \
	${BETA_DOCKER_EXEC} toby-beta-scheduler bash reload-config.sh

.PHONY: prod-deploy
prod-deploy:
	git fetch && \
	git checkout --force "${BRANCH_NAME}" && \
	git pull && \
	COMPOSE_DOCKER_CLI_BUILD=1 DOCKER_BUILDKIT=1 docker compose --file ${DOCKER_COMPOSE_PROD_FILENAME} build --pull && \
	docker compose --file ${DOCKER_COMPOSE_PROD_FILENAME} up --detach && \
	${PROD_DOCKER_EXEC} toby-prod-php bash post-deploy-actions.sh && \
	${PROD_DOCKER_EXEC} toby-prod-worker bash post-deploy-actions.sh && \
	${PROD_DOCKER_EXEC} toby-prod-scheduler bash post-deploy-actions.sh

.PHONY: prod-reload-config
prod-reload-config:
	${PROD_DOCKER_EXEC} toby-prod-php bash reload-config.sh && \
	${PROD_DOCKER_EXEC} toby-prod-worker bash reload-config.sh && \
	${PROD_DOCKER_EXEC} toby-prod-scheduler bash reload-config.sh
