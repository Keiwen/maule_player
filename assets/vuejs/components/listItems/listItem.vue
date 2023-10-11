<template>
  <div>

    <div class="item-icon">
      <router-link :to="linkRouteParam" class="btn btn-primary item-link">
        <artist-icon v-if="itemType === 'artist'" />
        <album-icon v-if="itemType === 'album'" />
        <track-icon v-if="itemType === 'track'" />
      </router-link>
    </div>

    <div class="item-text">
        <span class="item-main-text">{{ getLimitedTitle(mainText, titleLimit) }}</span>
    </div>

    <div class="item-tag-top" v-if="!simpleView">
      <slot name="tag_top"></slot>
    </div>

  </div>
</template>

<script>
import {mapGetters} from "vuex";
import artistIcon from "../icons/artistIcon";
import albumIcon from "../icons/albumIcon";
import trackIcon from "../icons/trackIcon";

export default {
  name: "listItem",
  components: { artistIcon, albumIcon, trackIcon },
  props: {
    item: {
      type: Object,
      required: true
    },
    itemType: {
      type: String,
      required: true
    },
    linkRouteParam: {
      type: Object,
    },
    simpleView: {
      type: Boolean,
      default: false
    }
  },
  computed: {
    ...mapGetters(['getLimitedTitle']),
    mainText () {
      return this.item.name
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

.item-main-text {
  font-weight: bold;
}

.item-icon {
  float: left;
  margin-right: 10px;
  .item-link {
    height: var(--simple-button-size);
    width: var(--simple-button-size);
    svg {
      margin-left: -5px;
      height: 100%;
    }
  }
}

.item-tag-top {
  position: absolute;
  right: 5px;
  top: 5px;
}

.badge {
  font-size: 90%;
}


</style>
