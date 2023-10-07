<template>
  <div class="row">

    <div class="container-fluid row">
      <div class="col-12">
        <span class="track-title">{{ getLimitedTitle(track.name, 32) }}</span>
      </div>
    </div>

    <div class="container-fluid row">

      <div class="col-2 item-icon">
        <button class="btn btn-primary btn-playlist" @click="selectTrack">
          <i class="fa fa-folder-plus" />
        </button>
      </div>
      <div class="col-10 row">
        <div class="col-12">
          <span class="track-artist">{{ getLimitedTitle(track.artist.name, 22) }}</span>
          <span class="track-duration">{{ trackDuration }}</span>
        </div>

        <div class="col-2">
          <span class="track-number">#{{ track.trackNumber }}</span>
        </div>
        <div class="col-10">
          <span class="track-album">{{ getLimitedTitle(track.album.name, 18) }}</span>
          <span class="track-year">{{ track.year }}</span>
        </div>
      </div>

    </div>
  </div>
</template>

<script>
import {mapGetters, mapActions} from "vuex";

export default {
  name: "trackListItem",
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
  width: 40px;
  height: 40px;
  svg {
    height: 100%;
    margin-left: -6px;
  }
}

.track-duration {
  position: absolute;
  right: 0;
  margin-right: -45px;
}

.track-year {
  position: absolute;
  right: 0;
  margin-right: -45px;
}


</style>
