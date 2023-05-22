#!/bin/bash

# -e is for "automatic error detection", tell shell to abort any time an error occurred
set -e

# bash is not responding to the sigterm and container always have 10 second timeout (when stop/restart)
# exec is related with
# https://docs.docker.com/compose/faq/#why-do-my-services-take-10-seconds-to-recreate-or-stop
# https://github.com/moby/moby/issues/3766
# https://unix.stackexchange.com/a/196053

exec supervisord --configuration /etc/supervisor/custom-supervisord.conf
