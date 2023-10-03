<template>
  <div>
    <div class="container-fluid" v-if="allowSearch">
      <div class="form-inline row">
        <input class="form-control" type="search" v-model="search"
               :placeholder="this.$trans('track.list.search', {}, null, true)" aria-label="Search">
      </div>
      <hr/>
    </div>

    <ul class="list-group">
      <li class="list-group-item container" v-for="(track, trackIndex) in trackList"
          :class="{'playlist-item-active': playlistDisplay && (currentTrackIndex === trackIndex)}"
          v-if="isItemMatchSearch(track.name)">
        <playlist-track-item :track="track" :track-index="trackIndex" v-if="playlistDisplay" />
        <track-list-item :track="track" v-else />
      </li>
    </ul>
  </div>
</template>

<script>
import trackListItem from "../listItems/trackListItem";
import playlistTrackItem from "../listItems/playlistTrackItem";
import {mapGetters} from "vuex";

export default {
  name: "trackList",
  components: { trackListItem, playlistTrackItem },
  props: {
    trackList: {
      type: Array,
      required: true
    },
    allowSearch: {
      type: Boolean,
      default: true
    },
    playlistDisplay: {
      type: Boolean,
      default: false
    }
  },
  data () {
    return {
      search: ''
    }
  },
  computed: {
    ...mapGetters(['currentTrackIndex'])
  },
  methods: {
    isItemMatchSearch (name) {
      if (this.search === '') return true
      return name.toLowerCase().includes(this.search.toLowerCase())
    }
  }
}
</script>

<style lang="scss" scoped>

  .list-group-item {
    background-color: transparent;
    padding: .2rem 0.5rem;
    border: 1px solid var(--primary);
    &.playlist-item-active {
      border: 1px solid var(--secondary);
      box-shadow: 0px 0px 20px var(--secondary);
     }
  }

  .track-list-empty {
    font-style: italic;
    font-size: 0.8rem;
  }

</style>
