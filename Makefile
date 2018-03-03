V=5.7
DB_DIR=$(shell pwd)/_db-$(V)
mV=10.3
mDB_DIR=$(shell pwd)/_db-$(mV)

start_db:
	@echo Starting MySQL $(V)
	docker run --rm -d --name spatial-mysql \
            -p 3306:3306 \
            -v $(DB_DIR):/var/lib/mysql \
            -e MYSQL_DATABASE=spatial_test \
            -e MYSQL_ALLOW_EMPTY_PASSWORD=yes \
            mysql:$(V) --character-set-server=utf8 --collation-server=utf8_general_ci --default-authentication-plugin=mysql_native_password

start_db_maria:
	@echo Starting MariaDB $(mV)
	docker run --rm -d --name spatial-mysql \
			-p 3306:3306 \
			-v $(DB_DIR):/var/lib/mysql \
			-e MYSQL_DATABASE=spatial_test \
			-e MYSQL_ALLOW_EMPTY_PASSWORD=yes \
			mariadb:$(mV) --character-set-server=utf8 --collation-server=utf8_general_ci --default-authentication-plugin=mysql_native_password


rm_db:
	docker stop spatial-mysql || true
	rm -Rf $(DB_DIR)

refresh_db: rm_db start_db

get_ip:
	@docker inspect -f '{{range .NetworkSettings.Networks}}{{.IPAddress}}{{end}}' spatial-mysql