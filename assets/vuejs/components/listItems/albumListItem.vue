<template>
  <div>

    <div class="item-icon">
      <router-link :to="{ name: 'album', params: { id: album.id, album: album }}" class="btn btn-primary album-link">
        <album-icon />
      </router-link>
    </div>

    <div class="item-text">
      <span class="album-title">{{ getLimitedTitle(album.name, titleLimit) }}</span>
    </div>

    <div class="item-text-compl" v-if="!simpleView">
      <span class="albumYear">{{ album.year }}</span>
    </div>

    <div class="trackCount badge badge-pill badge-secondary" v-if="!simpleView">{{ album.tracksCount }}</div>
    <div class="albumDuration" v-if="!simpleView">{{ albumDuration }}</div>

  </div>
</template>

<script>
import {mapGetters} from "vuex";
import albumIcon from "../icons/albumIcon";

export default {
  name: "albumListItem",
  components: { albumIcon },
  props: {
    album: {
      type: Object,
      required: true
    },
    simpleView: {
      type: Boolean,
      default: false
    }
  },
  computed: {
    ...mapGetters(['getLimitedTitle', 'getDisplayTime']),
    albumDuration () {
      return this.getDisplayTime(this.album.totalDuration)
    },
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

.album-title {
  font-weight: bold;
}

.item-icon {
  float: left;
  margin-right: 10px;
  .album-link {
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

.albumDuration {
  position: absolute;
  right: 5px;
  bottom: 5px;
}


</style>
