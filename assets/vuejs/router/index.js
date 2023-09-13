import Vue from 'vue'
import Router from 'vue-router'
import Main from '../pages/main'

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
        }
    ]
})