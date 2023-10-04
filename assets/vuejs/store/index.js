
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
  state: {
    currentTrack: {},
    currentTrackIndex: -1,
    currentPlaylist: [],
    currentPlaylistDuration: 0,
    displayPlaylistTrash: false,
    loopPlaylist: false,
  },
  getters: {
    currentTrack: state => state.currentTrack,
    currentTrackIndex: state => state.currentTrackIndex,
    currentPlaylist: state => state.currentPlaylist,
    currentPlaylistDuration: state => state.currentPlaylistDuration,
    displayPlaylistTrash: state => state.displayPlaylistTrash,
    loopPlaylist: state => state.loopPlaylist,
    playedMediaFilepath: state => state.currentTrack.filepath,
    getNextPlaylistIndex: (state) => () => {
      const playlistLength = state.currentPlaylist.length
      if (state.currentTrackIndex === -1) return -1
      if (playlistLength === 0) return -1
      if ((state.currentTrackIndex + 1) >= playlistLength) {
        if (state.loopPlaylist) return 0
        return -1
      }
      return state.currentTrackIndex + 1
    },
    getPrevPlaylistIndex: (state) => () => {
      const playlistLength = state.currentPlaylist.length
      if (state.currentTrackIndex === -1) return -1
      if (playlistLength === 0) return -1
      if ((state.currentTrackIndex - 1) < 0) {
        if (state.loopPlaylist) return playlistLength - 1
        return -1
      }
      return state.currentTrackIndex - 1
    },
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
    changeTrackIndex ({state, commit, dispatch}, {oldIndex, newIndex}) {
      const track = state.currentPlaylist[oldIndex]
      const isCurrent = (oldIndex === state.currentTrackIndex)
      dispatch('removeTrackByIndex', {index: oldIndex, loadNextIfCurrent: !isCurrent })
      dispatch('addTrackInIndex', {track: track, index: newIndex})
      if (isCurrent) {
        // update index if current was moved
        commit(types.SET_CURRENT_TRACK_INDEX, newIndex)
      }
    },
    removeTrackByIndex ({state, commit, dispatch}, {index, loadNextIfCurrent}) {
      commit(types.REMOVE_TRACK_FROM_PLAYLIST, index)
      if (state.currentTrackIndex === index) {
        // if current is removed, play next (use removed index, as current was removed)
        if (loadNextIfCurrent) {
          dispatch('playTrackInPlaylist', index)
        } else {
          commit(types.SET_CURRENT_TRACK_INDEX, -1)
        }
      }
      if (index < state.currentTrackIndex) {
        // if a previous is removed, update current index to - 1)
        commit(types.SET_CURRENT_TRACK_INDEX, state.currentTrackIndex - 1)
      }
    },
    addTrackInIndex ({state, commit}, {track, index}) {
      commit(types.INSERT_TRACK_IN_PLAYLIST, {track: track, index: index})
      if (index <= state.currentTrackIndex) {
        // if added before current one, update current index to + 1)
        commit(types.SET_CURRENT_TRACK_INDEX, state.currentTrackIndex + 1)
      }
    },
    emptyPlaylist ({commit}) {
      commit(types.EMPTY_PLAYLIST)
      commit(types.SET_CURRENT_TRACK_INDEX, -1)
    },
    shufflePlaylist ({commit}) {
      commit(types.SHUFFLE_PLAYLIST)
    },
    grabPlaylistElement({commit}) {
      commit(types.SET_DISPLAY_TRASH, true)
    },
    dropPlaylistElement({commit}) {
      commit(types.SET_DISPLAY_TRASH, false)
    },
    setLoopPlaylist({commit}, loop) {
      commit(types.SET_LOOP_PLAYLIST, loop)
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
      for (let i = 0; i < tracks.length; i++) {
        if (tracks[i].duration !== undefined) {
          state.currentPlaylistDuration += tracks[i].duration
        }
      }
    },
    [types.REMOVE_TRACK_FROM_PLAYLIST] (state, index) {
      const removedTracks = state.currentPlaylist.splice(index, 1)
      const removedTrack = removedTracks[0]
      if (removedTrack !== undefined && removedTrack.duration !== undefined) {
        state.currentPlaylistDuration -= removedTrack.duration
      }
    },
    [types.INSERT_TRACK_IN_PLAYLIST] (state, {track, index}) {
      state.currentPlaylist.splice(index, 0, track);
      if (track.duration !== undefined) {
        state.currentPlaylistDuration += track.duration
      }
    },
    [types.EMPTY_PLAYLIST] (state) {
      state.currentPlaylist = []
      state.currentPlaylistDuration = 0
    },
    [types.SHUFFLE_PLAYLIST] (state) {
      let oldIndex = state.currentTrackIndex
      let countToShuffle = state.currentPlaylist.length
      let tempElement
      let randomIndex
      // while there remain elements to shuffle...
      while (countToShuffle) {
        // pick a remaining element...
        randomIndex = Math.floor(Math.random() * countToShuffle)
        // decrement elements to shuffle...
        countToShuffle--
        // swap picked element with the current element
        tempElement = state.currentPlaylist[countToShuffle]
        state.currentPlaylist[countToShuffle] = state.currentPlaylist[randomIndex]
        state.currentPlaylist[randomIndex] = tempElement

        // update current index if we picked current element
        if (randomIndex === oldIndex) {
          state.currentTrackIndex = countToShuffle
          // cancel old index search
          oldIndex = -1
        }
        // update old index if we swapped current element
        if (countToShuffle === oldIndex) {
          oldIndex = randomIndex
        }
      }
    },
    [types.SET_DISPLAY_TRASH] (state, display) {
      if (display === undefined) display = false
      state.displayPlaylistTrash = display
    },
    [types.SET_LOOP_PLAYLIST] (state, loop) {
      if (loop === undefined) loop = false
      state.loopPlaylist = loop
    }
  },
  modules: {
    messageBag
  },
  strict: debug,
  plugins: [persistedState(persistOptions)]
})
