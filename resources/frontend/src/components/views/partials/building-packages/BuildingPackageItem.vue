<template>

    <li class="list-group-item list-item">
        <table>
            <tr>
                <td class="list-left">
                    <popover placement="right" trigger="click" class="pointer" :auto-placement="false" :enable="image.publicPath !== null">
                        <div slot="popover">
                            <pop-img ref="popImg"
                                     :source="image"
                                     :src="image.publicPath">
                            </pop-img>
                        </div>

                        <img class="image-preview" data-role="trigger"
                             :src="image.publicPath">
                    </popover>
                </td>
                <td class="list-desc">
                    <h5>
                        <strong>{{ item.name }}</strong>
                        <span class="label label-success">{{ filters.money(item.totalPrice) }}</span>
                    </h5>
                    <h6><strong>{{ item.buildingModel.name }}</strong> <span class="label label-success">{{ filters.money(item.buildingModel.shellPrice) }}</span></h6>
                    <h6><span>{{ item.description }}</span></h6>
                </td>
                <td class="text-right">
                    <button type="button"
                            class="btn btn-sm btn-default"
                            v-on:click="select(item)">
                        <i class="fa fa-cogs" aria-hidden="true"></i> Select
                    </button>
                    <button class="btn btn-sm btn-default"
                            style="margin: 0.5em 0 0.5em 0"
                            v-on:click="toggleOptions" v-bind:class="{ 'active': showOptions }">
                        Options ({{ countOptions }}) <i class="fa fa-angle-down" aria-hidden="true"></i>
                    </button>
                </td>
            </tr>

            <tr is="building-package-options" v-if="item.options && showOptions" :options="item.options"></tr>
        </table>
    </li>

</template>

<script type="text/babel">
    import Popover from 'uiv/src/components/popover/Popover.vue'
    import PopImg from 'src/components/ui/PopImg.vue'
    import BuildingPackageOptions from './BuildingPackageOptions.vue'

    export default {
        data() {
            return {
                showOptions: false
            }
        },
        components: {
            Popover,
            PopImg,
            BuildingPackageOptions
        },
        props: {
            item: {
                default() {
                    return {}
                }
            }
        },
        computed: {
            countOptions() {
                return _.size(this.item.options)
            },
            image() {
                let image = {}
                if (_.isArray(this.item.files) && this.item.files.length > 0) {
                    image = _.cloneDeep(_.last(this.item.files))
                    return image
                }

                return {
                    publicPath: this.item.buildingModel.style.iconPath || null,
                    width: 60,
                    height: 60
                }
            }
        },
        methods: {
            select(item) {
                this.$emit('select', item)
            },
            toggleOptions() {
                this.showOptions = !(this.showOptions)
            }
        }
    }
</script>

<style type="text/css" lang="scss" rel="stylesheet/scss" scoped>

    .list-item {
        /* position: static;
        overflow: auto; */
        vertical-align: top;
        width: 100%;

        .list-left {
            padding-right: 0.5em;
            position: relative;

            .image-preview {
                height: auto;
                width: 5em;
                padding: 3px;
            }
        }
        .list-desc {
            vertical-align: top;
            width: 100%;
        }
        .list-price {
            float: right;
            margin-bottom: 0.5em;
        }
        .thumbnail {
            width: 100%;
            height: 100%;
        }
    }

</style>