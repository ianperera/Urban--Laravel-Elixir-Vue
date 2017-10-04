<template>

    <div>
        <div class="panel-heading">
            <h2 class="panel-title">Inventory Forms</h2>
        </div>

        <div class="panel-body overlayable">
            <div class="form-container">
                <data-process :process="dataProcess" :with_loader="true"></data-process>

                    <div class="row">
                        <div class="col-md-12 col-xs-12">
                            <div class="form-group">
                                <label for="inventory-form__serial-number">Serial Number</label>
                                <input type="text"
                                       class="form-control"
                                       id="inventory-form__serial-number"
                                       placeholder="Serial Number"
                                       v-model="serialNumber">
                                <div v-if="$v.serialNumber.$dirty && $v.serialNumber.required === false" class="alert alert-danger" role="alert">This field is required.</div>
                            </div>
                        </div>
                        <div class="text-center" v-if="item && item.id !== null">
                            <a @click="openDocument('inventory')" target="_blank" class="btn btn-danger btn-lg">
                                <i class="fa fa-file-pdf-o" aria-hidden="true"></i> Download
                            </a>
                        </div>

                    </div>

            </div>
        </div>

        <div class="panel-footer" style="text-align: center">
            <button type="button" class="btn btn-default" @click="close">Close</button>
            <button type="button" class="btn btn-primary" @click="search" :disabled="dataProcess.running">Search</button>
        </div>
    </div>

</template>

<script type="text/babel">
    import baseDataItem from 'src/components/views/_base/Block/DataItem.vue'
    import {mapActions, mapGetters} from 'vuex'
    import {required} from 'vuelidate/lib/validators'

    export default {
        extends: baseDataItem,
        data() {
            return {
                serialNumber: null,
                item: null,
                dataProcess: {
                    type: 'form',
                    running: false
                }
            }
        },
        computed: {
            ...mapGetters({
                getUiToolsStateInventoryForm: 'dealerOrderForm/uiTools/getUiToolsStateInventoryForm',
                orderDealerID: 'dealerOrderForm/orderDealerID'
            })
        },
        methods: {
            ...mapActions({
                uiToolsSetStateInventoryForm: 'dealerOrderForm/uiTools/uiToolsSetStateInventoryForm',
                searchBuildingBySerial: 'dealerOrderForm/searchBySerial'
            }),
            openDocument(document) {
                if (document === 'inventory' && this.orderDealerID) {
                    let url = '/buildings/' + this.item.id + '/inventory-form?dealer_id=' + this.orderDealerID
                    window.open(url, '_blank')
                }
            },
            close() {
                this.item = null
                this.uiToolsSetStateInventoryForm({'show': false})
            },
            search() {
                this.$v.$touch()
                if (this.$v.$error) return

                let self = this
                this.searchBuildingBySerial({
                    params: {
                        query: this.serialNumber,
                        dealer: this.orderDealerID,
                        condition: '=',
                        loc: 0
                    },
                    beforeCb() {
                        self.item = null
                        self.run({text: 'Searching..', type: 'form'})
                    },
                    successCb(response) {
                        self.item = response.data[0]
                        self.$emit('data-process-update', {running: false})
                    },
                    errorCb(response, msg) {
                        self.$emit('data-failed', response)
                    }
                })
            }
        },
        validations: {
            serialNumber: {required}
        }
    }
</script>

<style type="text/css" lang="scss" rel="stylesheet/scss">

</style>