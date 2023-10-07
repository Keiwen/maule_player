<template>
  <div class="row">

    <div class="container-fluid row">

      <div class="col-2 item-icon">
        <router-link :to="{ name: 'artist', params: { id: artist.id, artist: artist }}" class="btn btn-primary artist-link">
          <artist-icon />
        </router-link>
      </div>

      <div class="col-10">
        <div class="col-12">
          <span class="artist-title">{{ getLimitedTitle(artist.name, 22) }}</span>
          <span class="trackCount badge badge-pill badge-secondary">{{ artist.tracksCount }}</span>
        </div>
      </div>

    </div>
  </div>
</template>

<script>
import {mapGetters} from "vuex";
import artistIcon from "../icons/artistIcon";

export default {
  name: "artistListItem",
  components: { artistIcon },
  props: {
    artist: {
      type: Object,
      required: true
    }
  },
  computed: {
    ...mapGetters(['getLimitedTitle'])
  }
}
</script>

<style lang="scss" scoped>

.artist-title {
  font-weight: bold;
}

.trackCount {
  font-size: 90%;
  position: absolute;
  right: 0;
  margin-right: -15px;
}

.artist-link {
  width: 50px;
  height: 50px;
  svg {
    margin-left: -5px;
    height: 100%;
  }
}


</style>
