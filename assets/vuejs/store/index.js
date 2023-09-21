
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
    limitTitle: () => (title, limit = 20) => {
      if (title.length <= limit) return title
      return title.substring(0, limit - 1) + '...'
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
