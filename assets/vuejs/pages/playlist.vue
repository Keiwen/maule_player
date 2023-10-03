<template>
  <div>
    <h1>{{ this.$trans('playlist.title', {}, null, true) }}</h1>

    <playlist-track-list :track-list="currentPlaylist"></playlist-track-list>

  </div>
</template>

<script>
import {mapGetters} from "vuex";
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
  }
}
</script>

<style scoped>

</style>
