# Kit de développement WordPress

- [Kit de développement WordPress](#kit-de-développement-wordpress)
  - [Pré-requis](#pré-requis)
  - [Configuration (première installation)](#configuration-première-installation)
  - [Lancer le projet](#lancer-le-projet)
  - [Arrêter le projet](#arrêter-le-projet)
  - [Création de la base de données](#création-de-la-base-de-données)
  - [Compilation des assets avec Gulp](#compilation-des-assets-avec-gulp)
  - [wp-cli](#wp-cli)
  - [Environnement de test pour l'envoi d'emails, avec Mailhog](#environnement-de-test-pour-lenvoi-demails-avec-mailhog)
  - [Coding standards WordPress avec phpcs et phpcbf (phpCodeSniffer)](#coding-standards-wordpress-avec-phpcs-et-phpcbf-phpcodesniffer)
  - [Analyse statique du code](#analyse-statique-du-code)
  - [Génération de la documentation du thème avec phpDocumentor](#génération-de-la-documentation-du-thème-avec-phpdocumentor)
  - [Remarques](#remarques)
    - [Permissions d'écriture dans le dossier `web`](#permissions-décriture-dans-le-dossier-web)
  - [Références](#références)
    - [Images Docker et services utilisés](#images-docker-et-services-utilisés)
    - [CI](#ci)
      - [Linter](#linter)
      - [Analyse statique de code](#analyse-statique-de-code)
      - [Générateur de documentation à partir du code](#générateur-de-documentation-à-partir-du-code)


## Pré-requis

1. Installer [Composer](https://getcomposer.org/) globalement
2. (Optionnel) Si vous utilisez VS Code, installer les extensions [vscode-phpcs](https://marketplace.visualstudio.com/items?itemName=ikappas.phpcs) [vscode-phpcbf](https://marketplace.visualstudio.com/items?itemName=persoderlind.vscode-phpcbf) pour configurer PHP_CodeSniffer correctement.

## Configuration (première installation)

Cloner le dépôt, puis créer les fichiers suivants :

~~~bash
cp .env.dist .env
mkdir -p web
composer install
~~~

## Lancer le projet

À la racine :

~~~bash
docker compose up -d
~~~

Accéder :

- À [WordPress](http://localhost:8080) : http://localhost:8080. Suivre les instructions pour installer la base de données. Dans les réglages, activer le thème `mon_theme`. Afficher la *home*.
- À [Adminer](http://localhost:8081) : http://localhost:8081. Se connecter avec les credentials roots
- À [Mailhog](http://localhost:8025) : http://localhost:8025;

> Modifier les ports publiés au besoin.

## Arrêter le projet

À la racine :

~~~bash
docker compose down
~~~

## Création de la base de données

La base de données `wordpress` est créée automatiquement par le service `db`.

## Compilation des assets avec Gulp

Pour gérer les assets (scss, css, js, blocks, etc.) et les watcher, lancer le conteneur [gulp](https://gulpjs.com/):

~~~bash
docker compose exec -it gulp gulp
~~~

À chaque sauvegarde d'un fichier source d'asset, les *watcher* vont faire leur travail (recompilation => minification => copie dans les assets du thème).

Adapter en fonction des besoins le programme [`gulpfile.js`](./gulpfile.js).

> Laisser tourner en tâche de fond. Lancer lors de chaque phase de développement si vous modifier les assets.

## wp-cli

Pour exécuter une commande de `wp-cli` depuis le conteneur :

```bash
    docker compose run --rm wpcli
```
Créer un alias dans `~/.bash_aliases` :

```bash
    wp=docker compose run --rm wpcli
```

Tester avec la commande :
```bash
    wp cli version
```
> Bien vérifier que `wp-cli` est sur le même réseau Docker que le reste du projet, sinon il ne pourra pas accéder à l’hôte mysql.

## Environnement de test pour l'envoi d'emails, avec Mailhog

[Mailhog](https://github.com/mailhog/MailHog) est un mail catcher inspiré de MailCatcher

- On envoie des mails suivant le protocole SMTP via le serveur SMTP du conteneur WordPress, sur le port 1025
- On accede au client mail via le serveur HTTP sur le port 8025

## Coding standards WordPress avec phpcs et phpcbf (phpCodeSniffer)

Le kit utilise [les codings standards PHP de WordPress](https://github.com/WordPress/WordPress-Coding-Standards), appliqués par phpCodeSniffer (celui-ci doit être installé globalement, ainsi que le standard). Le projet fournit des *settings* pour vscode pour les extensions [phpcs](https://marketplace.visualstudio.com/items?itemName=shevaua.phpcs) et [phpcbf](https://marketplace.visualstudio.com/items?itemName=persoderlind.vscode-phpcbf)

Sinon manuellement :

~~~bash
phpcs --standard=WordPress web/wp-content/themes/mon-theme
phpcbf --standard=WordPress web/wp-content/themes/mon_theme/
~~~

## Analyse statique du code

Utiliser phpStan (s'assurer d'avoir fait `composer update` pour l'installer localement dans le projet)

~~~bash
vendor/bin/phpstan analyze -l8 web/wp-content/themes/mon_theme/
~~~

> [Accéder à la documentation de phpStan](https://phpstan.org/user-guide/getting-started)

## Génération de la documentation du thème avec phpDocumentor

~~~bash
vendor/bin/phpdoc run -d web/wp-content/mon_theme -t docs/theme
vendor/bin/phpdoc run -d web/plugins/my_plugin -t docs/plugins
~~~

> [Accéder à la documentation de phpDocumentor](https://phpdoc.org/)

## Remarques

### Permissions d'écriture dans le dossier `web`

Vérifier que vous donnez la propriété du volume/bind-mount `web` à votre utilisateur courant et non à `root`.

~~~yml
  wordpress:
    # ...
    user: "${UID}:${GID}"
~~~

où `${UID}` et `${GID}` sont des variables d'environnement définies dans le `.env`. S'assurer que l'id de votre user et de votre groupe sur la machine hôte (avec la commande `id`).

> C'est pour cela que l'on crée à l'avance le dossier `web`, sinon Docker va le créer lui-même avec les droits root.

## Références

### Images Docker et services utilisés

- [Mysql Docker official image](https://hub.docker.com/_/mysql)
- [Wordpress Docker official image](https://hub.docker.com/_/wordpress/)
- [MailHog](https://github.com/mailhog/MailHog), un mailcatcher
- [Exemple d'installation Wordpress avec Docker](https://www.datanovia.com/en/fr/lessons/utilisation-de-docker-wordpress-cli-pour-gerer-les-sites-web-wordpress/#installer-wordpress-en-utilisant-le-docker-compose-et-wp-cli)

### CI

Outils pour maintenir une bonne qualité de code avant mise en production

#### Linter

- [Dépot Wordpress coding standards](https://github.com/WordPress/WordPress-Coding-Standards/tree/develop/WordPress)
- [Setting up WordPress Coding Standards in VS Code](https://www.edmundcwm.com/setting-up-wordpress-coding-standards-in-vs-code/)
- [PHP_CodeSniffer](https://github.com/PHPCSStandards/PHP_CodeSniffer), linter PHP
- [Setting Up PHP CodeSniffer in Visual Studio Code](https://tommcfarlin.com/php-codesniffer-in-visual-studio-code/)
- [vscode-phpcs](https://marketplace.visualstudio.com/items?itemName=ikappas.phpcs) : extension Phpcodesniffer pour vscode
- [vscode-phpcbf](https://marketplace.visualstudio.com/items?itemName=persoderlind.vscode-phpcbf) : extension pour configurer `phpcbf` script frère de `phpcs` pour beautifer et fixer le code PHP selon le standard choisi

#### Analyse statique de code

- [PhpStan](https://phpstan.org/), outil très puissant pour analyser le code et détecter des bugs/erreurs de manière statique;
- [Using PHPStan in a WordPress project](https://pascalbirchler.com/phpstan-wordpress/)


#### Générateur de documentation à partir du code

- [phpDocumentor](https://phpdoc.org/)



