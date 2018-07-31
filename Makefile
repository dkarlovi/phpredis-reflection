ifndef VERSION
VERSION=release
endif

test: build run
	docker-compose exec reflector php /test/reflect.php

build:
	docker-compose build

release:
	docker-compose push

run:
	docker-compose up -d

clean:
	docker-compose stop
	docker rmi dkarlovi/phpredis-reflection:release
	docker rmi dkarlovi/phpredis-reflection:develop
