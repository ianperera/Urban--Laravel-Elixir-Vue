/*eslint no-unused-vars:0 */
import _base from 'src/pages/_base'
import VueRouter from 'vue-router'
// import { sync } from 'vuex-router-sync'
_base.Vue.use(VueRouter)

var App = {}
var Router = new VueRouter({
    // mode: 'history',
    routes: [
        {
            path: '/',
            props: {title: 'Options'},
            component: function (resolve) {
                require(['src/components/views/options/list/ListItems.vue'], resolve)
            }
        }
    ]
})

// sync store to vuex (global)
// sync(_base.store, Router)

_base.initialize(App, Router)
