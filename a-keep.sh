#!/bin/bash
# a-keep: run artisan inside the existing container—no volumes harmed

docker compose \
  -f docker-compose-prod.yml \
  -p shoptopus \
  run --rm \
    sh-artisan \
    "$@"
