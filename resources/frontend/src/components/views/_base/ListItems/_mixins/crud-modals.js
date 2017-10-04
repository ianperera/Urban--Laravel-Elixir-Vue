export default {
    data() {
        return {
            modal: null
        }
    },
    created() {
        this.$on('change-entry', this.changeEntry)
    },
    methods: {
        changeEntry(modal) {
            this.modal = modal
        },
        openModalCreate() {
            this.$emit('change-entry', {
                item: {},
                mode: 'create'
            })
        },
        closeModalCreate() {
            this.$emit('change-entry', null)
        },
        openModalImport() {
            this.$emit('change-entry', {
                item: {},
                mode: 'import'
            })
        },
        closeModalImport() {
            this.$emit('change-entry', null)
        },
        closeModalUpdate() {
            this.$emit('change-entry', null)
        },
        closeModalDelete() {
            this.$emit('change-entry', null)
        },
        closeModalPriceList() {
            this.$emit('change-entry', null)
        },
        closeModalPublish() {
            this.$emit('change-entry', null)
        }
    }
}
