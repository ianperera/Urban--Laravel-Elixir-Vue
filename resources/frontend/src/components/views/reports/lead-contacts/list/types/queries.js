export default {
    searches: {
        customer_id(search) {
            let query = {}
            let models = search.value.split(',')
            _.set(query, ['where', 'customer.id', 'inq'], models)
            return query
        }
    }
}
