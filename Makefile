fresh-db: $(warning api db will be freshed)
	php artisan migrate:fresh
	php artisan db:seed
