<template>
  <div>

    <div class="item-icon">
      <router-link :to="{ name: 'artist', params: { id: artist.id, artist: artist }}" class="btn btn-primary artist-link">
        <artist-icon />
      </router-link>
    </div>

    <div class="item-text">
        <span class="artist-title">{{ getLimitedTitle(artist.name, titleLimit) }}</span>
    </div>

    <div class="trackCount badge badge-pill badge-secondary" v-if="!simpleView">{{ artist.tracksCount }}</div>

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
    },
    simpleView: {
      type: Boolean,
      default: false
    }
  },
  computed: {
    ...mapGetters(['getLimitedTitle']),
    titleLimit () {
      if (this.$root.screenWidthClass === 'xl') return 150
      if (this.$root.screenWidthClass === 'lg') return 100
      if (this.$root.screenWidthClass === 'md') return 80
      if (this.$root.screenWidthClass === 'sm') return 50
      return 22
    }
  }
}
</script>

<style lang="scss" scoped>

.artist-title {
  font-weight: bold;
}

.item-icon {
  float: left;
  margin-right: 10px;
  .artist-link {
    height: var(--simple-button-size);
    width: var(--simple-button-size);
    svg {
      margin-left: -5px;
      height: 100%;
    }
  }
}

.trackCount {
  font-size: 90%;
  position: absolute;
  right: 5px;
  top: 5px;
}



</style>
