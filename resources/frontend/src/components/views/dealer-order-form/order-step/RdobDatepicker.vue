<script type="text/babel">
    import {mapActions, mapGetters} from 'vuex'
    import Datepicker from 'components/ui/Datepicker.vue'

    export default {
        name: 'rdob-datepicker',
        extends: Datepicker,
        created() {
            this.$on('input', (val) => {
                this.updateOrderRenter({ renterDob: val })
            })
        },
        computed: {
            ...mapGetters({
                orderRenter: 'orderRenter'
            })
        },
        methods: {
            ...mapActions({
                updateOrderRenter: 'dealerOrderForm/updateOrderRenter'
            })
        }
    }
</script>