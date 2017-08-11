#!/usr/bin/env bash

docker run -d --name spatial-mysql-7 \
    -p 3306:3306 \
    -v $(pwd)/db:/var/lib/mysql \
    -e MYSQL_DATABASE=test \
    -e MYSQL_ALLOW_EMPTY_PASSWORD=yes \
    mysql:latest
