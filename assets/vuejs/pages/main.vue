<template>
  <div>
    <h1>{{ this.$trans('track.list.title', {}, null, true) }}</h1>
    <track-list :track-list="trackList"></track-list>
  </div>
</template>

<script>
import {useRemoteCall} from "../composables/useRemoteCall";
import TrackList from "../components/trackList";
import {mapActions} from "vuex";

export default {
  name: "mainPage",
  components: { TrackList },
  data () {
    return {
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
    this.updateList()
  },
  methods: {
    ...mapActions(['addError']),
    updateList () {
      const urlToCall = this.$url(URL_API.track_list, {})
      const {callData, callError} = useRemoteCall(urlToCall)
      this.remoteCallData = callData
      this.remoteCallError = callError
    }
  }
}
</script>

<style scoped>

</style>
