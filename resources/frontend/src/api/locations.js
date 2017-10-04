/**
 * Communicates with API server about locations
 */

import Vue from 'vue'
import {csrfToken} from './_config'

const locationResource = Vue.resource('/api/locations{/id}',
    {},
    {},
    {
        headers: {
            'X-CSRF-TOKEN': csrfToken()
        }
    }
)

export default {
    get ({id, query}) {
        return locationResource.get({id, ...query})
    },
    save ({item, data}) {
        if (typeof item.id === 'undefined') {
            return locationResource.save({}, data)
        } else {
            if (data instanceof FormData) data.append('_method', 'put')
            else data._method = 'put'

            return locationResource.save({
                id: item.id
            }, data)
        }
    },
    delete({ item }) {
        return locationResource.delete({ id: item.id })
    },
    categories ({query}) {
        return Vue.http.get('/api/locations/categories/', {
            params: query
        })
    },
    getActiveFlags({query}) {
        return Vue.http.get('/api/locations/active-flags', {
            params: query
        })
    },
    getFiles({query}) {
        return Vue.http.get('/api/locations/get-files', {
            params: query
        })
    }
}
