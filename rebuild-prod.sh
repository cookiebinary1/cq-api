#!/bin/sh
git pull
docker-compose -f prod-docker-compose.yml build && \
docker-compose -f prod-docker-compose.yml down && \
docker-compose -f prod-docker-compose.yml up -d
