# Lecteur media

Le lecteur media permet de contrôler la lecture
de la piste en cours.

## Disposition
![Accueil](https://raw.githubusercontent.com/Keiwen/maule_player/main/docs/img/homepage.png)

En haut du lecteur, les informations principales
de la piste sont affichées:
le titre, le nom de l'artiste et celui de l'album.

A gauche est situé le bouton pour activer la lecture
ou mettre en pause.

Au milieu est affiché le ligne du temps,
permettant de visualiser la progression de la
lecture pour la piste en cours.
Cliquer sur cette barre de progression pour avancer
ou reculer à un temps spécifique pour cette piste.

Sur la partie basse sont affichés le temps actuel
de lecture de la piste, ainsi que sa durée.
Deux boutons sont également affichés pour accèder
à la piste précédente ou suivante dans la liste de
lecture.

## Comportement
Lorsque la liste de lecture est vide et qu'une
ou plusieurs pistes y sont ajoutées,
le lecteur va automatiquement démarrer
la première piste.

Lorsque la progression de la piste en cours est
supérieure à 3 secondes, le bouton précédent va
redémarrer la piste en cours plutôt que de charger
la piste précédente. 
Si la progression est inférieure à 3 secondes,
la piste précédente est bine chargée.

Si l'écran est trop petit pour afficher les
informations principales, le texte devrait
être affiché en défilement horizontal.
