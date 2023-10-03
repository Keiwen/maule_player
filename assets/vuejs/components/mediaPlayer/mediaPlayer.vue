<template>
  <div class="container audio-container">
    <div class="custom-player-container row">
      <div class="col-12 custom-player-text-wrapper">
        <div class="row custom-player-text">{{ mediaText }}</div>
      </div>

      <div class="col-2 custom-player-play">
        <div class="row">
          <play-button :playing="playingAudio" @click-play="togglePlay" color="var(--light)" />
        </div>
      </div>
      <div class="col-10 custom-player-middle">
        <div class="row custom-player-timeline">
          <timeline :percent-progress="percentProgress" @change-progress="changeProgress"  />
        </div>
        <div class="row">
          <span class="custom-player-time">
            <span class="custom-player-current-time">{{ currentTime }}</span>
            /
            <span class="custom-player-duration">{{ duration }}</span>
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
import {mapActions, mapGetters} from "vuex";
import playButton from "./playButton";
import timeline from "./timeline";

export default {
  name: "mediaPlayer",
  components: { playButton, timeline },
  data () {
    return {
      audioElement: null,
      duration: '',
      currentTime: '',
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
    this.duration = this.getDisplayTime(0)
    this.currentTime = this.getDisplayTime(0)
    this.audioElement = document.getElementById("audio_player")
    if (this.audioElement !== null) {
      this.audioElement.addEventListener('loadedmetadata', this.audioLoaded)
      this.audioElement.addEventListener('timeupdate', this.audioTimeUpdate)
      this.audioElement.addEventListener('ended', this.audioEnded)
      this.audioElement.addEventListener('error', this.audioError)
      this.audioElement.addEventListener('pause', this.audioPaused)
      this.audioElement.addEventListener('play', this.audioPlayed)
    }
  },
  computed: {
    ...mapGetters(['currentTrack', 'currentTrackIndex', 'getNextPlaylistIndex', 'getDisplayTime']),
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
      this.duration = this.getDisplayTime(this.audioElement.duration)
    },
    audioTimeUpdate (e) {
      this.currentTime = this.getDisplayTime(Math.floor(this.audioElement.currentTime))
      this.percentProgress = Math.round((this.audioElement.currentTime / this.audioElement.duration) * 100)
    },
    audioEnded (e) {
      this.playingAudio = false
      const nextIndex = this.getNextPlaylistIndex()
      if (nextIndex !== -1) {
        this.playTrackInPlaylist(nextIndex)
      }
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
  .play-button {
    background-color: var(--secondary);
  }
  .custom-player-time {
    width: 100%;
    text-align: center;
    color: var(--light);
    text-shadow: var(--secondary) 0 0 10px;
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
      animation: movingText 10s infinite linear;
      padding-left: 100%;
      &:hover {
        animation-play-state: paused;
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
