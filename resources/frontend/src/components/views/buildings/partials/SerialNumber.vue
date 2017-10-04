<template>

    <div id="sn">
        <span class="label" :class="labelClass" v-if="finalSerialNumber">
            <a v-bind:href="'/buildings/' + id" v-if="finalSerialNumber.id !== 'xxxxxxxxx' ">
                {{ finalSerialNumber.shortCode }}-{{ finalSerialNumber.size }}-{{ finalSerialNumber.id }}
            </a>
            <span v-else>
                {{ finalSerialNumber.shortCode }}-{{ finalSerialNumber.size }}-{{ finalSerialNumber.id }}
            </span>
        </span>
        <span v-else>
            <span class="label label-default">None</span>
        </span>
        &nbsp;
        <div class="checkbox checkbox-circle checkbox-inline" v-if="finalSerialNumber">
            <input id="opt_update_sn"
                   name="opt_update_sn"
                   type="checkbox"
                   value="1"
                   v-bind:true-value="1"
                   v-bind:false-value="0"
                   v-bind:checked="serialNumber ? true : false"
                   v-model="opts.updateSerialNumber">
            <label for="opt_update_sn"></label>
        </div>
    </div>

</template>

<script type="text/babel">
    export default {
        data() {
            return {
                opts: {}
            }
        },
        props: {
            id: {
                default: null
            },
            serialNumber: {
                default: null
            },
            buildingModel: {
                default() {
                    return {}
                }
            }
        },
        computed: {
            currentParts() {
                if (this.serialNumber) {
                    let parts = _.zipObject(['shortCode', 'size', 'id'], this.serialNumber.split('-'))
                    return parts
                }
                return null
            },
            newParts() {
                if (this.buildingModel) {
                    let parts = {}
                    parts.shortCode = this.buildingModel.style.shortCode
                    parts.size = '' + _.padStart(this.buildingModel.width, 2, 0) + _.padStart(this.buildingModel.length, 2, 0) + _.padStart(this.buildingModel.wallHeight, 2, 0)

                    if (this.currentParts) {
                        parts.id = this.currentParts.id
                    } else {
                        parts.id = _.padStart('', 9, 'x')
                    }

                    return parts
                }
                return null
            },
            finalSerialNumber() {
                if (!_.isEqual(this.currentParts, this.newParts)) {
                    return this.newParts
                }
                return this.currentParts
            },
            labelClass() {
                if (!_.isEqual(this.currentParts, this.newParts)) {
                    return 'label-warning'
                }
                return 'label-success'
            }
        },
        methods: {
        }
    }
</script>

<style type="text/css" lang="scss" rel="stylesheet/scss">
    #sn {
        span {
            font-size: 100%;
        }

        a {
            font-size: 100%;
            color: white;
        }
    }
</style>