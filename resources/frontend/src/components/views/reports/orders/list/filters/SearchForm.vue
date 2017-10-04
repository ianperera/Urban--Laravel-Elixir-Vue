<template>

    <div class="search-form form-horizontal">
        <ul class="list-group list-group-horizontal list-tools-form">
            <template v-for="search in currentSearches">
                <form-search-date v-if="search.id === 'date_created'"
                                  v-bind:item="search"
                                  v-bind:format="'YYYY-MM-DD HH:mm:ss'">
                </form-search-date>
                <form-search-select v-if="search.id === 'status_id'"
                                    v-bind:datas="statuses"
                                    v-bind:item="search">
                </form-search-select>
                <form-search-select v-if="search.id === 'dealer'"
                                    :label="'businessName'"
                                    v-bind:title="'businessName'"
                                    v-bind:datas="dealers"
                                    v-bind:item="search">
                </form-search-select>
                <form-search-buildings v-if="search.id === 'building_id'"
                                       v-bind:datas="buildings"
                                       v-bind:item="search">
                </form-search-buildings>
                <form-search-select v-if="search.id === 'order_type'"
                                    v-bind:datas="orderTypes"
                                    v-bind:item="search">
                </form-search-select>
                <form-search-select v-if="search.id === 'payment_type'"
                                    v-bind:datas="paymentTypes"
                                    v-bind:item="search">
                </form-search-select>
                <form-search-text v-if="search.id === 'order_id'"
                                  v-bind:item="search">
                </form-search-text>
                <form-search-date v-if="search.id === 'order_date'"
                                  v-bind:format="'YYYY-MM-DD'"
                                  v-bind:item="search">
                </form-search-date>
                <form-search-async-text v-if="search.id === 'customer'"
                                        :is-loading="customerNamesLoading"
                                        :autocomplete-values="customerNames"
                                        @fetch-autocomplete="fetchCustomers"
                                        v-bind:item="search">
                </form-search-async-text>
                <form-search-text v-if="search.id === 'retail'"
                                  v-bind:item="search">
                </form-search-text>
            </template>
        </ul>
    </div>

</template>

<script type="text/babel">
    import baseSearchForm from 'src/components/views/_base/ListItems/filters/SearchForm.vue'
    import FormSearchDate from 'src/components/views/_base/ListItems/search-forms/Date.vue'
    import FormSearchSelect from 'src/components/views/_base/ListItems/search-forms/Select.vue'
    import FormSearchBuildings from './search-forms/Buildings.vue'
    import FormSearchText from 'src/components/views/_base/ListItems/search-forms/TextInput.vue'
    import FormSearchAsyncText from 'src/components/views/_base/ListItems/search-forms/AsyncInput.vue'

    import apiOrders from 'src/api/orders'
    import apiDealers from 'src/api/dealers'
    import apiBuildings from 'src/api/buildings'
    import apiReferences from 'src/api/order-references'

    export default {
        extends: baseSearchForm,
        data() {
            return {
                statuses: {},
                dealers: [],
                buildings: [],
                orderTypes: {},
                paymentTypes: {},
                customerNames: [],
                customerNamesLoading: false
            }
        },
        components: {
            FormSearchDate,
            FormSearchSelect,
            FormSearchBuildings,
            FormSearchText,
            FormSearchAsyncText
        },
        methods: {
            syncSearches() {},
            fetchData() {
                const datas = [
                    apiOrders.statuses(),
                    apiDealers.get({
                        params: {
                            fields: ['id', 'business_name'],
                            per_page: 9999
                        }
                    }),
                    apiBuildings.get({
                        query: {
                            fields: ['id', 'serial_number'],
                            per_page: 9999,
                            order_by: ['serial_number asc']
                        }
                    }),
                    apiOrders.paymentTypes(),
                    apiOrders.orderTypes()
                ]

                return Promise.all(datas)
                    .then(response => {
                        this.statuses = response[0].data
                        this.dealers = response[1].data.data
                        this.buildings = response[2].data.data
                        this.paymentTypes = response[3].data
                        this.orderTypes = response[4].data
                        this.$emit('data-ready')
                        return response
                    })
                    .catch(response => {
                        this.$emit('data-failed', response)
                    })
            },
            fetchCustomers(searchKeyword) {
                this.customerNamesLoading = true
                let query = {
                    per_page: 99999
                }
                let searchQuery = '%' + searchKeyword + '%'
                _.set(query, ['where', 'first_name', 'like'], searchQuery)
                _.set(query, ['where', 'or', 'last_name', 'like'], searchQuery)
                const namesData = apiReferences.get({
                    query
                })

                return Promise.resolve(namesData)
                    .then(response => {
                        let customerNamesStructured = []
                        const dataNotStructured = response.body.data
                        for (let item of dataNotStructured) {
                            if (item.firstName.toLowerCase().indexOf(searchKeyword.toLowerCase()) >= 0) {
                                if (_.findIndex(customerNamesStructured, { 'name': item.firstName }) < 0) {
                                    customerNamesStructured.push({name: item.firstName})
                                }
                            }
                            if (item.lastName.toLowerCase().indexOf(searchKeyword.toLowerCase()) >= 0) {
                                if (_.findIndex(customerNamesStructured, { 'name': item.lastName }) < 0) {
                                    customerNamesStructured.push({name: item.lastName})
                                }
                            }
                        }
                        this.customerNamesLoading = false
                        this.customerNames = customerNamesStructured
                    })
                    .catch(response => {
                        this.$emit('data-failed', response)
                    })
            }
        }
    }
</script>