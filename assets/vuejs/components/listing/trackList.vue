<template>
  <div>
    <div class="container-fluid" v-if="allowSearch">
      <div class="form-inline row">
        <input class="form-control track-search" type="search" v-model="search"
               :placeholder="this.$trans('track.list.search', {}, null, true)" aria-label="Search">
      </div>
      <hr/>
    </div>

    <ul class="list-group">
      <li class="list-group-item" v-for="(track, trackIndex) in trackList" :key="'key-'+trackIndex"
           v-if="isItemMatchSearch(track.name)">
        <track-list-item :track="track" />
      </li>
    </ul>

  </div>
</template>

<script>
import trackListItem from "../listItems/trackListItem";
import playlistTrackItem from "../listItems/playlistTrackItem";
import {mapActions, mapGetters} from "vuex";

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

  .form-control.track-search {
    width: 100%;
  }

  .list-group-item {
    background-color: transparent;
    padding: .2rem 0.5rem;
    border: 1px solid var(--primary);
  }

</style>
