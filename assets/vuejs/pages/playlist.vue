<template>
  <div>
    <h1>{{ this.$trans('playlist.title', {}, null, true) }}</h1>

    <button class="btn btn-dark" @click="emptyPlaylist">
      <i class="fa fa-broom" />
      {{ this.$trans('playlist.empty', {}, null, true) }}
    </button>
    <hr/>

    <track-list :track-list="currentPlaylist" :allowSearch="false" :playlist-display="true"></track-list>

  </div>
</template>

<script>
import {mapGetters, mapActions} from "vuex";
import trackList from "../components/listing/trackList";

export default {
  name: "playlistPage",
  components: { trackList },
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
