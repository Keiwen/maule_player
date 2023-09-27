<template>
  <div class="row">

    <div class="container-fluid row">
      <div class="col-12">
        <span class="album-title">{{ getLimitedTitle(album.name, 30) }}</span>
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
    ...mapGetters(['getLimitedTitle'])
  }
}
</script>

<style lang="scss" scoped>

.album-title {
  font-weight: bold;
}

</style>
