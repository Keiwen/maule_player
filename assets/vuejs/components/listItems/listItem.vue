<template>
  <div>
    <div class="item-title" v-if="itemTitle">
      {{ getLimitedTitle(itemTitle, titleLimit) }}
    </div>

    <div class="item-icon">
      <router-link :to="linkRouteParam" class="btn btn-primary item-link">
        <artist-icon v-if="itemType === 'artist'" />
        <album-icon v-if="itemType === 'album'" />
        <track-icon v-if="itemType === 'track'" />
      </router-link>
    </div>

    <div class="item-box">
      <div class="item-text">
        <span class="item-main-text">{{ getLimitedTitle(mainText, titleLimit) }}</span>
      </div>
      <div class="item-text-compl" v-if="!simpleView">
        <span class="item-sub-text">{{ subText }}</span>
      </div>

      <div class="item-tag-top" :class="{'item-tag-top-low': itemTitle}" v-if="!simpleView">
        <slot name="tag_top"></slot>
      </div>
      <div class="item-tag-bottom" v-if="!simpleView">
        <slot name="tag_bottom"></slot>
      </div>
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
    itemTitle: {
      type: String,
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
      if (this.itemType === 'track') {
        return this.getLimitedTitle(this.item.artist.name, this.smallTitleLimit)
      }
      return this.item.name
    },
    subText () {
      if (this.itemType === 'album') return this.item.year
      if (this.itemType === 'track') {
        const trackNumber = '#' + this.item.trackNumber
        const albumName = this.getLimitedTitle(this.item.album.name, this.smallTitleLimit - 5)
        return trackNumber + ' ' + albumName
      }
      return ''
    },
    titleLimit () {
      if (this.$root.screenWidthClass === 'xl') return 150
      if (this.$root.screenWidthClass === 'lg') return 100
      if (this.$root.screenWidthClass === 'md') return 80
      if (this.$root.screenWidthClass === 'sm') return 50
      return 22
    },
    smallTitleLimit () {
      if (this.$root.screenWidthClass === 'xl') return 140
      if (this.$root.screenWidthClass === 'lg') return 90
      if (this.$root.screenWidthClass === 'md') return 70
      if (this.$root.screenWidthClass === 'sm') return 45
      return 18
    }
  }
}
</script>

<style lang="scss" scoped>

.item-title {
  font-size: 1.2rem;
  font-weight: bold;
}

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
  &.item-tag-top-low {
    top: 30px;
  }
}

.item-tag-bottom {
  position: absolute;
  right: 5px;
  bottom: 5px;
}

.badge {
  font-size: 90%;
}


</style>
