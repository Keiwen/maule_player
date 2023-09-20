<template>
  <div class="container audio-container">
    <div class="custom-player-container row">
      <div class="col-2 custom-player-play">
        <div class="row">
          <play-button :playing="playingAudio" @click-play="togglePlay" color="var(--light)" />
        </div>
      </div>
      <div class="col-7 custom-player-middle">
        <div class="row custom-player-text">Text</div>
        <div class="row custom-player-timeline">
          <timeline percent-progress="50" />
        </div>
      </div>
      <div class="col-3 custom-player-prevnext">
        <div class="row">Right</div>
        <div class="row">
          <span class="custom-player-time">
            <span class="custom-player-current-time">0:00</span>
            /
            <span class="custom-player-duration">0:00</span>
          </span>
        </div>
      </div>
    </div>


    <audio controls id="audio_player">
      <source :src="mediaSrc">
    </audio>
  </div>
</template>

<script>
import {mapGetters} from "vuex";
import playButton from "./playButton";
import timeline from "./timeline";

export default {
  name: "mediaPlayer",
  components: { playButton, timeline },
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

.custom-player-container {
  background-color: var(--primary);
  .play-button {
    background-color: var(--secondary);
  }
}


audio {
  // CHROME DEFAULT AUDIO
  // container
  &::-webkit-media-controls-panel {
    background-color: var(--primary);
    width: 100%;
  }
  // play button
  &::-webkit-media-controls-play-button {
    background-color: var(--secondary);
    border-radius: 50%;
  }
  // volume button
  &::-webkit-media-controls-mute-button {
    background-color: var(--secondary);
    border-radius: 50%;
  }
  // current time
  &::-webkit-media-controls-current-time-display {
    color: var(--white);
  }
  // remaining time
  &::-webkit-media-controls-time-remaining-display {
    color: var(--white);
  }
  // timeline
  &::-webkit-media-controls-timeline {
    background-color: var(--background);
    border-radius: 25px;
    margin-left: 10px;
    margin-right: 10px;
  }
}


</style>
