/*eslint no-unused-vars:0 */
import _base from 'src/pages/_base'
import VueRouter from 'vue-router'

_base.Vue.use(VueRouter)

var App = {}
var Router = new VueRouter({
    // mode: 'history',
    routes: [
        {
            path: '/dealer-inventory',
            props: {title: 'Dealer Inventory'},
            component: function (resolve) {
                require(['src/components/views/reports/dealer-inventory/list/ListItems.vue'], resolve)
            }
        },
        {
            path: '/sales',
            props: {title: 'Sales'},
            component: function (resolve) {
                require(['src/components/views/reports/sales/list/ListItems.vue'], resolve)
            }
        },
        {
            path: '/orders',
            props: {title: 'Orders'},
            component: function (resolve) {
                require(['src/components/views/reports/orders/list/ListItems.vue'], resolve)
            }
        },
        {
            path: '/lead-contacts',
            props: {title: 'Lead and Closing Report'},
            component: function (resolve) {
                require(['src/components/views/reports/lead-contacts/list/ListItems.vue'], resolve)
            }
        }
    ]
})

_base.initialize(App, Router)
