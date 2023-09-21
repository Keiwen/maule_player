
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
    currentTrack: {}
  },
  getters: {
    currentTrack: state => state.currentTrack,
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
    }
  },
  modules: {
    messageBag
  },
  strict: debug,
  plugins: [persistedState(persistOptions)]
})
