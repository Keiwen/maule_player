<template>
  <div>

    <div class="item-title">
      <span class="track-title">{{ getLimitedTitle(track.name, titleLimit) }}</span>
    </div>

    <div class="item-icon">
      <router-link :to="{ name: 'track', params: { id: track.id, track: track }}" class="btn btn-primary track-link">
        <track-icon />
      </router-link>
    </div>

    <div class="item-box">

      <div class="item-text">
        <span class="track-artist">{{ getLimitedTitle(track.artist.name, smallTitleLimit) }}</span>
      </div>

      <div class="item-text-compl">
        <span class="track-number">#{{ track.trackNumber }}</span>
        <span class="track-album">{{ getLimitedTitle(track.album.name, (smallTitleLimit - 5)) }}</span>
      </div>

      <div class="trackYear">{{ track.year }}</div>
      <div class="trackDuration">{{ trackDuration }}</div>

    </div>

    <div class="item-action">
      <button class="btn btn-secondary btn-playlist" @click="selectTrack">
        <i class="fa fa-folder-plus" />
      </button>
    </div>

  </div>
</template>

<script>
import {mapGetters, mapActions} from "vuex";
import trackIcon from "../icons/trackIcon";

export default {
  name: "trackListItem",
  components: { trackIcon },
  props: {
    track: {
      type: Object,
      required: true
    }
  },
  computed: {
    ...mapGetters(['getLimitedTitle', 'getDisplayTime']),
    trackDuration () {
      return this.getDisplayTime(this.track.duration)
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
    ...mapActions(['addTracksInPlaylist']),
    selectTrack() {
      this.addTracksInPlaylist([this.track])
    }
  }
}
</script>

<style lang="scss" scoped>

.track-title {
  font-size: 1.2rem;
  font-weight: bold;
}
.track-artist {
  font-weight: bold;
}

.btn-playlist {
  float: right;
  height: var(--simple-button-size);
  width: var(--simple-button-size);
  svg {
    height: 100%;
    margin-left: -5px;
  }
}

.trackYear {
  position: absolute;
  right: -webkit-calc(var(--simple-button-size) + 15px);
  right:    -moz-calc(var(--simple-button-size) + 15px);
  right:         calc(var(--simple-button-size) + 15px);
  top: 30px;
}

.trackDuration {
  position: absolute;
  right: -webkit-calc(var(--simple-button-size) + 15px);
  right:    -moz-calc(var(--simple-button-size) + 15px);
  right:         calc(var(--simple-button-size) + 15px);
  bottom: 5px;
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


</style>
