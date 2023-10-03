<template>
  <div class="row">

    <div class="container-fluid row">

      <div class="col-2 item-icon">
        <router-link :to="{ name: 'album', params: { album: album }}" class="btn btn-primary album-link">
          <album-icon />
        </router-link>
      </div>

      <div class="col-10">
        <div class="col-12">
          <span class="album-title">{{ getLimitedTitle(album.name, 25) }}</span>
          <span class="trackCount badge badge-pill badge-secondary">{{ album.tracksCount }}</span>
        </div>
        <div class="col-12">
          <span class="albumYear">{{ album.year }}</span>
          <span class="albumDuration">{{ albumDuration }}</span>
        </div>
      </div>

    </div>
  </div>
</template>

<script>
import {mapGetters} from "vuex";
import albumIcon from "./icons/albumIcon";

export default {
  name: "albumListItem",
  components: { albumIcon },
  props: {
    album: {
      type: Object,
      required: true
    }
  },
  computed: {
    ...mapGetters(['getLimitedTitle', 'getDisplayTime']),
    albumDuration () {
      return this.getDisplayTime(this.album.totalDuration)
    }
  }
}
</script>

<style lang="scss" scoped>

.album-title {
  font-weight: bold;
}

.trackCount {
  font-size: 90%;
  position: absolute;
  right: 0;
  margin-right: -15px;
}

.albumDuration {
  position: absolute;
  right: 0;
  margin-right: -15px;
}

.album-link {
  width: 50px;
  height: 50px;
  svg {
    margin-left: -5px;
    height: 100%;
  }
}


</style>
