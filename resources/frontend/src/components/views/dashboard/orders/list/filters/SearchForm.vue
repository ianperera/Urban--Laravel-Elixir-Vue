<template>

    <div class="search-form form-horizontal">
        <ul class="list-group list-group-horizontal list-tools-form">
            <template v-for="search in currentSearches">
                <form-search-select v-if="search.id === 'status_id'"
                                    v-bind:title="'title'"
                                    v-bind:datas="orderStatuses"
                                    v-bind:item="search">
                </form-search-select>
            </template>
        </ul>
    </div>

</template>

<script type="text/babel">
    import baseSearchForm from 'src/components/views/_base/ListItems/filters/SearchForm.vue'
    import FormSearchSelect from 'src/components/views/_base/ListItems/search-forms/Select.vue'

    import apiOrders from 'src/api/orders'
    import apiReferences from 'src/api/order-references'

    export default {
        extends: baseSearchForm,
        data() {
            return {
                orderStatuses: {},
                salesPersonNamesLoading: false,
                customerNamesLoading: false
            }
        },
        components: {
            FormSearchSelect
        },
        methods: {
            syncSearches() {},
            fetchData() {
                const datas = [
                    apiOrders.statuses()
                ]

                return Promise.all(datas)
                    .then(response => {
                        this.orderStatuses = response[0].data
                        this.$emit('data-ready')
                        return response
                    })
                    .catch(response => {
                        this.$emit('data-failed', response)
                    })
            },
            fetchSalesPerson(searchKeyword) {
                this.salesPersonNamesLoading = true
                let query = {
                    per_page: 99999
                }
                let searchQuery = '%' + searchKeyword + '%'
                _.set(query, ['where', 'sales_person', 'like'], searchQuery)
                const namesData = apiOrders.get({
                    query
                })

                return Promise.resolve(namesData)
                    .then(response => {
                        let salesPersonNamesStructured = []
                        const dataNotStructured = response.body.data
                        for (let item of dataNotStructured) {
                            if (_.findIndex(salesPersonNamesStructured, { 'name': item.salesPerson }) < 0) {
                                salesPersonNamesStructured.push({name: item.salesPerson})
                            }
                        }
                        this.salesPersonNamesLoading = false
                        this.salesPersonNames = salesPersonNamesStructured
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