<script type="text/babel">
    // base
    import baseListItemsCrud from 'src/components/views/_base/ListItems/ListItemsCrud.vue'
    // related
    import Filters from './Filters.vue'
    import List from './table/List.vue'
    import types from './types'
    import queries from './types/queries'
    import apiBuildings from 'src/api/buildings'
    // modals
    import ModalCreate from '../modals/ModalCreate.vue'
    import ModalUpdate from '../modals/ModalUpdate.vue'
    import ModalDelete from '../modals/ModalDelete.vue'
    import ModalDuplicate from '../modals/ModalDuplicate.vue'
    import ModalReturn from '../modals/ModalReturn.vue'

    import Presets from './Presets.vue'

    // order modals
    import ModalUpdateOrder from '../../orders/modals/ModalUpdate.vue'
    import ModalAttachments from '../../orders/modals/ModalAttachments.vue'

    // sale modals
    import ModalUpdateSale from '../../sales/modals/ModalUpdate.vue'

    export default {
        extends: baseListItemsCrud,
        components: {
            List,
            Filters,
            ModalCreate,
            ModalUpdate,
            ModalDuplicate,
            ModalReturn,
            ModalDelete,
            ModalUpdateOrder,
            ModalAttachments,
            ModalUpdateSale,
            Types: Presets
        },
        data() {
            return {
                entity: 'Building',
                types: types,
                type: _.cloneDeep(types.def),
                modal: null
            }
        },
        methods: {
            apiGet(query) {
                return apiBuildings.get({ query })
            },
            queries() {
                return queries
            },
            closeModalAttachments() {
                this.$emit('change-entry', null)
            }
        }
    }
</script>