composer-install:
	docker exec test-app composer install

run-migrations:
	docker exec test-app php artisan migrate

db-seed:
	docker exec test-app php artisan db:seed

install: composer-install run-migrations db-seed

run:
	docker compose up --build -d
	make install

process-webhooks:
	docker exec test-app php artisan app:process-webhooks

test:
	docker exec cli ./vendor/bin/phpunit
