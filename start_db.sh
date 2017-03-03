#!/usr/bin/env bash

docker run -d --name spatial-mysql \
    -p 3306:3306 \
    -v $(pwd)/db:/var/lib/mysql \
    -e MYSQL_DATABASE=db_test \
    -e MYSQL_USER=test_user \
    -e MYSQL_PASSWORD=123456 \
    -e MYSQL_ROOT_PASSWORD=123456 \
    mysql:latest
