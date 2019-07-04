**Setting Up local environment**

_For Linux:_

- Clone project from git repository: `https://git-codecommit.eu-west-1.amazonaws.com/v1/repos/vod-store`
- Install **Docker** 
- Install **Docker-Compose**
- Run `docker-compose up -d` inside of project folder. After that you will be able to use following containers:
    * redis: 192.168.101.3
    * webserver: 192.168.101.4
    * php-fpm: 192.168.101.5
    * mysql: 192.168.101.6
- Make copy of `parameters.php.dist` to `parameters.php`
- Set up following settings to `parameters.php`:
    * DATABASE_HOST - 192.168.101.6    
    * DATABASE_USER - root    
    * DATABASE_PASSWORD - 123456
    * REDIS_HOST - 192.168.101.3
    
- Install **php-cli** (v7.2) and **composer**.
    * This an optional step - instead you can run all php commands inside of container: `docker-compose exec php-fpm bash`

- Run `composer install` inside of project folder
- Run following commands to setup database:
    * `php bin/console doctrine:database:create --if-not-exists`
    * `php bin/console doctrine:schema:update --force`
    * `php bin/console doctrine:fixtures:load`
    * `php bin/console doctrine:migrations:version --add --all`
- Run following commands to install application assets:
    * `php bin/console assets:install --symlink`
