import types from '../types'

import {
    orderType,
    orderPaymentType,
    orderPaymentMethod,
    currentOrderSalesTax,
    currentOrderSecurityDeposit,
    orderRtoType,
    orderGrossBuydown,
    currentOrderNetBuydown,
    currentOrderDepositAmount,
    currentOrderRtoPayment,
    orderDepositReceived,
    orderTransactionId,
    currentOrderBuildingTotal,
    currentOrderPaymentType,
    currentOrderPaymentMethod,
    currentOrderType,
    currentOrderRtoType,
    currentOrderRtoTerm
} from '../_getters/order-order'

const computeOrderSummary = function ({commit, state}) {
    let summary = {
        type: orderType(state),
        paymentType: orderPaymentType(state),
        paymentMethod: orderPaymentMethod(state),
        salesTax: currentOrderSalesTax(state),
        securityDeposit: currentOrderSecurityDeposit(state),
        rtoType: orderRtoType(state),
        grossBuydown: orderGrossBuydown(state),
        netBuydown: currentOrderNetBuydown(state),
        depositAmount: currentOrderDepositAmount(state),
        rtoPayment: currentOrderRtoPayment(state)(),
        depositReceived: orderDepositReceived(state),
        transactionId: orderTransactionId(state),
        currentTotal: currentOrderBuildingTotal(state),
        currentPaymentType: currentOrderPaymentType(state),
        currentPaymentMethod: currentOrderPaymentMethod(state),
        currentType: currentOrderType(state),
        currentRtoType: currentOrderRtoType(state),
        currentRtoTerm: currentOrderRtoTerm(state)
    }

    commit(types.UPDATE_SUMMARY, {order: summary})
}

export default computeOrderSummary