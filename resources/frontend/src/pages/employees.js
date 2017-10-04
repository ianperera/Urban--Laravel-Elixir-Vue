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
            name: 'employees',
            component: function (resolve) {
                require(['src/components/views/employees/index.vue'], resolve)
            },
            children: [
                {
                    path: '/',
                    name: 'list',
                    props: {title: 'Employees'},
                    component: function (resolve) {
                        require(['src/components/views/employees/list/ListItems.vue'], resolve)
                    }
                },
                {
                    path: '/:id',
                    name: 'show',
                    component: function (resolve) {
                        require(['src/components/views/employees/show/ShowItem.vue'], resolve)
                    }
                }
            ]
        }
    ]
})

_base.initialize(App, Router)
