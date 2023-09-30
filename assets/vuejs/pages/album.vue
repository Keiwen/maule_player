<template>
  <div>
    <h1><album-icon /> {{ album.name }}</h1>
    <h2>{{ this.$trans('album.all_tracks', {}, null, true) }}</h2>
    <track-list :track-list="trackList" :allowSearch="false"></track-list>
    <loading-icon v-if="isLoading" />
  </div>
</template>

<script>
import {useRemoteCall} from "../composables/useRemoteCall";
import {mapActions} from "vuex";
import AlbumIcon from "../components/albumIcon";
import TrackList from "../components/trackList";
import loadingIcon from "../components/loadingIcon";

export default {
  name: "albumPage",
  components: { TrackList, AlbumIcon, loadingIcon },
  data () {
    return {
      album: {},
      remoteCallData: {},
      remoteCallError: null,
      trackList: []
    }
  },
  computed: {
    isLoading () {
      return (this.remoteCallData == null && this.remoteCallError == null)
    },
  },
  watch: {
    remoteCallData: function(newValue, oldValue) {
      if (newValue !== null && this.remoteCallError === null) {
        this.trackList = newValue.tracks
      }
    },
    remoteCallError: function(newValue, oldValue) {
      if (newValue !== null && this.remoteCallData === null) {
        this.addError(this.$trans('track.list.error', {}, null, true))
      }
    }
  },
  mounted () {
    if (this.$route.params.album !== undefined) {
      this.album = this.$route.params.album
      this.updateList()
    }
  },
  methods: {
    ...mapActions(['addError']),
    updateList () {
      const urlToCall = this.$url(URL_API.album_tracks, {id: this.album.id})
      const {callData, callError} = useRemoteCall(urlToCall)
      this.remoteCallData = callData
      this.remoteCallError = callError
    }
  }
}
</script>

<style lang="scss" scoped>

h1 svg {
  max-height: 30px;
}

</style>
