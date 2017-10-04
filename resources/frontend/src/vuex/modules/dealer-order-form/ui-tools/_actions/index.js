import {
  HIDE_SAVE_FORM_TOOL,
  SHOW_SAVE_FORM_TOOL,
  SET_STATE_SAVE_FORM_TOOL,

  HIDE_LOAD_FORM_TOOL,
  SHOW_LOAD_FORM_TOOL,
  SET_STATE_LOAD_FORM_TOOL,

  SET_STATE_INVENTORY_FORM_TOOL,

  HIDE_ALL_MODAL_FORM_TOOL
} from '../types'
// import { saveOrder } from './order.js'

export default {
  /* uiToolsSaveForm({ commit, state }) {
    saveOrder(
      { commit, state },
      {
        beforeCb: () => { commit(SHOW_SAVE_FORM_TOOL) }
      }
    )
  },*/
  uiToolsShowSaveForm ({ commit }) {
    commit(SHOW_SAVE_FORM_TOOL)
  },
  uiToolsHideSaveForm ({ commit }) {
    commit(HIDE_SAVE_FORM_TOOL)
  },
  uiToolsSetStateSaveForm ({ commit }, params) {
    commit(SET_STATE_SAVE_FORM_TOOL, params)
  },

  uiToolsShowLoadForm ({ commit }) {
    commit(SHOW_LOAD_FORM_TOOL)
  },
  uiToolsHideLoadForm ({ commit }) {
    commit(HIDE_LOAD_FORM_TOOL)
  },
  uiToolsSetStateLoadForm ({ commit }, dataState) {
    commit(SET_STATE_LOAD_FORM_TOOL, dataState)
  },

  uiToolsSetStateInventoryForm ({ commit }, data, object) {
    commit(SET_STATE_INVENTORY_FORM_TOOL, data, object)
  },

  uiToolsHideAllModals ({ commit }) {
    commit(HIDE_ALL_MODAL_FORM_TOOL)
  }
}
