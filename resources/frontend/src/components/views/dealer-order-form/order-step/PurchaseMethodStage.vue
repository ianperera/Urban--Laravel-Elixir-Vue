<template>

    <!-- Purchase Method -->
    <div class="row">
        <div class="col-md-7">
            <div class="list-group">
                <div class="list-group-item item-heading">
                    <div class="col-xs-2 plr-none text-left">
                        <button class="btn btn-default"
                                v-on:click.prevent="goToStep('previous')"><i class="fa fa-arrow-left fa-fw"></i> Previous
                        </button>
                    </div>
                    <div class="col-xs-8 plr-none text-center">
                        <h4>Purchase Method</h4>
                    </div>
                    <div class="col-xs-2 plr-none text-right">
                        <button class="btn"
                                v-bind:class="buttonSuggesting"
                                v-on:click.prevent="goToStep('next')">Next <i class="fa fa-arrow-right fa-fw"></i>
                        </button>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="list-group-item sub-step">

                    <!-- [Payment Type] -->
                    <h4 class="list-group-item-heading">Please choose payment type:</h4>
                    <div class="btn-group btn-group-lg1" data-toggle="buttons">
                        <label :class="{'active': paymentType === 'cash'}" class="btn btn-default">
                            <input @click="change({'paymentType': $event.target.value})"
                                   type="radio"
                                   name="paymentType"
                                   :value="'cash'"
                                   :checked="paymentType === 'cash'">
                            <i class="fa fa-money"></i> Cash
                        </label>
                        <label :class="{'active': paymentType === 'rto'}" class="btn btn-default">
                            <input @click="change({'paymentType': $event.target.value})"
                                   type="radio"
                                   name="paymentType"
                                   :value="'rto'"
                                   :checked="paymentType === 'rto'">
                            <i class="fa fa-history"></i> Rent-to-Own
                        </label>
                    </div>
                    <div v-if="$v.paymentType.$dirty && $v.paymentType.required === false"
                         class="alert alert-danger" role="alert">The type of payment must be indicated.
                    </div>
                    <!-- [Payment Type] -->

                </div>
                <div class="list-group-item sub-step" v-if="paymentType === 'rto'">

                    <!-- [RTO Type] -->
                    <h4 class="list-group-item-heading">Please choose Rent-to-Own method:</h4>
                    <div class="btn-group btn-group-md" data-toggle="buttons">
                        <label :class="{'active': rtoType === 'buydown'}" class="btn btn-default">
                            <input @click="change({'rtoType': $event.target.value})"
                                   type="radio"
                                   name="rtoType"
                                   :value="'buydown'"
                                   :checked="rtoType === 'buydown'">
                            <i class="fa fa-hand-rock-o"></i> Buydown
                        </label>
                        <label :class="{'active': rtoType === 'no-buydown'}" class="btn btn-default">
                            <input @click="change({'rtoType': $event.target.value})"
                                   type="radio"
                                   name="rtoType"
                                   :value="'no-buydown'"
                                   :checked="rtoType === 'no-buydown'">
                            <i class="fa fa-hand-paper-o"></i> No buydown
                        </label>
                    </div>
                    <div v-if="$v.rtoType.$dirty && $v.rtoType.required === false" class="alert alert-danger"
                         role="alert">This field is required.
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div>
                                <label>
                                    <input type="checkbox"
                                           @change="change({'promo99': $event.target.checked})"
                                           :value="true"
                                           :checked="promo99">
                                    $99 promo sale
                                </label>
                            </div>
                            <!--
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox"
                                           @change="switchAdditionalDeposit()"
                                           :value="true"
                                           :checked="showAdditionalDeposit">
                                    Extra Security Deposit
                                </label>
                            </div>
                            <div class="list-group sub-step" v-if="paymentType === 'rto' && showAdditionalDeposit">
                                <label for="additionalDeposit">Extra Security Deposit</label>
                                <div class="input-group">
                                    <span class="input-group-addon">$</span>
                                    <input class="form-control"
                                           @change="change({'additionalDeposit': parseInt($event.target.value)})"
                                           id="additionalDeposit"
                                           type="text"
                                           name="additionalDeposit"
                                           :value="additionalDeposit">
                                </div>
                            </div>
                            -->
                        </div>
                    </div>
                    <!-- [/RTO Type] -->

                    <!-- [RTO Term] -->
                    <h4 class="list-group-item-heading">Please choose your terms:</h4>
                    <div class="btn-group" data-toggle="buttons">
                        <label v-for="term in rtoTerms" :class="{'active': rtoTerm && rtoTerm == term.value}" class="btn btn-default">
                            <input @click="change({'rtoTerm': $event.target.value})"
                                   type="radio"
                                   name="rtoTerm"
                                   :value="term.value"
                                   :checked="rtoTerm && rtoTerm == term.value">
                            {{ term.name }}
                        </label>
                    </div>
                    <div v-if="$v.rtoTerm.$dirty && $v.rtoTerm.required === false" class="alert alert-danger"
                         role="alert">Rent-to-own terms are required.
                    </div>
                    <!-- [/RTO Term] -->

                </div>
                <div class="list-group-item sub-step" v-if="rtoType === 'buydown'">
                    <!-- [Gross buydown] -->
                    <h4 class="list-group-item-heading">Please enter gross buydown amount:</h4>
                    <div class="btn-group">
                        <div class="input-group">
                            <span class="input-group-addon">$</span>
                            <input @change="change({'grossBuydown': filters.currency($event.target.value)})"
                                   type="number"
                                   :class="{'invalid': $v.grossBuydown.$error}" class="form-control"
                                   :value="grossBuydown | currency"/>
                        </div>
                    </div>
                    <div v-if="$v.grossBuydown.$dirty && $v.grossBuydown.between === false"
                         class="alert alert-danger" role="alert">
                        The minimum Gross Buydown is $ {{ filters.currency(minDepositAmount) }}
                    </div>
                    <div v-if="$v.grossBuydown.$dirty && $v.grossBuydown.required === false"
                         class="alert alert-danger" role="alert">This field is required.
                    </div>
                    <!-- [/Gross buydown] -->
                </div>
                <div class="list-group-item text-left" v-if="paymentType !== null">
                    <button type="button" @click.prevent="showCollectDepositModal" class="btn btn-danger">Collect Deposit</button>
                </div>

                <div class="list-group-item visible-xs visible-sm">
                    <div class="col-xs-6 plr-none text-left">
                        <button class="btn btn-default"
                                v-on:click.prevent="goToStep('previous')"><i class="fa fa-arrow-left fa-fw"></i> Previous
                        </button>
                    </div>
                    <div class="col-xs-6 plr-none text-right">
                        <button class="btn"
                                v-bind:class="buttonSuggesting"
                                v-on:click.prevent="goToStep('next')">Next <i class="fa fa-arrow-right fa-fw"></i>
                        </button>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <h4>Order Summary</h4>

            <div class="list-group">

                <expense-details ref="expenseDetails"></expense-details>
            </div>
        </div>
    </div>

</template>

<script type="text/babel">
    import {mapActions, mapGetters} from 'vuex'
    import vuealidateAnyerror from 'src/mixins/vuelidate/anyerror'
    import purchaseMethodStageValidation from 'src/validations/dealer-order-form/purchase-method-stage.validation.js'
    import OrderDatepicker from './OrderDatepicker.vue'
    import ExpenseDetails from '../ExpenseDetails.vue'

    export default {
        name: 'purchase-method-stage',
        mixins: [vuealidateAnyerror],
        components: {OrderDatepicker, ExpenseDetails},
        validations: purchaseMethodStageValidation,
        data: function () {
            return {
                total: 0,
                date: {
                    placeholder: 'MM/DD/YYYY',
                    format: 'MM/dd/yyyy'
                },
                showAdditionalDeposit: false
            }
        },
        created() {
            this.$watch('$anyerror', (value) => {
                this.$parent.revalidate()
            })

            let self = this
            this.getOrderRtoTerms({
                successCb() {
                    self.$bus.$emit('enable-form')
                }
            })
        },
        computed: {
            dataSync() {
                return this.orderState.sync.merging
            },
            buttonSuggesting() {
                if (!this.nextStep('next') && this.$v.$dirty && !this.$anyerror) {
                    return {'btn-success': true}
                }

                return {'btn-default': true}
            },
            deliveryRemarksSummary() {
                var total = _.size(this.deliveryRemarks)
                var selectedEls = _.filter(this.deliveryRemarks, function (el) {
                    return !(el === false || el === null || el === '')
                })

                var selected = _.size(selectedEls)
                return selected + '/' + total
            },
            ...mapGetters({
                orderState: 'dealerOrderForm/orderState',
                dealer: 'dealerOrderForm/orderDealer',
                building: 'dealerOrderForm/orderBuilding',
                rtoTerms: 'dealerOrderForm/orderTerms/orderRtoTerms',

                type: 'dealerOrderForm/orderType',
                paymentType: 'dealerOrderForm/orderPaymentType',
                grossBuydown: 'dealerOrderForm/orderGrossBuydown',
                deliveryCharge: 'dealerOrderForm/orderDeliveryCharge',
                taxDeliveryCharge: 'dealerOrderForm/orderTaxDeliveryCharge',
                deliveryRemarks: 'dealerOrderForm/orderDeliveryRemarks',
                orderDate: 'dealerOrderForm/orderDate',
                rtoType: 'dealerOrderForm/orderRtoType',
                rtoTerm: 'dealerOrderForm/orderRtoTerm',
                promo99: 'dealerOrderForm/orderPromo99',

                currentType: 'dealerOrderForm/currentOrderType',
                currentPaymentType: 'dealerOrderForm/currentOrderPaymentType',
                currentRtoType: 'dealerOrderForm/currentOrderRtoType',
                currentRtoTerm: 'dealerOrderForm/currentOrderRtoTerm',
                currentOrderCustomerExpectsDate: 'dealerOrderForm/currentOrderCustomerExpectsDate',

                // for validation
                additionalDeposit: 'dealerOrderForm/currentOrderAdditionalDeposit',
                securityDeposit: 'dealerOrderForm/currentOrderSecurityDeposit',
                depositAmount: 'dealerOrderForm/currentOrderDepositAmount',
                minDepositAmount: 'dealerOrderForm/currentOrderMinDepositAmount'
            })
        },
        methods: {
            ...mapActions({
                getOrderRtoTerms: 'dealerOrderForm/orderTerms/getOrderRtoTerms',
                updateOrderOrder: 'dealerOrderForm/updateOrderOrder',
                showCollectDepositModal: 'dealerOrderForm/collectDeposit/showModal'
            }),
            change(object) {
                this.updateOrderOrder(object)

                _.each(object, (val, key) => {
                    let $v = _.get(this.$v, key, false)
                    if ($v) $v.$touch()
                })
            },
            switchAdditionalDeposit() {
                this.showAdditionalDeposit = !this.showAdditionalDeposit
                this.change({'additionalDeposit': 0})
            },
            goToStep(direction) {
                return this.$parent.goToStep(direction)
            },
            nextStep(direction) {
                return this.$parent.nextStep(direction)
            }
        },
        watch: {
            type(newStep, oldStep) {
                if (this.dataSync === 'running') return false
                if (newStep === oldStep) return false

                this.updateOrderOrder({
                    paymentType: null,
                    rtoType: null,
                    rtoTerm: null,
                    promo99: false,
                    grossBuydown: 0,
                    deliveryCharge: 0,
                    depositReceived: null,
                    generatedFile: null,
                    fileGenerated: null
                })
            },
            paymentType(paymentType, oldPaymentType) {
                if (this.dataSync === 'running') return false
                if (paymentType === oldPaymentType) return false

                if (paymentType === 'cash') {
                    // set RTO options to null
                    this.updateOrderOrder({
                        rtoType: null,
                        rtoTerm: null,
                        promo99: false,
                        grossBuydown: 0
                    })
                }
                if (paymentType === 'rto') {}
                if (paymentType !== oldPaymentType) {
                    this.$nextTick(() => {
                        this.$parent.revalidate()
                    })
                }
            },
            rtoType(rtoType, oldRtoType) {
                if (this.dataSync === 'running') return false
                if (rtoType === oldRtoType) return false

                if (oldRtoType !== null) {
                    if (oldRtoType === 'no-buydown' && rtoType === 'buydown') {
                        this.change({grossBuydown: 0})
                    }
                }
            }
        }
    }
</script>

<style type="text/css" lang="scss" rel="stylesheet/scss" scoped>
    span.badge.notice {
        color: red;
        background: rgb(218, 218, 218);
    }
</style>