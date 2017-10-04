<template>

    <!-- Order Info -->
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
                        <h4>Order Options</h4>
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
                    <!-- [Delivery Charge] -->
                    <h4 class="list-group-item-heading">If there is an extra delivery charge, please enter it:</h4>
                    <div class="btn-group">
                        <div class="input-group">
                            <span class="input-group-addon">$</span>
                            <input @change="change({'deliveryCharge': filters.currency($event.target.value)})"
                                   type="number"
                                   :class="{'invalid': $v.deliveryCharge.$error}" class="form-control"
                                   :value="deliveryCharge | currency"/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div>
                                <label>
                                    <input @change="change({'taxDeliveryCharge': $event.target.checked})"
                                           type="checkbox"
                                           :value="true"
                                           :checked="taxDeliveryCharge">
                                    Sales Tax
                                </label>
                            </div>
                        </div>
                    </div>

                    <div v-if="$v.deliveryCharge.$dirty && $v.deliveryCharge.between === false"
                         class="alert alert-danger text-standard" role="alert">
                        The Delivery Charge must be more or equals 0
                    </div>
                    <div v-if="$v.deliveryCharge.$dirty && $v.deliveryCharge.required === false"
                         class="alert alert-danger text-standard" role="alert">This field is required.
                    </div>
                    <!-- [/Delivery Charge] -->

                    <!-- [Delivery Remarks] -->
                    <h4 class="list-group-item-heading">Delivery Remarks:</h4>
                    <div class="row">
                        <div class="col-md-12">
                            <div>
                                <label>
                                    <input type="checkbox" :value="true" :checked="deliveryRemarks.levelPad"
                                           @change="change({'deliveryRemarks.levelPad': $event.target.checked})">
                                    Level Pad
                                </label>
                            </div>
                            <div>
                                <label>
                                    <input type="checkbox" :value="true" :checked="deliveryRemarks.softWhenWet"
                                           @change="change({'deliveryRemarks.softWhenWet': $event.target.checked})">
                                    Soft when wet
                                </label>
                            </div>
                            <div>
                                <label>
                                    <input type="checkbox" :value="true" :checked="deliveryRemarks.widthRestrictions"
                                           @change="change({'deliveryRemarks.widthRestrictions': $event.target.checked})">
                                    Width Restrictions
                                </label>
                            </div>
                            <div>
                                <label>
                                    <input type="checkbox" :value="true" :checked="deliveryRemarks.heightRestrictions"
                                           @change="change({'deliveryRemarks.heightRestrictions': $event.target.checked})">
                                    Height Restrictions
                                </label>
                            </div>
                            <div>
                                <label>
                                    <input type="checkbox" :value="true" :checked="deliveryRemarks.mustCrossNeighboringProperty"
                                           @change="change({'deliveryRemarks.mustCrossNeighboringProperty': $event.target.checked})">
                                    Must cross neighboring property
                                </label>
                            </div>
                            <div>
                                <label>
                                    <input type="checkbox" :value="true" :checked="deliveryRemarks.requiresSiteVisit"
                                           @change="change({'deliveryRemarks.requiresSiteVisit': $event.target.checked})">
                                    Requires site visit
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <h4 class="list-group-item-heading">Notes:</h4>
                            <textarea class="form-control"
                                      placeholder="Enter any special notes with respect to the building or delivery here."
                                      :value="deliveryRemarks.notes"
                                      @change="change({'deliveryRemarks.notes': $event.target.value})"></textarea>
                        </div>
                    </div>
                    <!-- [Delivery Remarks] -->

                    <!-- [Order Date] -->
                    <h4 class="list-group-item-heading">Order date (MM/DD/YYYY):</h4>
                    <order-datepicker ref="dateExpected"
                                      :format="date.format"
                                      :placeholder="date.placeholder"
                                      :value="orderDate">
                    </order-datepicker>

                    <div v-if="$v.orderDate.$dirty && $v.orderDate.date === false" class="alert alert-danger"
                         role="alert">Enter a valid Order date (MM-DD-YYYY)
                    </div>
                    <div v-if="$v.orderDate.$dirty && $v.orderDate.required === false"
                         class="alert alert-danger" role="alert">This field is required.
                    </div>

                    <h4 class="list-group-item-heading" style="margin-top: 1em">Estimated Delivery Period:</h4>
                    <div class="row">
                        <div class="col-md-12 form-inline">
                            <div class="form-group">
                                <label>From:</label>
                                <input type="text"
                                       disabled="disabled"
                                       class="form-control"
                                       v-bind:value="currentOrderCustomerExpectsDate.start">
                            </div>
                            <div class="form-group">
                                <label>To:</label>
                                <input type="text"
                                       disabled="disabled"
                                       class="form-control"
                                       v-bind:value="currentOrderCustomerExpectsDate.end">
                            </div>
                        </div>
                    </div>
                    <!-- [/Order Date] -->
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
    import orderStageValidation from 'src/validations/dealer-order-form/order-stage.validation.js'
    import OrderDatepicker from './OrderDatepicker.vue'
    import ExpenseDetails from '../ExpenseDetails.vue'

    export default {
        name: 'order-stage',
        mixins: [vuealidateAnyerror],
        components: {OrderDatepicker, ExpenseDetails},
        validations: orderStageValidation,
        data: function () {
            return {
                total: 0,
                date: {
                    placeholder: 'MM/DD/YYYY',
                    format: 'MM/dd/yyyy'
                }
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
            deliveryRemarksSummary() {
                var total = _.size(this.deliveryRemarks)
                var selectedEls = _.filter(this.deliveryRemarks, function (el) {
                    return !(el === false || el === null || el === '')
                })

                var selected = _.size(selectedEls)
                return selected + '/' + total
            },
            buttonSuggesting() {
                return {'btn-default': true}
            },
            ...mapGetters({
                orderState: 'dealerOrderForm/orderState',
                dealer: 'dealerOrderForm/orderDealer',
                building: 'dealerOrderForm/orderBuilding',

                deliveryCharge: 'dealerOrderForm/orderDeliveryCharge',
                taxDeliveryCharge: 'dealerOrderForm/orderTaxDeliveryCharge',
                deliveryRemarks: 'dealerOrderForm/orderDeliveryRemarks',
                orderDate: 'dealerOrderForm/orderDate',

                currentOrderCustomerExpectsDate: 'dealerOrderForm/currentOrderCustomerExpectsDate'
            })
        },
        methods: {
            ...mapActions({
                getOrderRtoTerms: 'dealerOrderForm/orderTerms/getOrderRtoTerms',
                updateOrderOrder: 'dealerOrderForm/updateOrderOrder'
            }),
            change(object) {
                this.updateOrderOrder(object)

                _.each(object, (val, key) => {
                    let $v = _.get(this.$v, key, false)
                    if ($v) $v.$touch()
                })
            },
            goToStep(direction) {
                return this.$parent.goToStep(direction)
            }
        }
    }
</script>

<style type="text/css" lang="scss" rel="stylesheet/scss" scoped>
    span.badge.notice {
        color: red;
        background: rgb(218, 218, 218);
    }

    h4.list-group-item-heading {
        margin-top: 10px;
        margin-bottom: 5px;
    }

    .datepicker {
        float: none;
    }

    .form-group {
        margin-bottom: 15px!important;
    }
</style>