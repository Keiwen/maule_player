<template>
  <div class="row">

    <div class="container-fluid row">
      <div class="col-12">
        <span class="track-title">{{ limitTitle(track.name, 30) }}</span>
      </div>
    </div>
    <div class="container-fluid row">
      <div class="col-12">
        <span class="track-artist">{{ limitTitle(track.artist.name, 30) }}</span>
      </div>
    </div>

    <div class="container-fluid row">
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
</template>

<script>
import {mapGetters} from "vuex";

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
  }
}
</script>

<style scoped>

.track-title {
  font-size: 1.2rem;
  font-weight: bold;
}
.track-artist {
  font-weight: bold;
}

</style>
