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
        <list-item item-type="track" :item="track" :item-title="track.name"
                   :link-route-param="{ name: 'track', params: { id: track.id, track: track }}">

          <template v-slot:tag_top>
            <div>{{ track.year }}</div>
          </template>
          <template v-slot:tag_bottom>
            <div>{{ getDisplayTime(track.duration) }}</div>
          </template>
          <template v-slot:actions>
            <button class="btn btn-secondary btn-playlist" @click="selectTrack(track)">
              <i class="fa fa-folder-plus" />
            </button>
          </template>

        </list-item>
      </li>
    </ul>

  </div>
</template>

<script>
import listItem from "./listItem";
import {mapActions, mapGetters} from "vuex";

export default {
  name: "trackList",
  components: { listItem },
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
    ...mapGetters(['currentTrackIndex', 'getDisplayTime'])
  },
  methods: {
    ...mapActions(['addTracksInPlaylist']),
    selectTrack(track) {
      this.addTracksInPlaylist([track])
    },
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

</style>
