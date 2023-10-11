<template>
  <div :class="{'item-active': isCurrent}">

    <div class="item-icon">
      <router-link :to="{ name: 'track', params: { id: track.id, track: track }}" class="btn btn-primary track-link">
        <track-icon />
      </router-link>
    </div>

    <div class="item-box ddHandle">
      <div class="item-text">
        <span class="track-title">{{ getLimitedTitle(track.name, titleLimit) }}</span>
      </div>

      <div class="item-text-compl">
        <span class="track-number">#{{ trackIndex + 1 }}</span>
        <span class="track-artist">{{ getLimitedTitle(track.artist.name, smallTitleLimit) }}</span>
      </div>

    </div>

    <div class="item-action">
      <button class="btn btn-light btn-pause" disabled>
        <i class="fa fa-pause" />
      </button>
      <button class="btn btn-secondary btn-play" @click="selectTrack">
        <i class="fa fa-play" />
      </button>
    </div>

  </div>
</template>

<script>
import {mapGetters, mapActions} from "vuex";
import trackIcon from "../icons/trackIcon";
import TrackIcon from "../icons/trackIcon";

export default {
  name: "playlistTrackItem",
  components: {TrackIcon},
  component: { trackIcon },
  props: {
    track: {
      type: Object,
      required: true
    },
    trackIndex: {
      type: Number,
      required: true
    }
  },
  computed: {
    ...mapGetters(['getLimitedTitle', 'currentTrackIndex']),
    isCurrent () {
      return this.trackIndex === this.currentTrackIndex
    },
    titleLimit () {
      if (this.$root.screenWidthClass === 'xl') return 150
      if (this.$root.screenWidthClass === 'lg') return 100
      if (this.$root.screenWidthClass === 'md') return 80
      if (this.$root.screenWidthClass === 'sm') return 50
      return 22
    },
    smallTitleLimit () {
      if (this.$root.screenWidthClass === 'xl') return 140
      if (this.$root.screenWidthClass === 'lg') return 90
      if (this.$root.screenWidthClass === 'md') return 70
      if (this.$root.screenWidthClass === 'sm') return 45
      return 18
    }
  },
  methods: {
    ...mapActions(['playTrackInPlaylist']),
    selectTrack() {
      this.playTrackInPlaylist(this.trackIndex)
    }
  }
}
</script>

<style lang="scss" scoped>

.track-title {
  font-weight: bold;
}

.btn-play,.btn-pause {
  float: right;
  height: var(--simple-button-size);
  width: var(--simple-button-size);
  svg {
    height: 100%;
    margin-left: -5px;
  }
}

.btn-pause {
  display: none;
}

.item-active {
  .btn-play {
    display: none;
  }
  .btn-pause {
    display: block;
  }
}

.item-icon {
  float: left;
  margin-right: 10px;
  .track-link {
    height: var(--simple-button-size);
    width: var(--simple-button-size);
    svg {
      margin-left: -5px;
      height: 100%;
    }
  }
}

.item-box {
  width: -webkit-calc(100% - 2*(var(--simple-button-size)) - 10px);
  width:    -moz-calc(100% - 2*(var(--simple-button-size)) - 10px);
  width:         calc(100% - 2*(var(--simple-button-size)) - 10px);
  float: left;
}


.track-order {
  margin-top: -5px;
}

.ddHandle {
  cursor: move;
}

</style>
