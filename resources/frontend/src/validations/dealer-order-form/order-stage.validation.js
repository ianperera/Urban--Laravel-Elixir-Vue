import {required, between} from 'vuelidate/lib/validators'
import date from 'src/validators/date'

export default function () {
    let allRules = {
        deliveryCharge: {required, between: between(0, 1000000)},
        orderDate: {required, date}
    }

    return allRules
}
