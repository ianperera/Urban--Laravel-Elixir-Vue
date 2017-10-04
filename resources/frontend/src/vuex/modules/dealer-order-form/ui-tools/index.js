import types from './types'
import updateStoreState from 'src/helpers/update-store-state.js'

const state = function () {
    return {
        loadForm: {
            show: false,
            state: null
        },
        saveForm: {
            show: false,
            state: null
        },
        inventoryForm: {
            show: false,
            state: null
        }
    }
}

const mutations = {
    [types.HIDE_LOAD_FORM_TOOL](state) {
        state.loadForm.show = false
    },
    [types.SHOW_LOAD_FORM_TOOL](state) {
        state.saveForm.show = false
        state.loadForm.show = true
        state.loadForm.state = 'new'
    },
    [types.SET_STATE_LOAD_FORM_TOOL](state, dataState) {
        state.loadForm.state = dataState
    },

    [types.HIDE_SAVE_FORM_TOOL](state) {
        state.saveForm.show = false
        state.saveForm.onContinue = null
    },
    [types.SHOW_SAVE_FORM_TOOL](state) {
        state.loadForm.show = false
        state.saveForm.show = true
        state.saveForm.state = 'new'
    },
    [types.SET_STATE_SAVE_FORM_TOOL](state, params) {
        state.saveForm = updateStoreState(state.saveForm, params)
    },

    [types.SET_STATE_INVENTORY_FORM_TOOL](state, data, object) {
        state.inventoryForm = updateStoreState(state.inventoryForm, data, object)
    },

    [types.HIDE_ALL_MODAL_FORM_TOOL](state) {
        state.loadForm.show = false
        state.saveForm.show = false
        state.inventoryForm.show = false
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

