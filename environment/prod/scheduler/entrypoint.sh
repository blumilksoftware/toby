#!/bin/sh

# -e is for "automatic error detection", tell shell to abort any time an error occurred
set -e

# https://manpages.debian.org/bullseye/cron/cron.8.en.html
# -f  Stay in foreground mode, don't daemonize.

crontab ./environment/prod/scheduler/tasks.cron \
  && cron -f
