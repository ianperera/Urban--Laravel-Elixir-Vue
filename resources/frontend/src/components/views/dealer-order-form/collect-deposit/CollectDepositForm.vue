<template>

    <!-- Collect Deposit Info -->
    <div class="row">
        <div class="col-md-12">
            <div class="list-group">
                <div class="list-group-item sub-step">

                    <!-- [Deposit Amount Received] -->

                    <h4 class="list-group-item-heading">Please enter the deposit amount received:</h4>
                    <div class="btn-group btn-group-sm">
                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon">$</span>
                                <input @change="updateOrder({'depositReceived': filters.currency($event.target.value)})"
                                       type="number" class="form-control input-sm"
                                       :class="{'invalid': $v.depositReceived.$error}"
                                       :value="depositReceived | currency"/>
                            </div>
                        </div>
                    </div>
                    <div>
                        <i class="fa fa-info-circle" aria-hidden="true"></i> Min. deposit amount: <ins>$ {{ filters.currency(depositAmount) }}</ins>
                    </div>

                    <div v-if="$v.depositReceived.$dirty && $v.depositReceived.required === false" class="alert alert-danger text-standard" role="alert">This field is required.</div>
                    <div v-if="$v.depositReceived.$dirty && $v.depositReceived.between === false" class="alert alert-danger text-standard" role="alert">The Deposit Received must equal the Deposit Amount.</div>

                    <!-- [/Deposit Amount Received] -->

                </div>
                <div class="list-group-item sub-step" v-if="depositReceived > 0">

                    <!-- [Payment Method] -->

                    <h4 class="list-group-item-heading">How does the customer intend to pay the desposit?</h4>
                    <div class="btn-group btn-group-sm" data-toggle="buttons">
                        <label :class="{'active': paymentMethod === 'cash'}" class="btn btn-default">
                            <input @click="checkboxPaymentMethod('paymentMethod', $event.target.value, 'final')" type="radio" name="paymentMethod"
                                   :value="'cash'"
                                   :checked="paymentMethod === 'cash'">Cash
                        </label>
                        <label :class="{'active': paymentMethod === 'check'}" class="btn btn-default">
                            <input @click="checkboxPaymentMethod('paymentMethod', $event.target.value, 'transaction-id')" type="radio" name="paymentMethod"
                                   :value="'check'"
                                   :checked="paymentMethod == 'check'">Check
                        </label>
                        <label :class="{'active': paymentMethod === 'credit_card'}" class="btn btn-default">
                            <input @click="checkboxPaymentMethod('paymentMethod', $event.target.value)" type="radio" name="paymentMethod"
                                   :value="'credit_card'"
                                   :checked="paymentMethod === 'credit_card'">Credit Card
                        </label>
                    </div>

                    <div v-if="paymentMethod === 'credit_card'" class="payment-method__cc">
                        <h4 class="list-group-item-heading">Online payment:</h4>
                        <stripe-checkout :disabled="true"
                                         :payment-token="paymentToken"
                                         :amount="currentTotalPurchase"
                                         :email="orderCustomer.email"
                                         @complete-payment="completePayment"></stripe-checkout>
                    </div>

                    <div v-if="$v.paymentMethod.$dirty && $v.paymentMethod.required === false" class="alert alert-danger text-standard" role="alert">This field is required.</div>

                    <!-- [/Payment Method] -->

                </div>
                <div class="list-group-item sub-step" v-if="paymentMethod == 'check' || paymentMethod == 'credit_card'">

                    <!-- [Transaction ID] -->

                    <h4 class="list-group-item-heading">
                        <template v-if="paymentMethod === 'check'">Please enter the check number:</template>
                        <template v-if="paymentMethod === 'credit_card'">Please enter the last four digits of the credit card number:</template>
                    </h4>
                    <div class="btn-group btn-group-sm">
                        <input @change="updateOrder({'transactionId': $event.target.value})" type="text"
                               class="form-control input-sm"
                               :value="transactionId">
                    </div>
                    <div v-if="$v.transactionId.$dirty && $v.transactionId.required === false" class="alert alert-danger text-standard" role="alert">This field is required.</div>

                    <!-- [/Transaction ID] -->

                </div>

                <div class="list-group-item item-heading">
                    <div class="col-xs-12 plr-none text-center">
                        <download-deposit-receipt-button :id="orderCurrent.id"
                                                         :categoryId="'deposit_receipt'"
                                                         :downloadLabel="'Download Receipt'"/>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>

    </div>

</template>

<script type="text/babel">
    import {mapActions, mapGetters} from 'vuex'
    import vuealidateAnyerror from 'src/mixins/vuelidate/anyerror'
    import subSteps from 'src/mixins/sub-steps'
    import validation from 'src/validations/dealer-order-form/deposit-stage.validation.js'
    import ExpenseDetails from '../ExpenseDetails.vue'
    import StripeCheckout from 'src/components/libs/stripe/Checkout.vue'
    import DownloadDepositReceiptButton from './DownloadDepositReceiptButton.vue'
    // import DownloadDocumentButton from '../partial/DownloadDocumentButton.vue'

    export default {
        name: 'deposit-stage',
        mixins: [vuealidateAnyerror, subSteps],
        validations: validation,
        data: function () {
            return {
                currentStep: 'deposit-received'
            }
        },
        components: {ExpenseDetails, StripeCheckout, DownloadDepositReceiptButton},
        beforeCreate() {
            this.$bus.$on('dofOrderLoaded', () => {
                this.receipt = {}
            })
        },
        created() {
            this.$v.$touch()
            this.$watch('$anyerror', (value) => {
                this.updateValidFlag(!value)
            })
        },
        props: {
            paymentToken: null
        },
        computed: {
            ...mapGetters({
                type: 'dealerOrderForm/orderType',
                orderState: 'dealerOrderForm/orderState',
                orderCustomer: 'dealerOrderForm/orderCustomer',
                orderCurrent: 'dealerOrderForm/orderCurrent',
                paymentMethod: 'dealerOrderForm/orderPaymentMethod',
                depositReceived: 'dealerOrderForm/orderDepositReceived',
                transactionId: 'dealerOrderForm/orderTransactionId',

                currentTotalPurchase: 'dealerOrderForm/currentTotalPurchase',
                currentPaymentMethod: 'dealerOrderForm/currentOrderPaymentMethod',
                depositAmount: 'dealerOrderForm/currentOrderDepositAmount',
                securityDeposit: 'dealerOrderForm/currentOrderSecurityDeposit',
                additionalDeposit: 'dealerOrderForm/currentOrderAdditionalDeposit'
            }),
            dataSync() {
                return this.orderState.sync.merging
            }
        },
        watch: {
            depositReceived(depositReceived, oldDepositReceived) {
                if (this.dataSync === 'running') return false
                if (depositReceived === oldDepositReceived) return false

                if (depositReceived === null) {
                    this.updateOrderOrder({
                        paymentMethod: null,
                        transactionId: null
                    })
                }
            }
        },
        methods: {
            ...mapActions({
                updateOrderOrder: 'dealerOrderForm/updateOrderOrder',
                updateValidFlag: 'dealerOrderForm/collectDeposit/updateValidFlag'
            }),
            updateOrder(object, nextStep) {
                this.updateOrderOrder(object)

                _.each(object, (val, key) => {
                    if (this.$v[key]) this.$v[key].$touch()
                })

                if (nextStep) this.goToStep(nextStep)
            },
            checkboxPaymentMethod(key, value, nextStep) {
                if (this[key] === value) {
                    this.updateOrderOrder({[key]: null})
                } else {
                    this.updateOrderOrder({[key]: value})

                    if (this.$v[key]) this.$v[key].$touch()
                    if (nextStep) this.goToStep(nextStep)
                }
            },
            // run/check values from batch of $validator fields/or sub-components
            $validate(options = {}) {
                if (options.$reset) this.$v.$reset()
                if (options.$touch) this.$v.$touch()
                return !this.$anyerror
            },
            completePayment(token) {
                this.$emit('complete-payment', token)
            }
        }
    }
</script>

<style type="text/css" lang="scss" rel="stylesheet/scss" scoped>
    .payment-method__cc {
        margin-top: 1em;
    }

    .list-group {
        margin-bottom: 0px;
    }

    .form-group {
        margin-bottom: 15px!important;
    }
</style>