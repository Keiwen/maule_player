<template>
  <div>

    <h1>{{ this.$trans('playlist.title', {}, null, true) }}</h1>

    <side-actions>
      <button class="dropdown-item" @click="switchReordering">
        <i class="fa fa-up-down-left-right" />
        <span v-if="reordering">{{ this.$trans('playlist.stop_reordering', {}, null, true) }}</span>
        <span v-else>{{ this.$trans('playlist.start_reordering', {}, null, true) }}</span>
      </button>
      <button class="dropdown-item" @click="switchLoopPlaylist">
        <i class="fa fa-repeat" />
        <span v-if="loopPlaylist">{{ this.$trans('playlist.unloop', {}, null, true) }}</span>
        <span v-else>{{ this.$trans('playlist.loop', {}, null, true) }}</span>
      </button>
      <button class="dropdown-item" @click="randomizePlaylist">
        <i class="fa fa-shuffle" />
        {{ this.$trans('playlist.shuffle', {}, null, true) }}
      </button>
      <button class="dropdown-item" @click="cleanPlaylist">
        <i class="fa fa-square-minus" />
        {{ this.$trans('playlist.empty', {}, null, true) }}
      </button>
    </side-actions>

    <playlist-track-list :track-list="currentPlaylist" :allow-reorder="reordering" :key="playlistKey"></playlist-track-list>

  </div>
</template>

<script>
import {mapActions, mapGetters} from "vuex";
import playlistTrackList from "../components/listing/playlistTrackList";
import sideActions from "../components/sideActions";

export default {
  name: "playlistPage",
  components: { playlistTrackList, sideActions },
  data () {
    return {
      playlistKey: 0,
      reordering: false,
    }
  },
  computed: {
    ...mapGetters(['currentPlaylist', 'loopPlaylist'])
  },
  methods: {
    ...mapActions(['addSuccess', 'shufflePlaylist', 'emptyPlaylist', 'setLoopPlaylist']),
    cleanPlaylist () {
      this.emptyPlaylist()
      this.reloadTrackList()
      this.addSuccess(this.$trans('playlist.removed', {}, null, true))
    },
    randomizePlaylist () {
      this.shufflePlaylist()
      this.reloadTrackList()
    },
    switchLoopPlaylist () {
      this.setLoopPlaylist(!this.loopPlaylist)
    },
    switchReordering () {
      this.reordering = !this.reordering
    },
    reloadTrackList () {
      // here we force update of list by changing component key
      // we actually have issue when empty playlist
      this.playlistKey = parseInt(this.playlistKey)
      this.playlistKey++
    }
  }
}
</script>

<style lang="scss" scoped>


</style>
