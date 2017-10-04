/*eslint no-unused-vars:0 */
import _base from 'src/pages/_base'
import VueRouter from 'vue-router'
_base.Vue.use(VueRouter)

var App = {}
var Router = new VueRouter({
    // mode: 'history',
    routes: [
        {
            path: '/',
            props: { title: 'Building Package Categories' },
            component: function (resolve) {
                require(['src/components/views/building-package-categories/list/ListItems.vue'], resolve)
            }
        }
    ]
})

_base.initialize(App, Router)
