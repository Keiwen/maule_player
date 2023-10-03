<template>
  <div>
    <h1>{{ this.$trans('album.list.title', {}, null, true) }}</h1>
    <album-list :album-list="albumList"></album-list>
    <loading-icon v-if="isLoading" />
  </div>
</template>

<script>
import {useRemoteCall} from "../composables/useRemoteCall";
import albumList from "../components/listing/albumList";
import {mapActions} from "vuex";
import loadingIcon from "../components/icons/loadingIcon";

export default {
  name: "albumsPage",
  components: { albumList, loadingIcon },
  data () {
    return {
      remoteCallData: {},
      remoteCallError: null,
      albumList: []
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
        this.albumList = newValue.albums
      }
    },
    remoteCallError: function(newValue, oldValue) {
      if (newValue !== null && this.remoteCallData === null) {
        this.addError(this.$trans('album.list.error', {}, null, true))
      }
    }
  },
  mounted () {
    this.updateList()
  },
  methods: {
    ...mapActions(['addError']),
    updateList () {
      const urlToCall = this.$url(URL_API.album_list, {})
      const {callData, callError} = useRemoteCall(urlToCall)
      this.remoteCallData = callData
      this.remoteCallError = callError
    }
  }
}
</script>

<style scoped>

</style>
