<template>
    <modal
        :title = "'Edit ' + utmType.type"
        :submit-text = "'Edit ' + utmType.type"
        size="small"
        :show.sync="open"        
        :submit-loading="btnLoading"
        @submit="updateUtm"
        @close="$emit('update:open', false)">

        <form data-vv-scope="form-eidt-utm" class="mt-3">
            
            <div class="form-group">
                <label>Name</label>
                <input type="text" name="name"
                    :class="['form-control', { 'is-invalid': errors.has('form-add-utm.name') }]"
                    v-validate="'required|max:40'"
                    v-model="editUtm.utm_name">
                <div class="invalid-feedback" v-show="errors.has('form-edit-utm.name')">{{ errors.first('form-edit-utm.name') }}</div>
            </div>

            <div class="form-group">
                <label>Description</label>
                <textarea rows="5" name="description" placeholder="e.g. summer_sale" 
                    :class="['form-control', { 'is-invalid': errors.has('form-edit-utm.description') }]"
                    v-validate="'required'"
                    v-model="editUtm.utm_description"></textarea>
                <div class="invalid-feedback" v-show="errors.has('form-edit-utm.description')">{{ errors.first('form-edit-utm.description') }}</div>
            </div>

            <div class="custom-control custom-switch">
                <input type="checkbox" class="custom-control-input" id="edit_utm_active" name="edit_utm_active" :true-value="true" v-model="editUtm.disabled">
                <label class="custom-control-label" for="edit_utm_active">Active</label>
            </div>

        </form>

    </modal>
</template>


<script>
export default {
	name: 'edit-utm-modal',

    props: {
        open: {
            type: Boolean,
            twoWay: true,
            default: false
        },

        utmType: {
            index: Number,
            name: String
        },

        editUtm: {},
    },


    data() {
        return {
            btnLoading: false
        };
    },

    methods: {
        updateUtm() {
            this.$validator.validateAll('form-edit-utm').then((success) => {
                if ( ! success )
                    return

                this.btnLoading = true;

                axios.put('utms/' + this.editUtm.id, this.editUtm)
                .then(response => {

                    this.btnLoading = false;
                    this.$emit('update:open', false);

                    this.$resetForm(this.editUtm);
                    this.$alert.success(response.data.message);

                    // Emit a done event so that the parent can take action
                    this.$emit('done');

                })
                .catch(error => {

                    this.btnLoading = false;
                    this.$backendErrors(error.response.data, 'form-edit-utm');

                    // Show error message if there is any
                    if ( error.response.data.message )
                        this.$alert.error(error.response.data.message);

                });

            });
        }
    }

}
</script>