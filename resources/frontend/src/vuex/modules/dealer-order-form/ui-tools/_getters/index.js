const getUiToolsShowLoadForm = state => {
    return state.loadForm.show
}

const getUiToolsStateLoadForm = state => {
    return state.loadForm.state
}

const getUiToolsSaveForm = state => {
    return state.saveForm
}

const getUiToolsShowSaveForm = state => {
    return state.saveForm.show
}

const getUiToolsStateSaveForm = state => {
    return state.saveForm.state
}

const getUiToolsShowInventoryForm = state => {
    return state.inventoryForm.show
}

const getUiToolsStateInventoryForm = state => {
    return state.inventoryForm.state
}

export default {
    getUiToolsShowLoadForm,
    getUiToolsStateLoadForm,
    getUiToolsSaveForm,
    getUiToolsShowSaveForm,
    getUiToolsStateSaveForm,
    getUiToolsShowInventoryForm,
    getUiToolsStateInventoryForm
}