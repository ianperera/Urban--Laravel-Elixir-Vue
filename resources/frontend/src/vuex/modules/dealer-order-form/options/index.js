import types from './types'
// import updateStoreState from 'src/helpers/update-store-state.js'

const state = function() {
  return {
      categories: null,
      list: []
  }
}

const mutations = {
  [types.RECEIVE_OPTION_CATEGORIES] (state, items) {
    state.categories = items
  },
  [types.RECEIVE_OPTIONS] (state, list) {
    state.list = list
  }
}

import actions from './_actions'
import getters from './_getters'

export default {
    namespaced: true,
    state,
    mutations,
    actions,
    getters
}
