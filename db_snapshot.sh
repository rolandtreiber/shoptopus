#!/bin/bash

rm ./database/seeders/test-data/db-dump/*
sudo docker exec `docker ps -q --filter ancestor=mysql:8.2` /usr/bin/mysqldump -uhomestead -psecret shoptopus > "database/seeders/test-data/db-dump/$(date +"%FT%T").sql" --no-tablespaces
