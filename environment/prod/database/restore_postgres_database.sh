# example:
# bash restore_postgres_database.sh <filename> <database name> <username> <docker service name>

DUMP_FILE_NAME=${1?provide dump file name as 1st argument}
DATABASE_NAME=${2?provide database name as 2nd argument}
DATABASE_USERNAME=${3?provide database usernname as 3rd argument}
DOCKER_COMPOSE_POSTGRES_SERVICE=${4?provide postgres docker compose service as 4th argument}
DOCKER_COMPOSE_FILE_PATH=./../../../docker-compose.beta.yml

echo "Copying dump file (${DUMP_FILE_NAME}) into container (${DOCKER_COMPOSE_POSTGRES_SERVICE})" \
  && docker cp ${DUMP_FILE_NAME} ${DOCKER_COMPOSE_POSTGRES_SERVICE}:/var/lib/postgresql/${DUMP_FILE_NAME} \
  && echo "Done"

echo "restoring data from file: (/var/lib/postgresql/${DUMP_FILE_NAME}) into database: ${DATABASE_NAME}" \
  && docker-compose -f $DOCKER_COMPOSE_FILE_PATH exec -T --workdir /var/lib/postgresql ${DOCKER_COMPOSE_POSTGRES_SERVICE} \
      pg_restore \
      --exit-on-error \
      --verbose \
      --username=${DATABASE_USERNAME} \
      --dbname=${DATABASE_NAME} \
      --no-owner \
      --if-exists \
      --clean \
      --single-transaction \
      ${DUMP_FILE_NAME} \
  && echo "Done." \
  && echo "Removing dump file from container" \
  && docker-compose -f $DOCKER_COMPOSE_FILE_PATH exec -T --workdir /var/lib/postgresql ${DOCKER_COMPOSE_POSTGRES_SERVICE} rm ${DUMP_FILE_NAME} \
  && echo "Done."
