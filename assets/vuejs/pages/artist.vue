<template>
  <div>
    <h1><artist-icon /> {{ artist.name }}</h1>

    <vue-tiny-tabs id="artist-tabs" :anchor="false" :closable="false" :hideTitle="true">
      <div class="section" id="tab-discography">
        <h2 class="title">{{ this.$trans('artist.discography', {}, null, true) }}</h2>
        <album-list :album-list="albumList" :allowSearch="false"></album-list>
      </div>
      <div class="section" id="tab-recent">
        <h2 class="title">{{ this.$trans('artist.recent_tracks', {}, null, true) }}</h2>
        <track-list :track-list="trackList" :allowSearch="false"></track-list>
      </div>
    </vue-tiny-tabs>
    <loading-icon v-if="isLoading" />

  </div>
</template>

<script>
import {useRemoteCall} from "../composables/useRemoteCall";
import {mapActions} from "vuex";
import artistIcon from "../components/icons/artistIcon";
import trackList from "../components/listing/trackList";
import albumList from "../components/listing/albumList";
import vueTinyTabs from 'vue-tiny-tabs';
import loadingIcon from "../components/icons/loadingIcon";

export default {
  name: "artistPage",
  components: { trackList, artistIcon, albumList, vueTinyTabs, loadingIcon },
  data () {
    return {
      artist: {},
      remoteCallTrackData: {},
      remoteCallTrackError: null,
      remoteCallAlbumData: {},
      remoteCallAlbumError: null,
      remoteCallArtistData: {},
      remoteCallArtistError: null,
      trackList: [],
      albumList: []
    }
  },
  computed: {
    isLoading () {
      return ((this.remoteCallTrackData == null && this.remoteCallTrackError == null)
          || (this.remoteCallAlbumData == null && this.remoteCallAlbumError == null)
          || (this.remoteCallArtistData == null && this.remoteCallArtistError == null))
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
        this.albumList = newValue.albums
      }
    },
    remoteCallAlbumError: function(newValue, oldValue) {
      if (newValue !== null && this.remoteCallAlbumData === null) {
        this.addError(this.$trans('album.list.error', {}, null, true))
      }
    },
    remoteCallArtistData: function(newValue, oldValue) {
      if (newValue !== null && this.remoteCallArtistError === null) {
        this.artist = newValue.artist
        this.updateAlbumList()
        this.updateTrackList()
      }
    },
    remoteCallArtistError: function(newValue, oldValue) {
      if (newValue !== null && this.remoteCallArtistData === null) {
        this.addError(this.$trans('artist.get.error', {}, null, true))
      }
    }
  },
  mounted () {
    if (this.$route.params.artist !== undefined) {
      this.artist = this.$route.params.artist
      this.updateAlbumList()
      this.updateTrackList()
    } else if(this.$route.params.id !== undefined) {
      this.updateArtistData(this.$route.params.id)
    }
  },
  methods: {
    ...mapActions(['addError']),
    updateTrackList () {
      const urlToCall = this.$url(URL_API.artist_tracks, {id: this.artist.id, limit: 5})
      const {callData, callError} = useRemoteCall(urlToCall)
      this.remoteCallTrackData = callData
      this.remoteCallTrackError = callError
    },
    updateAlbumList () {
      const urlToCall = this.$url(URL_API.artist_albums, {id: this.artist.id})
      const {callData, callError} = useRemoteCall(urlToCall)
      this.remoteCallAlbumData = callData
      this.remoteCallAlbumError = callError
    },
    updateArtistData (id) {
      const urlToCall = this.$url(URL_API.artist_get, {id: id})
      const {callData, callError} = useRemoteCall(urlToCall)
      this.remoteCallArtistData = callData
      this.remoteCallArtistError = callError
    }
  }
}
</script>

<style lang="scss" scoped>

h1 svg {
  max-height: 30px;
}

</style>
