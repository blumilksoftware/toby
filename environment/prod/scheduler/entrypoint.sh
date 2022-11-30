#!/bin/sh

# -e is for "automatic error detection", tell shell to abort any time an error occurred
set -e

# -f  foreground
# -L log output

crontab ./environment/prod/scheduler/tasks.cron && crond -f -L /dev/stdout
