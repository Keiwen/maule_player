<template>
  <div>
    <h1><artist-icon /> {{ artist.name }}</h1>
    <h2>{{ this.$trans('artist.recent_tracks', {}, null, true) }}</h2>
    <track-list :track-list="trackList" :allowSearch="false"></track-list>
  </div>
</template>

<script>
import {useRemoteCall} from "../composables/useRemoteCall";
import {mapActions} from "vuex";
import ArtistIcon from "../components/artistIcon";
import TrackList from "../components/trackList";

export default {
  name: "artistPage",
  components: { TrackList, ArtistIcon },
  data () {
    return {
      artist: {},
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
    if (this.$route.params.artist !== undefined) {
      this.artist = this.$route.params.artist
      this.updateList()
    }
  },
  methods: {
    ...mapActions(['addError']),
    updateList () {
      const urlToCall = this.$url(URL_API.artist_tracks, {id: this.artist.id, limit: 5})
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
