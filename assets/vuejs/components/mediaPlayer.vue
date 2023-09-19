<template>
  <div class="container audio-container">
    <div class="custom-player-container row">
      <div class="col-2">
        <div @click="togglePlay()">
          <play-button :playing="playingAudio" />
        </div>
      </div>
      <div class="col-8">Custom player middle</div>
      <div class="col-2">right</div>
    </div>


    <audio controls id="audio_player">
      <source :src="mediaSrc">
    </audio>
  </div>
</template>

<script>
import {mapGetters} from "vuex";
import playButton from "./playButton";

export default {
  name: "mediaPlayer",
  components: { playButton },
  data () {
    return {
      audioElement: null,
      playingAudio: false,
    }
  },
  watch: {
    playedMediaFilepath: function(newValue, oldValue) {
      this.audioElement.load() // reload audio component
      this.audioElement.play()
    }
  },
  mounted () {
    this.audioElement = document.getElementById("audio_player");
  },
  computed: {
    ...mapGetters(['playedMediaFilepath']),
    mediaSrc () {
      return '/media_lib/' + this.playedMediaFilepath;
    }
  },
  methods: {
    togglePlay () {
      this.playingAudio = !this.playingAudio
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
