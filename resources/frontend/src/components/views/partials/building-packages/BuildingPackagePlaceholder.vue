<template>

    <div class="item col-xs-4 col-md-3">
        <div class="thumbnail">
            <button class="btn btn-default start-from-scratch-button" v-on:click="nextStep">
                <i class="fa fa-cogs fa-2x"></i>
                <h4>
                    Start from scratch
                </h4>
            </button>
        </div>
    </div>

</template>

<script type="text/babel">
    export default {
        data() {
            return {
            }
        },
        components: {
        },
        computed: {
        },
        methods: {
            nextStep() {
                this.$emit('next-step')
            }
        }
    }
</script>

<style type="text/css" lang="scss" rel="stylesheet/scss" scoped>
    .item {
        padding: 0.3em 5px 0 5px;
    }

    .start-from-scratch-button {
        height: 100%;
        width: 100%;
        border-radius: 0px;
        white-space: normal;
    }

    div.thumbnail {
        text-align: center;
        margin-bottom: 0px;
        // padding: 0;
        padding: 3px;
        -webkit-border-radius: 0px;
        -moz-border-radius: 0px;
        border-radius: 0px;

        /* trying to set the same height for all elements */
        height: 200px;
        max-width: 200px;
    }
</style>