<template>
  <div>
    <div class="container-fluid" v-if="allowSearch">
      <div class="form-inline row">
        <input class="form-control" type="search" v-model="search"
               :placeholder="this.$trans('track.list.search', {}, null, true)" aria-label="Search">
      </div>
      <hr/>
    </div>

    <vue-draggable v-model="orderedTrackList"
                   v-bind="{animation: 150, handle: '.ddHandle', group: 'playlist'}"
                   @start="startDrag" @end="endDrag">
      <transition-group id="dropzone-tracklist" tag="div">
        <div class="list-group-item container" v-for="(track, trackIndex) in orderedTrackList" :key="'key-'+trackIndex"
             :class="{'playlist-item-active': playlistDisplay && (currentTrackIndex === trackIndex)}"
             v-if="isItemMatchSearch(track.name)">
          <playlist-track-item :track="track" :track-index="trackIndex" v-if="playlistDisplay" />
          <track-list-item :track="track" v-else />
        </div>
      </transition-group>
    </vue-draggable>

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
    },
    playlistDisplay: {
      type: Boolean,
      default: false
    }
  },
  data () {
    return {
      search: '',
      orderedTrackList: []
    }
  },
  computed: {
    ...mapGetters(['currentTrackIndex'])
  },
  mounted () {
    this.orderedTrackList = this.trackList
  },
  methods: {
    ...mapActions(['changeTrackIndex', 'grabPlaylistElement', 'dropPlaylistElement', 'removeTrackByIndex', 'addSuccess']),
    isItemMatchSearch (name) {
      if (this.search === '') return true
      return name.toLowerCase().includes(this.search.toLowerCase())
    },
    startDrag (e) {
      this.grabPlaylistElement();
    },
    endDrag (e) {
      this.dropPlaylistElement();
      if (e.to.id === 'dropzone-tracklist') {
        // dropped in playlist
        this.changeTrackIndex({oldIndex: e.oldIndex, newIndex: e.newIndex})
      } else if (e.to.id === 'dropzone-trashlist') {
        // dropped in trash
        this.removeTrackByIndex({index: e.oldIndex, loadNextIfCurrent: true})
        this.addSuccess(this.$trans('playlist.removed', {}, null, true))
      }
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
      box-shadow: 0 0 20px var(--secondary);
     }
  }

  .track-list-empty {
    font-style: italic;
    font-size: 0.8rem;
  }

  #dropzone-tracklist {
    height: 100%;
    width: 100%;
    min-height: 400px;
  }

</style>
