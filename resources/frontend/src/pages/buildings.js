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
            name: 'buildings',
            component: function (resolve) {
                require(['src/components/views/buildings/index.vue'], resolve)
            },
            children: [
                {
                    path: '/',
                    name: 'list',
                    props: {title: 'Buildings'},
                    component: function (resolve) {
                        require(['src/components/views/buildings/list/ListItems.vue'], resolve)
                    }
                },
                {
                    path: '/:id',
                    name: 'show',
                    component: function (resolve) {
                        require(['src/components/views/buildings/show/ShowItem.vue'], resolve)
                    }
                }
            ]
        }
    ]
})

_base.initialize(App, Router)
