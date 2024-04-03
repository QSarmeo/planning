# planning

## Pour lancer builder/démarrer
> docker compose up -d

## Lancer une commande composer
> docker compose run --rm php composer ...

## URL de base
> localhost

## Execution des tests
> docker compose run --rm php ./vendor/bin/phpunit tests/

## Analyse PHP-CS-FIXER
> docker compose run --rm php ./vendor/bin/php-cs-fixer fix src

## Analyse PHPSTAN
> docker compose run --rm php ./vendor/bin/phpstan analyse src tests --level 9

