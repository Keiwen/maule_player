# Installation

Toutes les étapes décrites dans cette section
sont à suivre sur l'ordinateur ou le serveur
qui hébergera l'application, et non sur
l'appareil connecté qui utilisera cette
application.

## Pré-requis
Cette application nécessite PHP,
une base de données SQL,
ainsi que les gestionnaires de librairies
composer et NPM.
Il est possible d'utiliser un serveur intégré
comme WAMP ou LAMP.

Préparer une nouvelle base de données
et un utilisateur associé, qui seront
utilisés par l'application.

Cette application charge des fichiers MP3
et utilise leurs méta-données.
Les autres formats ne sont pas encore supportés.


## Initialisation

### Répertoire du code source
Dans le dossier cible de l'application,
copier le code source. Il peut être directement
téléchargé.

Si Git est utilisé, le dossier cible
peut être associé au dépôt principal :

``git remote add origin
https://github.com/Keiwen/maule_player.git
``

Ou le dépôt peut être cloné
pour créer le dossier cible :

``git clone
https://github.com/Keiwen/maule_player.git``


### Configuration du serveur
Le serveur doit être configuré pour accueillir
l'application
(voir [configuration serveur Symfony 5.4](https://symfony.com/doc/5.4/setup/web_server_configuration.html#apache))

Par exemple, si WAMP est utilisé, définir
un vhost pointant vers le dossier ``public``.
Le répertoire contenant le code source peut être
situé n'importe où.
Ensuite, modifier le fichier ``httpd-vhosts.conf``
en conséquence. Cela peut inclure :
```
    <Directory /var/www/project/public>
        AllowOverride None
        Require all granted
        FallbackResource /index.php
    </Directory>
```
Vérifier que le serveur Apache est autorisé
par le parefeu (autorisations pour l'application
``httpd.exe`` dans le dossier bin d'apache)

S'il est prévu d'utiliser un port spécifique
pour l'application, ajouter un port d'écoute
sur Apache, par exemple ``8081``.
Le fichier ``httpd.conf`` sera modifié avec
``Listen 0.0.0.0:${MYPORT8081}``.
Le fichier ``httpd-vhosts.conf`` peut
maintenant être modifié, en définissant
le vhost avec cette nouvelle variable.

```
<VirtualHost *:${MYPORT8081}>
```
Ensuite, si le serveur utilise une IP 192.168.1.X,
l'application sera accessible par un
appareil connecté au réseau en entrant
dans un navigateur
``192.168.1.X:8081``

### Initialisation du répertoire
Dans le dossier du code source, 
copier le fichier ``.env`` pour créer
le fichier ``.env.local``.

Il est nécessaire de modifier :
- APP_ENV: utiliser ``prod`` pour utiliser l'application en mode production
- DATABASE_URL: configurer la connexion à la base de données définie auparavant
- USER_PASSWORD_MAULE_ADMIN: définir le mot de passe administrateur (non utilisé actuellement)
- MEDIA_PATH_SEPARATOR: ``/`` sur Linux, `\ ` pour Windows

Créer le dossier ``/public/media_lib/`` s'il n'existe pas.

Enfin, suivre le processus de mise à jour une fois
avant de pouvoir utiliser l'application.

## Mise à jour
Si Git est utilisé, il ets possible de mettre
à jour le code source :

``git pull origin main``

Ensuite, mettre à jour les librairies PHP avec composer

``composer install --no-dev --optimize-autoloader``

S'assurer que la base de données est à jour

``php bin/console doctrine:migrations:migrate``

Mettre à jour les librairies JavaScript avec NPM
``npm install``

Relancer un build localement avec
``npm run build``

Et enfin nettoyer le cache de l'application

``php bin/console cache:clear``

