<template>
    <!-- TODO: FIX IT-->
    <div>
        <!-- Building total -->
        <div class="list-group-item list-group-item-warning lead main" v-if="totalPurchase > 0">
            <div class="row">
                <div class="col-xs-6 text-right">Total Purchase:</div>
                <div class="col-xs-6"><strong>{{ totalPurchase | money }}</strong></div>
            </div>
        </div>

        <div class="list-group-item list-group-item-success lead" v-if="totalPurchase > 0">
            <div class="row">
                <div class="col-xs-6 text-right">Building Total:</div>
                <div class="col-xs-6"><strong>{{ currentBuilding.totalPrice | money }}</strong></div>
            </div>
        </div>

        <div class="list-group-item list-group-item-info lead" v-if="totalPurchase > 0">
            <div class="row">
                <div class="col-xs-6 text-right">Shell Price:</div>
                <div class="col-xs-6"><strong>{{ currentBuilding.shellPrice | money }}</strong></div>
            </div>
        </div>

        <div class="list-group-item list-group-item-info lead" v-if="totalPurchase > 0">
            <div class="row">
                <div class="col-xs-6 text-right">Total Option Price:</div>
                <div class="col-xs-6"><strong>{{ currentBuilding.totalOptions | money }}</strong></div>
            </div>
        </div>

        <!-- (cash) sales Tax -->
        <div class="list-group-item list-group-item-info lead" v-if="totalPurchase > 0 ">
            <div class="row">
                <div class="col-xs-6 text-right">Sales Tax:</div>
                <div class="col-xs-6"><strong>{{ salesTax | money }}</strong></div>
            </div>
        </div>

        <!-- (rto) Security Deposit -->
        <div class="list-group-item list-group-item-info lead" v-if="paymentType === 'rto' && totalPurchase > 0">
            <div class="row">
                <div class="col-xs-6 text-right">Security Deposit:</div>
                <div class="col-xs-6"><strong>{{ securityDeposit | money }}</strong></div>
            </div>
        </div>

        <!-- (rto) Gross buydown -->
        <div class="list-group-item list-group-item-info lead" v-if="paymentType === 'rto' && rtoType == 'buydown' ">
            <div class="row">
                <div class="col-xs-6 text-right">Gross buydown:</div>
                <div class="col-xs-6"><strong>{{ grossBuydown | money }}</strong></div>
            </div>
        </div>

        <!-- (rto) Net buydown -->
        <div class="list-group-item list-group-item-info lead" v-if="paymentType === 'rto' && rtoType == 'buydown' ">
            <div class="row">
                <div class="col-xs-6 text-right">Net Buydown:</div>
                <div class="col-xs-6"><strong>{{ netBuydown | money }}</strong></div>
            </div>
        </div>

        <!-- (rto) Total Advanced Monthly Payment (rtoPayment) -->
        <div class="list-group-item list-group-item-info lead" v-if="paymentType === 'rto' && totalPurchase > 0">
            <div class="row">
                <div class="col-xs-6 text-right">
                    <span class="hidden-xs hidden-md">RTO Payment:</span>
                    <span class="hidden-sm hidden-lg">RTO Payment:</span>
                </div>
                <div class="col-xs-6"><strong>{{ rtoPayment() | money }}</strong></div>
            </div>
        </div>

        <!-- Deposit Amount -->
        <div class="list-group-item list-group-item-info lead" v-if="paymentType !== null">
            <div class="row">
                <div class="col-xs-6 text-right">Required Deposit:</div>
                <div class="col-xs-6"><strong>{{ depositAmount | money }}</strong></div>
            </div>
        </div>

        <!-- Deposit Received -->
        <div class="list-group-item list-group-item-info lead" v-if="paymentType !== null">
            <div class="row">
                <div class="col-xs-6 text-right">Deposit Received:</div>
                <div class="col-xs-6">
                    <strong>{{ depositReceived | money }}</strong>
                    <span v-if="isValidRepositReceived === false">
                        <i class="fa fa-exclamation-circle invalid" aria-hidden="true"></i>
                    </span>
                    <button type="button"
                            @click.prevent="showCollectDepositModal()"
                            class="btn btn-xs btn-danger">Collect Deposit
                    </button>
                </div>
            </div>
        </div>

        <div class="list-group-item text-center" v-if="totalPurchase > 0">
            <small>Retail price includes delivery and setup (taxes not included)</small>
        </div>

        <!-- Rent-to-Own Opts -->
        <div class="list-group-item list-group-item-warning main" v-if="totalPurchase > 0">
            <div class="row">
                <div class="col-xs-12 text-center"><strong>Rent-to-Own Options</strong></div>
            </div>
            <div class="row">
                <div class="col-xs-6 text-right">Security Deposit:</div>
                <div class="col-xs-6"><strong>{{ securityDeposit | money }}</strong></div>
            </div>

            <div class="row" v-for="(rtoPayment, termId) in rtoPayments">
                <div class="col-xs-6 text-right">{{ termId }}-month payment:</div>
                <div class="col-xs-6"><strong>{{ rtoPayment | money }}</strong></div>
            </div>
        </div>
    </div>

</template>

<script type="text/babel">
    import {mapGetters, mapActions} from 'vuex'

    export default {
        data() {
            return {
            }
        },
        computed: {
            ...mapGetters({
                dealer: 'dealerOrderForm/orderDealer',
                rtoTerms: 'dealerOrderForm/orderTerms/orderRtoTerms',
                rtoType: 'dealerOrderForm/orderRtoType',
                paymentType: 'dealerOrderForm/orderPaymentType',
                grossBuydown: 'dealerOrderForm/orderGrossBuydown',
                depositReceived: 'dealerOrderForm/orderDepositReceived',
                isValidRepositReceived: 'dealerOrderForm/collectDeposit/isValid',

                currentBuilding: 'dealerOrderForm/currentBuilding',

                // buildingTotal: 'dealerOrderForm/currentOrderBuildingTotal',
                totalPurchase: 'dealerOrderForm/currentTotalPurchase',
                salesTax: 'dealerOrderForm/currentOrderSalesTax',
                rtoAmount: 'dealerOrderForm/currentOrderRtoAmount',
                securityDeposit: 'dealerOrderForm/currentOrderSecurityDeposit',
                netBuydown: 'dealerOrderForm/currentOrderNetBuydown',
                rtoPayment: 'dealerOrderForm/currentOrderRtoPayment',
                depositAmount: 'dealerOrderForm/currentOrderDepositAmount'
            }),
            rtoPayments() {
                let self = this
                let terms = {
                    24: 0,
                    36: 0,
                    48: 0,
                    60: 0
                }

                if (_.size(self.rtoTerms) === 0) return terms

                let rtoAmount = this.rtoAmount
                _.each(terms, function(value, key) {
                    let rtoAdvanceMRP = rtoAmount / self.rtoTerms[key]['rtoFactor']
                    let rtoSalesTax = rtoAdvanceMRP * (self.dealer.taxRate / 100)
                    let rtoTotalAdvanceMRP = rtoAdvanceMRP + rtoSalesTax
                    terms[key] = rtoTotalAdvanceMRP
                })
                return terms
            }
        },
        methods: {
            ...mapActions({
                showCollectDepositModal: 'dealerOrderForm/collectDeposit/showModal'
            })
        }
    }
</script>

<style type="text/css">

</style>