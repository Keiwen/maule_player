# Navigation artiste / album

## Liste des artistes
![Artist list](https://raw.githubusercontent.com/Keiwen/maule_player/main/docs/img/artist_list.png)

Sur cette page sont affichés les artistes
de la librairie. Le champ de recherche
permet de rapidement filtrer par noms.

Pour chaque artiste est affiché :
* un bouton pour accèder à la page de l'artiste
* le nom de l'artiste
* un nombre représentant le nombre de pistes pour cet artiste

## Page artiste
![Artist page](https://raw.githubusercontent.com/Keiwen/maule_player/main/docs/img/artist_page.png)

Cette page liste les albums de l'artiste choisi
(du plus récent au plus ancien),
ainsi que les pistes les plus récentes dans un
second onglet.

Pour chaque album est affiché :
* un bouton pour accèder à la page de l'album
* le nom de l'album, ainsi que son année de sortie
* un nombre représentant le nombre de pistes pour cet album
* la durée totale de l'album

![Artist actions](https://raw.githubusercontent.com/Keiwen/maule_player/main/docs/img/artist_actions.png)

En haut à droite de la partie contenu, plusieurs
actions sont accessibles afin de :
* jouer toutes les pistes (remplace la liste
de lecture actuelle par toutes les pistes de cet
artiste)
* ajouter à la liste de lecture (ajoute toutes les
pistes de cet artiste à la liste de lecture actuelle)


## Page album
![Album page](https://raw.githubusercontent.com/Keiwen/maule_player/main/docs/img/album_page.png)

Cette page liste les pistes de l'album choisi
(selon le numéro de piste dans l'album).

Pour chaque piste est affiché :
* le titre de la piste
* un bouton pour accèder à la page de la piste
* le nom de l'artiste
* le numéro de piste au sein de l'album, ainsi que le nom de l'album
* l'année de sortie de la piste
* la durée de la piste
* un bouton pour ajouter la piste à la liste de lecture

En haut à droite de la partie contenu, plusieurs
actions sont accessibles afin de :
* jouer toutes les pistes (remplace la liste
  de lecture actuelle par toutes les pistes de cet
  album)
* ajouter à la liste de lecture (ajoute toutes les
  pistes de cet album à la liste de lecture actuelle)

## Page piste
![Track page](https://raw.githubusercontent.com/Keiwen/maule_player/main/docs/img/track_page.png)

Cette page affiche les informations de
la piste choisie :
* l'artiste (avec un lien vers sa page)
* l'album (avec un lien vers sa page)
* le numéro de la piste dans l'album, ainsi que son année de sortie
* la durée de la piste
* un bouton pour ajouter la piste à la liste de lecture

Plus bas sont également affichés :
* la date d'import dans la librarie (voir [administration](admin.md))
* le chemin d'accès au fichier media
(relativement au dossier `media_lib`, voir [installation](setup.md))
