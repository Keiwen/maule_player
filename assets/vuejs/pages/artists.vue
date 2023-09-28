<template>
  <div>
    <h1>{{ this.$trans('artist.list.title', {}, null, true) }}</h1>
    <artist-list :artist-list="artistList"></artist-list>
  </div>
</template>

<script>
import {useRemoteCall} from "../composables/useRemoteCall";
import ArtistList from "../components/artistList";
import {mapActions} from "vuex";

export default {
  name: "artistsPage",
  components: { ArtistList },
  data () {
    return {
      remoteCallData: {},
      remoteCallError: null,
      artistList: []
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
        this.artistList = newValue.artists
      }
    },
    remoteCallError: function(newValue, oldValue) {
      if (newValue !== null && this.remoteCallData === null) {
        this.addError(this.$trans('artist.list.error', {}, null, true))
      }
    }
  },
  mounted () {
    this.updateList()
  },
  methods: {
    ...mapActions(['addError']),
    updateList () {
      const {callData, callError} = useRemoteCall(URL_API.artist_list)
      this.remoteCallData = callData
      this.remoteCallError = callError
    }
  }
}
</script>

<style scoped>

</style>
