<template>
  <div>
    <div>
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
      <transition-group id="dropzone-tracklist" tag="ul" class="list-group">
        <li class="list-group-item" v-for="(track, trackIndex) in orderedTrackList" :key="'key-'+trackIndex"
            :class="{'playlist-item-active': (currentTrackIndex === trackIndex)}">
          <list-item item-type="playlist" :item="track" :item-index="trackIndex"
                     :class="{'item-active': (trackIndex === currentTrackIndex)}"
                     :draggable="allowReorder" >
            <template v-slot:actions>
              <button class="btn btn-light btn-pause" disabled>
                <i class="fa fa-pause" />
              </button>
              <button class="btn btn-secondary btn-play" @click="selectTrack(trackIndex)">
                <i class="fa fa-play" />
              </button>
            </template>

          </list-item>
        </li>
      </transition-group>
    </vue-draggable>
  </div>
</template>

<script>
import listItem from "./listItem";
import {mapActions, mapGetters} from "vuex";
import sideActions from "../sideActions";

export default {
  name: "trackList",
  components: { listItem, sideActions },
  props: {
    trackList: {
      type: Array,
      required: true
    },
    allowReorder: {
      type: Boolean,
      required: false
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
      'addSuccess', 'playTrackInPlaylist']),
    selectTrack(playlistIndex) {
      this.playTrackInPlaylist(playlistIndex)
    },
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

  #dropzone-tracklist {
    height: 100%;
    width: 100%;
    min-height: 400px;
  }

  .btn-pause {
    display: none;
  }

  .item-active {
    .btn-play {
      display: none;
    }
    .btn-pause {
      display: block;
    }
  }


</style>
