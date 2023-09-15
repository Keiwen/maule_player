# Maule Player
Media AUdio Library Experience Player

## Why this project?
I have multiple audio files on a computer/server,
and I want to play them with other connected devices,
ideally with a bit a management part
(search, browse artist/album, playlists, ...).

Some native apps on said devices can do this
but with ads and not-exactly-what-I-want features...
Outrageous! So I decided to start this side-project
that should not be so complex.
It could also be used as demo for a
Symfony/VueJS Progressive Web App
(for development and for showcase).

This application should be deployed on said
computer/server.
We could link any media directory to load it in
application, that will use associated MP3 metatags.
Any devices connected on same network (cloud or local)
could access to application.
We do not manage users as this application
aimed to be quite 'private'.

## Setup
All steps described in this section
are based on computer/server
that will run the application,
not on connected devices that will use it.

### Requirements
This application require PHP, a SQL DataBase,
as well as package managers composer and NPM.
It could use a built-in server such as WAMP/LAMP.

### Initialization
In the application target directory,
copy the source code. You can download it directly.

If you are using Git, you will be able
to easily update the application.
In target empty directory, initialize wth git:

``git remote add origin
https://github.com/Keiwen/maule_player.git``

---
In directory, copy ``.env`` file
to create ``.env.local`` file.
You will need to change:
- APP_ENV: use ``prod`` to run like a production application
- DATABASE_URL: set up you own DB connexion
- USER_PASSWORD_MAULE_ADMIN: set up the admin password

!! TODO !! define .htaccess if needed

Create ``/public/media_lib/`` directory if not exists.

Then follow update process once before
you can use the application

### Update
If you are using Git, you can update source code:

``git pull origin master``

Then update PHP libraries with composer

``composer install --no-dev --optimize-autoloader``

Ensure your database is also up-to-date

``php bin/console doctrine:migrations:migrate``

Update JavaScript libraries with NPM
``npm install``

!! TODO !! add public/build to git or rebuild
locally (but require more complex manipulation)

Finally clear the application cache

``php bin/console cache:clear``

### Media Lib directory
All media files will be tracked inside
``/public/media_lib/`` directory.
At initialization, there is an existing
``/public/media_lib_samples`` folder with some files.
You can copy it inside ``/public/media_lib/``
if needed or ignore it.

The easiest way to add some directory in Media Lib
is to add a symlink.
Instead of copying the directory where you
store your media files, it will create a link.
Application will have access to all files
without having them in both places.

!! TODO !! script to symlink in media_lib

Open a command interface in application folder
and run

``php bin/console app:import-tracks``

That will check all files inside Media Lib and
load them all to the application DataBase.

