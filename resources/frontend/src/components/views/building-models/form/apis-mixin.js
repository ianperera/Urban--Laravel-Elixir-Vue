import apiBuildingModels from 'src/api/building-models'
import apiBuildingStatuses from 'src/api/building-statuses'
import apiOptions from 'src/api/options'
import apiStyles from 'src/api/styles'

const receiveActiveFlags = function() {
    return apiBuildingModels.getActiveFlags({})
}

const receiveBuildingStatuses = function() {
    return apiBuildingStatuses.get({
        query: {
            per_page: 9999,
            where: {
                is_active: 'yes'
            },
            order: 'priority asc'
        }
    })
}

const receiveStyles = function() {
    return apiStyles.get({
        query: { per_page: 9999 }
    })
}

const receiveOptions = function() {
    return apiOptions.get({
        query: {
            per_page: 9999,
            include: ['category']
        }
    })
}

export default {
    methods: {
        receiveActiveFlags,
        receiveBuildingStatuses,
        receiveStyles,
        receiveOptions
    }
}