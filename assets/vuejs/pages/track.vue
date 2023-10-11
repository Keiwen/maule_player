<template>
  <div>
    <h1><track-icon /> {{ track.name }}</h1>

    <loading-icon v-if="isLoading" />
    <div v-else>

      <ul class="list-group">
        <li class="list-group-item">
          <list-item item-type="artist" :item="track.artist" :simple-view="true"
                     v-if="track.artist"
                     :link-route-param="{ name: 'artist', params: { id: track.artist.id, artist: track.artist }}">
          </list-item>
        </li>
        <li class="list-group-item">
          <list-item item-type="album" :item="track.album" :simple-view="true"
                     v-if="track.album"
                     :link-route-param="{ name: 'album', params: { id: track.album.id, artist: track.album }}">
          </list-item>
        </li>
        <li class="list-group-item">
          <div class="track-data">
            <div>
              {{ this.$trans('track.track_number', {}, null, true) }}{{ track.trackNumber }}
              ({{ track.year }})
            </div>
            <div>
              {{ getDisplayTime(track.duration) }}
            </div>
          </div>

          <div class="item-action">
            <button class="btn btn-secondary btn-playlist" @click="selectTrack">
              <i class="fa fa-folder-plus" />
            </button>
          </div>

        </li>
      </ul>

      <div class="row">
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
import listItem from "../components/listing/listItem";

export default {
  name: "trackPage",
  components: { trackIcon, loadingIcon, listItem },
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
    },
    selectTrack() {
      this.addTracksInPlaylist([this.track])
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

.track-data {
  float: left;
}

.list-group-item {
  background-color: transparent;
  padding: .2rem 0.5rem;
  border: 1px solid var(--primary);
}


.btn-playlist {
  float: right;
  height: var(--simple-button-size);
  width: var(--simple-button-size);
  svg {
    height: 100%;
    margin-left: -5px;
  }
}


.track-actions {
  margin-top: 5px;
}

</style>
