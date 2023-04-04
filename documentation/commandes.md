# Création Api

## Démarrer serveur en local
```bash
$ symfony serve -d
```
Lien pour accéder à l'interface: https://127.0.0.1:8000

## Git 
```bash
$ git init
$ git remote add origin https://github.com/radoibogdan/-sf5-symfonycasts-security.git
$ git add .
$ git commit -m "initial commit + create user"
```

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
$ symfony console make:entity (add firstName property)
$ symfony console make:migration
$ symfony console d:m:m
$ symfony console make:factory
$ symfony console doctrine:fixtures:load
$ symfony console doctrine:query:sql 'Select * FROM user'
```

Pour le json des objets (voir UserController)
```bash
$ composer req serializer
```

# Registration form

```bash
$ composer require form validator
$ symfony console make:registration-form
```

Configure the Symfony application to use Bootstrap 5 styles when rendering forms
config/packages/twig.yaml  
 [Symfony Bootstrap 5 doc][1]  
```bash
    twig:  
        form_themes: ['bootstrap_5_layout.html.twig']
```

Pour l'auto enregistrement, modifie RegistrationController + services.yaml pour donner un alias a $formLoginAuthenticator
```bash
symfony console debug:container form_login
```
services.yaml
```bash
    bind:
        bool $isDebug: '%kernel.debug%'
        $formLoginAuthenticator: '@security.authenticator.form_login.main'
```










# Tests (pas sur ce projet)
Jouer les tests (srs/tests):

```bash
$ php bin/phpunit
```

[1]: https://symfony.com/doc/current/form/bootstrap5.html
