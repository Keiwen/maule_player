
import Vue from 'vue'
import Vuex from 'vuex'
import messageBag from './modules/messageBag'
import * as types from './mutation-types'
import persistedState from 'vuex-persistedstate'

Vue.use(Vuex)

const debug = process.env.NODE_ENV !== 'production'

const persistOptions = {
  key: 'maule_player'
}

export default new Vuex.Store({
  getters: {
  },
  actions: {
  },
  modules: {
    messageBag
  },
  strict: debug,
  plugins: [persistedState(persistOptions)]
})
