<template>
  <div class="row">

    <div class="container-fluid row">

      <div class="col-2 item-icon">
        <i class="fa fa-compact-disc"/>
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

export default {
  name: "albumListItem",
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

.item-icon svg {
  height: 100%;
  max-height: 50px;
}

</style>
