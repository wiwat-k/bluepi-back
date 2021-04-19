
## Setup Docker

$ docker-compose up -d

Then Generate Key

$ docker-compose exec app php artisan key:generate

$ docker-compose exec app php artisan config:cache

As a final step, visit http://your_server_ip in the browser.

## Setup Mysql

$ docker-compose exec db bash

$ root@12345:/# mysql -u root -p

$ mysql> GRANT ALL ON *.* TO 'root'@'%';

$ mysql> FLUSH PRIVILEGES;

$ mysql> EXIT;

$ root@12345:/# exit

## Setup Laravel

$ docker-compose exec app php artisan migrate

## Finish!
