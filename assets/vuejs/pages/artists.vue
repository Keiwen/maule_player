<template>
  <div>
    <h1>{{ this.$trans('artist.list.title', {}, null, true) }}</h1>
    <artist-list :artist-list="artistList"></artist-list>
    <loading-icon v-if="isLoading" />
  </div>
</template>

<script>
import {useRemoteCall} from "../composables/useRemoteCall";
import artistList from "../components/listing/artistList";
import {mapActions, mapGetters} from "vuex";
import loadingIcon from "../components/icons/loadingIcon";

export default {
  name: "artistsPage",
  components: { artistList, loadingIcon },
  data () {
    return {
      remoteCallData: {},
      remoteCallError: null,
      artistList: []
    }
  },
  computed: {
    ...mapGetters(['getArtists']),
    isLoading () {
      return (this.remoteCallData == null && this.remoteCallError == null)
    },
  },
  watch: {
    remoteCallData: function(newValue, oldValue) {
      if (newValue !== null && this.remoteCallError === null) {
        this.artistList = newValue.artists
        // store result
        this.storeArtists(this.artistList)
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
    ...mapActions(['addError', 'storeArtists']),
    updateList () {
      this.artistList = []
      // check in storage first
      this.artistList = this.getArtists()
      if (this.artistList.length !== 0) return
      // if empty look in API
      const urlToCall = this.$url(URL_API.artist_list, {})
      const {callData, callError} = useRemoteCall(urlToCall)
      this.remoteCallData = callData
      this.remoteCallError = callError
    }
  }
}
</script>

<style scoped>

</style>
