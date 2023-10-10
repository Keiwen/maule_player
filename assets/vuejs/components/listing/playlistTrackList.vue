<template>
  <div>
    <div class="row col-12">
      <span>
        {{ this.$trans('playlist.duration', {}, null, true, true) }}
        {{ getDisplayTime(currentPlaylistDuration) }}
      </span>
      <span class="ml-1" v-if="loopPlaylist">
          <i class="fa fa-repeat" />
      </span>
    </div>

    <hr/>

    <vue-draggable v-model="orderedTrackList"
                   v-bind="{animation: 150, handle: '.ddHandle', group: 'playlist'}"
                   @start="startDrag" @end="endDrag">
      <transition-group id="dropzone-tracklist" tag="div">
        <div class="list-group-item container" v-for="(track, trackIndex) in orderedTrackList" :key="'key-'+trackIndex"
             :class="{'playlist-item-active': (currentTrackIndex === trackIndex)}">
          <playlist-track-item :track="track" :track-index="trackIndex" />
        </div>
      </transition-group>
    </vue-draggable>
  </div>
</template>

<script>
import playlistTrackItem from "../listItems/playlistTrackItem";
import {mapActions, mapGetters} from "vuex";
import sideActions from "../sideActions";

export default {
  name: "trackList",
  components: { playlistTrackItem, sideActions },
  props: {
    trackList: {
      type: Array,
      required: true
    }
  },
  data () {
    return {
      orderedTrackList: []
    }
  },
  computed: {
    ...mapGetters(['currentTrackIndex', 'currentPlaylistDuration', 'getDisplayTime', 'loopPlaylist'])
  },
  mounted () {
    this.orderedTrackList = this.trackList
  },
  methods: {
    ...mapActions(['changeTrackIndex', 'grabPlaylistElement', 'dropPlaylistElement', 'removeTrackByIndex',
      'addSuccess']),
    startDrag (e) {
      this.grabPlaylistElement()
    },
    endDrag (e) {
      this.dropPlaylistElement()
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

  #dropzone-tracklist {
    height: 100%;
    width: 100%;
    min-height: 400px;
  }

</style>
