import {required, between} from 'vuelidate/lib/validators'

export default function () {
    let allRules = {
        paymentType: {required}
    }

    if (this.paymentType === 'rto') {
        allRules.rtoType = {required}
        allRules.rtoTerm = {required}
        allRules.grossBuydown = {}

        if (this.rtoType === 'buydown') {
            allRules.grossBuydown.between = between(this.minDepositAmount, 10000000)
        }

        if (this.rtoType === 'buydown' && this.orderState.mode === 'submit') {
            allRules.grossBuydown.required = required
        }
    }

    return allRules
}
