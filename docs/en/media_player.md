# Media player

The media player provides controls on current track.

## Layout
![Homepage](https://raw.githubusercontent.com/Keiwen/maule_player/main/docs/img/homepage.png)

On the top of the player, you can see the
main information of the track:
its name, artist and album.

On the left, a large button to play or pause
the player.

In the middle, you will find the timeline, showing
the time progress for this track.
You can click on this progress bar to move to a
specific time in the track.

On the bottom part, current track time is showed
along with track duration. You will also find 2
buttons to move to the previous or next track in
your current playlist.

## Behavior
When your playlist is empty and you
add one or more track in it, the first track is
automatically played.

When track time is above 3 seconds, the previous
button will start over the current track instead
of loading the previous track. If track time is
less than 3 seconds, it will load the previous track.

If your screen is too small to display the track text,
this text should be displayed scrolling horizontally.
