import {required, between} from 'vuelidate/lib/validators'

export default function () {
    let allRules = {
        // inital step
        depositReceived: {},
        paymentMethod: {},
        transactionId: {}
    }

    allRules.depositReceived.between = between(this.depositAmount, 9999999)
    allRules.depositReceived.required = required
    allRules.paymentMethod.required = required

    if (this.paymentMethod === 'credit_card' || this.paymentMethod === 'check') {
        allRules.transactionId.required = required
    }

    return allRules
}
