#!/bin/sh

# -e is for "automatic error detection", tell shell to abort any time an error occurred
set -e

supervisord --nodaemon --configuration ./environment/prod/worker/worker.conf
