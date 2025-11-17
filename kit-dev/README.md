# Kit de développement WordPress

- [Kit de développement WordPress](#kit-de-développement-wordpress)
  - [Pré-requis](#pré-requis)
  - [Configuration (première installation)](#configuration-première-installation)
  - [Lancer le projet](#lancer-le-projet)
  - [Arrêter le projet](#arrêter-le-projet)
  - [Création de la base de données](#création-de-la-base-de-données)
  - [Configuration de WordPress (**mode Debug**)](#configuration-de-wordpress-mode-debug)
  - [Compilation des assets avec Gulp](#compilation-des-assets-avec-gulp)
  - [wp-cli](#wp-cli)
  - [Environnement de test pour l'envoi d'emails, avec Mailhog](#environnement-de-test-pour-lenvoi-demails-avec-mailhog)
  - [Coding standards WordPress avec PHP\_CodeSniffer](#coding-standards-wordpress-avec-php_codesniffer)
  - [Analyse statique du code](#analyse-statique-du-code)
  - [Génération de la documentation du thème avec phpDocumentor](#génération-de-la-documentation-du-thème-avec-phpdocumentor)
  - [Remarques](#remarques)
    - [Image WordPress](#image-wordpress)
    - [Configuration de la machine virtuelle PHP (php.ini)](#configuration-de-la-machine-virtuelle-php-phpini)
    - [Permissions d'écriture dans le dossier `web`](#permissions-décriture-dans-le-dossier-web)
    - [Redis](#redis)
  - [Références](#références)
    - [Images Docker et services utilisés](#images-docker-et-services-utilisés)
    - [CI](#ci)
      - [Linter](#linter)
      - [Analyse statique de code](#analyse-statique-de-code)
      - [Générateur de documentation à partir du code](#générateur-de-documentation-à-partir-du-code)


## Pré-requis

1. Installer [Composer](https://getcomposer.org/), le gestionnaire de dépendances de PHP. Sera utilisé pour quelques outils (remarque : on pourrait les conteneuriser également...) ;
2. (Optionnel) Si vous utilisez VS Code, installer les extensions [vscode-phpcs](https://marketplace.visualstudio.com/items?itemName=ikappas.phpcs) [vscode-phpcbf](https://marketplace.visualstudio.com/items?itemName=persoderlind.vscode-phpcbf) pour utiliser PHP_CodeSniffer directement dans l'IDE.

## Configuration (première installation)

Cloner le dépôt, puis créer les fichiers suivants :

~~~bash
cd kit-dev
cp .env.dist .env
mkdir -p web
composer install
~~~

## Lancer le projet

À la racine :

~~~bash
docker compose up -d
~~~

Vérifier que tous les conteneurs sont actifs :

~~~bash
docker compose ps
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

## Configuration de WordPress (**mode Debug**)

Placer ces instructions dans le fichier `web/wp-config.php` :

~~~php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true); // écrit les erreurs dans wp-content/debug.log
define('WP_DEBUG_DISPLAY', true); // empêche l’affichage à l’écran

//Provoquer une erreur. Ces messages doivent être log dans /var/log/wordpress.log (reporting de PHP, défini dans php-wordpress.ini)
//error_log("test error_log(); avant crash");
//trigger_error("Erreur volontaire", E_USER_ERROR);
~~~

Dé-commenter pour générer volontairement une erreur et vérifier que les erreurs sont bien log dans `/var/log/wordpress.log`.

> Ces erreurs arrivent **avant** que `wp-settings.php` soit chargé et redéfinisse un gestionnaire d'erreurs pour rediriger les logs vers le système de WordPress (`wp-content/debug.log`). 

## Compilation des assets avec Gulp

Créer les répertoires suivants s'ils n'existent pas :

~~~bash
mkdir -p src/mon_theme/blocks/scripts
mkdir -p src/mon_theme/icons
~~~

Pour gérer les *assets* (scss, css, js, blocks, etc.) et les *watcher*, lancer le conteneur [gulp](https://gulpjs.com/) :

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

## Coding standards WordPress avec PHP_CodeSniffer

Le kit utilise [les codings standards PHP de WordPress](https://github.com/WordPress/WordPress-Coding-Standards), appliqués par le *linter* [PHP_CodeSniffer](https://github.com/PHPCSStandards/PHP_CodeSniffer/) (celui-ci doit être installé globalement, ainsi que le standard). 

On peut installer les extensions [phpcs](https://marketplace.visualstudio.com/items?itemName=shevaua.phpcs) et [phpcbf](https://marketplace.visualstudio.com/items?itemName=persoderlind.vscode-phpcbf) pour intégrer le linter **directement dans VS Code**, en tant que formateur par défaut.

Sinon manuellement :

~~~bash
# Linter votre theme
./vendor/bin/phpcs --standard=WordPress web/wp-content/themes/mon-theme
./vendor/bin/phpcbf --standard=WordPress web/wp-content/themes/mon-theme

#Linter vos plugins
./vendor/bin/phpcs --standard=WordPress web/wp-content/plugins/mon-plugin
./vendor/bin/phpcbf --standard=WordPress web/wp-content/themes/mon-theme
~~~

## Analyse statique du code

Utiliser phpStan (s'assurer d'avoir fait `composer update` pour l'installer localement dans le projet) :

~~~bash
./vendor/bin/phpstan analyze -l8 web/wp-content/themes/mon_theme/
~~~

> [Accéder à la documentation de phpStan](https://phpstan.org/user-guide/getting-started)

## Génération de la documentation du thème avec phpDocumentor

~~~bash
./vendor/bin/phpdoc run -d web/wp-content/mon_theme -t docs/theme
./vendor/bin/phpdoc run -d web/plugins/my_plugin -t docs/plugins
~~~

> [Accéder à la documentation de phpDocumentor](https://phpdoc.org/)

## Remarques

### Image WordPress

WordPress tourne ici sous Apache avec `mod_php` (*Server API : Apache 2.0 Handler*), l'ancien mode d’exécution intégré à Apache. **Ce mode n’est pas recommandé en production**. On l’utilise en dev pour la simplicité et la rapidité de mise en place. Il est toujours utile pour tester rapidement le code et les extensions PHP.

En environnement réel, on privilégiera [PHP-FPM](https://www.php.net/manual/fr/install.fpm.php) (ou [FrankenPHP](https://frankenphp.dev/)), plus moderne, isolé et performant.

> Exercice : migrer l'image WordPress vers PHP-FPM ou FrankenPHP.

### Configuration de la machine virtuelle PHP (php.ini)

[Le fichier de configuration `php-wordpress.ini`](./php-wordpress.ini) est monté en *bind-mount* sur le conteneur WordPress. Vous pouvez le modifier à votre guise pour tester différentes configurations. **Inutile de relancer le conteneur pour appliquer les changements**, la nouvelle configuration est immédiatement prise en compte.

### Permissions d'écriture dans le dossier `web`

Vérifier que vous donnez la propriété du *volume/bind-mount* `web` à votre utilisateur courant et non à `root`.

~~~yml
  wordpress:
    # ...
    user: "${UID}:${GID}"
~~~

où `${UID}` et `${GID}` sont des variables d'environnement définies dans le `.env`. 

S'assurer que l'id de votre user et de votre groupe sur la machine hôte (avec la commande `id`) correspondent à ceux renseignés dans le `.env`. Ils sont définis à `1000` dans le `.env`, valeur attribuée au premier utilisateur crée sur la machine (généralement le vôtre) sur les systèmes Unix .

> C'est pour cela que l'on crée à l'avance le dossier `web`, sinon Docker va le créer lui-même avec les droits `root`. 


**Non testé sur Windows !** **Peut provoquer des problèmes notamment pour l'écriture des logs !** 

Solutions : 

- Solution 1 : 
  1. Ouvrir un bash sur le conteneur de wordpress : `docker compose exec -it wordpress bash`
  2. Donner les droits à l'utilisateur du conteneur (`id`) sur le fichier `wp-content/debug.log`, avec `chown -R user:group wp-content/debug.log`.

- **Solution 2** : utiliser `docker compose` **directement depuis la WSL**, et non depuis une invite de commande Windows.
 
### Redis

Nous utilisons [Redis](https://fr.wikipedia.org/wiki/Redis) pour ajouter une couche de cache à WordPress.

Tester la connexion entre les services wordpress et redis :

~~~bash
docker compose exec wordpress redis-cli -h redis ping
~~~

Doit afficher `PONG`.

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



