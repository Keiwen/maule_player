<template>
  <div>
    <h1>Albums list</h1>
    <album-list :album-list="albumList"></album-list>
  </div>
</template>

<script>
import {useRemoteCall} from "../composables/useRemoteCall";
import AlbumList from "../components/albumList";
import {mapActions} from "vuex";

export default {
  name: "albumsPage",
  components: { AlbumList },
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
      const {callData, callError} = useRemoteCall(URL_API.album_list)
      this.remoteCallData = callData
      this.remoteCallError = callError
    }
  }
}
</script>

<style scoped>

</style>
