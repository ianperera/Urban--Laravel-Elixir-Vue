import attachmentsFns from './attachments'
import customBuildOptionsFns from './custom-build-options'
import customerExpectsDate from './customer-expects-date'
import date from './date'
// import orderBuildingFns from './order-building'
// import orderOrderFns from './order-order'
import orderSummaryFns from './order-summary'

const orderState = state => {
    return state.state
}

const orderSyncStatus = state => {
    return state.state.sync.status
}

const orderStateMode = state => {
    return state.state.mode
}

const orderCurrent = state => {
    return state.current
}

const orderDealer = state => {
    return state.dealer
}

const orderDealerID = state => {
    return state.dealer.id
}

const orderCustomer = state => {
    return state.customer
}

const orderBuilding = state => {
    return state.building
}

const orderOrder = state => {
    return state.order
}

const orderRenter = state => {
    return state.renter
}

const orderSummary = state => {
    return state.summary
}

const orderValidation = state => {
    return state.validation
}

const signaturePingActive = state => {
    return state.current &&
        state.current.status &&
        state.order.signatureMethodId === 'e_signature' &&
        state.current.status.id === 'signature_pending' &&
        state.order.paymentType === 'cash'
}

export default {
    ...attachmentsFns,
    // ...orderBuildingFns,
    // ...orderOrderFns,
    ...orderSummaryFns,
    ...customBuildOptionsFns,
    customerExpectsDate,
    date,

    orderState,
    orderSyncStatus,
    orderStateMode,
    orderCurrent,
    orderDealer,
    orderDealerID,
    orderBuilding,
    orderOrder,
    orderSummary,
    orderCustomer,
    orderRenter,
    orderValidation,
    signaturePingActive
}