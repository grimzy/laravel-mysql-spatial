DB_DIR=$(shell pwd)/_db
v=5.7
MYSQL_VERSION=$(v)

start_db:
	@echo Starting MySQL $(MYSQL_VERSION)
	docker run --rm -d --name spatial-mysql \
            -p 3306:3306 \
            -v $(DB_DIR):/var/lib/mysql \
            -e MYSQL_DATABASE=spatial_test \
            -e MYSQL_ALLOW_EMPTY_PASSWORD=yes \
            mysql:$(MYSQL_VERSION) --character-set-server=utf8 --collation-server=utf8_general_ci
	docker logs -f spatial-mysql

purge_db:
	docker stop spatial-mysql
	rm -Rf $(DB_DIR)

get_ip:
	@docker inspect -f '{{range .NetworkSettings.Networks}}{{.IPAddress}}{{end}}' spatial-mysql