<script type="text/babel">
    import {mapActions, mapGetters} from 'vuex'
    import Datepicker from 'components/ui/Datepicker.vue'

    export default {
        name: 'codob-datepicker',
        extends: Datepicker,
        created() {
            this.$on('input', (val) => {
                this.updateOrderRenter({ coRenterDob: val })
            })
        },
        computed: {
            ...mapGetters({
                orderRenter: 'dealerOrderForm/orderRenter'
            })
        },
        methods: {
            ...mapActions({
                updateOrderRenter: 'dealerOrderForm/updateOrderRenter'
            })
        }
    }
</script>