<template>

    <div>
        <data-process v-bind:process="dataProcess" v-bind:with_loader="true"></data-process>
        <form v-if="dataIsReady">
            <div class="form-group">
                <div class="row col-xs-12" v-if="curItem.createdAt">
                    <div class="col-xs-12 col-md-3">
                        <label for="date_created" class="control-label">Date Created</label>
                        <div id="date_created">
                            {{ filters.moment(curItem.createdAt, 'YYYY-MM-DD HH:mm:ss', 'MM/DD/YYYY HH:mm:ss') }}
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-3">
                        <label for="date_updated" class="control-label">Date Updated</label>
                        <div id="date_updated">
                            {{ filters.moment(curItem.updatedAt, 'YYYY-MM-DD HH:mm:ss', 'MM/DD/YYYY HH:mm:ss') }}
                        </div>
                    </div>
                </div>

                <!-- common -->
                <div class="row col-xs-12 col-sm-12" v-if="!curItem.createdAt">
                    <div class="col-xs-12 col-md-12">
                        <label for="id" class="control-label">ID</label>
                        <input id="id" placeholder="ID" type="text" class="form-control" v-model="curItem.id">
                    </div>
                </div>

                <div class="row col-xs-12 col-sm-12">
                    <div class="col-xs-12 col-md-12">
                        <label for="title" class="control-label">Title</label>
                        <input id="title" placeholder="Title" type="text" class="form-control" v-model="curItem.title">
                    </div>
                </div>

                <div class="row col-xs-12 col-sm-12">
                    <div class="col-xs-12 col-md-12">
                        <label for="description" class="control-label">Description</label>
                        <textarea id="description" placeholder="Description" type="text" class="form-control" v-model="curItem.description"></textarea>
                    </div>
                </div>

                <div class="row col-xs-12 col-sm-12">
                    <div class="col-xs-12 col-md-12">
                        <label for="value" class="control-label">Value</label>
                        <input id="value" placeholder="Value" type="text" class="form-control" v-model="curItem.value">
                    </div>
                </div>
            </div>
        </form>
    </div>

</template>

<script type="text/babel">
    import BaseDataItem from 'src/components/views/_base/Block/DataItem.vue'

    import apiSettings from 'src/api/settings'

    import objectToFormData from 'src/helpers/object-to-form-data'
    import convertKeys from 'convert-keys'

    export default {
        name: 'setting-form-item',
        extends: BaseDataItem,
        data() {
            return {
                activeFlags: {},
                curItem: {
                    id: null,
                    title: null,
                    description: null,
                    value: null
                }
            }
        },
        computed: {
            id() {
                if (!_.isUndefined(this.item.id)) {
                    return this.item.id
                }
                return null
            }
        },
        methods: {
            save({ item, data }) {
                return apiSettings.save({ item, data }).then(response => response.data)
            },
            submit() {
                let self = this
                let item = _.merge({}, {
                    id: this.curItem.id,
                    title: this.curItem.title,
                    description: this.curItem.description,
                    value: this.curItem.value,
                    createdAt: this.curItem.createdAt
                })

                if (this.curItem.id) item.id = this.curItem.id

                let form = objectToFormData(convertKeys.toSnake(item))

                this.run({text: 'Saving..', type: 'form'})
                return this.save({ item: item, data: form })
                    .then(data => {
                        self.$emit('data-process-update', {
                            running: false,
                            success: data
                        })
                        self.$emit('item-saved')
                    })
                    .catch(response => {
                        self.$emit('data-failed', response)
                    })
            },
            isAllowableModel(model) {
                let isAllowable = _.find(this.curItem.allowableModels, function(item) {
                    return item.id === model.id
                })

                return !_.isUndefined(isAllowable)
            },
            initData() {
                if (this.id) {
                    apiSettings.get({
                        id: this.id,
                        query: {}
                    })
                        .then(response => {
                            return this.initDependencies().then(() => {
                                return response
                            })
                        })
                        .then(response => {
                            let item = response.data
                            this.curItem = _.cloneDeep(item)
                        })
                        .then(() => {
                            this.$emit('data-ready')
                        })
                        .catch(response => {
                            this.$emit('data-failed', response)
                        })
                } else {
                    this.initDependencies()
                        .then(response => { this.$emit('data-ready') })
                        .catch(response => { this.$emit('data-failed', response) })
                }
            },
            initDependencies() {
                const datas = [
                    apiSettings.get({
                        query: {
                            per_page: 99999
                        }
                    })
                ]

                return Promise.all(datas)
                    .then(response => {
                        return response
                    })
            }
        }
    }
</script>

<style type="text/css" lang="scss" rel="stylesheet/scss" scoped>

</style>