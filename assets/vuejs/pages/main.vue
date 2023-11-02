<template>
  <div>

    <div class=" col-12 btn-group btn-group-sm mt-2">
      <router-link :to="{name: 'settings'}" class="btn btn-dark">
        <i class="fa fa-gears" /> {{ this.$trans('settings.title', {}, null, true) }}
      </router-link>
    </div>

    <div class="form-inline row mt-2 mx-2">
      <input class="form-control col-10" type="search" v-model="search" @keyup.enter="updateSearch"
             :placeholder="this.$trans('search.global', {}, null, true)" aria-label="Search">
      <button class="btn btn-primary col-2" type="submit" @click="updateSearch" aria-label="Submit search">
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

    <loading-icon v-if="isLoading" />

    <div v-if="noIdea">
      {{ this.$trans('search.no_idea', {}, null, true) }}
      <div class="form-inline row mt-2 mx-2">

        <div class="input-group col-10">
          <input class="form-control" v-model="randomLimit" type="text"
                 @keyup.enter="randomPlay" aria-label="random limit"  aria-describedby="random-limit-description">
          <div class="input-group-append">
            <span class="input-group-text" id="random-limit-description">
              {{ this.$trans('search.tracks') }}
            </span>
          </div>
        </div>

        <button class="btn btn-secondary col-2" type="submit" @click="randomPlay" aria-label="Submit random tracks">
          <i class="fa fa-play" />
        </button>
      </div>
    </div>

  </div>
</template>

<script>
import {useRemoteCall} from "../composables/useRemoteCall";
import trackList from "../components/listing/trackList";
import artistList from "../components/listing/artistList";
import albumList from "../components/listing/albumList";
// NOTE: tiny tabs does not have dynamic title. By default, changing title does not change tab text
// here we use v-if isLoading on full tiny-tab component. That way, component reload after remote call
// and then update the texts.
import vueTinyTabs from 'vue-tiny-tabs';
import {mapActions} from "vuex";
import loadingIcon from "../components/icons/loadingIcon";

export default {
  name: "mainPage",
  components: { trackList, artistList, albumList, vueTinyTabs, loadingIcon },
  data () {
    return {
      remoteCallData: {},
      remoteCallError: null,
      remoteCallRandomData: {},
      remoteCallRandomError: null,
      search: '',
      trackList: [],
      artistList: [],
      albumList: [],
      noIdea: true,
      randomLimit: 20,
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
    },
    remoteCallRandomData: function(newValue, oldValue) {
      if (newValue !== null && this.remoteCallRandomError === null) {
        this.emptyPlaylist()
        this.addTracksInPlaylist(newValue.tracks)
      }
    },
    remoteCallRandomError: function(newValue, oldValue) {
      if (newValue !== null && this.remoteCallRandomData === null) {
        this.addError(this.$trans('track.list.error', {}, null, true))
      }
    }
  },
  methods: {
    ...mapActions(['addError', 'addWarning', 'addTracksInPlaylist', 'emptyPlaylist']),
    updateSearch () {
      this.noIdea = false
      this.remoteCallData = null
      this.remoteCallError = null
      this.trackList = []
      this.artistList = []
      this.albumList = []
      if (this.search.length < 3) {
        this.remoteCallData = {}
        this.addWarning(this.$trans('search.too_short', {}, null, true))
        return
      }
      const urlToCall = this.$url(URL_API.search, {q: this.search})
      const {callData, callError} = useRemoteCall(urlToCall)
      this.remoteCallData = callData
      this.remoteCallError = callError
    },
    randomPlay () {
      this.remoteCallRandomData = null
      this.remoteCallRandomError = null
      const urlToCall = this.$url(URL_API.track_list, {limit: this.randomLimit, randomize: 1})
      const {callData, callError} = useRemoteCall(urlToCall)
      this.remoteCallRandomData = callData
      this.remoteCallRandomError = callError
    }
  }
}
</script>

<style scoped>

</style>
