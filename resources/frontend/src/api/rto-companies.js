/**
 * Communicates with API server about rto companies
 */

import Vue from 'vue'

export default {
    getRtoCompanies () {
        const resource = Vue.resource('/api/rto_companies')
        return resource.get()
    }
}