<template>
  <div class="row">

    <div class="container-fluid row">
      <div class="col-12">
        <span class="track-title">{{ limitTitle(track.name, 30) }}</span>
      </div>
    </div>

    <div class="container-fluid row">

      <div class="col-2">
        <button class="btn btn-primary btn-play" @click="selectTrack">
          <v-icon name="play"/>
        </button>
      </div>
      <div class="col-10 row">
        <div class="col-12">
          <span class="track-artist">{{ limitTitle(track.artist.name, 30) }}</span>
        </div>

        <div class="col-2">
          <span class="track-number">#{{ track.trackNumber }}</span>
        </div>
        <div class="col-8">
          <span class="track-album">{{ limitTitle(track.album.name, 20) }}</span>
        </div>
        <div class="col-2">
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
    ...mapGetters(['limitTitle'])
  },
  methods: {
    ...mapActions(['setPlayerSrc']),
    selectTrack() {
      this.setPlayerSrc(this.track.filepath)
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

.btn-play {
  color: var(--background);
}

</style>
