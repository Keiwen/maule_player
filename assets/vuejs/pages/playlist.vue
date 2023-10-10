<template>
  <div>

    <div class="row">
      <div class="col-10">
        <h1>{{ this.$trans('playlist.title', {}, null, true) }}</h1>
      </div>
      <div class="col-2 playlist-actions">

        <side-actions>
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

      </div>
    </div>

    <playlist-track-list :track-list="currentPlaylist" :key="playlistKey"></playlist-track-list>

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
      playlistKey: 0
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

.playlist-actions {
  margin-top: 5px;
}


</style>
