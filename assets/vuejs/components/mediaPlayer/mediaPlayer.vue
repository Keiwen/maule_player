<template>
  <div class="audio-container">
    <div class="custom-player-container row">
      <div class="col-12 custom-player-text-wrapper">
        <span class="row custom-player-text" :class="{'custom-player-text-animated': titleWidthTooLarge}" id="audio_player_title">{{ mediaText }}</span>
      </div>

      <div class="custom-player-play">
        <play-button :playing="playingAudio" @click-play="togglePlay" color="var(--light)" />
      </div>

      <div class="custom-player-data">
        <div class="custom-player-timeline">
          <timeline :percent-progress="percentProgress" @change-progress="changeProgress"  />
        </div>

        <time-view :duration="duration"
                   :current-time="currentTime"
                   @restart-track="changeProgress(0)"></time-view>

      </div>
    </div>


    <audio controls id="audio_player">
      <source :src="mediaSrc">
    </audio>
  </div>
</template>

<script>
import {mapActions, mapGetters} from "vuex";
import playButton from "./playButton";
import timeline from "./timeline";
import timeView from "./timeView";

export default {
  name: "mediaPlayer",
  components: { playButton, timeline, timeView },
  data () {
    return {
      audioElement: null,
      audioTitleElement: null,
      duration: 0,
      currentTime: 0,
      percentProgress: 0,
      playingAudio: false,
    }
  },
  watch: {
    currentTrack: function(newValue, oldValue) {
      this.percentProgress = 0
      if (this.audioElement !== null) {
        this.audioElement.load() // reload audio component
        if (newValue.name !== undefined) {
          this.audioElement.play()
        } else {
          this.audioElement.pause()
          this.playingAudio = false
        }
      }
      this.playingAudio = true
    }
  },
  mounted () {
    this.duration = 0
    this.currentTime = 0
    this.audioElement = document.getElementById("audio_player")
    if (this.audioElement !== null) {
      this.audioElement.addEventListener('loadedmetadata', this.audioLoaded)
      this.audioElement.addEventListener('timeupdate', this.audioTimeUpdate)
      this.audioElement.addEventListener('ended', this.audioEnded)
      this.audioElement.addEventListener('error', this.audioError)
      this.audioElement.addEventListener('pause', this.audioPaused)
      this.audioElement.addEventListener('play', this.audioPlayed)
    }
    this.audioTitleElement = document.getElementById('audio_player_title')
  },
  computed: {
    ...mapGetters(['currentTrack']),
    mediaSrc () {
      return '/media_lib/' + this.currentTrack.filepath;
    },
    mediaText () {
      if (this.currentTrack.artist === undefined) {
        return ''
      }
      const trackTitle = this.currentTrack.name
      const trackArtist = this.currentTrack.artist.name
      const trackAlbum = this.currentTrack.album.name
      return trackTitle + ' - ' + trackArtist + ' (' + trackAlbum + ')'
    },
    titleWidthTooLarge () {
      if (this.audioTitleElement === null) return false
      if (!getComputedStyle) return true
      const elementStyle = getComputedStyle(this.audioTitleElement)
      // compute text width: offsetWidth property include padding
      const textWidth = this.audioTitleElement.offsetWidth - parseFloat(elementStyle.paddingLeft) - parseFloat(elementStyle.paddingRight)
      return textWidth >= this.$root.windowSize.width
    }
  },
  methods: {
    ...mapActions(['playTrackInPlaylist']),
    togglePlay () {
      if (this.audioElement !== null) {
        if (this.audioElement.paused) {
          this.audioElement.play()
        } else {
          this.audioElement.pause()
        }
        this.playingAudio = !this.playingAudio
      }
    },
    changeProgress (percentProgress) {
      if (this.audioElement !== null) {
        this.audioElement.currentTime = (parseInt(percentProgress) / 100) * this.audioElement.duration
      }
    },
    audioLoaded (e) {
      this.percentProgress = 0
      this.duration = this.audioElement.duration
    },
    audioTimeUpdate (e) {
      this.currentTime = Math.floor(this.audioElement.currentTime)
      this.percentProgress = Math.round((this.audioElement.currentTime / this.audioElement.duration) * 100)
    },
    audioEnded (e) {
      this.playingAudio = false
      this.playerNext()
    },
    audioError (e) {
      this.playingAudio = false
    },
    audioPaused (e) {
      this.playingAudio = false
    },
    audioPlayed (e) {
      this.playingAudio = true
    }
  }
}
</script>

<style lang="scss" scoped>

.audio-container {
  width: 100%;
}

@keyframes movingText {
  0% {
    transform: translate(0, 0);
  }
  100% {
    transform: translate(-100%, 0);
  }
}

.custom-player-container {
  background-color: var(--primary);
  .custom-player-play {
    float: left;
    width: var(--play-button-size);
    height: var(--play-button-size);
    margin-right: 10px;
    .play-button {
      background-color: var(--secondary);
    }
  }
  .custom-player-data {
    float: right;
    width: -webkit-calc(100% - var(--play-button-size) - 10px);
    width:    -moz-calc(100% - var(--play-button-size) - 10px);
    width:         calc(100% - var(--play-button-size) - 10px);
  }

  .custom-player-text-wrapper {
    max-width: 100%;
    overflow: hidden;
    .custom-player-text {
      font-size: x-large;
      color: var(--light);
      text-shadow: var(--secondary) 0 0 10px;
      white-space: nowrap;
      display: inline-block;
      &.custom-player-text-animated {
        animation: movingText 10s infinite linear;
        padding-left: 100%;
        &:hover {
          animation-play-state: paused;
        }
      }
  }
  }
}


audio {
  display: none;
  /*
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
  */
}


</style>
