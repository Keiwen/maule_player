<template>
  <div class="audio-container">
    <audio controls id="audio_player">
      <source :src="mediaSrc">
    </audio>
  </div>
</template>

<script>
import {mapGetters} from "vuex";

export default {
  name: "mediaPlayer",
  watch: {
    playedMediaFilepath: function(newValue, oldValue) {
      var audio = document.getElementById("audio_player");
      audio.load() // reload audio component
      audio.play()
    }
  },
  computed: {
    ...mapGetters(['playedMediaFilepath']),
    mediaSrc () {
      return '/media_lib/' + this.playedMediaFilepath;
    }
  }
}
</script>

<style lang="scss" scoped>

.audio-container {
  width: 100%;
}

/* container */
audio::-webkit-media-controls-panel {
  background-color: var(--primary);
  width: 100%;
}

/* play button */
audio::-webkit-media-controls-play-button {
  background-color: var(--secondary);
  border-radius: 50%;
}

/* volume button */
audio::-webkit-media-controls-mute-button {
  background-color: var(--secondary);
  border-radius: 50%;
}

/* current time */
audio::-webkit-media-controls-current-time-display {
  color: var(--white);
}

/* remaining time */
audio::-webkit-media-controls-time-remaining-display {
  color: var(--white);
}

/* timeline */
audio::-webkit-media-controls-timeline {
  background-color: var(--background);
  border-radius: 25px;
  margin-left: 10px;
  margin-right: 10px;
}

</style>
