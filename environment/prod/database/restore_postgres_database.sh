# example:
# bash restore_postgres_database.sh <filename> <database name> <username> <docker container name>

DUMP_FILE_NAME=${1?provide dump file name as 1st argument}
DATABASE_NAME=${2?provide database name as 2nd argument}
DATABASE_USERNAME=${3?provide database usernname as 3rd argument}
DOCKER_POSTGRES_CONTAINER_NAME=${4?provide postgres docker container name as 4th argument}
DESTINATION_DIR=/var/lib/postgresql

echo "Copying dump file (${DUMP_FILE_NAME}) into container (${DOCKER_POSTGRES_CONTAINER_NAME})" \
  && docker cp ${DUMP_FILE_NAME} ${DOCKER_POSTGRES_CONTAINER_NAME}:${DESTINATION_DIR}/${DUMP_FILE_NAME} \
  && echo "Done"

echo "restoring data from file: (${DESTINATION_DIR}/${DUMP_FILE_NAME}) into database: ${DATABASE_NAME}" \
  && docker exec --workdir ${DESTINATION_DIR} ${DOCKER_POSTGRES_CONTAINER_NAME} \
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
  && docker exec --workdir ${DESTINATION_DIR} ${DOCKER_POSTGRES_CONTAINER_NAME} rm ${DUMP_FILE_NAME} \
  && echo "Done."
