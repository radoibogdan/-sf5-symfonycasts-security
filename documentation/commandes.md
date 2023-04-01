# Création Api

## Démarrer serveur en local
```bash
$ symfony serve -d
```
Lien pour accéder à l'interface: https://127.0.0.1:8000

## Lignes jouées:
### Installer API et dépendances
```bash
$ composer update
$ symfony console doctrine:database:create
$ symfony console doctrine:migrations:migrate
$ symfony console doctrine:fixtures:load
$ yarn install
$ composer require security
$ symfony console make:user
$ git init
$ git remote add origin https://github.com/radoibogdan/-sf5-symfonycasts-security.git
$ git add .
$ git commit -m "initial commit + create user"
```

# Tests (pas sur ce projet)
Jouer les tests (srs/tests):

```bash
$ php bin/phpunit
```


