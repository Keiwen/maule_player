<template>
  <div class="custom-player-timeview">
    <button class="btn btn-secondary custom-player-prevnext custom-player-prevnext-prev"
            aria-label="Previous track"
            @click="playerPrevious">
      <i class="fa fa-backward-step" />
    </button>

    <div class="custom-player-time">
      <span class="custom-player-current-time">{{ getDisplayTime(currentTime) }}</span>
      /
      <span class="custom-player-duration">{{ getDisplayTime(duration) }}</span>
    </div>

    <button class="btn btn-secondary custom-player-prevnext custom-player-prevnext-next"
            aria-label="Next track"
            @click="playerNext" :class="{disabled: !hasNextMedia}" :disabled="!hasNextMedia">
      <i class="fa fa-forward-step" />
    </button>
  </div>
</template>

<script>
import {mapActions, mapGetters} from "vuex";

export default {
  name: "timeView",
  props: {
    duration: {
      type: Number
    },
    currentTime: {
      type: Number
    }
  },
  data () {
    return {
      percentProgress: 0
    }
  },
  computed: {
    ...mapGetters(['getNextPlaylistIndex', 'getPrevPlaylistIndex', 'getDisplayTime']),
    hasNextMedia () {
      return this.getNextPlaylistIndex() !== -1
    },
    hasPrevMedia () {
      return this.getPrevPlaylistIndex() !== -1
    }
  },
  methods: {
    ...mapActions(['playTrackInPlaylist']),
    isTrackConsideredClosedToStart () {
      if (this.duration <= 0) return false
      return (this.currentTime < 3)
    },
    playerPrevious () {
      if (this.hasPrevMedia && this.isTrackConsideredClosedToStart()) {
        // go to previous
        const prevIndex = this.getPrevPlaylistIndex()
        if (prevIndex !== -1) {
          this.playTrackInPlaylist(prevIndex)
        }
      } else {
        // start over current track
        this.$emit('restart-track')
      }
    },
    playerNext () {
      const nextIndex = this.getNextPlaylistIndex()
      if (nextIndex !== -1) {
        this.playTrackInPlaylist(nextIndex)
      }
    }
  }
}
</script>

<style lang="scss" scoped>

  .custom-player-timeview {
    .custom-player-prevnext {
      width: var(--simple-button-size);
    }
    .custom-player-time {
      display: inline-block;
      width: -webkit-calc(100% - 2*(var(--simple-button-size)) - 10px);
      width:    -moz-calc(100% - 2*(var(--simple-button-size)) - 10px);
      width:         calc(100% - 2*(var(--simple-button-size)) - 10px);
      text-align: center;
      color: var(--light);
      text-shadow: var(--secondary) 0 0 10px;
    }
  }

</style>
