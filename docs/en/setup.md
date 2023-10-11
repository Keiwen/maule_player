# Setup

All steps described in this section
are based on computer/server
that will run the application,
not on connected devices that will use it.

## Requirements
This application require PHP, a SQL DataBase,
as well as package managers composer and NPM.
It could use a built-in server such as WAMP/LAMP.

Prepare a new database and related user
to be used by the application.

This application is loading MP3 files with its
MP3 metatags. Other format are not yet supported.

## Initialization

### Source code directory
In the application target directory,
copy the source code. You can download it directly.

If you are using Git, you will be able
to easily update the application.
In target empty directory, initialize wth git:

``git remote add origin
https://github.com/Keiwen/maule_player.git
``

Or clone the project to create the target directory

``git clone
https://github.com/Keiwen/maule_player.git``


### Server setup
You will need to configure your server
(see [Symfony 5.4 server configuration](https://symfony.com/doc/5.4/setup/web_server_configuration.html#apache))

For example, if you are using WAMP, define a vhost pointing to
``public`` folder. The directory containing
the code itself can be located anywhere.
Then modify your ``httpd-vhosts.conf``
accordingly. It may include:
```
    <Directory /var/www/project/public>
        AllowOverride None
        Require all granted
        FallbackResource /index.php
    </Directory>
```
Check that your apache server is allowed through the firewall
(application ``httpd.exe`` in apache bin folder)

If you want to define your application on a
specific port, add a listen port to apache,
let's say ``8081``.
It should change ``httpd.conf`` with
``Listen 0.0.0.0:${MYPORT8081}``.
You can now change your ``httpd-vhosts.conf``
defining your vhost with this variable

```
<VirtualHost *:${MYPORT8081}>
```
Then if your server have IP 192.168.1.X,
you will be able to access your application,
from another device in the network,
by entering in your browser
``192.168.1.X:8081``

### Directory initialization
In directory, copy ``.env`` file
to create ``.env.local`` file.
You will need to change:
- APP_ENV: use ``prod`` to run like a production application
- DATABASE_URL: set up you own DB connexion
- USER_PASSWORD_MAULE_ADMIN: set up the admin password (not used yet)
- MEDIA_PATH_SEPARATOR: ``/`` if you are on linux, `\ ` if you are on windows

Create ``/public/media_lib/`` directory if not exists.

Then follow update process once before
you can use the application

## Update
If you are using Git, you can update source code:

``git pull origin main``

Then update PHP libraries with composer

``composer install --no-dev --optimize-autoloader``

Ensure your database is also up-to-date

``php bin/console doctrine:migrations:migrate``

Update JavaScript libraries with NPM with
``npm install``

Rebuild locally with
``npm run build``

Finally clear the application cache

``php bin/console cache:clear``

