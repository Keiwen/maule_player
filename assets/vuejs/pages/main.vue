<template>
  <div>

    <div class="container btn-group btn-group-sm mt-2">
      <router-link :to="{name: 'settings'}" class="btn btn-dark">
        <i class="fa fa-gears" /> {{ this.$trans('settings.title', {}, null, true) }}
      </router-link>
    </div>

    <div class="form-inline row mt-2 mx-2">
      <input class="form-control col-10" type="search" v-model="search" @keyup.enter="updateSearch"
             :placeholder="this.$trans('search.global', {}, null, true)" aria-label="Search">
      <button class="btn btn-primary col-2" type="submit" @click="updateSearch">
        <i class="fa fa-search" />
      </button>
    </div>
    <hr/>

    <vue-tiny-tabs id="search-tabs" :anchor="false" :closable="false" :hideTitle="true" v-if="!isLoading">
      <div class="section" id="tab-tracks" v-if="trackList.length">
        <h2 class="title">
          {{ this.$trans('track.list.title', {}, null, true) }}
          <span class="resultCount badge badge-light">{{ trackList.length }}</span>
        </h2>
        <track-list :track-list="trackList" :allowSearch="false"></track-list>
      </div>
      <div class="section" id="tab-artists" v-if="artistList.length">
        <h2 class="title">
          {{ this.$trans('artist.list.title', {}, null, true) }}
          <span class="resultCount badge badge-light">{{ artistList.length }}</span>
        </h2>
        <artist-list :artist-list="artistList" :allowSearch="false"></artist-list>
      </div>
      <div class="section" id="tab-albums" v-if="albumList.length">
        <h2 class="title">
          {{ this.$trans('album.list.title', {}, null, true) }}
          <span class="resultCount badge badge-light">{{ albumList.length }}</span>
        </h2>
        <album-list :album-list="albumList" :allowSearch="false"></album-list>
      </div>
    </vue-tiny-tabs>

  </div>
</template>

<script>
import {useRemoteCall} from "../composables/useRemoteCall";
import TrackList from "../components/trackList";
import ArtistList from "../components/artistList";
import AlbumList from "../components/albumList";
// NOTE: tiny tabs does not have dynamic title. By default, changing title does not change tab text
// here we use v-if isLoading on full tiny-tab component. That way, component reload after remote call
// and then update the texts.
import VueTinyTabs from 'vue-tiny-tabs';
import {mapActions} from "vuex";

export default {
  name: "mainPage",
  components: { TrackList, ArtistList, AlbumList, VueTinyTabs },
  data () {
    return {
      remoteCallData: {},
      remoteCallError: null,
      search: '',
      trackList: [],
      artistList: [],
      albumList: []
    }
  },
  computed: {
    isLoading () {
      return (this.remoteCallData == null && this.remoteCallError == null)
    }
  },
  watch: {
    remoteCallData: function(newValue, oldValue) {
      if (newValue !== null && this.remoteCallError === null) {
        this.trackList = newValue.tracks
        this.artistList = newValue.artists
        this.albumList = newValue.albums
        if (this.trackList.length === 0 && this.artistList.length === 0 && this.albumList.length === 0) {
          this.addWarning(this.$trans('search.empty', {}, null, true))
        }
      }
    },
    remoteCallError: function(newValue, oldValue) {
      if (newValue !== null && this.remoteCallData === null) {
        this.addError(this.$trans('track.list.error', {}, null, true))
      }
    }
  },
  methods: {
    ...mapActions(['addError', 'addWarning']),
    updateSearch () {
      this.remoteCallData = null
      this.remoteCallError = null
      this.trackList = []
      this.artistList = []
      this.albumList = []
      if (this.search.length < 3) {
        this.addWarning(this.$trans('search.too_short', {}, null, true))
        return
      }
      const urlToCall = this.$url(URL_API.search, {q: this.search})
      const {callData, callError} = useRemoteCall(urlToCall)
      this.remoteCallData = callData
      this.remoteCallError = callError
    }
  }
}
</script>

<style scoped>

</style>
