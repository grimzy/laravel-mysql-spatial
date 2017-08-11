#!/usr/bin/env bash

echo "MySQL version: " $MYSQL_VERSION

docker run -d --name spatial-mysql-$MYSQL_VERSION \
    -p 3306:3306 \
    -v $(pwd)/db:/var/lib/mysql \
    -e MYSQL_DATABASE=test \
    -e MYSQL_ALLOW_EMPTY_PASSWORD=yes \
    mysql:$MYSQL_VERSION --character-set-server=utf8 --collation-server=utf8_general_ci
