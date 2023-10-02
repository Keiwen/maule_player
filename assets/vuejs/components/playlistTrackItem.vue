<template>
  <div class="row">

    <div class="col-2 item-icon">
      <button class="btn btn-primary btn-play" @click="selectTrack">
        <i class="fa fa-play" />
      </button>
    </div>
    <div class="col-10 row">
      <div class="col-12">
        <span class="track-title">{{ getLimitedTitle(track.name, 25) }}</span>
      </div>

      <div class="col-12">
        <span class="track-artist">{{ getLimitedTitle(track.artist.name, 15) }}</span>
        <span class="track-album">({{ getLimitedTitle(track.album.name, 15) }})</span>
      </div>
    </div>

  </div>
</template>

<script>
import {mapGetters, mapActions} from "vuex";

export default {
  name: "playlistTrackItem",
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
    ...mapGetters(['getLimitedTitle'])
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

.btn-play {
  width: 50px;
  height: 50px;
  svg {
    height: 100%;
  }
}

</style>
