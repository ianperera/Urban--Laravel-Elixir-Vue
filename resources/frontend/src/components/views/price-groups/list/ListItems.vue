<script type="text/babel">
    // base
    import baseListItemsCrud from 'src/components/views/_base/ListItems/ListItemsCrud.vue'
    // related
    import Filters from './Filters.vue'
    import List from './table/List.vue'
    import types from './types'
    import queries from './types/queries'
    import apiPriceGroups from 'src/api/price-groups'
    import snakeCaseObjectKeys from 'src/helpers/snake-case-converter'

    // modals
    import ModalCreate from '../modals/ModalCreate.vue'
    import ModalUpdate from '../modals/ModalUpdate.vue'
    import ModalDelete from '../modals/ModalDelete.vue'
    import ModalImport from '../modals/ModalImport.vue'
    import ModalPriceList from '../modals/ModalPriceList.vue'
    import ModalPublish from '../modals/ModalPublish.vue'

    export default {
        extends: baseListItemsCrud,
        components: {
            List,
            Filters,
            ModalCreate,
            ModalUpdate,
            ModalDelete,
            ModalImport,
            ModalPriceList,
            ModalPublish
        },
        data() {
            return {
                entity: 'Price Group',
                types: types,
                type: _.cloneDeep(types.def),
                exportUrl: '',
                apiPath: 'price-group',
                modal: null
            }
        },
        methods: {
            apiGet(query) {
                let currentQuery = this.$url(apiPriceGroups.actions.get.url, snakeCaseObjectKeys(query))
                this.exportUrl = this.getQueryParamsOnly(currentQuery)

                return apiPriceGroups.get({ query })
            },
            queries() {
                return queries
            },
            getQueryParamsOnly(currentQuery) {
                return currentQuery.substring(currentQuery.indexOf('?') + 1)
            }
        }
    }
</script>