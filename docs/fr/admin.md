# Administration

Cette partie explique comment gérer la
librairie de media sur le serveur.

## Dossier MediaLib
### Ajouter des fichiers media
Tous les fichiers médias sont suivis dans le dossier
``/public/media_lib/``.
A l'initialisation, il existe un dossier
``/public/media_lib_samples`` avec quelques fichiers.
Ce dossier peut être copié dans ``/public/media_lib/``
si besoin ou être ignoré.

La méthode la plus facile pour ajouter des dossiers
dans Media Lib est d'utiliser des liens symboliques.
Au lieu de copier les dossiers où
sont stockés les fichiers media, un lien symboliques
permet de créer une sorte de raccourci vers ce dossier.
L'application aura accès à tous les fichiers,
sans qu'il y ait besoin de les avoir aux deux endroits.

Sur Windows, il est possible d'exécuter
(en tant qu'administrateur) le script
``add_media_symlink.bat`` inclus dans le
dossier bin.
Ce script demande le dossier ciblé
(où sont stockés les fichiers média), le nom
du lien, et crée ensuite le lien symbolique.

### Chargement des fichiers media
Une fois que les fichiers sont ajoutés dans le
dossier Media Lib,
ouvrir une interface de commande dans le dossier
de l'application et exécuter

``php bin/console app:import-tracks``

Cette commande permet d'inspecter les fichiers
dans le dossier Media Lib et de les charger
dans la base de données.

L'aide est accessible pour vérifier les options
du script
``php bin/console app:import-tracks --help``

