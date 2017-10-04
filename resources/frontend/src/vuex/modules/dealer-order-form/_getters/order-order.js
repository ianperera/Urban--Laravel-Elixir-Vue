import {
    orderDealer,
    orderBuilding,
    orderOrder
} from './'

import orderDate from './date'
// ex.Computed
import currentOrderCustomerExpectsDate from './customer-expects-date'

// grumpy =(
const orderType = state => {
    return state.order.type
}

const orderPaymentType = state => {
    return state.order.paymentType
}

const orderGrossBuydown = state => {
    if (state.order.grossBuydown === null) return 0
    return state.order.grossBuydown
}

const orderDeliveryCharge = state => {
    return state.order.deliveryCharge
}

const orderTaxDeliveryCharge = state => {
    return state.order.taxDeliveryCharge
}

const orderDepositReceived = state => {
    return state.order.depositReceived
}

const orderPaymentMethod = state => {
    return state.order.paymentMethod
}

const orderTransactionId = state => {
    return state.order.transactionId
}

const orderDeliveryRemarks = state => {
    return state.order.deliveryRemarks
}

const orderRtoType = state => {
    return state.order.rtoType
}

const orderRtoTerm = state => {
    return state.order.rtoTerm
}

const orderPromo99 = state => {
    return state.order.promo99
}

const orderSignatureMethodId = state => {
    return state.order.signatureMethodId
}

const currentOrderRtoType = state => {
    let rtoType = state.order.rtoType
    if (rtoType === null) {
        return 'Not yet selected'
    }

    if (rtoType === 'buydown') {
        return 'Buydown'
    } else {
        return 'No Buydown'
    }
}

const currentOrderRtoTerm = state => {
    let rtoTerms = state.orderTerms.rtoTerms
    let rtoTerm = orderRtoTerm(state) || null

    if (!_.isNil(rtoTerms) && !_.isNil(rtoTerm) && !_.isNil(rtoTerms[rtoTerm])) {
        return rtoTerms[rtoTerm]
    }

    return null
}

const currentOrderType = state => {
    let type = state.order.type
    if (type === 'order') {
        return 'Order'
    }
    if (type === 'quote') {
        return 'Quote'
    }
    return null
}

const currentOrderPaymentType = state => {
    let paymentType = state.order.paymentType
    if (paymentType === null) {
        return 'Not yet selected'
    }

    if (paymentType === 'cash') {
        return 'Cash'
    } else {
        return 'Rent-to-Own'
    }
}

const currentOrderPaymentMethod = state => {
    let paymentMethod = state.order.paymentMethod
    if (paymentMethod === 'cash') {
        return 'Cash'
    }

    if (paymentMethod === 'check') {
        return 'Check'
    }

    if (paymentMethod === 'credit_card') {
        return 'Credit Card'
    }

    return null
}

const currentOrderAdditionalDeposit = state => {
    return state.order.additionalDeposit ? state.order.additionalDeposit : 0
}

const currentOrderSecurityDeposit = state => {
    let building = orderBuilding(state)
    let dealer = orderDealer(state)
    let deliveryCharge = parseFloat(orderDeliveryCharge(state))
    let taxDeliveryCharge = orderTaxDeliveryCharge(state)
    let promo99 = orderOrder(state).promo99
    let additionalDeposit = currentOrderAdditionalDeposit(state)

    if (building === null) return 0 // building not specified yet

    let securityDeposit = 0 // init value
    let dealerTaxFactor = dealer.taxRate / 100 // for example 4.5% = 0.045

    // add tax on delivery fee
    if (taxDeliveryCharge === true) {
        deliveryCharge += deliveryCharge * dealerTaxFactor
    }

    // define security deposit
    if (promo99) {
        securityDeposit = 99
    } else if (building.saleType === 'dealer-inventory' && building.inventoryBuilding.serial !== null) {
        securityDeposit = building.inventoryBuilding.securityDeposit
    } else if (building.saleType === 'custom-order' && building.buildingDimension !== null) {
        var width = building.buildingDimension.width
        if (width <= 8) securityDeposit = 150
        if (width > 8 && width <= 10) securityDeposit = 200
        if (width > 10 && width <= 12) securityDeposit = 250
        if (width > 12 && width <= 14) securityDeposit = 300
    }

    // add delivery charge
    securityDeposit = securityDeposit + deliveryCharge

    securityDeposit += additionalDeposit
    return securityDeposit
}

const currentOrderNetBuydown = state => {
    let rtoType = orderRtoType(state)
    let grossBuydown = orderGrossBuydown(state)
    let securityDeposit = currentOrderSecurityDeposit(state)
    let dealerTaxFactor = orderDealer(state).taxRate / 100

    let netBuydown = 0
    if (rtoType === 'buydown') {
        if (grossBuydown !== null && securityDeposit !== null) {
            netBuydown = (grossBuydown - securityDeposit) / (1 + dealerTaxFactor)
        }
    }

    return netBuydown
}

const currentOrderBuildingTotal = state => {
    let building = orderBuilding(state)
    let total = 0

    // Custom Order
    if (building.saleType === 'dealer-inventory') {
        // Get Base Model Price
        if (building.inventoryBuilding.serial !== null) {
            total += building.inventoryBuilding.price
        }
    }

    // Custom Order
    if (building.saleType === 'custom-order') {
        // Get Base Model Price
        if (building.buildingDimension) {
            total += building.buildingDimension.shellPrice
        }

        let totalOptions = 0
        if (building.customBuildOptions.length > 0) {
            totalOptions = _.reduce(building.customBuildOptions, function (total, el) {
                total += (el.unitPrice * el.quantity)
                return total
            }, 0)
        }
        total += totalOptions
    }

    return total
}

const currentOrderSalesTax = state => {
    let dealer = orderDealer(state)
    if (dealer.taxRate !== null) {
        let buildingTotal = currentOrderBuildingTotal(state)
        let taxRate = dealer.taxRate
        return buildingTotal * (taxRate / 100)
    }
    return 0
}

const currentOrderRtoAmount = state => {
    let buildingTotal = currentOrderBuildingTotal(state)
    let netBuydown = currentOrderNetBuydown(state)
    let rtoAmount = buildingTotal

    if (parseFloat(netBuydown) > 0) {
        rtoAmount = buildingTotal - parseFloat(netBuydown)
    }
    return rtoAmount
}

const currentOrderAdvancedMonthlyRenewalPayment = state => {
    let paymentType = orderOrder(state).paymentType
    let rtoTerm = currentOrderRtoTerm(state)

    if (paymentType === 'rto' && rtoTerm !== null) {
        var rtoFactor = rtoTerm.rtoFactor
        return (currentOrderRtoAmount(state) / rtoFactor)
    }
    return 0
}

const currentOrderRtoPayment = state => args => {
    args = args || {}
    let buildingTotal = currentOrderBuildingTotal(state)
    let grossBuydown = orderGrossBuydown(state)
    let paymentType = orderOrder(state).paymentType
    let rtoTerm = currentOrderRtoTerm(state)
    let rtoType = orderRtoType(state)
    let dealerTaxFactor = orderDealer(state).taxRate / 100

    if (!_.isUndefined(args.rtoType)) {
        rtoType = args.rtoType
    }

    if (paymentType === 'rto' && rtoTerm !== null) {
        let rtoFactor = rtoTerm.rtoFactor

        if (rtoType === 'no-buydown') {
            let total = buildingTotal / rtoFactor
            return total * (1 + dealerTaxFactor)
        }

        if (rtoType === 'buydown' && grossBuydown !== null) {
            let advancedMonthlyRtoPayment = currentOrderAdvancedMonthlyRenewalPayment(state)
            let rtoPayment = advancedMonthlyRtoPayment * (1 + dealerTaxFactor)
            return rtoPayment
        }
    }

    return 0
}

// total deposit amount
const currentOrderDepositAmount = state => {
    let buildingTotal = currentOrderBuildingTotal(state)
    let deliveryCharge = parseFloat(orderDeliveryCharge(state))
    let taxDeliveryCharge = orderTaxDeliveryCharge(state)
    let paymentType = orderOrder(state).paymentType
    let dealer = orderDealer(state)

    let depositAmount = 0
    let cashSaleDepositFactor = dealer.cashSaleDepositRate === null ? 1 : dealer.cashSaleDepositRate / 100
    let dealerTaxFactor = dealer.taxRate / 100

    if (paymentType === 'cash') {
        if (dealer.depositType === 1) {
            depositAmount = cashSaleDepositFactor * buildingTotal
        } else {
            depositAmount = cashSaleDepositFactor * buildingTotal * (1 + dealerTaxFactor)

            // add delivery fee with tax
            if (taxDeliveryCharge === true) {
                depositAmount += deliveryCharge * (1 + dealerTaxFactor)
            }
        }
    }

    if (paymentType === 'rto') {
        let minDepositAmount = currentOrderMinDepositAmount(state)
        let grossBuydown = orderGrossBuydown(state)

        // initial deposit amount calculated with grossbuydown = 0
        if (minDepositAmount > grossBuydown) {
            depositAmount = minDepositAmount + deliveryCharge
        } else {
            depositAmount = grossBuydown + deliveryCharge
        }
    }

    return Math.round(depositAmount * 100) / 100
}

const currentOrderMinDepositAmount = state => {
    let paymentType = orderOrder(state).paymentType
    let promo99 = orderOrder(state).promo99
    let securityDeposit = currentOrderSecurityDeposit(state)

    let minDepositAmount = 0
    if (paymentType === 'rto') {
        let rtoPayment = 0
        if (!promo99) {
            rtoPayment = currentOrderRtoPayment(state)({rtoType: 'no-buydown'})
        }

        minDepositAmount = securityDeposit + rtoPayment
    }

    return Math.round(minDepositAmount * 100) / 100
}

const currentTotalPurchase = state => {
    let buildingTotal = currentOrderBuildingTotal(state)
    let salesTax = currentOrderSalesTax(state)

    return buildingTotal + salesTax
}

export default {
    orderDate,
    orderType,
    orderPaymentType,
    orderGrossBuydown,
    orderDeliveryCharge,
    orderTaxDeliveryCharge,
    orderDepositReceived,
    orderPaymentMethod,
    orderTransactionId,
    orderDeliveryRemarks,
    orderRtoType,
    orderRtoTerm,
    orderPromo99,
    orderSignatureMethodId,
    currentOrderCustomerExpectsDate,
    currentOrderRtoType,
    currentOrderRtoTerm,
    currentOrderType,
    currentOrderPaymentType,
    currentOrderPaymentMethod,
    currentOrderSecurityDeposit,
    currentOrderAdditionalDeposit,
    currentOrderNetBuydown,
    currentOrderBuildingTotal,
    currentOrderSalesTax,
    currentOrderRtoAmount,
    currentOrderAdvancedMonthlyRenewalPayment,
    currentOrderRtoPayment,
    currentOrderDepositAmount,
    currentOrderMinDepositAmount,
    currentTotalPurchase
}