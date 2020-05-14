<template>
<div class="bg-gray h-100 p-3">

    <div class="card shadow-sm">

        <div class="card-header header-elements">
            <div>
                <h4 class="card-title">UTM Parameters</h4>
                <span class="font-size-sm text-gray-600">Setup commonly used parameters to help standardize ones are used across your organization.</span>
            </div>

            <div class="header-elements d-flex justify-content-between">
                <div class="btn-group btn-group-toggle" data-toggle="buttons">
                    <button v-for="item in utms" :key="item.index"
                        v-bind:class="[curUtm === item.index ? 'btn-primary' : 'btn-outline-primary']"
                        @click="handleUtms(item)"
                        type="button" class="btn"
                    >
                        {{ item.type }}
                    </button>
                </div>

                <button
                    :disabled="!$can('access admin') && subscriptionUsage.consumed == subscriptionUsage.value"
                    type="button"
                    class="btn btn-primary"
                    @click="modals.addUtm = true"
                >
                    <i class="fal fa-plus"></i>
                    New {{utms[curUtm].type}}
                </button>
            </div>
        </div>


        <div class="border-top py-1 px-3">

            <div class="w-25 ml-auto">
                <form-select placeholder="Search"
                    multiple
                    collapse-tags
                    v-model="tableParams.campaign_where"
                    @remove-tag="filterTable(false)"
                    @visible-change="e => filterTable(e)">

                    <select-option
                        v-for="item in campaigns"
                        :key="item.id"
                        :label="item.name"
                        :value="item.id">
                    </select-option>
                </form-select>
            </div>

        </div>

        <datatable ref="utmtable"
            :api-url="'utms?utm_type=' + curUtm"
            :disabled-border="true"
            :append-params="tableParams"
            :fields-data="utmFields">

            <template slot="name-slot" slot-scope="props">
                <div class="text-crop-1 line-height-1">
                    <span>{{ props.rowData.utm_name }}</span>
                </div>
            </template>

            <template slot="created-on-slot" slot-scope="props">
                <div >{{ props.rowData.created_at }}</div>
            </template>

            <template slot="created-by-slot" slot-scope="props">
                <span>{{ props.rowData.user_name }}</span>
            </template>

            <template slot="url-slot" slot-scope="props">
                <div>
                    <span class="font-weight-semibold">{{ props.rowData.url_counts }}</span>
                </div>
            </template>

            <template slot="click-slot" slot-scope="props">
                <div>
                    <span class="font-weight-semibold">{{ props.rowData.click_counts }}</span>
                </div>
            </template>

            <template slot="action-slot" slot-scope="props">
                <div class="list-icons">
                    <dropdown menu-right>
                        <a slot="heading" href="#" class="list-icons-item" v-tooltip.hover title="Link Actions"><i class="far fa-ellipsis-v"></i></a>

                        <button class="dropdown-item" type="button" @click="modals.editUtm = true, handleEditUtm(props.rowData)"><i class="fal fa-eye"></i> View </button>
                        <button class="dropdown-item" type="button" @click="modals.editUtm = true, handleEditUtm(props.rowData)"><i class="fal fa-edit"></i> Edit </button>
                        <button v-if="!props.rowData.disabled" class="dropdown-item" type="button" @click="changeStatus(props.rowData, 1)"><i class="fal fa-ban"></i> Disable </button>
                        <button v-if="props.rowData.disabled" class="dropdown-item" type="button" @click="changeStatus(props.rowData, null)"><i class="fal fa-check-circle"></i> Enable </button>
                        <button class="dropdown-item text-danger" type="button" @click="deleteUtm(props.rowData.id)"><i class="fal fa-trash"></i> Delete </button>
                    </dropdown>
                </div>
            </template>

        </datatable>

    </div>

    <add-utm-modal :open.sync="modals.addUtm" :utmType="utms[curUtm]" @done="addUtmDone"></add-utm-modal>
    <edit-utm-modal :open.sync="modals.editUtm" :utmType="utms[curUtm]" :edit-utm="editUtm" @done="addUtmDone"></edit-utm-modal>

</div>
</template>


<script>
import AddUtmModal from './AddUtmModal.vue';
import EditUtmModal from './EditUtmModal.vue';

export default {
    components: {
        AddUtmModal,
        EditUtmModal
    },

    data() {
        return {
            subscriptionUsage: window.subscriptionUsage,

            modals: {
                addUtm: false,
                editUtm: false
            },

            editUtm: {},

            campaigns: [],

            curUtm: 0,

            utms: [
                {
                    index: 0,
                    type: 'campaign'
                },
                {
                    index: 1,
                    type: 'medium'
                },
                {
                    index: 2,
                    type: 'source'
                },
                {
                    index: 3,
                    type: 'content'
                },
                {
                    index: 4,
                    type: 'term'
                }
            ],

            tableParams: {
                campaign_where: []
            },

            utmFields: [
               {
                    name: 'name-slot',
                    title: 'Name',
                    dataClass: 'w-25'
                },
                {
                    name: 'created-on-slot',
                    title: 'Created On'
                },
                {
                    name: 'created-by-slot',
                    title: 'Created By'
                },
                {
                    name: 'url-slot',
                    title: 'URLs'
                },
                {
                    name: 'click-slot',
                    title: 'Clicks'
                },
                {
                    name: 'action-slot',
                    title: 'Actions'
                }
            ],

        };
    },

    methods: {

        handleEditUtm(utm) {
            this.editUtm = Object.assign({}, utm);
            this.editUtm.disabled = utm.disabled ? false : true;
        },

        filterTable(event) {
            if ( event === false )
                this.reloadTableData();
        },

        changeStatus(utm, status) {
            axios.put('utms/status/'+ utm.id, {disabled: status} )
            .then(response => {
                utm.disabled = status;
                this.$alert.success(response.data.message);

            }).catch(error => {
                this.$alert.error(error.response.data.message);
            });
        },

        deleteUtm(utmId) {
            this.$confirm('Are you sure you want to delete this ' + this.utms[this.curUtm].type + '? Once deleted it cannot be undone.', 'Delete Link', {
                confirmButtonText: "Yes I'm sure",
            }).then(() => {

                // Send ajax request
                axios.delete('utms/'+ utmId)
                .then(response => {

                    // Reduce feature usage count
                    if ( ! this.$can('access admin') )
                        this.subscriptionUsage.consumed--;

                    // Remove pixel from the DOM
                    this.reloadTableData();
                    this.$alert.success(response.data.message);

                }).catch(error => {
                    this.$alert.error(error.response.data.message);
                });

            }).catch(() => { });
        },

        addUtmDone() {
            // Record feature usage count
            if ( ! this.$can('access admin') )
                this.subscriptionUsage.consumed++;

            this.reloadTableData();
        },

        handleUtms(utm) {
            if(utm.index !== this.curUtm) {
                this.curUtm = utm.index;
            }
        },

        reloadTableData() {
            this.$refs.utmtable.loadData();
        }
    }
}
</script>
