
import Vue from 'vue'
import Vuex from 'vuex'
import messageBag from './modules/messageBag'
import * as types from './mutation-types'
import persistedState from 'vuex-persistedstate'
import {SET_CURRENT_TRACK} from "./mutation-types";

Vue.use(Vuex)

const debug = process.env.NODE_ENV !== 'production'

const persistOptions = {
  key: 'maule_player'
}

export default new Vuex.Store({
  state: {
    currentTrack: {},
    currentTrackIndex: -1,
    currentPlaylist: []
  },
  getters: {
    currentTrack: state => state.currentTrack,
    currentTrackIndex: state => state.currentTrackIndex,
    currentPlaylist: state => state.currentPlaylist,
    playedMediaFilepath: state => state.currentTrack.filepath,
    getLimitedTitle: () => (title, limit = 20) => {
      if (title.length <= limit) return title
      return title.substring(0, limit - 1) + '...'
    },
    getDisplayTime: () => (timeInSecond) => {
      let partHours = 0
      let partMinutes = 0
      let timeToWork = 0
      let partSeconds = Math.floor(timeInSecond)
      if (partSeconds >= 60) {
        timeToWork = partSeconds / 60
        partMinutes = Math.floor(timeToWork)
        partSeconds = Math.round((timeToWork - partMinutes) * 60)
      }
      if (partMinutes >= 60) {
        timeToWork = partMinutes / 60
        partHours = Math.floor(timeToWork)
        partMinutes = Math.round((timeToWork - partHours) * 60)
      }
      if (partSeconds < 10) partSeconds = '0' + partSeconds
      if (partMinutes < 10) partMinutes = '0' + partMinutes

      let displayTime = partMinutes + ':' + partSeconds
      if (partHours > 0) {
        displayTime = partHours + ':' + displayTime
      }
      return displayTime

    }
  },
  actions: {
    setCurrentTrack ({commit}, track) {
      commit(types.SET_CURRENT_TRACK, track)
      commit(types.SET_CURRENT_TRACK_INDEX, -1)
    },
    playTrackInPlaylist ({state, commit}, index) {
      if (state.currentPlaylist[index] === undefined) return
      commit(types.SET_CURRENT_TRACK, state.currentPlaylist[index])
      commit(types.SET_CURRENT_TRACK_INDEX, index)
    },
    addTracksInPlaylist ({state, commit, dispatch}, tracks) {
      const previousPlaylistLength = state.currentPlaylist.length
      commit(types.ADD_TRACKS_IN_PLAYLIST, tracks)
      dispatch('addSuccess', this._vm.$trans('playlist.added', {}, null, true))
      if (previousPlaylistLength === 0 && state.currentPlaylist.length > 0) {
        dispatch('playTrackInPlaylist', 0)
      }
    },
    emptyPlaylist ({commit}) {
      commit(types.EMPTY_PLAYLIST)
      commit(types.SET_CURRENT_TRACK_INDEX, -1)
    },
    resetState () {
      // call this.$store.dispatch('resetState') from a component action
      localStorage.removeItem(persistOptions.key)
      location.reload()
    }
  },
  mutations: {
    [types.SET_CURRENT_TRACK] (state, track) {
      state.currentTrack = track
    },
    [types.SET_CURRENT_TRACK_INDEX] (state, trackIndex) {
      state.currentTrackIndex = trackIndex
    },
    [types.ADD_TRACKS_IN_PLAYLIST] (state, tracks) {
      state.currentPlaylist = state.currentPlaylist.concat(tracks)
    },
    [types.EMPTY_PLAYLIST] (state) {
      state.currentPlaylist = []
    }
  },
  modules: {
    messageBag
  },
  strict: debug,
  plugins: [persistedState(persistOptions)]
})
