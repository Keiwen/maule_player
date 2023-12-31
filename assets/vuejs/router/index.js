import Vue from 'vue'
import Router from 'vue-router'
import Main from '../pages/main'
import Settings from "../pages/settings";
import Artists from '../pages/artists'
import Artist from '../pages/artist'
import Albums from '../pages/albums'
import Album from '../pages/album'
import Track from '../pages/track'
import Playlist from "../pages/playlist";

Vue.use(Router)

export default new Router({
    scrollBehavior (to, from, savedPosition) {
        if (savedPosition) return savedPosition
        if (to.hash) return { selector: to.hash }
        return { x: 0, y: 0 }
    },
    routes: [
        {
            path: '/',
            name: 'main',
            component: Main
        },
        {
            path: '/settings',
            name: 'settings',
            component: Settings
        },
        {
            path: '/artists/',
            name: 'artists',
            component: Artists
        },
        {
            path: '/artist/:id',
            name: 'artist',
            component: Artist
        },
        {
            path: '/albums/',
            name: 'albums',
            component: Albums
        },
        {
            path: '/album/:id',
            name: 'album',
            component: Album
        },
        {
            path: '/playlist/',
            name: 'playlist',
            component: Playlist
        },
        {
            path: '/track/:id',
            name: 'track',
            component: Track
        }
    ]
})
