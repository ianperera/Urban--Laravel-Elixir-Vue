import {
  RECEIVE_SETTINGS
} from '../types'
import settings from 'src/api/settings'

export default {
  getAllSettings ({commit}, {beforeCb, successCb, errorCb}) {
    settings.getSettings(
      () => {
        if (beforeCb) beforeCb()
      },
      (options) => {
        commit(RECEIVE_SETTINGS, options)
        if (successCb) successCb(options)
      },
      (response) => {
        if (errorCb) errorCb(response)
      }
    )
  }
}
