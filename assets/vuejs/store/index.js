
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
    limitTitle: () => (title, limit = 20) => {
      if (title.length <= limit) return title
      return title.substring(0, limit - 1) + '...'
    }
  },
  actions: {
  },
  modules: {
    messageBag
  },
  strict: debug,
  plugins: [persistedState(persistOptions)]
})
