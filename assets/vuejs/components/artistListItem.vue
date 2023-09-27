<template>
  <div class="row">

    <div class="container-fluid row">
      <div class="col-12">
        <span class="artist-title">{{ getLimitedTitle(artist.name, 30) }}</span>
      </div>
    </div>
  </div>
</template>

<script>
import {mapGetters} from "vuex";

export default {
  name: "artistListItem",
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

</style>
