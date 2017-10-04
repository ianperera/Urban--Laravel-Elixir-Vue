<template>

        <div class="col-xs-12 plr-none">

                <div class="row">
                    <div class="col-md-7">
                        <div class="list-group">
                            <div class="list-group-item item-heading">
                                <div class="col-xs-2 plr-none text-left">
                                    <button class="btn btn-default"
                                            v-on:click.prevent="goToStep('previous')"><i class="fa fa-arrow-left fa-fw"></i>Previous
                                    </button>
                                </div>
                                <div class="col-xs-8 plr-none text-center">
                                    <h4>Building Options</h4>
                                </div>
                                <div class="col-xs-2 plr-none text-right">
                                    <button class="btn"
                                            v-bind:class="buttonSuggesting()"
                                            v-on:click.prevent="goToStep('next')">Next<i class="fa fa-arrow-right fa-fw"></i>
                                    </button>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="list-group-item sub-step" v-if="currentStep == 'building-condition'">

                                <!-- [Building Condition] -->

                                <h4 class="list-group-item-heading">Please select the Building Condition:</h4>
                                <div class="btn-group" data-toggle="buttons">
                                    <label :class="{'active': buildingCondition == 'new'}" class="btn btn-default">
                                        <input type="radio"
                                               name="buildingCondition"
                                               :value="'new'"
                                               v-on:click="change({'buildingCondition': $event.target.value}, 'sale-type')"
                                               :checked="buildingCondition == 'new'">New
                                    </label>
                                    <label :class="{'active': buildingCondition == 'used'}" class="btn btn-default">
                                        <input type="radio"
                                               name="buildingCondition"
                                               :value="'used'"
                                               v-on:click="change({'buildingCondition': $event.target.value}, 'dealer-inventory')"
                                               :checked="buildingCondition == 'used'">Used
                                    </label>
                                </div>
                                <div v-if="$v.buildingCondition.$dirty && !$v.buildingCondition.required" class="alert alert-danger" role="alert">Building Condition is required.</div>

                                <!-- [/Building Condition] -->

                            </div>
                            <div class="list-group-item sub-step" v-if="currentStep == 'sale-type'">

                                <!-- [Sale Type] -->

                                <h4 class="list-group-item-heading">Please choose sale type:</h4>
                                <div class="btn-group btn-group-lg1" data-toggle="buttons">
                                    <label :class="{'active': saleType == 'dealer-inventory'}" class="btn btn-default">
                                        <input type="radio"
                                               name="saleType"
                                               :value="'dealer-inventory'"
                                               v-on:click="change({'saleType': $event.target.value}, 'dealer-inventory')"
                                               :checked="saleType == 'dealer-inventory'"><i class="fa fa-home"></i> Dealer Inventory
                                    </label>
                                    <label :class="{'active': saleType == 'custom-order'}" class="btn btn-default">
                                        <input type="radio"
                                               name="saleType"
                                               :value="'custom-order'"
                                               v-on:click="change({'saleType': $event.target.value})"><i class="fa fa-cogs"></i> Custom Order
                                    </label>
                                </div>
                                <div class="row" v-if="currentStep == 'sale-type' && saleType == 'custom-order'" style="margin-top: 1em">
                                    <building-package-categories v-bind:building-packages="buildingPackages"
                                                                 v-on:select-building-package="selectBuildingPackage"
                                                                 v-on:go-to-step="goToStep"
                                                                 v-bind:placeholder="true">
                                    </building-package-categories>
                                </div>
                                <div v-if="$v.saleType.$dirty && !$v.saleType.required" class="alert alert-danger" role="alert">The type of sale must be indicated.</div>

                                <!-- [/Sale Type] -->

                            </div>
                            <div class="list-group-item sub-step" v-if="currentStep == 'dealer-inventory'">

                                <!-- [Dealer-Inventory] -->

                                <h4 class="list-group-item-heading">Please enter the Building Serial Number:</h4>
                                <div class="form-group has-feedback">
                                    <serial-typeahead ref="serialTypeahead"></serial-typeahead>
                                </div>

                                <button class="btn btn-default" @click.prevent="serialLookup()">Apply</button>
                                <button class="btn btn-default" @click.prevent="goToStep('next')" v-show="inventoryBuilding.options.length > 0">Next<i class="fa fa-arrow-right fa-fw"></i></button>
                                <hr>
                                <strong>Building details:</strong>
                                <table class="table table-condensed" v-show="inventoryBuilding.options.length > 0">
                                    <thead>
                                        <tr class="option-item">
                                            <th><strong>Option</strong> </th>
                                            <th><strong>Quantity</strong> </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="option-item" v-for="option in inventoryBuilding.options">
                                            <td>{{ option.name }}</td>
                                            <td>{{ option.quantity ? option.quantity : ''  }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <button class="btn btn-default" @click.prevent="goToStep('next')" v-show="inventoryBuilding.options.length > 0">Next<i class="fa fa-arrow-right fa-fw"></i></button>

                                <!-- [/Dealer-Inventory] -->

                            </div>
                            <div class="list-group-item sub-step" v-if="currentStep == 'building-style'">

                                <!-- [/Building Style] -->

                                <h4 class="list-group-item-heading">Please select building style:</h4>
                                <div class="btn-group btn-group-lg1" data-toggle="buttons">
                                    <label v-for="style in buildingStyles" :class="{'active':buildingStyle && buildingStyle.id == style.id}" class="btn btn-default">
                                        <input type="radio"
                                               name="buildingStyle"
                                               :value="style.id"
                                               v-on:click="change({'buildingStyle': style}, 'building-dimension')"
                                               :checked="buildingStyle && buildingStyle.id === style.id">
                                        <img class="img-responsive1" v-bind:src="style.iconPath" width="60" height="60" v-bind:alt="style.name">
                                        {{ style.name }}
                                    </label>
                                </div>
                                <div v-if="$v.buildingStyle.$dirty && !$v.buildingStyle.required" class="alert alert-danger" role="alert">Building style is required.</div>

                                <!-- [/Building Style] -->

                            </div>
                            <div class="list-group-item sub-step" v-if="currentStep == 'building-dimension'">

                                <!-- [/Building Dimension] -->

                                <h4 class="list-group-item-heading">Please choose building dimension:</h4>
                                <div class="btn-group" data-toggle="buttons" v-if="buildingStyle">
                                    <label v-for="dimension in buildingStyle.buildingModels"
                                           :class="{'active': buildingDimension && buildingDimension.id == dimension.id}"
                                           class="btn btn-default">
                                        <input type="radio"
                                               name="buildingDimension"
                                               :value="dimension.id"
                                               v-on:click="change({'buildingDimension': dimension}, 'custom-build')"
                                               :checked="buildingDimension && buildingDimension.id == dimension.id">
                                        {{ dimension.width }}x{{ dimension.length }}
                                    </label>
                                </div>
                                <div v-if="$v.buildingDimension.$dirty && !$v.buildingDimension.required" class="alert alert-danger" role="alert">Building dimension is required.</div>

                                <!-- [/Building Dimension] -->

                            </div>
                            <div class="list-group-item sub-step" v-if="currentStep == 'custom-build'">

                                <!-- [/Building Options] -->

                                <h5>Selected Options:</h5>

                                <custom-build-options-modal ref="cboModal"></custom-build-options-modal>
                                <option-viewer ref="optionViewer"
                                               v-bind:validation="getValidation()"
                                               v-bind:total-options="totalCustomOptions"
                                               v-bind:building-options="customBuildOptions">
                                </option-viewer>

                                <div v-if="$v.currentRequiredCategories.$dirty && !$v.currentRequiredCategories.maxLength"
                                     v-for="(optionCategory, i) in currentRequiredCategories"
                                     v-bind:key="optionCategory.name">
                                    <div class="alert alert-danger"
                                         role="alert"
                                         style="margin-bottom: 0.5em">
                                        Custom builds require a minimum of one {{ optionCategory.name }} option.
                                    </div>
                                </div>

                                <p class="text-right">
                                    <button type="button"
                                            v-on:click.prevent="openCustomBuildOptionsModal"
                                            class="btn btn-danger">
                                        Edit Building Options
                                    </button>
                                    <button type="button"
                                            :class="buttonSuggesting()"
                                            v-on:click.prevent="goToStep('next')"
                                            class="btn btn-default">
                                        Next<i class="fa fa-arrow-right fa-fw"></i>
                                    </button>
                                </p>

                                <!-- [/Building Options] -->

                            </div>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <h4>Building Summary</h4>
                        <p v-if="saleType == null">Start the process by choosing the sale type on your left.</p>
                        <div class="list-group" ref="buildingSummary">

                            <expense-details ref="expenseDetails"></expense-details>

                            <!-- Building Condition -->
                            <a href="" :class="{'active': currentStep === 'building-condition'}" class="list-group-item" v-on:click.prevent="goToStep('building-condition')">
                                Condition <small class="hidden-xs">(click to change)</small>

                                <span class="pull-right" v-if="$v.buildingCondition.$error">&nbsp;<i class="fa fa-exclamation-circle invalid" aria-hidden="true"></i></span>
                                <span class="badge" v-if="buildingCondition && buildingCondition !== null ">{{ buildingCondition }}</span>
                            </a>

                            <!-- Sale Type -->
                            <a href=""
                               :class="{'active': currentStep === 'sale-type'}"
                               class="list-group-item" v-on:click.prevent="goToStep('sale-type')" v-if=" buildingCondition === 'new' ">
                                Sale Type <small class="hidden-xs">(click to change)</small>

                                <span class="pull-right" v-if="$v.saleType.$error">&nbsp;<i class="fa fa-exclamation-circle invalid" aria-hidden="true"></i></span>
                                <span class="badge" v-if="saleType && saleType != null">{{ currentSaleType }}</span>
                                <span class="badge notice" v-if="buildingPackage">{{ buildingPackage.name }}</span>
                            </a>

                            <!-- Dealer Inventory -->
                            <a href=""
                               :class="{'active': currentStep === 'dealer-inventory'}"
                               class="list-group-item" v-on:click.prevent="goToStep('dealer-inventory')" v-if=" saleType === 'dealer-inventory' || buildingCondition === 'used' ">
                                Building <small class="hidden-xs">(click to change)</small>

                                <span class="pull-right" v-if="$v.inventoryBuilding.$error">&nbsp;<i class="fa fa-exclamation-circle invalid" aria-hidden="true"></i></span>
                                <span class="badge" v-if="inventoryBuilding && inventoryBuilding.serial !== null">{{ inventoryBuilding.serial }}</span>
                            </a>

                            <!-- Building Style -->
                            <a href="" :class="{'active': currentStep === 'building-style'}" class="list-group-item" v-on:click.prevent="goToStep('building-style')" v-if=" saleType === 'custom-order' ">
                                Building Style <small class="hidden-xs">(click to change)</small>

                                <span class="pull-right" v-if="$v.buildingStyle.$error">&nbsp;<i class="fa fa-exclamation-circle invalid" aria-hidden="true"></i></span>
                                <span class="badge" v-if="buildingStyle && buildingStyle.name !== null">{{ buildingStyle.name }}</span>
                            </a>

                            <!-- Building Dimensions -->
                            <a href="" :class="{'active': currentStep === 'building-dimension'}" class="list-group-item" v-on:click.prevent="goToStep('building-dimension')" v-if=" saleType === 'custom-order' && buildingStyle !== null ">
                                Building Dimension <small class="hidden-xs">(click to change)</small>

                                <span class="pull-right" v-if="$v.buildingDimension.$error">&nbsp;<i class="fa fa-exclamation-circle invalid" aria-hidden="true"></i></span>
                                <span class="badge" v-if="buildingDimension && buildingDimension.id !== null">{{ buildingDimension.width }}x{{ buildingDimension.length }}</span>
                            </a>

                            <!-- Custom Build Options -->
                            <a href="" :class="{'active': currentStep === 'custom-build'}" class="list-group-item" v-on:click.prevent="goToStep('custom-build')" v-if=" saleType === 'custom-order' && buildingStyle !== null && buildingDimension !== null ">
                                Custom Options <small class="hidden-xs">(click to change)</small>

                                <span class="pull-right" v-if="!$validate('custom-build-options')">&nbsp;<i class="fa fa-exclamation-circle invalid" aria-hidden="true"></i></span>
                                <span class="badge" v-if="customBuildOptions.length > 0">{{ customBuildOptions.length }} option(s) selected</span>
                            </a>
                        </div>
                    </div>
                </div>

        </div>

</template>

<script type="text/babel">
    /*global swal*/
    import vuelidateAnyerror from 'src/mixins/vuelidate/anyerror'
    import subSteps from 'src/mixins/sub-steps'
    import buildingStepValidation from 'src/validations/dealer-order-form/building-step.validation.js'
    import customBuildOptionsValidation from 'src/validations/dealer-order-form/custom-build-options.js'
    import manageOptionPickerDataMixin from './building-step/option-picker/manage-data-mixin-vue.js'

    import SerialTypeahead from './building-step/SerialTypeahead.vue'

    import OptionViewer from 'src/components/views/partials/OptionViewer/OptionViewer.vue'
    import CustomBuildOptionsModal from './building-step/CustomBuildOptionsModal.vue'

    import ExpenseDetails from './ExpenseDetails.vue'
    import BuildingPackageCategories from 'src/components/views/partials/building-packages/BuildingPackageCategories.vue'
    import {mapActions, mapGetters} from 'vuex'

    export default {
        name: 'building-step',
        mixins: [vuelidateAnyerror, subSteps, manageOptionPickerDataMixin],
        validations: buildingStepValidation,
        components: {
            SerialTypeahead,
            OptionViewer,
            CustomBuildOptionsModal,
            ExpenseDetails,
            BuildingPackageCategories
        },
        data() {
            return {
                alias: {
                    buildingOptions: 'customBuildOptions'
                },
                syncBuildingPackage: false,
                currentStep: 'building-condition'
            }
        },
        created() {
            let self = this
            this.receiveOptions({
                query: {
                    role: 'dealer',
                    per_page: 9999,
                    where: {
                        is_active: 'yes'
                    },
                    include: {
                        category: true,
                        files: true,
                        allowable_models: {
                            where: {
                                is_active: 'yes'
                            },
                            fields: ['id']
                        },
                        allowable_colors: true
                    }
                },
                onSuccess() {
                    self.$bus.$emit('enable-form')
                }
            })
            this.receiveOptionCategories({
                query: { role: 'dealer', perPage: 9999 },
                onSuccess() { self.$bus.$emit('enable-form') }
            })
            this.receiveBuildingPackages({
                query: {
                    per_page: 9999,
                    where: {
                        is_active: 'yes'
                    },
                    include: {
                        'building_model.style': {
                            fields: ['id', 'name', 'icon_path', 'description']
                        },
                        'category': {
                            fields: ['id', 'name', 'description']
                        },
                        'category.files': true,
                        'options.option.allowable_colors': true,
                        'options.option.allowable_models': {
                            fields: ['id', 'shell_price', 'name', 'description']
                        },
                        'files': true
                    }
                },
                onSuccess() {
                    self.$bus.$emit('enable-form')
                }
            })
            this.receiveStyles({
                query: {
                    per_page: 9999,
                    where: {
                        is_active: 'yes'
                    },
                    include: {
                        building_models: {
                            where: {
                                is_active: 'yes'
                            },
                            order_by: ['width asc', 'length asc']
                        }
                    }
                },
                onSuccess() { self.$bus.$emit('enable-form') }
            })

            this.$watch('$anyerror', (value) => {
                this.updateOrderValidation({'building': !value})
            })
        },
        watch: {
            buildingStyle(buildingStyle, oldBuildingStyle) {
                if (this.dataSync === 'running') return false
                if (this.syncBuildingPackage) return false
                if (buildingStyle === oldBuildingStyle) return false

                this.updateOrderBuilding({
                    buildingDimension: null,
                    buildingPackage: null
                })
            },
            buildingDimension(buildingDimension, oldBuildingDimension) {
                if (this.dataSync === 'running') return false
                if (this.syncBuildingPackage) return false
                if (buildingDimension === oldBuildingDimension) return false

                this.updateOrderBuilding({
                    buildingPackage: null,
                    customBuildOptions: []
                })
            },
            buildingCondition: function(buildingCondition, oldBuildingCondition) {
                if (this.dataSync === 'running') return false
                if (buildingCondition === oldBuildingCondition) return false

                if (buildingCondition === 'used') {
                    this.updateOrderBuilding({
                        saleType: 'dealer-inventory',
                        buildingPackage: null
                    })
                }
            },
            saleType: function(saleType, oldSaleType) {
                if (this.dataSync === 'running') return false
                if (saleType === oldSaleType) return false

                if (saleType) {
                    if (oldSaleType === 'custom-order' && saleType === 'dealer-inventory') {
                        this.updateOrderBuilding({
                            buildingStyle: null,
                            buildingDimension: null,
                            buildingPackage: null,
                            customBuildOptions: []
                        })
                    }

                    if (oldSaleType === 'dealer-inventory' && saleType === 'custom-order') {
                        this.updateOrderBuilding({
                            serial: null,
                            price: 0,
                            securityDeposit: 0,
                            options: []
                        })
                    }

                    // recalculate estimated delivery period based on sale type
                    this.calculateCed()
                }
            }
        },
        computed: {
            ...mapGetters({
                // main
                orderState: 'dealerOrderForm/orderState',
                selectedDealer: 'dealerOrderForm/orderDealerID',
                // submap
                buildingPackage: 'dealerOrderForm/orderBuildingPackage',
                buildingCondition: 'dealerOrderForm/orderBuildingCondition',
                buildingStyle: 'dealerOrderForm/orderBuildingStyle',
                buildingDimension: 'dealerOrderForm/orderBuildingDimension',
                saleType: 'dealerOrderForm/orderSaleType',
                serial: 'dealerOrderForm/orderSerial',
                inventoryBuilding: 'dealerOrderForm/orderInventoryBuilding',
                customBuildOptions: 'dealerOrderForm/orderCustomBuildOptions',
                totalCustomOptions: 'dealerOrderForm/totalCustomOptions',

                currentSerialNumber: 'dealerOrderForm/currentOrderSerialNumber',
                currentSaleType: 'dealerOrderForm/currentOrderSaleType',
                currentRequiredCategories: 'dealerOrderForm/currentRequiredCategories',
                // modules
                options: 'dealerOrderForm/options/list',
                buildingStyles: 'dealerOrderForm/styles/list',
                buildingPackages: 'dealerOrderForm/buildingPackages/list'
            }),
            dataSyncStatus() {
                return this.orderState.sync.status
            },
            dataSync() {
                return this.orderState.sync.merging
            }
        },
        methods: {
            ...mapActions({
                // main
                calculateCed: 'dealerOrderForm/computeCed',
                addOrderBuildingCustomOption: 'dealerOrderForm/addOrderBuildingCustomOption',
                removeOrderBuildingCustomOption: 'dealerOrderForm/removeOrderBuildingCustomOption',
                increaseOrderBuildingCustomOption: 'dealerOrderForm/increaseOrderBuildingCustomOption',
                decreaseOrderBuildingCustomOption: 'dealerOrderForm/decreaseOrderBuildingCustomOption',
                updateOrderBuildingCustomOption: 'dealerOrderForm/updateOrderBuildingCustomOption',
                updateOrderBuilding: 'dealerOrderForm/updateOrderBuilding',
                updateOrderValidation: 'dealerOrderForm/updateOrderValidation',
                // modules
                receiveStyles: 'dealerOrderForm/styles/receiveList',
                receiveOptions: 'dealerOrderForm/options/receiveList',
                receiveOptionCategories: 'dealerOrderForm/options/receiveCategories',
                receiveBuildingPackages: 'dealerOrderForm/buildingPackages/receiveList'
            }),
            openCustomBuildOptionsModal() {
                this.$refs.cboModal.$emit('open-modal')
            },
            getValidation() {
                return customBuildOptionsValidation
            },
            buttonSuggesting() {
                let currentStep = _.camelCase(this.currentStep)
                if (this[currentStep] !== null) return { 'btn-warning': true }
                return { 'btn-default': true }
            },
            // better to move this to component?
            selectBuildingPackage(buildingPackage) {
                let self = this
                if (_.isEmpty(buildingPackage)) {
                    self.updateOrderBuilding({'buildingPackage': buildingPackage})
                    return
                }

                this.syncBuildingPackage = true
                this.$nextTick(function () {
                    new Promise(function (resolve) {
                        let buildingStyle = _.find(self.buildingStyles, { id: buildingPackage.buildingModel.style.id })
                        self.updateOrderBuilding({
                            'buildingPackage': buildingPackage,
                            'buildingStyle': buildingStyle,
                            'buildingDimension': buildingPackage.buildingModel,
                            'customBuildOptions': []
                        })

                        resolve()
                    }).then(function () {
                        _.each(buildingPackage.options, function(buildingPackageOption) {
                            self.addOption(buildingPackageOption.option, {
                                quantity: buildingPackageOption.quantity,
                                defaultColor: _.find(buildingPackageOption.option.allowableColors, {id: _.parseInt(buildingPackageOption.option.defaultColorId)}),
                                color: _.find(buildingPackageOption.option.allowableColors, {id: _.parseInt(buildingPackageOption.option.defaultColorId)})
                            })
                            /* self.$refs.cboModal.$refs.optionManager.$emit('option-manager-add-option', buildingPackageOption.option, {
                                quantity: buildingPackageOption.quantity
                            })*/
                        })
                    }).then(function () {
                        self.syncBuildingPackage = false
                        self.goToStep('custom-build')
                    })
                })
            },
            serialLookup: function() {
                let vm = this
                $.getJSON('/api/dealer-inventory/' + this.selectedDealer + '/' + this.serial, function(data) {
                    if (data.status === 'error') {
                        swal('Error', data.message, 'error')
                    }
                    if (data.status === 'success') {
                        vm.updateOrderBuilding({ inventoryBuilding: data.payload })
                        vm.$validate('dealer-inventory', { $touch: true })
                    }
                })
            },
            nextStep(direction, currentStep) {
                currentStep = currentStep || this.currentStep
                let nextStep

                if (direction === 'next') {
                    if (currentStep === 'building-condition' && this.buildingCondition === 'new') nextStep = 'sale-type'
                    if (currentStep === 'building-condition' && this.buildingCondition === 'used') nextStep = 'dealer-inventory'
                    if (currentStep === 'dealer-inventory') nextStep = null
                    if (currentStep === 'sale-type' && this.saleType === 'dealer-inventory') nextStep = 'dealer-inventory'
                    if (currentStep === 'sale-type' && this.saleType === 'custom-order') nextStep = 'building-style'
                    if (currentStep === 'building-style') nextStep = 'building-dimension'
                    if (currentStep === 'building-dimension') nextStep = 'custom-build'
                    if (currentStep === 'custom-build') nextStep = null
                }

                if (direction === 'previous') {
                    if (currentStep === 'custom-build') nextStep = 'building-dimension'
                    if (currentStep === 'building-dimension') nextStep = 'building-style'
                    if (currentStep === 'building-style') nextStep = 'sale-type'

                    if (currentStep === 'dealer-inventory' && this.buildingCondition === 'used') nextStep = 'building-condition'
                    if (currentStep === 'dealer-inventory' && this.buildingCondition === 'new') nextStep = 'sale-type'

                    if (currentStep === 'sale-type') nextStep = 'building-condition'
                }

                return nextStep || null
            },
            // run/check values from batch of $validator fields/or sub-components
            $validate(steps, options = {}) {
                if (_.isString(steps) && !_.isArray(steps)) steps = [steps]

                let allSteps = {
                    'sale-type': 'saleType',
                    'building-condition': 'buildingCondition',
                    'building-style': 'buildingStyle',
                    'building-dimension': 'buildingDimension',
                    'dealer-inventory': 'inventoryBuilding'
                }

                steps = steps || _.keys(allSteps)
                let validSteps = _.filter(steps, step => {
                    if (step === 'custom-build') {
                        if (options.$reset) this.$v.currentRequiredCategories.$reset()
                        if (options.$touch) this.$v.currentRequiredCategories.$touch()
                        return !this.$v.currentRequiredCategories.$error
                    }

                    if (!_.isUndefined(this.$v[allSteps[step]])) {
                        if (options.$reset) this.$v[allSteps[step]].$reset()
                        if (options.$touch) this.$v[allSteps[step]].$touch()
                        return !this.$v[allSteps[step]].$error
                    }
                    return true
                })
                return (_.isEqual(validSteps, steps))
            },
            change(object, nextStep) {
                this.updateOrderBuilding(object)
                if (nextStep) {
                    this.$nextTick(() => {
                        this.goToStep(nextStep)
                    })
                }
            }
        }
    }
</script>

<style type="text/css">

</style>

<style type="text/css" lang="scss" rel="stylesheet/scss" scoped>
    span.badge.notice {
        color: red;
        background: rgb(218, 218, 218);
    }
    .option-item {
        background-color: #fff;
    }
    .option-item td{
        padding: 8px;
    }
</style>