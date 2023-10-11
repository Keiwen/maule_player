# Admin

This part explain how to manage the
media library from the server.

## MediaLib folder
### Adding media files
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

On windows you can execute the script included
in bin folder ``add_media_symlink.bat``
as administrator.
This script will prompt for the targeted folder,
the name of the link, and then will create the symlink.

### Loading media files
Once medias are added in Media Lib,
open a command interface in application folder
and run

``php bin/console app:import-tracks``

That will check all files inside Media Lib and
load them all to the application DataBase.

You can check full options for the script with
``php bin/console app:import-tracks --help``

