
import Vue from 'vue'
import Vuex from 'vuex'
import messageBag from './modules/messageBag'
import * as types from './mutation-types'
import persistedState from 'vuex-persistedstate'

Vue.use(Vuex)

const debug = process.env.NODE_ENV !== 'production'

const persistOptions = {
  key: 'maule_player',
  // select below which attribute to persist
  paths: ['playlist']
}

export default new Vuex.Store({
  state: {
    playlist: {
      currentTrack: {},
      currentTrackIndex: -1,
      currentPlaylist: [],
      currentPlaylistDuration: 0,
      loopPlaylist: false,
    },
    temp: {
      displayPlaylistTrash: false,
    },
    medialib: {
      artists: {},
      albumsByArtist: {},
      albums: {},
      tracksByAlbum: {},
      tracksByArtist: {},
      tracks: {},
    }
  },
  getters: {
    currentTrack: state => state.playlist.currentTrack,
    currentTrackIndex: state => state.playlist.currentTrackIndex,
    currentPlaylist: state => state.playlist.currentPlaylist,
    currentPlaylistDuration: state => state.playlist.currentPlaylistDuration,
    displayPlaylistTrash: state => state.temp.displayPlaylistTrash,
    loopPlaylist: state => state.playlist.loopPlaylist,
    playedMediaFilepath: state => state.playlist.currentTrack.filepath,
    getNextPlaylistIndex: (state) => () => {
      const playlistLength = state.playlist.currentPlaylist.length
      if (state.playlist.currentTrackIndex === -1) return -1
      if (playlistLength === 0) return -1
      if ((state.playlist.currentTrackIndex + 1) >= playlistLength) {
        if (state.playlist.loopPlaylist) return 0
        return -1
      }
      return state.playlist.currentTrackIndex + 1
    },
    getPrevPlaylistIndex: (state) => () => {
      const playlistLength = state.playlist.currentPlaylist.length
      if (state.playlist.currentTrackIndex === -1) return -1
      if (playlistLength === 0) return -1
      if ((state.playlist.currentTrackIndex - 1) < 0) {
        if (state.playlist.loopPlaylist) return playlistLength - 1
        return -1
      }
      return state.playlist.currentTrackIndex - 1
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
    },
    getArtists: (state) => () => {
      let artists = []
      for (const [key, value] of Object.entries(state.medialib.artists)) {
        artists.push(value);
      }
      return artists
    },
    getArtist: (state) => (id) => {
      return state.medialib.artists[id] ?? {}
    },
    getAlbums: (state) => (fromArtistId) => {
      let albums = []
      if (fromArtistId != null) {
        const albumsId = state.medialib.albumsByArtist[fromArtistId] ?? []
        for (let i; i < albumsId.length; i++) {
          if (state.medialib.albums[albumsId[i]].id !== undefined) {
            albums.push(state.medialib.albums[albumsId[i]])
          }
        }
      } else {
        for (const [key, value] of Object.entries(state.medialib.albums)) {
          albums.push(value);
        }
      }
      return albums
    },
    getAlbum: (state) => (id) => {
      return state.medialib.albums[id] ?? {}
    },
    getTracks: (state) => (fromAlbumId, fromArtistId) => {
      let tracks = []
      if (fromAlbumId != null) {
        const tracksId = state.medialib.tracksByAlbum[fromAlbumId] ?? []
        for (let i; i < tracksId.length; i++) {
          if (state.medialib.tracks[tracksId[i]].id !== undefined) {
            tracks.push(state.medialib.tracks[tracksId[i]])
          }
        }
      } else if (fromArtistId != null) {
        const tracksId = state.medialib.tracksByArtist[fromArtistId] ?? []
        for (let i; i < tracksId.length; i++) {
          if (state.medialib.tracks[tracksId[i]].id !== undefined) {
            tracks.push(state.medialib.tracks[tracksId[i]])
          }
        }
      } else {
        for (const [key, value] of Object.entries(state.medialib.tracks)) {
          tracks.push(value);
        }
      }
      return tracks
    },
    getTrack: (state) => (id) => {
      return state.medialib.tracks[id] ?? {}
    }
  },
  actions: {
    setCurrentTrack ({commit}, track) {
      commit(types.SET_CURRENT_TRACK, track)
      commit(types.SET_CURRENT_TRACK_INDEX, -1)
    },
    playTrackInPlaylist ({state, commit}, index) {
      if (state.playlist.currentPlaylist[index] === undefined) return
      commit(types.SET_CURRENT_TRACK, state.playlist.currentPlaylist[index])
      commit(types.SET_CURRENT_TRACK_INDEX, index)
    },
    addTracksInPlaylist ({state, commit, dispatch}, tracks) {
      const previousPlaylistLength = state.playlist.currentPlaylist.length
      commit(types.ADD_TRACKS_IN_PLAYLIST, tracks)
      dispatch('addSuccess', this._vm.$trans('playlist.added', {}, null, true))
      if (previousPlaylistLength === 0 && state.playlist.currentPlaylist.length > 0) {
        dispatch('playTrackInPlaylist', 0)
      }
    },
    changeTrackIndex ({state, commit, dispatch}, {oldIndex, newIndex}) {
      const track = state.playlist.currentPlaylist[oldIndex]
      const isCurrent = (oldIndex === state.playlist.currentTrackIndex)
      dispatch('removeTrackByIndex', {index: oldIndex, loadNextIfCurrent: !isCurrent })
      dispatch('addTrackInIndex', {track: track, index: newIndex})
      if (isCurrent) {
        // update index if current was moved
        commit(types.SET_CURRENT_TRACK_INDEX, newIndex)
      }
    },
    removeTrackByIndex ({state, commit, dispatch}, {index, loadNextIfCurrent}) {
      commit(types.REMOVE_TRACK_FROM_PLAYLIST, index)
      if (state.playlist.currentTrackIndex === index) {
        // if current is removed, play next (use removed index, as current was removed)
        if (loadNextIfCurrent) {
          dispatch('playTrackInPlaylist', index)
        } else {
          commit(types.SET_CURRENT_TRACK_INDEX, -1)
        }
      }
      if (index < state.playlist.currentTrackIndex) {
        // if a previous is removed, update current index to - 1)
        commit(types.SET_CURRENT_TRACK_INDEX, state.playlist.currentTrackIndex - 1)
      }
    },
    addTrackInIndex ({state, commit}, {track, index}) {
      commit(types.INSERT_TRACK_IN_PLAYLIST, {track: track, index: index})
      if (index <= state.playlist.currentTrackIndex) {
        // if added before current one, update current index to + 1)
        commit(types.SET_CURRENT_TRACK_INDEX, state.playlist.currentTrackIndex + 1)
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
    storeArtists({commit}, artists) {
      for (let i = 0; i < artists.length; i++) {
        commit(types.STORE_ARTIST, artists[i])
      }
    },
    storeAlbums({commit}, albums) {
      for (let i = 0; i < albums.length; i++) {
        commit(types.STORE_ALBUM, albums[i])
      }
    },
    storeTracks({commit}, tracks) {
      for (let i = 0; i < tracks.length; i++) {
        commit(types.STORE_TRACK, tracks[i])
      }
    },
    resetState () {
      // call this.$store.dispatch('resetState') from a component action
      localStorage.removeItem(persistOptions.key)
      location.reload()
    }
  },
  mutations: {
    [types.SET_CURRENT_TRACK] (state, track) {
      state.playlist.currentTrack = track
    },
    [types.SET_CURRENT_TRACK_INDEX] (state, trackIndex) {
      state.playlist.currentTrackIndex = trackIndex
    },
    [types.ADD_TRACKS_IN_PLAYLIST] (state, tracks) {
      state.playlist.currentPlaylist = state.playlist.currentPlaylist.concat(tracks)
      for (let i = 0; i < tracks.length; i++) {
        if (tracks[i].duration !== undefined) {
          state.playlist.currentPlaylistDuration += tracks[i].duration
        }
      }
    },
    [types.REMOVE_TRACK_FROM_PLAYLIST] (state, index) {
      const removedTracks = state.playlist.currentPlaylist.splice(index, 1)
      const removedTrack = removedTracks[0]
      if (removedTrack !== undefined && removedTrack.duration !== undefined) {
        state.playlist.currentPlaylistDuration -= removedTrack.duration
        if (state.playlist.currentPlaylistDuration < 0) state.playlist.currentPlaylistDuration = 0
      }
    },
    [types.INSERT_TRACK_IN_PLAYLIST] (state, {track, index}) {
      state.playlist.currentPlaylist.splice(index, 0, track);
      if (track.duration !== undefined) {
        state.playlist.currentPlaylistDuration += track.duration
      }
    },
    [types.EMPTY_PLAYLIST] (state) {
      state.playlist.currentPlaylist = []
      state.playlist.currentPlaylistDuration = 0
    },
    [types.SHUFFLE_PLAYLIST] (state) {
      let oldIndex = state.playlist.currentTrackIndex
      let countToShuffle = state.playlist.currentPlaylist.length
      let tempElement
      let randomIndex
      // while there remain elements to shuffle...
      while (countToShuffle) {
        // pick a remaining element...
        randomIndex = Math.floor(Math.random() * countToShuffle)
        // decrement elements to shuffle...
        countToShuffle--
        // swap picked element with the current element
        tempElement = state.playlist.currentPlaylist[countToShuffle]
        state.playlist.currentPlaylist[countToShuffle] = state.playlist.currentPlaylist[randomIndex]
        state.playlist.currentPlaylist[randomIndex] = tempElement

        // update current index if we picked current element
        if (randomIndex === oldIndex) {
          state.playlist.currentTrackIndex = countToShuffle
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
      state.temp.displayPlaylistTrash = display
    },
    [types.SET_LOOP_PLAYLIST] (state, loop) {
      if (loop === undefined) loop = false
      state.playlist.loopPlaylist = loop
    },
    [types.STORE_ARTIST] (state, artist) {
      if (artist.id === undefined) return
      state.medialib.artists[artist.id] = artist
    },
    [types.STORE_ALBUM] (state, album) {
      if (album.id === undefined) return
      state.medialib.albums[album.id] = album
    },
    [types.STORE_TRACK] (state, track) {
      if (track.id === undefined) return
      state.medialib.tracks[track.id] = track
    }
  },
  modules: {
    messageBag
  },
  strict: debug,
  plugins: [persistedState(persistOptions)]
})
