<template>
  <div>
    <div class="row">
      <div class="col-12">
        <h1><track-icon /> {{ track.name }}</h1>
      </div>
    </div>

    <loading-icon v-if="isLoading" />
    <div v-else>

      <div class="mt-2"></div>
      <artist-list-item :artist="track.artist" :simple-view="true" v-if="track.artist"></artist-list-item>
      <div class="mt-2"></div>
      <album-list-item :album="track.album" :simple-view="true" v-if="track.album"></album-list-item>

      <div class="row">
        <div class="col-4">
          {{ this.$trans('track.track_number', {}, null, true) }}{{ track.trackNumber }}
        </div>
        <div class="col-4">
          {{ track.year }}
        </div>
        <div class="col-4">
          {{ getDisplayTime(track.duration) }}
        </div>
      </div>


      <hr/>

      <p>
        <strong>{{ this.$trans('track.import_date', {}, null, true, true) }}</strong> {{ track.importDateIso }}
      </p>
      <p>
        <strong>{{ this.$trans('track.filepath', {}, null, true, true) }}</strong> {{ track.filepath }}
      </p>
    </div>

  </div>
</template>

<script>
import {useRemoteCall} from "../composables/useRemoteCall";
import {mapActions, mapGetters} from "vuex";
import trackIcon from "../components/icons/trackIcon";
import loadingIcon from "../components/icons/loadingIcon";
import artistListItem from "../components/listItems/artistListItem";
import albumListItem from "../components/listItems/albumListItem";

export default {
  name: "trackPage",
  components: { trackIcon, loadingIcon, artistListItem, albumListItem },
  data () {
    return {
      track: {},
      remoteCallTrackData: {},
      remoteCallTrackError: null,
    }
  },
  computed: {
    ...mapGetters(['getDisplayTime']),
    isLoading () {
      return (this.remoteCallTrackData == null && this.remoteCallTrackError == null)
    },
    artistList () {
      if (!this.track.artist) return []
      return [this.track.artist]
    },
    albumList () {
      if (!this.track.album) return []
      return [this.track.album]
    }
  },
  watch: {
    remoteCallTrackData: function(newValue, oldValue) {
      if (newValue !== null && this.remoteCallTrackError === null) {
        this.track = newValue.track
      }
    },
    remoteCallTrackError: function(newValue, oldValue) {
      if (newValue !== null && this.remoteCallTrackData === null) {
        this.addError(this.$trans('track.get.error', {}, null, true))
      }
    }
  },
  mounted () {
    if (this.$route.params.track !== undefined) {
      this.track = this.$route.params.track
    } else if(this.$route.params.id !== undefined) {
      this.updateTrackData(this.$route.params.id)
    }
  },
  methods: {
    ...mapActions(['addError', 'addTracksInPlaylist', 'emptyPlaylist']),
    updateTrackData (id) {
      this.track = {}
      const urlToCall = this.$url(URL_API.track_get, {id: id})
      const {callData, callError} = useRemoteCall(urlToCall)
      this.remoteCallTrackData = callData
      this.remoteCallTrackError = callError
    }
  }
}
</script>

<style lang="scss" scoped>

h1 svg {
  max-height: 30px;
}

.dropdown-item {
  svg {
    height: 20px;
  }
}

.track-actions {
  margin-top: 5px;
}

</style>
