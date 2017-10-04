<script type="text/babel">
    import baseListItem from './ListItem.vue'

    export default {
        extends: baseListItem,
        data() {
            return {}
        },
        components: {},
        methods: {
            openUpdateModal() {
                this.$parent.$parent.$emit('change-entry', {
                    item: _.cloneDeep(this.item),
                    mode: 'edit'
                })
            },
            openPublishModal() {
                this.$parent.$parent.$emit('change-entry', {
                    item: _.cloneDeep(this.item),
                    mode: 'publish'
                })
            },
            openPriceListModal() {
                this.$parent.$parent.$emit('change-entry', {
                    item: _.cloneDeep(this.item),
                    mode: 'price_list'
                })
            },
            openDeleteModal() {
                this.$parent.$parent.$emit('change-entry', {
                    item: _.cloneDeep(this.item),
                    mode: 'delete'
                })
            },
            openDuplicateModal() {
                let item = _.cloneDeep(this.item)
                this.$parent.$parent.$emit('change-entry', {
                    item: item,
                    mode: 'duplicate'
                })
            },
            openReturnModal() {
                let item = _.cloneDeep(this.item)
                this.$parent.$parent.$emit('change-entry', {
                    item: item,
                    mode: 'return'
                })
            }
        }
    }
</script>

<style type="text/css" lang="scss" rel="stylesheet/scss">
</style>