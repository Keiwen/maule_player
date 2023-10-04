<template>
  <div>
    <h1><album-icon /> {{ album.name }}</h1>
    <div class="row">
      <div class="col-10">
        <h2>{{ this.$trans('album.all_tracks', {}, null, true) }}</h2>
      </div>
      <div class="col-2 album-actions">

        <side-actions>
          <button class="dropdown-item" @click="setAsPlaylist">
            <i class="fa fa-play" />
            {{ this.$trans('album.set_as_playlist', {}, null, true) }}
          </button>
          <button class="dropdown-item" @click="addInPlaylist">
            <i class="fa fa-folder-plus" />
            {{ this.$trans('album.add_to_playlist', {}, null, true) }}
          </button>
        </side-actions>

      </div>
    </div>
    <track-list :track-list="trackList" :allowSearch="false"></track-list>
    <loading-icon v-if="isLoading" />
  </div>
</template>

<script>
import {useRemoteCall} from "../composables/useRemoteCall";
import {mapActions} from "vuex";
import albumIcon from "../components/icons/albumIcon";
import trackList from "../components/listing/trackList";
import loadingIcon from "../components/icons/loadingIcon";
import sideActions from "../components/sideActions";

export default {
  name: "albumPage",
  components: { trackList, albumIcon, loadingIcon, sideActions },
  data () {
    return {
      album: {},
      remoteCallTrackData: {},
      remoteCallTrackError: null,
      remoteCallAlbumData: {},
      remoteCallAlbumError: null,
      trackList: []
    }
  },
  computed: {
    isLoading () {
      return ((this.remoteCallTrackData == null && this.remoteCallTrackError == null)
          || (this.remoteCallAlbumData == null && this.remoteCallAlbumError == null))
    },
  },
  watch: {
    remoteCallTrackData: function(newValue, oldValue) {
      if (newValue !== null && this.remoteCallTrackError === null) {
        this.trackList = newValue.tracks
      }
    },
    remoteCallTrackError: function(newValue, oldValue) {
      if (newValue !== null && this.remoteCallTrackData === null) {
        this.addError(this.$trans('track.list.error', {}, null, true))
      }
    },
    remoteCallAlbumData: function(newValue, oldValue) {
      if (newValue !== null && this.remoteCallAlbumError === null) {
        this.album = newValue.album
        this.updateTrackList()
      }
    },
    remoteCallAlbumError: function(newValue, oldValue) {
      if (newValue !== null && this.remoteCallAlbumData === null) {
        this.addError(this.$trans('album.get.error', {}, null, true))
      }
    }
  },
  mounted () {
    if (this.$route.params.album !== undefined) {
      this.album = this.$route.params.album
      this.updateTrackList()
    } else if(this.$route.params.id !== undefined) {
      this.updateAlbumData(this.$route.params.id)
    }
  },
  methods: {
    ...mapActions(['addError', 'addTracksInPlaylist', 'emptyPlaylist']),
    updateTrackList () {
      const urlToCall = this.$url(URL_API.album_tracks, {id: this.album.id})
      const {callData, callError} = useRemoteCall(urlToCall)
      this.remoteCallTrackData = callData
      this.remoteCallTrackError = callError
    },
    updateAlbumData (id) {
      const urlToCall = this.$url(URL_API.album_get, {id: id})
      const {callData, callError} = useRemoteCall(urlToCall)
      this.remoteCallAlbumData = callData
      this.remoteCallAlbumError = callError
    },
    addInPlaylist () {
      if (this.trackList.length === 0) return
      this.addTracksInPlaylist(this.trackList)
    },
    setAsPlaylist () {
      if (this.trackList.length === 0) return
      this.emptyPlaylist()
      this.addInPlaylist()
    }
  }
}
</script>

<style lang="scss" scoped>

h1 svg {
  max-height: 30px;
}

.album-actions {
  margin-top: 5px;
}

</style>
