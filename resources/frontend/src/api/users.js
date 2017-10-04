/**
 * Communicates with API server about building models
 */

import Vue from 'vue'
import { csrfToken } from './_config'

const userResource = Vue.resource('/api/users{/id}',
  {},
  {},
  {
    headers: {
      'X-CSRF-TOKEN': csrfToken()
    }
  }
)

export default {
  get ({ id, query }) {
    return userResource.get({ id, ...query })
  },
  save ({ item, data }) {
    if (typeof item.id === 'undefined') {
      return userResource.save({}, data)
    } else {
      data.append('_method', 'put')
      return userResource.save({
        id: item.id
      }, data)
    }
  },
  delete({ item }) {
    return userResource.delete({ id: item.id })
  },
  getActiveFlags({ query }) {
    return Vue.http.get('/api/users/active-flags', {
      params: query
    })
  }
}
