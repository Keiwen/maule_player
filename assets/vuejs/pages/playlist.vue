<template>
  <div>
    <h1>{{ this.$trans('playlist.title', {}, null, true) }}</h1>

    <button class="btn btn-dark" @click="emptyPlaylist">
      <i class="fa fa-broom" />
      {{ this.$trans('playlist.empty', {}, null, true) }}
    </button>
    <hr/>

    <playlist-track-list :track-list="currentPlaylist"></playlist-track-list>

  </div>
</template>

<script>
import {mapGetters, mapActions} from "vuex";
import playlistTrackList from "../components/listing/playlistTrackList";

export default {
  name: "playlistPage",
  components: { playlistTrackList },
  data () {
    return {
    }
  },
  computed: {
    ...mapGetters(['currentPlaylist'])
  },
  methods: {
    ...mapActions(['emptyPlaylist'])
  }
}
</script>

<style scoped>

</style>
